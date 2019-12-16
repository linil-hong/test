<?php
class ControllerInformationWhereToBuy extends Controller {
	public function index() {
		$this->load->language('information/where_to_buy');

		$this->document->setTitle($this->language->get('heading_title'));
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][]= array(
						'text' =>'Home'. '&nbsp;&gt;',
						'href' => $this->url->link('common/home')
					);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title') . '&nbsp;&gt;',
			'href' => $this->url->link('information/where_to_buy')
		);

		$data['online_sales'] = array(
			'amazon' => array(
				'icon' => 'catalog/view/theme/imuto/image/icon/icon-amazon.png',
				'countries' => array(
					array(
						'href' => 'https://www.amazon.com/imuto',
						'image' => 'catalog/view/theme/imuto/image/country/icon-usa.png'
					),
					array(
						'href' => 'https://www.amazon.ca/stores/node/14072772011?_encoding=UTF8&field-lbr_brands_browse-bin=imuto&ref_=bl_dp_s_web_14072772011',
						'image' => 'catalog/view/theme/imuto/image/country/icon-ca.png'
					),
					array(
						'href' => 'http://amz.masadora.net/shops/A28M2AA7622TQP?ref_=v_sp_storefront',
						'image' => 'catalog/view/theme/imuto/image/country/icon-jp.png'
					),
					array(
						'href' => 'https://www.amazon.co.uk/s/ref=nb_sb_noss_2?url=search-alias%3Daps&field-keywords=imuto&rh=i%3Aaps%2Ck%3Aimuto',
						'image' => 'catalog/view/theme/imuto/image/country/icon-uk.png'
					),
					array(
						'href' => 'https://www.amazon.fr/s/ref=nb_sb_noss_2/254-4987593-4227467?__mk_fr_FR=%C3%85M%C3%85%C5%BD%C3%95%C3%91&url=search-alias%3Daps&field-keywords=IMUTO',
						'image' => 'catalog/view/theme/imuto/image/country/icon-fr.png'
					),
					array(
						'href' => 'https://www.amazon.it/s/ref=nb_sb_noss_2/253-6645099-5502606?__mk_it_IT=%C3%85M%C3%85%C5%BD%C3%95%C3%91&url=search-alias%3Daps&field-keywords=IMUTO',
						'image' => 'catalog/view/theme/imuto/image/country/icon-it.png'
					),
					array(
						'href' => 'https://www.amazon.com.au/s?k=imuto&ref=nb_sb_noss_2',
						'image' => 'catalog/view/theme/imuto/image/country/icon-au.png'
					),
					array(
						'href' => 'https://uae.souq.com/ae-en/imuto/s/?as=1',
						'image' => 'catalog/view/theme/imuto/image/icon/icon-uae.png'
					)
				)
			),
			// 'souq' => array(
			// 	'icon' => 'catalog/view/theme/imuto/image/icon/icon-souq.png',
			// 	'countries' => array(
			// 		array(
			// 			'href' => 'https://uae.souq.com/ae-en/imuto/s/?as=1',
			// 			'image' => 'catalog/view/theme/imuto/image/icon/icon-uae.png'
			// 		)
			// 	)
			// ),
			// 'walmart' => array(
			// 	'icon' => 'catalog/view/theme/imuto/image/icon/icon-walmart.png',
			// 	'countries' => array(
			// 		array(
			// 			'href' => 'javascript:;',
			// 			'image' => 'catalog/view/theme/imuto/image/country/icon-usa.png'
			// 		)
			// 	)
			// )
		);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('information/where_to_buy', $data));
	}
}
