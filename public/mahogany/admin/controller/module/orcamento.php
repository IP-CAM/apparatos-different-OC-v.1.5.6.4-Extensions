<?php

class ControllerModuleOrcamento extends Controller {
    private $error = array();
    private $vqmod_file = 'mod_orcamento.xml';
    private $vqmod_disabled_file = 'mod_orcamento.xml-disabled';

    public function index() {
        $this->language->load('module/orcamento');

        $this->document->setTitle($this->language->get('heading_title_inner'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $DIR = str_replace('catalog/', '', DIR_CATALOG) . 'vqmod/xml/';

            if (!$this->request->post['orcamento_status'] && file_exists($DIR . $this->vqmod_file)) {
                @rename($DIR . $this->vqmod_file, $DIR . $this->vqmod_disabled_file);
            } elseif (file_exists($DIR . $this->vqmod_disabled_file)) {
                @rename($DIR . $this->vqmod_disabled_file, $DIR . $this->vqmod_file);
            }

            $this->model_setting_setting->editSetting('orcamento', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->data['heading_title'] = $this->language->get('heading_title_inner');

        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        $this->data['text_yes'] = $this->language->get('text_yes');
        $this->data['text_no'] = $this->language->get('text_no');
        $this->data['text_all_zones'] = $this->language->get('text_all_zones');
        $this->data['text_none'] = $this->language->get('text_none');

        $this->data['entry_order_status'] = $this->language->get('entry_order_status');
        $this->data['entry_form'] = $this->language->get('entry_form');
        $this->data['entry_status'] = $this->language->get('entry_status');

        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_inner'),
            'href' => $this->url->link('module/orcamento', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['action'] = $this->url->link('module/orcamento', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['orcamento_order_status_id'])) {
            $this->data['orcamento_order_status_id'] = $this->request->post['orcamento_order_status_id'];
        } else {
            $this->data['orcamento_order_status_id'] = $this->config->get('orcamento_order_status_id');
        }

        if (isset($this->request->post['orcamento_form'])) {
            $this->data['orcamento_form'] = $this->request->post['orcamento_form'];
        } else {
            $this->data['orcamento_form'] = $this->config->get('orcamento_form');
        }

        if (isset($this->request->post['orcamento_status'])) {
            $this->data['orcamento_status'] = $this->request->post['orcamento_status'];
        } else {
            $this->data['orcamento_status'] = $this->config->get('orcamento_status');
        }

        $this->load->model('localisation/order_status');

        $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->template = 'module/orcamento.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    public function install() {
        $store_id = $this->config->get('config_store_id');

        $this->db->query("UPDATE `" . DB_PREFIX . "setting` SET value = '0' WHERE (`key` = 'sub_total_status' OR `key` = 'total_status' OR `key` = 'shipping_estimator' OR `key` = 'voucher_status' OR `key` = 'coupon_status' OR `key` = 'tax_status') AND store_id = '" . (int) $store_id . "'");
    }

    public function uninstall() {
        $store_id = $this->config->get('config_store_id');

        $this->db->query("UPDATE `" . DB_PREFIX . "setting` SET value = '1' WHERE (`key` = 'sub_total_status' OR `key` = 'total_status' OR `key` = 'shipping_estimator' OR `key` = 'voucher_status' OR `key` = 'coupon_status' OR `key` = 'tax_status') AND store_id = '" . (int) $store_id . "'");

        $DIR = str_replace('catalog/', '', DIR_CATALOG) . 'vqmod/xml/';

        @rename($DIR . $this->vqmod_file, $DIR . $this->vqmod_disabled_file);
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'module/orcamento')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
?>