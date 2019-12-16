<?php
class ControllerMarketingGroup extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('marketing/group');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['user_token'] = $this->session->data['user_token'];

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('marketing/group', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['cancel'] = $this->url->link('marketing/group', 'user_token=' . $this->session->data['user_token'], true);

		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		$data['country'] = array('us','jp','fr','au','ca','it','uk','ae','de');
		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('marketing/group', $data));
	}

	public function send() {
		$this->load->language('marketing/group');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->user->hasPermission('modify', 'marketing/group')) {
				$json['error']['warning'] = $this->language->get('error_permission');
			}

			if (!$this->request->post['subject']) {
				$json['error']['subject'] = $this->language->get('error_subject');
			}

			if (!$this->request->post['message']) {
				$json['error']['message'] = $this->language->get('error_message');
			}

			if (!$json) {
				$this->load->model('setting/store');

				$store_info = $this->model_setting_store->getStore($this->request->post['store_id']);

				if ($store_info) {
					$store_name = $store_info['name'];
				} else {
					$store_name = $this->config->get('config_name');
				}
				
				$this->load->model('setting/setting');
				$setting = $this->model_setting_setting->getSetting('config', $this->request->post['store_id']);
				$store_email = isset($setting['config_email']) ? $setting['config_email'] : $this->config->get('config_email');

				$this->load->model('customer/customer');

				$this->load->model('customer/customer_group');

				$this->load->model('sale/order');

				if (isset($this->request->get['page'])) {
					$page = $this->request->get['page'];
				} else {
					$page = 1;
				}

				$email_total = 0;

				// $emails = array();
				// $emails = explode('/',$this->request->post['to']);
			$country = $this->request->post['to'];
			require_once 'C:/imuto_website/imuto/admin/PHPExcel/PHPExcel/IOFactory.php';
			require_once 'C:/imuto_website/imuto/admin/PHPExcel/PHPExcel/Reader/Excel5.php';
			require_once "C:/imuto_website/imuto/admin/PHPExcel/PHPExcel.php";
			//以上三步加载phpExcel的类
			$objReader = PHPExcel_IOFactory::createReader('Excel2007');//use excel2007 for 2007 format 
			$filename="C:/imuto_website/".$country.".xlsx";//指定excel文件
			$objPHPExcel = $objReader->load($filename); //$filename可以是上传的文件，或者是指定的文件
			$sheet = $objPHPExcel->getSheet(0); 
			$highestRow = $sheet->getHighestRow(); // 取得总行数
			$highestColumn = $sheet->getHighestColumn(); // 取得总列数
			$k = 0;

			//循环读取excel文件,读取一条,插入一条
			//j表示从哪一行开始读取
			//$a表示列号
			for($j=1;$j<=$highestRow;$j++)
			{
			    $emails[] = $objPHPExcel->getActiveSheet()->getCell("A".$j)->getValue();//获取A列的值
			}

				if ($emails) {
					$json['success'] = $this->language->get('text_success');

					$start = ($page - 1) * 10;
					$end = $start + 10;

					$json['success'] = sprintf($this->language->get('text_sent'), $start, $email_total);

					if ($end < $email_total) {
						$json['next'] = str_replace('&amp;', '&', $this->url->link('marketing/group/send', 'user_token=' . $this->session->data['user_token'] . '&page=' . ($page + 1), true));
					} else {
						$json['next'] = '';
					}

					$message  = '<html dir="ltr" lang="en">' . "\n";
					$message .= '  <head>' . "\n";
					$message .= '    <title>' . $this->request->post['subject'] . '</title>' . "\n";
					$message .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
					$message .= '  </head>' . "\n";
					$message .= '  <body>' . html_entity_decode($this->request->post['message'], ENT_QUOTES, 'UTF-8') . '</body>' . "\n";
					$message .= '</html>' . "\n";

					foreach ($emails as $email) {
						if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
							$mail = new Mail($this->config->get('config_mail_engine'));
							$mail->parameter = $this->config->get('config_mail_parameter');
							$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
							$mail->smtp_username = $this->config->get('config_mail_smtp_username');
							$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
							$mail->smtp_port = $this->config->get('config_mail_smtp_port');
							$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

							$mail->setTo($email);
							$mail->setFrom($store_email);
							$mail->setSender(html_entity_decode($store_name, ENT_QUOTES, 'UTF-8'));
							$mail->setSubject(html_entity_decode($this->request->post['subject'], ENT_QUOTES, 'UTF-8'));
							$mail->setHtml($message);
							$mail->send();
						}
					}
				} else {
					$json['error']['email'] = $this->language->get('error_email');
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
