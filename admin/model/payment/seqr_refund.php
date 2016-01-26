<?php

class ModelPaymentSeqrRefund extends Model {

	public function getSeqrOrders() {
		$query = "select CONCAT(o.firstname, ' ', o.lastname) as customerName, s.order_id, s.ers_reference, s.refund, o.total, o.currency_code from " . DB_PREFIX . "seqr s join oc_order o on s.order_id = o.order_id where s.ers_reference not like ''";

		$result = $this->db->query($query);
		return $result->rows;
    }

}
