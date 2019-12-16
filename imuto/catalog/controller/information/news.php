<?php
class ControllerInformationNews extends Controller {
	public function index() {
		$this->load->language('information/news');

		$this->load->model('catalog/news');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title') . '&nbsp;&gt;&gt;',
			'href' => $this->url->link('information/news')
		);

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = 6;
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

		$data['heading_title'] = $this->language->get('heading_title');

		$newsTotal = $this->model_catalog_news->getTotalNews();
		$newsList = $this->model_catalog_news->getNewsList($filter_data);

		$data['newsList'] = array();

		$this->load->model('tool/image');

		$imgWidth = 463;//350;
		$imgHidth = 175;//132;
		foreach($newsList as $news){
			if ($news['image']) {
				$image = $this->model_tool_image->resize($news['image'], $imgWidth, $imgHidth);
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', $imgWidth, $imgHidth);
			}

			$month_en = array(
				1 => "JAN",
				2 => "FEB",
				3 => "MAR",
				4 => "APR",
				5 => "MAY",
				6 => "JUN",
				7 => "JUL",
				8 => "AUG",
				9 => "SEP",
				10 => "OCT",
				11 => "NOV",
				12 => "DEC"
			);

			if($news['date_available'] != '0000-00-00') {
				$date = getdate(strtotime($news['date_available']));
				$date = 'NEWS &nbsp;&nbsp;' . $date['mday'] . '-' . $month_en[$date['mon']] . ' ' . $date['year'];
			} else {
				$date = getdate();
				$date = 'NEWS &nbsp;&nbsp;' . $date['mday'] . '-' . $month_en[$date['mon']] . ' ' . $date['year'];
			}

			$data['newsList'][] = array(
				'news_id' => $news['news_id'],
				'title' => $news['title'],
				'image' => $image,
				'date' => $date,
				'href' => $this->url->link('information/news/info', 'news_id=' . $news['news_id'])
			);
		}

		$url = '';

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}

		$pagination = new Pagination();
		$pagination->total = $newsTotal;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('information/news', $url . '&page={page}');

		$data['pagination'] = $pagination->render(true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('information/news_list', $data));
	}

	public function info() {
		$this->load->language('information/news');

		$this->load->model('catalog/news');

		$data['breadcrumbs'] = array();

		if (isset($this->request->get['news_id'])) {
			$news_id = (int)$this->request->get['news_id'];
		} else {
			$news_id = 0;
		}

		$news_info = $this->model_catalog_news->getSingleNews($news_id);

		if ($news_info) {
			$this->document->setTitle($news_info['meta_title']);
			$this->document->setDescription($news_info['meta_description']);
			$this->document->setKeywords($news_info['meta_keyword']);

			$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');

			$data['breadcrumbs'][] = array(
				'text' => $news_info['title'] . '&nbsp;&gt;&gt;',
				'href' => $this->url->link('information/news/info', 'news_id=' .  $news_id)
			);

			$data['heading_title'] = $news_info['title'];

			$data['description'] = html_entity_decode($news_info['description'], ENT_QUOTES, 'UTF-8');

			//2019-3-27: 新闻详情的图片，点击放大
			$preg_res = preg_match_all('/<\s*img\s+[^>]*?src\s*=\s*(\'|\")(.*?)\\1[^>]*?\/?\s*>/i',$data['description'],$match);
			if(!empty($preg_res)){
				$img_tags = $match[0];
				$img_srcs = $match[2];
				foreach ($img_tags as $key => $img_tag) {
					$img_src = $img_srcs[$key];
					$replace_html = '<a class="image-popup-vertical-fit" href="'.$img_src.'" title="'.$news_info['meta_title'].'">'.$img_tag.'</a>';
					$data['description'] = str_ireplace($img_tag, $replace_html, $data['description']);
				}
			}

			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('information/news_info', $data));
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error') . '&nbsp;&gt;&gt;',
				'href' => $this->url->link('information/news/info', 'news_id=' . $news_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}
}