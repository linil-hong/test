<?php
class ControllerCommonHome extends Controller {
	public function index() {
		$this->document->setTitle($this->config->get('config_meta_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->document->setKeywords($this->config->get('config_meta_keyword'));
		$this->load->language('common/home');
		if (isset($this->request->get['route'])) {
			$this->document->addLink($this->config->get('config_url'), 'canonical');
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		//2019-1-31: 首页的Discoun Acivity说明文案通过后台设置，前台获取数据
		$data['config_discount_activity_description'] = $this->config->get('config_discount_activity_description');

		$this->load->model('catalog/news');
		$filter_data = array(
			'start'               => 0,
			'limit'               => 4
		);
		$newsList = $this->model_catalog_news->getNewsList($filter_data);
		// var_dump($newsList[0]['title']);
		$data['news_one']=$newsList[0];
		$data['news_one']['news_url']=$this->url->link('information/news/info','news_id='. $newsList[0]['news_id']);
		// var_dump($data['news_one']['news_url']);
		$data['news_infos'] = array();
		foreach ($newsList as $news) {
			$data['news_infos'][] = array(
				'news_id' => $news['news_id'],
				'title'   => $news['title'],
				'summary' => nl2br($news['summary']),
				'image'   => 'image/' . $news['image'],
				'news_url'=> $this->url->link('information/news/info', 'news_id=' . $news['news_id'])
			);
		}
		// var_dump($data['news_infos'][0]['news_url']);
		$data['best_seller_href'] = $this->url->link('product/best_seller');
		$data['new_products_href'] = $this->url->link('product/new_products');
		$data['discount_activity_href'] = $this->url->link('product/discount_activity');
		$data['our_technology_href'] = $this->url->link('information/our_technology');
		$data['news_href'] = $this->url->link('information/news');

		$this->response->setOutput($this->load->view('common/home', $data));
	}

	public function subscribe(){
		$this->load->language('common/footer');
		$this->load->model('catalog/information');

		$json = array();
		$email = isset($this->request->post['email'])?urldecode($this->request->post['email']):0;
		//$rules = '/^[^\@]+@.*.[a-z]{2,15}$/i';
		$rules = '/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/i';
		if ((utf8_strlen($email) < 3) || !preg_match($rules, $email)) {
			$json['error'] = $this->language->get('error_email');
		}
		else{
			if( !$this->model_catalog_information->getSubscribes($email) ){
				$this->model_catalog_information->addSubscribe($email);
				$json['result'] = $this->language->get( 'text_success' );
			}
			else{
				$json['error'] = $this->language->get( 'text_repeat' );
			}

		}


		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput( json_encode($json) );


	}
}
