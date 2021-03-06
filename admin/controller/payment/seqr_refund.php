<?php
class ControllerPaymentSeqrRefund extends Controller {
    private $error = array();

    public function index() {
    	$this->load->language('payment/seqr_refund');
    	$this->document->setTitle($this->language->get('heading_title'));
    	$this->load->model('setting/setting');
    	
        $messages = array('heading_title', 'text_edit', 'text_enabled', 'text_disabled','text_yes', 'text_no',
        		'seqr_soap_wsdl_url', 'seqr_terminal_id', 'seqr_terminal_password', 'entry_test', 'seqr_order_status_paid',
        		'seqr_user_id', 'seqr_order_status_canceled', 'seqr_status', 'button_save', 'button_cancel');
        foreach ($messages as $code) $data['msg_' . $code] = $this->language->get($code);
        
        // Prepare navigation
        $data['breadcrumbs'] = array(
        		array(
        				'text'=> $this->language->get('text_home'),
        				'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
        				'separator' => false
        		),
        
        		array(
        				'text' => $this->language->get('text_payment'),
        				'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
        				'separator' => ' :: '
        		),
        
        		array(
        				'text' => $this->language->get('heading_title'),
        				'href' => $this->url->link('payment/seqr', 'token=' . $this->session->data['token'], 'SSL'),
        				'separator' => ' :: '
        		)
        );
        
        
        $this->load->model('payment/seqr_refund');
        $this->load->model('payment/seqr_api');
        if(isset($_POST['order_id'])) {
        	$seqrOrder = $this->model_payment_seqr_refund->getSeqrOrder($_POST['order_id']);
        	if(($_POST['return'] + $seqrOrder['refund']) <= $seqrOrder['total']) {
        		$this->model_payment_seqr_refund->createRefund($_POST['order_id'], $_POST['return'] + $seqrOrder['refund']);
        		$this->model_payment_seqr_api->refundPayment($seqrOrder['ers_reference'], $_POST['return'], $seqrOrder['currency_code']);
        		$data['ok'] = $this->language->get("ok_refund_success");
        	} else {
        		$data['error'] = $this->language->get("error_seqr_refund_greater_then_total_cost");
        	}
        }
        
        $data['seqr_order'] = $this->model_payment_seqr_refund->getSeqrOrders();
        
        
        
        // Prepare output
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['token'] = $this->session->data['token'];
        $this->response->setOutput($this->load->view('payment/seqr_refund.tpl', $data));
    }

}
