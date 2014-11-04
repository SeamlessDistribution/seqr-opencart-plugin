<?php

class ModelPaymentSeqrApi extends Model {
    public function sendInvoice() {

        try {
            $SOAP = $this->SOAP();
            $arr = array(
                'context' => $this->getRequestContext(),
                'invoice' => $this->getInvoiceRequest()
            );
            $result = $SOAP->sendInvoice($arr)->return;

            if ($result->resultCode != 0) throw new Exception($result->resultCode . ' : ' . $result->resultDescription);

            return $result;
        } catch(Exception $e) {
            $this->log->write('Error: '.$e->getMessage());
            return null;
        }
    }

    public function getPaymentStatus($ref, $version) {

        try {
            $SOAP = $this->SOAP();
            $result = $SOAP->getPaymentStatus(array(
                "context" => $this->getRequestContext(),
                "invoiceReference" => $ref,
                "invoiceVersion" => $version
            ))->return;

            if ($result->resultCode != 0) throw new Exception($result->resultCode . ' : ' . $result->resultDescription);

            return $result;
        } catch(Exception $e) {
            $this->log->write('Error: ' . $e->getMessage());
            return null;
        }
    }

    public function cancelInvoice() {
        $ref = $this->session->data['seqr']->invoiceReference;

        try {
            $SOAP = $this->SOAP();
            $result = $SOAP->cancelInvoice(array(
                "context" => $this->getRequestContext(),
                "invoiceReference" => $ref
            ))->return;

            if ($result->resultCode != 0) throw new Exception($result->resultCode . ' : ' . $result->resultDescription);

            return $result;
        } catch(Exception $e) {
            $this->log->write('Error: ' . $e->getMessage());
            return null;
        }
    }

    private function SOAP() {

        return new SoapClient($this->config->get('seqr_soap_wsdl_url'), array( 'trace' => 1, 'connection_timeout' => 3000 ));
    }

    private function getRequestContext() {

        return array(
            'initiatorPrincipalId' => array(
                'id' => $this->config->get('seqr_terminal_id'),
                'type' => 'TERMINALID',
                'userId' => 9900
            ),
            'password' => $this->config->get('seqr_terminal_password'),
            'clientRequestTimeout' => '0'
        );
    }

    private function getInvoiceRequest() {
        $order = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $totals = array();
        $total = 0;
        $taxes = $this->cart->getTaxes();

        $this->load->model('extension/extension');

        $sort_order = array();

        $results = $this->model_extension_extension->getExtensions('total');
        foreach ($results as $key => $value) $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');

        array_multisort($sort_order, SORT_ASC, $results);

        foreach ($results as $result) if ($this->config->get($result['code'] . '_status')) {
            $this->load->model('total/' . $result['code']);
            $this->{'model_total_' . $result['code']}->getTotal($totals, $total, $taxes);
        }

        // Prepare main part of request data (ex Shipping and Discounts)
        $invoice = array(
            'paymentMode' => 'IMMEDIATE_DEBIT',
            'acknowledgmentMode' => 'NO_ACKNOWLEDGMENT',

            'issueDate' => date('Y-m-d\Th:i:s'),
            'title' => $this->config->get('config_meta_title'),
            'clientInvoiceId' => $order['order_id'],

            'invoiceRows' => array(),

            'totalAmount' => array(
                'currency' => $order['currency_code'],
                'value' => $this->toFloat($order['total'] * $order['currency_value'])
            ),

            'notificationUrl' => "{$order['store_url']}index.php?route=payment/seqr/notify&order_id={$order['order_id']}",
            'backURL' => "{$order['store_url']}index.php?route=payment/seqr/back&order_id={$order['order_id']}"
        );

        // Cart invoice items
        foreach ($this->model_checkout_order->cart->getProducts() as $item) {
            $rates = $this->tax->getRates($item['price'], $item['tax_class_id']);

            $invoice['invoiceRows'][] = array(
                'itemDescription' => $item['name'],
                'itemSKU' => $item['key'],
                'itemTaxRate' => (empty($rates) ? 0 : array_sum($rates)),
                'itemQuantity' => $item['quantity'],
                'itemUnitPrice' > array(
                    'currency' => $order['currency_code'],
                    'value' => $this->toFloat($item['price'] * $order['currency_value'])
                ),
                'itemTotalAmount' => array(
                    'currency' => $order['currency_code'],
                    'value' => $this->toFloat($item['total'] * $order['currency_value'])
                )
            );
        }

        // Additional invoice items. Shipping & Handling, Coupon
        foreach ($totals as $item) {
            if (in_array($item['code'], array('shipping', 'coupon', 'voucher'))) {
                $invoice['invoiceRows'][] = array(
                    'itemDescription' => $item['title'],
                    'itemQuantity' => 1,
                    'itemTotalAmount' => array(
                        'currency' => $order['currency_code'],
                        'value' => $this->toFloat($item['value'] * $order['currency_value'])
                    ),
                    'itemUnitPrice' => array(
                        'currency' => $order['currency_code'],
                        'value' => $this->toFloat($item['value'] * $order['currency_value'])
                    )
                );
            }
        }

        return $invoice;
    }

    public function toFloat($number) {
        return number_format((float) $number, 2, '.', '');
    }
}