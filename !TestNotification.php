<?php
require_once('loader.php');

$_GET = array('state' => 'paid',
                 'transaction_id' => '1',
                 'shop_id' => '10483',
                 'customer_email' => 'foo@bar.com',
                 'amount' => '24.95',
                 'currency' => 'EUR',
                 'order_id' => '1',
                 'custom_var_0' => 'PHP SDK',
                 'custom_var_1' => 'Euro 2012',
                 'custom_var_2' => 'Barzahlen v.1.3.3.7',
                 'hash' => '85d13e7eda95276a655ef86947409f095be8ccd1736579d54a88fc9ce2ac5353964b33d8143439354ee46fa3ce0a7ea07c49429ae3bdbfeca4f2ab1990c15366'
                   );
try {
  $notification = new Barzahlen_Notification('10483', 'e5354004de1001f86004090d01982a6e05da1c12', $_GET);
  $notification->validate();
}
catch (Exception $e) {
  echo $e;
}

var_dump($notification->isValid());
?>