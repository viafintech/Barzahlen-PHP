<?php
require_once('zi_barzahlen.php');

$barzahlen = new BZ_SDK_Barzahlen('10483', 'de74310368a4718a48e0e244fbf3e22e2ae117f2', 'e5354004de1001f86004090d01982a6e05da1c12', 'true'); 
//$barzahlen->setCustomVar('MeinShop', 'PHP 5.1.2+', '{{}}');
//$barzahlen->setLanguage('en');

//$array = $barzahlen->create('foo@bar.com', '1', '24.95');
//$array = $barzahlen->refund('7690927', '24.95');
$array = $barzahlen->resend('7691945');

var_dump($array);
?>