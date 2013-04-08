<?php
require_once('../src/loader.php');

$api = new Barzahlen_Api('10483', '22b8231bcf3a47e81fc1c7ec19f07fb8c10e94e8', true);
$payment = new Barzahlen_Request_Payment('email@example.com', 'Musterstr. 1', '12345', 'Musterstadt', 'DE', '45.99');
//$update = new Barzahlen_Request_Update('21846231', '66');
//$refund = new Barzahlen_Request_Refund('21070102', '24.95');
//$resend = new Barzahlen_Request_Resend('21846833');
//$cancel = new Barzahlen_Request_Cancel('35487447');

try {
  $api->handleRequest($payment);
}
catch (Barzahlen_Exception $e) {
  echo $e;
}

echo "Request:\n";
var_dump($payment);
?>