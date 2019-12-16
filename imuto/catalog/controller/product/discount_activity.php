<?php
class ControllerProductDiscountActivity extends Controller {
	public function index() {
		$this->load->language('product/discount_activity');

		$this->load->model('catalog/product');
		
		$this->load->model('tool/image');
		$this->load->model('catalog/review');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = 12;
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		$filter_data = array(
			'start'               => ($page - 1) * $limit,
			'limit'               => $limit
		);

		$discountActivityProductsTotal = $this->model_catalog_product->getTotalDiscountActivityProducts($filter_data);
		$discountActivityProducts = $this->model_catalog_product->getDiscountActivityProducts($filter_data);

		$data['products'] = array();
		$symbol = "";
		$product_index_num = ($page - 1) * $limit + 1;
		foreach ($discountActivityProducts as $result) {
			if ($result['image']) {
				$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
			}

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$price = false;
			}

			if ((float)$result['special']) {
				$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$special = false;
			}

			if ($this->config->get('config_tax')) {
				$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
			} else {
				$tax = false;
			}

			if ($this->config->get('config_review_status')) {
				$rating = (int)$result['rating'];
			} else {
				$rating = false;
			}

			//2019-2-19: 分类列表如果没有摘要，则不显示任何内容
			$product_summary = trim(strip_tags(html_entity_decode($result['summary'])));
			if(!empty($product_summary)){
				$product_summary = utf8_substr(trim(strip_tags(html_entity_decode($result['summary'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..';
			}else{
				$product_summary = '';
			}

			//2019-2-27: 获取5星及4星的评论数量
			$five_star_review_count = $this->model_catalog_review->getTotalProductReviewsByRating($result['product_id'], 5);
			$four_star_review_count = $this->model_catalog_review->getTotalProductReviewsByRating($result['product_id'], 4);

			//2019-2-27：获取产品关联的国家属性链接
			$shop_country_attribute = array();
			$attribute_groups = $this->model_catalog_product->getProductAttributes($result['product_id']);
			if(!empty($attribute_groups)){
				foreach($attribute_groups as $attribute_group){
					$attr_group_key = strtolower($attribute_group['name']);
					if($attr_group_key == 'shoplink'){
						foreach($attribute_group['attribute'] as $attribute_item){
							$attr_key = strtolower($attribute_item['name']);
							$upper_attr_key = strtoupper($attribute_item['name']);
							$shop_country_attribute[] = array('name'=> $attr_key, 'text'=>$attribute_item['text'], 'upper_name'=>$upper_attr_key);
						}
					}
				}
			}

			//2019-4-11: 折扣活动页面的产品，不再获取产品关联的国家属性链接
			//改为获取oc_product_country_price的国家价格，前台点击对应国家图标后，显示不同的原价、折扣价、和折扣比例
			$product_country_prices = $this->model_catalog_product->getProductCountryPrices($result['product_id']);
			// var_dump($product_country_prices);die;
			$product_country_prices_data = array();
			foreach ($product_country_prices as $product_country_price) {
				$country_off = 0;
				$special_price = (float)$product_country_price['special_price'];
				if(!empty($special_price)){
					$special_price = $this->currency->format_d($this->tax->calculate($product_country_price['special_price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'],$product_country_price['symbol']);
					if(bccomp($product_country_price['special_price'], $product_country_price['original_price'], 2) !== 0) {
						$country_off = round(abs( 1-floatval($product_country_price['special_price'])/floatval($product_country_price['original_price']) ),2) * 100;
					}
				} else {
					$special_price = false;
				}	
				$product_country_prices_data[] = array(
					'name'=> strtolower($product_country_price['country_attribute_name']),
					'upper_name'=>strtoupper($product_country_price['country_attribute_name']),
					'original_price'=>$this->currency->format_d($this->tax->calculate($product_country_price['original_price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'],$product_country_price['symbol']),
					'special_price'=>$special_price,
					'date_start'=>$product_country_price['date_start'].' '.$product_country_price['time_start'],
					'date_end'=>$product_country_price['date_end'].' '.$product_country_price['time_end'],
					'country_off'=>$country_off
				);
			}
			//2019-2-27: 计算off百分比
			$off = $country_off;	//2019-5-21 
			// if(!empty($result['special']) && bccomp($result['special'], $result['price'], 2) !== 0) {
			// 	$off = round(abs( 1-floatval($result['special'])/floatval($result['price']) ),2) * 100;
			// }
			$data['products'][] = array(
				'serial_num' => $product_index_num,
				'product_id'  => $result['product_id'],
				'thumb'       => $image,
				'name'        => $result['name'],
				'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
				'summary' 	  => $product_summary,
				'price'       => $price,
				'special'     => $special,
				'off'		  => $off,
				'tax'         => $tax,
				'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
				'rating'      => $result['rating'],
				'five_star_count' => $five_star_review_count,
				'four_star_count' => $four_star_review_count,
				'shop_country_attribute' => $shop_country_attribute,
				'product_country_prices' => $product_country_prices_data,
				'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'] . $url)
			);
			$product_index_num++;
		}

		$url = '';

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		$pagination = new Pagination();
		$pagination->total = $discountActivityProductsTotal;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('product/discount_activity', $url . '&page={page}');

		$data['pagination'] = $pagination->render(true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['lang'] = $this->language->get('code');
		$data['en'] = 'en';
		$this->response->setOutput($this->load->view('product/discount_activity', $data));
	}
}
