<?php
/**
 * MODULO DE ORCAMENTO
 * @author Josean Matias
 * http://www.joseanmatias.com.br
 * @version 2.0
 */
class ControllerCheckoutOrcamento extends Controller {

    private $error = array();

    public function index() {

        if (!$this->customer->isLogged() && $this->config->get('orcamento_form')) {
            $this->session->data['redirect'] = $this->url->link('checkout/orcamento', '', 'SSL');

            $this->redirect($this->url->link('account/login', '', 'SSL'));
        }

        $this->language->load('checkout/orcamento');

        unset($this->session->data['shipping_method']);
        unset($this->session->data['shipping_methods']);

        // Validate cart has products and has stock.
        if (!$this->cart->hasProducts() || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
            $this->redirect($this->url->link('checkout/cart'));
        }

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {

            $this->language->load('checkout/checkout');

            $data = array();

            $data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
            $data['store_id'] = $this->config->get('config_store_id');
            $data['store_name'] = $this->config->get('config_name');

            if ($data['store_id']) {
                $data['store_url'] = $this->config->get('config_url');
            } else {
                $data['store_url'] = HTTP_SERVER;
            }

            $payment_address = '';

            if ($this->customer->isLogged()) {
                $data['customer_id'] = $this->customer->getId();
                $data['customer_group_id'] = $this->customer->getCustomerGroupId();
                $data['firstname'] = $this->customer->getFirstName();
                $data['lastname'] = $this->customer->getLastName();
                $data['email'] = $this->customer->getEmail();
                $data['telephone'] = $this->customer->getTelephone();
                $data['fax'] = $this->customer->getFax();

                $this->load->model('account/address');

                $payment_address = $this->model_account_address->getAddress($this->customer->getAddressId());
            } else {
                $data['customer_id'] = 0;
                $data['customer_group_id'] = 0;
                $data['firstname'] = $this->request->post['firstname'];
                $data['lastname'] = $this->request->post['lastname'];
                $data['email'] = $this->request->post['email'];
                $data['telephone'] = $this->request->post['telephone'];
                $data['fax'] = '';
            }

            if ($payment_address) {
                $data['payment_firstname'] = $payment_address['firstname'];
                $data['payment_lastname'] = $payment_address['lastname'];
                $data['payment_company'] = $payment_address['company'];
                $data['payment_company_id'] = isset($payment_address['company_id']) ? $payment_address['company_id'] : '';
                $data['payment_tax_id'] = isset($payment_address['tax_id']) ? $payment_address['tax_id'] : '';
                $data['payment_address_1'] = $payment_address['address_1'];
                $data['payment_address_2'] = $payment_address['address_2'];
                $data['payment_city'] = $payment_address['city'];
                $data['payment_postcode'] = $payment_address['postcode'];
                $data['payment_zone'] = $payment_address['zone'];
                $data['payment_zone_id'] = $payment_address['zone_id'];
                $data['payment_country'] = $payment_address['country'];
                $data['payment_country_id'] = $payment_address['country_id'];
                $data['payment_address_format'] = $payment_address['address_format'];
            } else {
                $data['payment_firstname'] = $this->request->post['firstname'];
                $data['payment_lastname'] = $this->request->post['lastname'];
                $data['payment_company'] = '';
                $data['payment_company_id'] = 0;
                $data['payment_tax_id'] = 0;
                $data['payment_address_1'] = '';
                $data['payment_address_2'] = '';
                $data['payment_city'] = '';
                $data['payment_postcode'] = '';
                $data['payment_zone'] = '';
                $data['payment_zone_id'] = 0;
                $data['payment_country'] = '';
                $data['payment_country_id'] = 0;
                $data['payment_address_format'] = '';
            }

            $data['payment_method'] = '';
            $data['payment_code'] = '';

            $data['shipping_firstname'] = '';
            $data['shipping_lastname'] = '';
            $data['shipping_company'] = '';
            $data['shipping_address_1'] = '';
            $data['shipping_address_2'] = '';
            $data['shipping_city'] = '';
            $data['shipping_postcode'] = '';
            $data['shipping_zone'] = '';
            $data['shipping_zone_id'] = '';
            $data['shipping_country'] = '';
            $data['shipping_country_id'] = '';
            $data['shipping_address_format'] = '';
            $data['shipping_method'] = '';
            $data['shipping_code'] = '';

            $product_data = array();

            foreach ($this->cart->getProducts() as $product) {
                $option_data = array();

                foreach ($product['option'] as $option) {
                    if ($option['type'] != 'file') {
                        $value = $option['option_value'];
                    } else {
                        $value = $this->encryption->decrypt($option['option_value']);
                    }

                    $option_data[] = array(
                        'product_option_id' => $option['product_option_id'],
                        'product_option_value_id' => $option['product_option_value_id'],
                        'option_id' => $option['option_id'],
                        'option_value_id' => $option['option_value_id'],
                        'name' => $option['name'],
                        'value' => $value,
                        'type' => $option['type']
                    );
                }

                $product_data[] = array(
                    'product_id' => $product['product_id'],
                    'name' => $product['name'],
                    'model' => $product['model'],
                    'option' => $option_data,
                    'download' => $product['download'],
                    'quantity' => $product['quantity'],
                    'subtract' => $product['subtract'],
                    'price' => $product['price'],
                    'total' => $product['total'],
                    'tax' => $this->tax->getTax($product['price'], $product['tax_class_id']),
                    'reward' => $product['reward']
                );
            }

            $data['products'] = $product_data;
            $data['vouchers'] = array();
            $data['totals'] = array();
            $data['comment'] = '';
            $data['total'] = 0;

            if (isset($this->request->cookie['tracking'])) {
                $this->load->model('affiliate/affiliate');

                $affiliate_info = $this->model_affiliate_affiliate->getAffiliateByCode($this->request->cookie['tracking']);
                $subtotal = $this->cart->getSubTotal();

                if ($affiliate_info) {
                    $data['affiliate_id'] = $affiliate_info['affiliate_id'];
                    $data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
                } else {
                    $data['affiliate_id'] = 0;
                    $data['commission'] = 0;
                }
            } else {
                $data['affiliate_id'] = 0;
                $data['commission'] = 0;
            }

            $data['language_id'] = $this->config->get('config_language_id');
            $data['currency_id'] = $this->currency->getId();
            $data['currency_code'] = $this->currency->getCode();
            $data['currency_value'] = $this->currency->getValue($this->currency->getCode());
            $data['ip'] = $this->request->server['REMOTE_ADDR'];

            if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
                $data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
            } elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
                $data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
            } else {
                $data['forwarded_ip'] = '';
            }

            if (isset($this->request->server['HTTP_USER_AGENT'])) {
                $data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
            } else {
                $data['user_agent'] = '';
            }

            if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
                $data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
            } else {
                $data['accept_language'] = '';
            }

            $this->load->model('checkout/order');

            $this->session->data['order_id'] = $this->model_checkout_order->addOrder($data);

            $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('orcamento_order_status_id'), $this->language->get('text_received'), $this->config->get('orcamento_notify'));

            $this->redirect($this->url->link('checkout/orcamento/success'));
        }

        // Validate minimum quantity requirments.
        $products = $this->cart->getProducts();

        foreach ($products as $product) {
            $product_total = 0;

            foreach ($products as $product_2) {
                if ($product_2['product_id'] == $product['product_id']) {
                    $product_total += $product_2['quantity'];
                }
            }

            if ($product['minimum'] > $product_total) {
                $this->redirect($this->url->link('checkout/cart'));
                break;
            }
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('checkout/orcamento'),
            'text' => $this->language->get('heading_title'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['column_name'] = $this->language->get('column_name');
        $this->data['column_model'] = $this->language->get('column_model');
        $this->data['column_quantity'] = $this->language->get('column_quantity');
        $this->data['column_price'] = $this->language->get('column_price');
        $this->data['column_total'] = $this->language->get('column_total');

        $this->data['entry_firstname'] = $this->language->get('entry_firstname');
        $this->data['entry_lastname'] = $this->language->get('entry_lastname');
        $this->data['entry_email'] = $this->language->get('entry_email');
        $this->data['entry_telephone'] = $this->language->get('entry_telephone');

        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['text_information'] = sprintf($this->language->get('text_information'), $this->language->get('button_confirm'));
        if($this->customer->isLogged()) {
            $this->data['text_logged'] = sprintf($this->language->get('text_logged'), $this->customer->getFirstName(), $this->url->link('account/logout'));
        } else {
            $this->data['text_logged'] = '';
        }
        $this->data['text_your_details'] = $this->language->get('text_your_details');

        $this->data['action'] = $this->url->link('checkout/orcamento', '', 'SSL');

        if (isset($this->session->data['warning'])) {
            $this->data['warning'] = $this->session->data['warning'];

            unset($this->session->data['warning']);
        } else {
            $this->data['warning'] = '';
        }

        if (isset($this->error['firstname'])) {
            $this->data['error_firstname'] = $this->error['firstname'];
        } else {
            $this->data['error_firstname'] = '';
        }

        if (isset($this->error['lastname'])) {
            $this->data['error_lastname'] = $this->error['lastname'];
        } else {
            $this->data['error_lastname'] = '';
        }

        if (isset($this->error['email'])) {
            $this->data['error_email'] = $this->error['email'];
        } else {
            $this->data['error_email'] = '';
        }

        if (isset($this->error['telephone'])) {
            $this->data['error_telephone'] = $this->error['telephone'];
        } else {
            $this->data['error_telephone'] = '';
        }

        if (isset($this->request->post['firstname'])) {
            $this->data['firstname'] = $this->request->post['firstname'];
        } else {
            $this->data['firstname'] = '';
        }

        if (isset($this->request->post['lastname'])) {
            $this->data['lastname'] = $this->request->post['lastname'];
        } else {
            $this->data['lastname'] = '';
        }

        if (isset($this->request->post['email'])) {
            $this->data['email'] = $this->request->post['email'];
        } else {
            $this->data['email'] = '';
        }

        if (isset($this->request->post['telephone'])) {
            $this->data['telephone'] = $this->request->post['telephone'];
        } else {
            $this->data['telephone'] = '';
        }

        $this->data['products'] = array();

        foreach ($products as $product) {
            $option_data = array();

            foreach ($product['option'] as $option) {
                if ($option['type'] != 'file') {
                    $value = $option['option_value'];
                } else {
                    $filename = $this->encryption->decrypt($option['option_value']);

                    $value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
                }

                $option_data[] = array(
                    'name' => $option['name'],
                    'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
                );
            }

            $this->data['products'][] = array(
                'product_id' => $product['product_id'],
                'name' => $product['name'],
                'model' => $product['model'],
                'option' => $option_data,
                'quantity' => $product['quantity'],
                'subtract' => $product['subtract'],
                'price' => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))),
                'total' => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity']),
                'href' => $this->url->link('product/product', 'product_id=' . $product['product_id'])
            );
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/checkout/orcamento.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/checkout/orcamento.tpl';
        } else {
            $this->template = 'default/template/checkout/orcamento.tpl';
        }

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }

    public function success() {

        if (isset($this->session->data['order_id']) && (!empty($this->session->data['order_id']))) {
            $this->session->data['last_order_id'] = $this->session->data['order_id'];
        }

        if (isset($this->session->data['order_id'])) {
            $this->cart->clear();

            unset($this->session->data['shipping_method']);
            unset($this->session->data['shipping_methods']);
            unset($this->session->data['payment_method']);
            unset($this->session->data['payment_methods']);
            unset($this->session->data['guest']);
            unset($this->session->data['comment']);
            unset($this->session->data['order_id']);
            unset($this->session->data['coupon']);
            unset($this->session->data['reward']);
            unset($this->session->data['voucher']);
            unset($this->session->data['vouchers']);
        }

        $this->language->load('checkout/orcamento');

        $this->document->setTitle($this->language->get('success_heading_title'));

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('checkout/cart'),
            'text' => $this->language->get('text_basket'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('checkout/success'),
            'text' => $this->language->get('text_success'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = $this->language->get('success_heading_title');
        $this->data['text_message'] = $this->language->get('text_message');
        $this->data['button_continue'] = $this->language->get('button_continue');
        $this->data['continue'] = $this->url->link('common/home');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/success.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/common/success.tpl';
        } else {
            $this->template = 'default/template/common/success.tpl';
        }

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }

    protected function validate() {

        if (!$this->customer->isLogged()) {
            if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
                $this->error['firstname'] = $this->language->get('error_firstname');
            }

            if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
                $this->error['lastname'] = $this->language->get('error_lastname');
            }

            if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
                $this->error['email'] = $this->language->get('error_email');
            }

            if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
                $this->error['telephone'] = $this->language->get('error_telephone');
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
?>
