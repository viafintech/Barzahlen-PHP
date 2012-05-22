<?php
require_once('loader.php');

$api = new Barzahlen_Api('10483', 'de74310368a4718a48e0e244fbf3e22e2ae117f2', true);
$payment = new Barzahlen_Request_Payment('foo@bar.com', '1', '24.95');
$api->handleRequest($payment);

$payment->getXmlData();
?>