<?php
require_once('loader.php');

$api = new Barzahlen_Api('10483', '22b8231bcf3a47e81fc1c7ec19f07fb8c10e94e8', true);
$payment = new Barzahlen_Request_Payment('mustermann@barzahlen.de', 'Fabeckstr. 15', '14195', 'Berlin', 'DE', '45.99');
$update = new Barzahlen_Request_Update('21846231', '66');
$refund = new Barzahlen_Request_Refund('21070102', '24.95');
$resend = new Barzahlen_Request_Resend('21846833');

try {
  $api->handleRequest($resend);
}
catch (Barzahlen_Exception $e) {
  echo $e;
}

echo "Request:\n";
var_dump($resend);
?>