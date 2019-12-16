<?php
class ControllerInformationDownload extends Controller {
	public function index() {
		$this->load->language('information/download');
		$this->load->model('catalog/download');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('tool/image');

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][]= array(
						'text' =>'Home'. '&nbsp;&gt;',
						'href' => $this->url->link('common/home')
					);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title') . '&nbsp;&gt;',
			'href' => $this->url->link('information/download')
		);

		//get download data  2019-04-24
		$download_ids = $this->model_catalog_download->getDownload();
		$download_list = array();

		foreach($download_ids as $download_id){
			$download = $this->model_catalog_download->getDownloadID($download_id['download_id']);

			//image url rewirte
			if($download['image']) {
				$download['image'] = $this->model_tool_image->resize($download['image'], 250,250);
			} else {
				$download['image'] = $this->model_tool_image->resize('/no_image.png', 250, 250);
			}

			if(!empty($download['content'])) {
				$download['content'] = substr(html_entity_decode($download['content'], ENT_QUOTES, 'UTF-8'),0,200);
			}else{
				$download['content'] = "";
			}
			$download_list[] = array(
				'title' => $download['name'],
				'content' => $download['content'],
				'image' => $download['image'],
				'href' => $this->url->link('information/download/down', 'download_id=' . $download['download_id'], true)
			);
		}

		$data['download'] = $download_list;
		$data['text_downloads'] = $this->language->get('downloads');
		$data['text_content'] = $this->language->get('content');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('information/download', $data));
	}

	public function down() {
	$this->load->model('catalog/download');

	if (isset($this->request->get['download_id'])) {
		$download_id = $this->request->get['download_id'];
	} else {
		$download_id = 0;
	}

	$download_info = $this->model_catalog_download->getDownloadID($download_id);

	if ($download_info) {
		$file = DIR_DOWNLOAD . $download_info['filename'];
		$mask = basename($download_info['mask']);

		if (!headers_sent()) {
			if (file_exists($file)) {
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="' . ($mask ? $mask : basename($file)) . '"');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
				header('Content-Length: ' . filesize($file));

				if (ob_get_level()) {
					ob_end_clean();
				}

				readfile($file, 'rb');

				exit();
			} else {
				exit('Error: Could not find file ' . $file . '!');
			}
		} else {
			exit('Error: Headers already sent out!');
		}
	} else {
		exit('Error: Could not find file!');
	}
	}
}
