# Barzahlen Payment Module PHP SDK (v1.1.4)

## Copyright
(c) 2013, Zerebro Internet GmbH  
http://www.barzahlen.de

## Preparation

### Merchant Data
The merchant credentials, which are necessary to handle payments with Barzahlen, can be received at https://partner.barzahlen.de. After a successful registration a shop ID is assigned to you as well as a payment and a notification key. Furthermore you can set you callback URL, which is used to send updates on payment and refund transactions.

### Installation
Download the Barzahlen PHP SDK and unzip it to a directory of your choice in your online shop system. Include the loader so you have access to all Barzahlen SDK classes.

> require_once('loader.php');

## Usage
It's recommended to surround api requests and notifications with try-catch-blocks since errors are thrown as exceptions so they can be logged as required by the system.

### Sending Requests to Barzahlen
Create a new api object, which you initiate with your unique shop ID and payment key. For testing purpose you can set the third, optional parameter, which handles the sandbox setting, to *true*. By default this parameter is set to *false*. Create a request object of your choice, e.g. a payment request. Every request object needs additional information. Following this, pass the request object to the api object with the order to handle the request.

**Important:** Make sure that all string variables are encoded UTF-8. This is necessary for the hash calculation.

> $api = new Barzahlen_Api('10000', 'e5354004de1001f86004090d01982a6e05da1c12', true);  
> $payment = new Barzahlen_Request_Payment($customerEmail, $customerStreetNr, $customerZipcode, $customerCity, $customerCountryId, $orderAmount[, $currency[, $orderId]]);  
>
> try {  
>   $api->handleRequest($payment);  
> }  
> catch (Exception $e) {  
>   // possibility for error logging  
> }

After a successful request (can be checked by $payment->isValid()) the received information can be taken from the request object. Either one by one or as a complete array.

> $payment->getTransactionId();  
> $payment->getPaymentSlipLink();  
> $payment->getExpirationNotice();  
> $payment->getInfotext1();  
> $payment->getInfotext2();  
>
> $payment->getXmlArray();

If there was no order ID available before the payment request, it can be updated later on. Therefore create an update object which you pass to the api object. Requests for refunds and e-mail resending are done in the same way.

> $update = new Barzahlen_Request_Update($transactionId, $orderId);  
> $refund = new Barzahlen_Request_Refund($transactionId, $refundAmount);  
> $resend = new Barzahlen_Request_Resend($transactionId);  
> $cancel = new Barzahlen_Request_Cancel($transactionId);

### Receive Notifications from Barzahlen
Create a notification object using your private shop ID and notification key as well as the received GET array.

> $notification = new Barzahlen_Notification('10483', 'e5354004de1001f86004090d01982a6e05da1c12', $_GET);
>
> try {  
>   $notification->validate();  
> }  
> catch (Exception $e) {  
>   // possibility for error logging  
> }

If a notification is valid, can be checked with $notification->isValid(). After that, you can request and process the parameters.

> $notification->getNotificationType();  
> $notification->getTransactionId();  
> $notification->getCustomVar1();  
> ...

## Support
The Barzahlen Team will happily assist you with any problems or questions.

Send us an email to support@barzahlen.de or use the contact form at http://www.barzahlen.de/partner/integration.