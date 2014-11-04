<?php

class ModelPaymentSeqr extends Model {
    public function getMethod($address, $total) {
        $this->language->load('payment/seqr');

        return array(
            'code'       => 'seqr',
            'title'      => $this->language->get('text_title'),
            'terms'      => '',
            'sort_order' => 3
        );
    }
}