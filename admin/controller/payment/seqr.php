<?php
class ControllerPaymentSeqr extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('payment/seqr');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        // Verify request
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
            $this->model_setting_setting->editSetting('seqr', $this->getPostData());
            $this->session->data['success'] = $this->language->get('text_success');

            $target = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
            $this->response->redirect($target);
        }

        $data['error'] = @ $this->error;

        // Load messages
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

        $data['action'] = $this->url->link('payment/seqr', 'token=' . $this->session->data['token'], 'SSL');
        $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        // Load properties from request (or configuration)
        $properties = array('seqr_terminal_id', 'seqr_terminal_password', 'seqr_test', 'seqr_order_status_paid',
            'seqr_user_id', 'seqr_order_status_canceled', 'seqr_soap_wsdl_url', 'seqr_status');
        foreach ($properties as $key) $data[$key] = $this->requestOrConfig($key);

        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        // Prepare output
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('payment/seqr.tpl', $data));
    }

    protected function validate() {
        if (! $this->user->hasPermission('modify', 'payment/seqr'))
            $this->error['warning'] = $this->language->get('error_permission');

        $required = array('seqr_terminal_id', 'seqr_terminal_password', 'seqr_soap_wsdl_url');
        foreach ($required as $field) $this->required($field);

        if (! array_key_exists('seqr_soap_wsdl_url', $this->error)) {
            try {
                @ new SoapClient($this->getPostData()['seqr_soap_wsdl_url']);
            } catch (Exception $e) {
                $this->error['seqr_soap_wsdl_url'] = $this->language->get('error_seqr_wsdl_unavailable');
            }
        }

        return ! $this->error;
    }

    private function requestOrConfig($key) {
        return array_key_exists($key, $this->getPostData()) ? $this->getPostData()[$key] : $this->config->get($key);
    }

    private function required($key) {
        if (! array_key_exists($key, $this->getPostData()) || ! $this->getPostData()[$key])
            $this->error[$key] = $this->language->get('error_' . $key);
    }

    private function getPostData() {
        return $this->request->post;
    }
}