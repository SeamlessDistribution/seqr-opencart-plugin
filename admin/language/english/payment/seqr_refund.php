<?php
// Heading
$_['heading_title'] = 'SEQR Refund';

// Text
$_['text_success'] = 'Success: You have modified SEQR account details!';
$_['text_edit'] = 'Edit SEQR Refund';
$_['seqr_logo'] = '<img src="https://cdn.seqr.com/webshop-plugin/images/seqr-logo.svg" style="width: 150px" />';
$_['text_seqr'] = '<a target="_blank" href="http://developer.seqr.com/merchant/webshop/">' . $_['seqr_logo'] . '</a>';
$_['text_payment'] = 'Payments';
$_['ok_refund_success'] = 'Success: Refund has been updated.';

// Entry
$_['seqr_soap_wsdl_url'] = 'URI to WSDL';
$_['seqr_user_id'] = 'User ID';
$_['seqr_terminal_id'] = 'Terminal ID';
$_['seqr_terminal_password'] = 'Terminal Password';

$_['entry_test'] = 'Test Mode';
$_['seqr_status'] = 'Status';
$_['seqr_order_status_paid'] = 'Order Status Paid';
$_['seqr_order_status_canceled'] = 'Order Status Canceled';

// Help
$_['help_test'] = 'Use the live or testing (sandbox) gateway server to process transactions?';
$_['help_total'] = 'The checkout total the order must reach before this payment method becomes active';

// Error
$_['error_permission'] = 'Warning: You do not have permission to modify SEQR account details!';

$_['error_seqr_terminal_id'] = 'Terminal identifier required!';
$_['error_seqr_terminal_password'] = 'Terminal password required!';
$_['error_seqr_soap_wsdl_url'] = 'SOAP WSDL url required!';
$_['error_seqr_wsdl_unavailable'] = 'SOAP WSDL is unavailable!';

$_['error_seqr_refund_greater_then_total_cost'] = 'Amount refunded must be greater than 0 and smaller than or equal to order value!';
$_['error_seqr_refund_failed'] = 'Refund failed!';