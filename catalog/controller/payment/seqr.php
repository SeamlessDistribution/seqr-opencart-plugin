<?php

class ControllerPaymentSeqr extends Controller {

    const STATUS_CANCELED = '{ "resultCode":0, "resultDescription":"SUCCESS", "status":"CANCELED", "version":0 }';

    public function index() {
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

        $data['url_poll'] = $baseUrl . 'index.php?route=payment/seqr/poll';

        $this->load->model('checkout/order');
        $this->load->model('payment/seqr_api');

        @$order = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $seqr = json_decode($order['payment_custom_field']);

        if ($seqr && $seqr->status == 'CANCELED') $this->model_payment_seqr_api->cancelInvoice();
        $result = $this->model_payment_seqr_api->sendInvoice();

        if ($result) {
            $result->version = 0;
            $result->status = 'ISSUED';

            $order['payment_custom_field'] = json_encode($result);
            @$this->model_checkout_order->editOrder($this->session->data['order_id'], $order);

            $data['qr_code'] = $result->invoiceQRCode;
            $data['reference'] = $result->invoiceReference;
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

        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $seqr = json_decode($order['payment_custom_field']);

        if (! $seqr) {
            ob_clean();
            echo self::STATUS_CANCELED;
            exit;
        }

        if (array_key_exists('status', $seqr) && in_array($seqr->status, array('CANCELED', 'PAID'))) {
            ob_clean();
            echo $order['payment_custom_field'];
            exit;
        }

        $result = $this->model_payment_seqr_api->getPaymentStatus($seqr->invoiceReference, $seqr->version);

        $seqr->version = $result->version;
        $seqr->status = $result->status;

        $order['payment_custom_field'] = json_encode($seqr);
        $this->model_checkout_order->editOrder($this->session->data['order_id'], $order);
        $this->updateOrder($this->session->data['order_id'], $seqr);

        ob_clean();
        echo @json_encode($seqr);
        exit;
    }

    public function notify() {
        if (! array_key_exists('order_id', $_GET) || ! $_GET['order_id']) $this->notFound();

        $this->load->model('payment/seqr_api');
        $this->load->model('checkout/order');

        $order = $this->model_checkout_order->getOrder($_GET['order_id']);
        if (! $order) $this->notFound();

        $seqr = json_decode($order['payment_custom_field']);
        if (array_key_exists('status', $seqr) && in_array($seqr->status, array('CANCELED', 'PAID'))) {
            ob_clean();
            exit;
        }

        $result = $this->model_payment_seqr_api->getPaymentStatus($seqr->invoiceReference, $seqr->version);

        $seqr->version = $result->version;
        $seqr->status = $result->status;

        $order['payment_custom_field'] = json_encode($seqr);
        $this->model_checkout_order->editOrder($this->session->data['order_id'], $order);
        $this->updateOrder($this->session->data['order_id'], $seqr);

        ob_clean();
    }

    public function back() {
        if (! array_key_exists('order_id', $_GET) || ! $_GET['order_id']) $this->notFound();

        $this->load->model('payment/seqr_api');
        $this->load->model('checkout/order');

        $order = $this->model_checkout_order->getOrder($_GET['order_id']);
        if (! $order) $this->notFound();

        $seqr = json_decode($order['payment_custom_field']);
        $result = $this->model_payment_seqr_api->getPaymentStatus($seqr->invoiceReference, $seqr->version);

        $seqr->version = $result->version;
        $seqr->status = $result->status;

        $order['payment_custom_field'] = json_encode($seqr);
        $this->model_checkout_order->editOrder($this->session->data['order_id'], $order);
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
}
