<?php
class ControllerCatalogNewProducts extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/new_products');

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
			'href' => $this->url->link('catalog/new_products', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['success_msg'] = '';
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_product->addNewProducts($this->request->post);
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
			$products = $this->model_catalog_product->getNewProducts();
		}

		$data['products'] = array();
		foreach ($products as $newProduct) {
			$new_product_info = $this->model_catalog_product->getProduct($newProduct['product_id']);

			if ($new_product_info) {
				$data['products'][] = array(
					'product_id' => $new_product_info['product_id'],
					'name'       => $new_product_info['name'],
					'is_new'     => $newProduct['is_new'],
					'sort_order' => $newProduct['sort_order']
				);
			}
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/new_products_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/new_products')) {
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