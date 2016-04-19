<?php

class ControllerPaymentSeqr extends Controller {

    const STATUS_CANCELED = '{ "resultCode":0, "resultDescription":"SUCCESS", "status":"CANCELED", "version":0 }';
    private $dbTable = "seqr";

    function __construct($registry) {
        parent::__construct($registry);
        $this->dbTable = DB_PREFIX . $this->dbTable;
    }

    public function index() {
        $this->install();
        $this->load->language('payment/seqr');

        $data['text_unavailable'] = $this->language->get('text_unavailable');
        $data['msg_title'] = $this->language->get('text_title');

        if (isset($this->request->server['HTTPS'])
            && (($this->request->server['HTTPS'] == 'on')
                || ($this->request->server['HTTPS'] == '1'))) {

            $baseUrl = $this->config->get('config_ssl');
        } else {
            $baseUrl = $this->config->get('config_url');
        }

        $data['url_poll'] = urlencode($baseUrl . 'index.php?route=payment/seqr/poll');

        $this->load->model('checkout/order');
        $this->load->model('payment/seqr_api');

        $seqr = $this->read($this->session->data['order_id']);

        if ($seqr && $seqr->status == 'CANCELED') {
            $this->model_payment_seqr_api->cancelInvoice($seqr->invoiceReference);
            $seqr = null;
        }

        if (! $seqr) {
            $result = $this->model_payment_seqr_api->sendInvoice();

            if ($result) {
                $result->version = 0;
                $result->status = 'ISSUED';

                $seqr = $result;
                $this->save($this->session->data['order_id'], $result);
            }
        }

        if ($seqr) {
            $data['test'] = $this->config->get('seqr_test');
            $data['qr_code'] = urlencode($seqr->invoiceQRCode);
            $data['reference'] = urlencode($seqr->invoiceReference);
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/seqr.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/payment/seqr.tpl', $data);
        }

        return $this->load->view('default/template/payment/seqr.tpl', $data);
    }

    public function poll() {
        if (! array_key_exists('order_id', $this->session->data)) $this->notFound();

        $this->load->model('payment/seqr_api');
        $this->load->model('checkout/order');

        $seqr = $this->read($this->session->data['order_id']);

        if (! $seqr) {
            ob_clean();
            echo self::STATUS_CANCELED;
            exit;
        }

        if (array_key_exists('status', $seqr) && in_array($seqr->status, array('CANCELED', 'PAID'))) {
            ob_clean();
            echo @json_encode($seqr);
            exit;
        }

        $result = $this->model_payment_seqr_api->getPaymentStatus($seqr->invoiceReference, $seqr->version);

        $seqr->version = $result->version;
        $seqr->status = $result->status;
        print_r($result->ersReference);
        $this->save($this->session->data['order_id'], $seqr, $result->ersReference);
        $this->updateOrder($this->session->data['order_id'], $seqr);

        ob_clean();
        echo @json_encode($seqr);
        exit;
    }

    public function notify() {
        if (! array_key_exists('order_id', $_GET) || ! $_GET['order_id']) $this->notFound();

        $this->load->model('payment/seqr_api');
        $this->load->model('checkout/order');

        $seqr = $this->read($_GET['order_id']);
        if (array_key_exists('status', $seqr) && in_array($seqr->status, array('CANCELED', 'PAID'))) {
            ob_clean();
            exit;
        }

        $result = $this->model_payment_seqr_api->getPaymentStatus($seqr->invoiceReference, $seqr->version);

        $seqr->version = $result->version;
        $seqr->status = $result->status;

        $this->save($this->session->data['order_id'], $seqr, $result->ersReference);
        $this->updateOrder($this->session->data['order_id'], $seqr);

        ob_clean();
    }

    public function back() {
        if (! array_key_exists('order_id', $_GET) || ! $_GET['order_id']) $this->notFound();

        $this->load->model('payment/seqr_api');
        $this->load->model('checkout/order');

        $order = $this->model_checkout_order->getOrder($_GET['order_id']);
        if (! $order) $this->notFound();

        $seqr = $this->read($_GET['order_id']);
        $result = $this->model_payment_seqr_api->getPaymentStatus($seqr->invoiceReference, $seqr->version);

        $seqr->version = $result->version;
        $seqr->status = $result->status;

        $this->save($this->session->data['order_id'], $seqr, $result->ersReference);
        $this->updateOrder($this->session->data['order_id'], $seqr);

        if (! in_array($seqr->status, array('CANCELED', 'PAID'))) $this->notFound();
        $target = $seqr->status == 'PAID' ? 'checkout/success' : 'checkout/cart';

        ob_clean();
        header("Location: {$order['store_url']}/index.php?route={$target}");
    }

    public function updateOrder($orderId, $seqr) {
        if (! in_array($seqr->status, array('CANCELED', 'PAID'))) return;

        $status = $seqr->status == 'PAID'
            ? $this->config->get('seqr_order_status_paid')
            : $this->config->get('seqr_order_status_canceled');

        $this->model_checkout_order->addOrderHistory($orderId, $status);
    }

    public function notFound() {
        ob_clean();
        header("HTTP/1.0 404 Not Found");
        exit;
    }

    private function save($order_id, $data, $ers_reference = '') {
        if (! $order_id || ! $data) return;

        $check_result = $this->db->query("SELECT order_id FROM {$this->dbTable} WHERE order_id = '{$this->db->escape($order_id)}'");
        $data_json = json_encode($data);

        if (! $check_result->num_rows) {
            $this->db->query("INSERT INTO {$this->dbTable} SET order_id = {$this->db->escape($order_id)}, json_data = '{$this->db->escape($data_json)}'");
            return;
        }

        $this->db->query("UPDATE {$this->dbTable} SET json_data = '{$this->db->escape($data_json)}', ers_reference = '{$this->db->escape($ers_reference)}' WHERE order_id = {$this->db->escape($order_id)}");
    }

    private function read($order_id) {
        if (! $order_id) return null;

        $query = $this->db->query("SELECT json_data FROM {$this->dbTable} WHERE order_id = '{$this->db->escape($order_id)}'");

        if (! $query->num_rows) return null;
        return json_decode($query->row['json_data']);
    }

    private function install() {
        $this->db->query("CREATE TABLE IF NOT EXISTS {$this->dbTable} (
            order_id int(11) NOT NULL,
            json_data VARCHAR(225) NOT NULL,
            refund decimal(15,4) default 0,
            ers_reference VARCHAR(225),
            PRIMARY KEY (order_id)
        )");
        
        $result = $this->db->query("Show columns from {$this->dbTable} like 'ers_reference'");
        
        if($result->num_rows == 0) {	
        	$this->db->query("ALTER TABLE {$this->dbTable} ADD refund decimal(15,4) default 0");
        	$this->db->query("ALTER TABLE {$this->dbTable} ADD ers_reference VARCHAR(225)");
        }
    }
}
