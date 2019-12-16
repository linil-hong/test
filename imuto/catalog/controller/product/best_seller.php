<?php
class ControllerProductBestSeller extends Controller {
	public function index() {
		$this->load->language('product/best_seller');

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
			$limit = 9;
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

		$bestSellerProductsTotal = $this->model_catalog_product->getTotalProductsBestSeller($filter_data);
		$bestSellerProducts = $this->model_catalog_product->getProductBestSeller($filter_data);

		$data['products'] = array();
		$product_index_num = ($page - 1) * $limit + 1;
		foreach ($bestSellerProducts as $result) {
						//2019-5-22 国家货币价格切换
			switch($this->session->data['currency']){
				case "JPY":
					if($result['jp_price'] != 0){
						$result['price'] = $result['jp_price'];
					}else{
						$result['price'] = 0;
					}
					break;
				case "CAD":
					if($result['ca_price'] != 0){
						$result['price'] = $result['ca_price'];
					}else{
						$result['price'] = 0;
					}
					break;
				case "ITE":
					if($result['it_price'] != 0){
						$result['price'] = $result['it_price'];
					}else{
						$result['price'] = 0;
					}
					break;
				case "FRE":
					if($result['fr_price'] != 0){
						$result['price'] = $result['fr_price'];
					}else{
						$result['price'] = 0;
					}
					break;
				case "GBP":
					if($result['uk_price'] != 0){
						$result['price'] = $result['uk_price'];
					}else{
						$result['price'] = 0;
					}
					break;
			}

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
			// $product_summary = trim(strip_tags(html_entity_decode($result['summary'])));
				$product_summary = $result['summary'];
			if(!empty($product_summary)){
				// $product_summary = utf8_substr(trim(strip_tags(html_entity_decode($result['summary'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..';

				$product_summary = substr(html_entity_decode($product_summary, ENT_QUOTES, 'UTF-8'),0,120)."..";
			// var_dump($product_summary);

			}else{
				$product_summary = '';
			}

			//2019-2-27: 获取5星及4星的评论数量
			$one_star_review_count = $this ->model_catalog_review->getTotalProductReviewsByRating($result['product_id'],1);
			$two_star_review_count = $this ->model_catalog_review->getTotalProductReviewsByRating($result['product_id'],2);
			$three_star_review_count = $this ->model_catalog_review->getTotalProductReviewsByRating($result['product_id'],3);
			$five_star_review_count = $this->model_catalog_review->getTotalProductReviewsByRating($result['product_id'], 5);
			$four_star_review_count = $this->model_catalog_review->getTotalProductReviewsByRating($result['product_id'], 4);
			$all_star_review_count = $one_star_review_count+$two_star_review_count+$three_star_review_count+$five_star_review_count+$four_star_review_count;

			$data['products'][] = array(
				'serial_num' => $product_index_num,
				'product_id'  => $result['product_id'],
				'thumb'       => $image,
				'name'        => $result['name'],
				'model'  	  => $result['model'],
				'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
				'summary' 	  => $product_summary,
				'price'       => $price,
				'special'     => $special,
				'tax'         => $tax,
				'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
				'rating'      => $result['rating'],
				'all_star_review_count' => $all_star_review_count,
				'five_star_count' => $five_star_review_count,
				'four_star_count' => $four_star_review_count,
				'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'] . $url)
			);

			$product_index_num++;
		}

		$url = '';

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		$pagination = new Pagination();
		$pagination->total = $bestSellerProductsTotal;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('product/best_seller', $url . '&page={page}');

		$data['pagination'] = $pagination->render(true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$data['lang'] = $this->language->get('code');
		$data['en'] = 'en';
		$this->response->setOutput($this->load->view('product/best_seller', $data));
	}
}
