<?php
  require_once('../loader.php');

  define('SHOPID', '10483');
  define('PAYMENTKEY', 'de74310368a4718a48e0e244fbf3e22e2ae117f2');
  define('NOTIFICATIONKEY', 'e5354004de1001f86004090d01982a6e05da1c12');

  function emptyLog() {

    fclose(fopen(__DIR__ . "/barzahlen.log", "w"));
  }
?>