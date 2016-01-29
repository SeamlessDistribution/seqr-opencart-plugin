<?php

class ModelPaymentSeqrApi extends Model {
   
    private function SOAP() {

        return new SoapClient($this->config->get('seqr_soap_wsdl_url'), array( 'trace' => 1, 'connection_timeout' => 3000 ));
    }

    private function getRequestContext() {

        return array(
            'initiatorPrincipalId' => array(
                'id' => $this->config->get('seqr_terminal_id'),
                'type' => 'TERMINALID',
                'userId' => $this->config->get('seqr_user_id')
            ),
            'password' => $this->config->get('seqr_terminal_password'),
            'clientRequestTimeout' => '0'
        );
    }

    public function refundPayment($ersReference, $amount, $currencyCode) {

    	try {
    		$SOAP = $this->SOAP();
    		$invoice = $this->createRefundInvoice($amount, $currencyCode);
    		$result = $SOAP->refundPayment(array(
    				'context' => $this->getRequestContext(),
    				'ersReference' => $ersReference,
    				'invoice' => $invoice
    		))->return;

    		if ($result->resultCode != 0) throw new Exception($result->resultCode . " : " . $result->resultDescription);

    		return $result;
    	} catch(Exception $e) {
    		throw new Exception("SEQR API - Refund payment error");
    	}
    }

    private function createRefundInvoice($amount, $currencyCode) {
    	return array(
	            'title' => "SEQR refund",
	            'totalAmount' => array(
	                'currency' => $currencyCode,
	                'value' => $this->toFloat($amount)
	            ),
        		'cashierId' => 'WEB'
    	);
    }

    public function toFloat($number) {
        return number_format((float) $number, 2, '.', '');
    }
}
