<?php

class ModelPaymentSeqrRefund extends Model {

	public function getSeqrOrders() {
		$query = "select CONCAT(o.firstname, ' ', o.lastname) as customerName, s.order_id, s.ers_reference, s.refund, o.total, o.currency_code from " . DB_PREFIX . "seqr s join oc_order o on s.order_id = o.order_id where s.ers_reference not like ''";

		$result = $this->db->query($query);
		$refunds = array();
		foreach ($result->rows as $row) {
			$shippingCost = $this->db->query("select SUM(value) shipping from oc_order_total where code ='shipping' and order_id = " . $row['order_id']);
			$row['shipping'] = $shippingCost->row['shipping'];
			$row['suggested_return'] = $row['total'] - $row['return'] - $row['shipping'];
			array_push($refunds, $row);
		}
		return $refunds;
    }

}
