<?php
class ControllerInformationSupport extends Controller {
	public function index() {
		$this->load->language('information/support');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][]= array(
						'text' =>'Home'. '&nbsp;&gt;',
						'href' => $this->url->link('common/home')
					);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title') . '&nbsp;&gt;',
			'href' => $this->url->link('information/support')
		);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('information/support', $data));
	}
}
