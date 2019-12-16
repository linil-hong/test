<?php
class ControllerCatalogBestSeller extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/best_seller');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');

		$data = array();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/best_seller', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['success_msg'] = '';
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_product->addBestSellerProducts($this->request->post);
			$data['success'] = $this->language->get('text_success');
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['product'])) {
			$data['error_product'] = $this->error['product'];
		} else {
			$data['error_product'] = '';
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['products']) && !empty($this->error)) {
			$products = $this->request->post['products'];
		} else {
			$products = $this->model_catalog_product->getBestSellerProducts();
		}

		$data['products'] = array();
		foreach ($products as $bestSellerProduct) {
			$best_seller_info = $this->model_catalog_product->getProduct($bestSellerProduct['product_id']);

			if ($best_seller_info) {
				$data['products'][] = array(
					'product_id' => $best_seller_info['product_id'],
					'name'       => $best_seller_info['name'],
					'sort_order' => $bestSellerProduct['sort_order']
				);
			}
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/best_seller_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/best_seller')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!empty($this->request->post['products']) && is_array($this->request->post['products'])) {
			foreach($this->request->post['products'] as $product) {
				if(empty($product['product_id']) || !is_numeric($product['product_id'])) {
					$this->error['product'] = $this->language->get('error_product');
					break;
				}
			}
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}
}