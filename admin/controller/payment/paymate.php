<?php 
class ControllerPaymentPayMate extends Controller {
	private $error = array(); 

	public function index() {
		$this->load->language('payment/paymate');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('paymate', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
				
		$this->data['entry_username'] = $this->language->get('entry_username');
		$this->data['entry_total'] = $this->language->get('entry_total');	
		$this->data['entry_order_status'] = $this->language->get('entry_order_status');			
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['tab_general'] = $this->language->get('tab_general');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['username'])) {
			$this->data['error_username'] = $this->error['username'];
		} else {
			$this->data['error_username'] = '';
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/paymate', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = $this->url->link('payment/paymate', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->post['paymate_username'])) {
			$this->data['paymate_username'] = $this->request->post['paymate_username'];
		} else {
			$this->data['paymate_username'] = $this->config->get('paymate_username');
		}
		
		if (isset($this->request->post['paymate_total'])) {
			$this->data['paymate_total'] = $this->request->post['paymate_total'];
		} else {
			$this->data['paymate_total'] = $this->config->get('paymate_total'); 
		} 
				
		if (isset($this->request->post['paymate_order_status_id'])) {
			$this->data['paymate_order_status_id'] = $this->request->post['paymate_order_status_id'];
		} else {
			$this->data['paymate_order_status_id'] = $this->config->get('paymate_order_status_id'); 
		} 
		
		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['paymate_geo_zone_id'])) {
			$this->data['paymate_geo_zone_id'] = $this->request->post['paymate_geo_zone_id'];
		} else {
			$this->data['paymate_geo_zone_id'] = $this->config->get('paymate_geo_zone_id'); 
		} 
		
		$this->load->model('localisation/geo_zone');
										
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['paymate_status'])) {
			$this->data['paymate_status'] = $this->request->post['paymate_status'];
		} else {
			$this->data['paymate_status'] = $this->config->get('paymate_status');
		}
		
		if (isset($this->request->post['paymate_sort_order'])) {
			$this->data['paymate_sort_order'] = $this->request->post['paymate_sort_order'];
		} else {
			$this->data['paymate_sort_order'] = $this->config->get('paymate_sort_order');
		}

		$this->template = 'payment/paymate.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/paymate')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['paymate_username']) {
			$this->error['username'] = $this->language->get('error_username');
		}
				
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>