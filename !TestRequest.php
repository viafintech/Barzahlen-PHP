<?php
require_once('loader.php');

$api = new Barzahlen_Api('10483', 'de74310368a4718a48e0e244fbf3e22e2ae117f2', true);
$payment = new Barzahlen_Request_Payment('foo@bar.com', '1', '24.95');
$refund = new Barzahlen_Request_Refund('7714272', '24.95');
$resend = new Barzahlen_Request_Resend('7766189');

try {
  $api->handleRequest($payment);
}
catch (Barzahlen_Exception $e) {
  echo $e;
}

echo "Payment:\n";
var_dump($payment->isValid());
echo $payment->getTransactionId() . "\r";
echo $payment->getPaymentSlipLink() . "\r";
echo $payment->getExpirationNotice() . "\r";
echo $payment->getInfotext1() . "\r";
echo $payment->getInfotext2() . "\r";

echo "Refund:\n";
var_dump($refund->isValid());
echo $refund->getOriginTransactionId() . "\r";
echo $refund->getRefundTransactionId() . "\r";

echo "Resend:\n";
var_dump($resend->isValid());
echo $resend->getTransactionId();
?>