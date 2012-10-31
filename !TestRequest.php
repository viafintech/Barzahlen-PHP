<?php
require_once('loader.php');

$api = new Barzahlen_Api('10483', '22b8231bcf3a47e81fc1c7ec19f07fb8c10e94e8');
$payment = new Barzahlen_Request_Payment('foo@bar.com', 'Fabeckstr. 15', '14195', 'Berlin', 'DE', '45.99');
$update = new Barzahlen_Request_Update('21162918', '66');
$refund = new Barzahlen_Request_Refund('21070102', '24.95');
$resend = new Barzahlen_Request_Resend('7766189');

try {
  $api->handleRequest($update);
}
catch (Barzahlen_Exception $e) {
  echo $e;
}

echo "Payment:\n";
var_dump($update);
?>