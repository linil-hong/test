<?php
class ControllerCommonMenu extends Controller {
	public function index() {
		$this->load->language('common/menu');

		// Menu
		$this->load->model('catalog/category');

		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		//2019-1-22: 设置选择的子分类对应的顶级分类的active样式
		$curr_parent_category_id = 0;
		if (isset($this->request->get['path'])) {
			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			//分类只有一个时，设置当前分类id为顶级分类
			if(count($parts) > 1){
				array_pop($parts);
			}

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = (int)$path_id;
				} else {
					$path .= '_' . (int)$path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);

				if ($category_info) {
					$curr_parent_category_id = $category_info['category_id'];
				}
			}
		}

		$data['categories'] = array();

		$categories = $this->model_catalog_category->getCategories(0);

		$index = 0;
		foreach ($categories as $category) {
			if($index >= 5){
				break;
			}
			$index++;
			if ($category['top']) {
				// Level 2
				$children_data = array();

				$children = $this->model_catalog_category->getCategories($category['category_id']);

				foreach ($children as $child) {
					$filter_data = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true
					);

					//2019-1-25: 生成子分类设定分辨率的分类图片
					$childArrSize = array(
						'width' => 400,
						'height' => 170
					);
					if(!empty($child['image'])){
						$children_category_image = $this->model_tool_image->resize($child['image'], $childArrSize['width'], $childArrSize['height']);
					}else{
						$children_category_image = $this->model_tool_image->resize('no_image.png', $childArrSize['width'], $childArrSize['height']);
					}

					$children_data[] = array(
						'name'  => $child['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
						'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id']),
						'image'	=> $children_category_image

					);
				}

				//2019-1-21: 生成设定分辨率的分类图片
				$arrSize = array(
					'width' => 100,
					'height' => 100
				);
				if(!empty($category['image'])){
					$category_image = $this->model_tool_image->resize($category['image'], $arrSize['width'], $arrSize['height']);
				}else{
					$category_image = $this->model_tool_image->resize('no_image.png', $arrSize['width'], $arrSize['height']);
				}

				$active_class = '';
				if($curr_parent_category_id == $category['category_id']) {
					$active_class = ' active';
				}

				// Level 1
				$data['categories'][] = array(
					'name'     => $category['name'],
					'image'	   => $category_image,
					'children' => $children_data,
					'column'   => $category['column'] ? $category['column'] : 1,
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id']),
					'active'   => $active_class
				);
			}
		}

		$data['support'] = $this->url->link('information/support');
		$data['download'] = $this->url->link('information/download');
		$data['about_us'] = $this->url->link('information/about_us');
		$data['where_to_buy'] = $this->url->link('information/where_to_buy');
		$data['company_profile'] = $this->url->link('information/company_profile');
		$data['brand_introduction'] = $this->url->link('information/brand_introduction');
		$data['contact_us'] = $this->url->link('information/contact/page');
		$data['support_active'] = '';
		$data['about_us_active'] = '';
		$data['where_to_buy_active'] = '';
		$data['download_active'] = '';

		//2019-1-22: 根据route判断menu链接的active状态
		$route = isset($this->request->get['route'])?$this->request->get['route']:'';
		switch($route){
			case 'information/support':
				$data['support_active'] = 'active';
				break;
			case 'information/about_us':
				$data['about_us_active'] = 'active';
				break;
			case 'information/where_to_buy':
				$data['where_to_buy_active'] = 'active';
				break;
			case 'information/download':
				$data['download_active'] = 'active';
				break;
		}

		return $this->load->view('common/menu', $data);
	}
}
