<?php
require_once('loader.php');

$api = new Barzahlen_Api('10483', 'de74310368a4718a48e0e244fbf3e22e2ae117f2', true);
$payment = new Barzahlen_Request_Payment('foo@bar.com', '1', '24.95');
$refund = new Barzahlen_Request_Refund('7714272', '24.95');
$resend = new Barzahlen_Request_Resend('7766189');
$api->handleRequest($refund);

echo $payment->getTransactionId() . "\r";
echo $payment->getPaymentSlipLink() . "\r";
echo $payment->getExpirationNotice() . "\r";
echo $payment->getInfotext1() . "\r";
echo $payment->getInfotext2();

echo $refund->getOriginTransactionId() . "\r";
echo $refund->getRefundTransactionId();

echo $resend->getTransactionId();
?>