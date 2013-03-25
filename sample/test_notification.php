<?php
require_once('../src/loader.php');

$_GET = array('state' => 'paid',
               'transaction_id' => '123',
               'shop_id' => '10483',
               'customer_email' => 'email@example.com',
               'amount' => '44.95',
               'currency' => 'EUR',
               'order_id' => '13',
               'custom_var_0' => '',
               'custom_var_1' => '',
               'custom_var_2' => '',
               'hash' => '5c976e71a3fe53adf8d6be16067e1119d38bf40f98a6b433c6d78e0282d86bdbc19d4f56af064d4f7668b89c99ea75d5381f590b3e440111ee540a726d5e1b54'
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