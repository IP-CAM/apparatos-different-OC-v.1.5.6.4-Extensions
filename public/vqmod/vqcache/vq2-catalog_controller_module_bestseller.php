<?php
class ControllerModuleBestSeller extends Controller {
	protected function index($setting) {
		$this->language->load('module/bestseller');

		$this->data['heading_title'] = $this->language->get('heading_title');
 $this->data['button_wishlist'] = $this->language->get('button_wishlist');
		$this->data['button_compare'] = $this->language->get('button_compare');
		$this->data['button_details'] = $this->language->get('button_details');
				
				
		$this->data['button_cart'] = $this->language->get('button_cart');

		$this->data['sale'] = $this->language->get('sale');
		$this->data['new'] = $this->language->get('new');
		
		
		$this->load->model('catalog/product');
		
		$this->load->model('tool/image');

		$this->data['products'] = array();
 
				$lim_last=5; 
				$data = array(
					'sort'  => 'p.date_added',
					'order' => 'DESC',
					'start' => 0,
					'limit' => $lim_last
				);
				$results1 = $this->model_catalog_product->getProducts($data);
				$last_array = array();
				foreach ($results1 as $result) {
				$last_array[] = $result['product_id'];			
				};
				
			

		$results = $this->model_catalog_product->getBestSellerProducts($setting['limit']);
		
		foreach ($results as $result) {
			if ($result['image']) {
				$image = $this->model_tool_image->resize($result['image'], $setting['image_width'], $setting['image_height']);
			} else {
				$image = false;
			}
			
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$price = false;
			}
					
 
			if ((float)$result['special']) { 
				$sale = round((1-$this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax'))/$this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')))*100);
			} else {
				$sale = false;
			}
			
			if ((float)$result['special']) {
				$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$special = false;
			}	
			
			if ($this->config->get('config_review_status')) {
				$rating = $result['rating'];
			} else {
				$rating = false;
			}
							

                      $price = null;
                      
            
			$this->data['products'][] = array(
				'product_id' => $result['product_id'],
 'description' =>html_entity_decode($result['description']),
				'description1' =>strip_tags (html_entity_decode($result['description'])),
				
				'thumb'   	 => $image,
				'name'    	 => $result['name'],
				'price'   	 => $price,
				'special' 	 => $special,
 'sale' 	 => $sale, 
				'rating'     => $rating,
  'last_array' => $last_array,  'limit_last' => $lim_last, 
				'reviews'    => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
				'href'    	 => $this->url->link('product/product', 'product_id=' . $result['product_id']),
			);
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/bestseller.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/bestseller.tpl';
		} else {
			$this->template = 'default/template/module/bestseller.tpl';
		}

		$this->render();
	}
}
?>