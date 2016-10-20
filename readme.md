# Barzahlen Payment Module PHP SDK (v2.0.2)

[![Build Status](https://travis-ci.org/Barzahlen/Barzahlen-PHP.svg?branch=master)](https://travis-ci.org/Barzahlen/Barzahlen-PHP)
[![Total Downloads](https://poser.pugx.org/barzahlen/barzahlen-php/downloads)](https://packagist.org/packages/barzahlen/barzahlen-php)
[![License](https://poser.pugx.org/barzahlen/barzahlen-php/license)](https://packagist.org/packages/barzahlen/barzahlen-php)

## Copyright
(c) 2016, Cash Payment Solutions GmbH  
https://www.barzahlen.de

## Preparation

### API Credentials
The API credentials, which are necessary to use the Barzahlen API, can be received at [Barzahlen Control Center](https://controlcenter.barzahlen.de). After a successful registration a division ID is assigned to you as well as a payment key.

### Installation
The Barzahlen PHP SDK can be installed using Composer.
```bash
composer require barzahlen/barzahlen-php
```

## Client
The client will connect your application to the Barzahlen API v2. Initiate it with the division ID and the payment key. Set the third, optional parameter to true if you want to send your requests to the sandbox for development purpose. Optional: Set a custom user agent.

```php
use \Barzahlen\Client;
use \Barzahlen\Exception\ApiException;

$client = new Client('12345', 'f2a173a210c7c8e7e439da7dc2b8330b6c06fc04', true);
$client->setUserAgent('Awesome Project v1.0.1');

try {
    $response = $client->handle($request);
    $stdClass = json_decode($response);
    $array = json_decode($response, true);
} catch (ApiException $e) {
    // @TODO: handle exception
}
```

It's recommended to surround API calls with try-catch-blocks since errors are thrown as exceptions. This way they can be logged as required by the system.

The API will send you JSON as response. Use json_decode() to transform it into an object or an associative array.

## Requests
There are five different requests which the client can handle for you. The required and optional parameters can be set using setters, an array or you can use a JSON string.

### CreateRequest
To request a new payment or refund slip simply initiate a new CreateRequest and add the parameters. Here are three examples for a minimal payment request using setters, an array and plain json.

```php
$request = new CreateRequest();
$request->setSlipType('payment');
$request->setCustomerKey('LDFKHSLFDHFL');
$request->setTransaction('14.95', 'EUR');
``` 

```php
$parameters = array(
    'slip_type' => 'payment',
    'customer' => array(
        'key' => 'LDFKHSLFDHFL'
    ),
    'transactions' => array(
        array(
            'amount' => '14.95',
            'currency' => 'EUR'
        )
    )
);

$request = new CreateRequest();
$request->setBody($parameters);
```

```php
$json = '{
  "slip_type": "payment",
  "customer": {
    "key": "LDFKHSLFDHFL"
  },
  "transactions": [
    { "currency": "EUR", "amount": "123.34" }
  ]
}';

$request = new CreateRequest();
$request->setBody($json);
```

This is an example for a minimal refund request. Please note that the amount is negative and must not exceed the initial payment amount. Multiple refunds for one payment up to the initial amount are possible.

```php
$request = new CreateRequest();
$request->setSlipType('refund');
$request->setForSlipId('slp-1b41145c-2dd3-4e3f-bbe1-72c09fbf3f94');
$request->setTransaction('-14.95', 'EUR');
```

You may set more parameters according to the [Barzahlen API v2 Documentation](https://docs.barzahlen.de/api/v2/).

```php
$request->setReferenceKey('REFKEY123');
$request->setHookUrl('https://www.example.tld/barzahlen/callback');
$request->setExpiresAt('2016-04-01T12:34:56Z');
$request->setCustomerKey('customer@provider.tld');
$request->setCustomerCellPhone('01234567910');
$request->setCustomerEmail('customer@provider.tld');
$request->setCustomerLanguage('de_DE');
$request->setAddress(array(
            'street_and_no' => 'Wallstr. 14a',
            'zipcode' => '10179',
            'city' => 'Berlin',
            'country' => 'DE'
        ));
$request->addMetadata('promo', 'summer2016');
```

The customer data can be set as array and the expiresAt value can be a DateTime object. Also, you can use chaining with the setters.

```php
$expire = new \DateTime();
$expire->modify('+1 week');

$request->setExpiresAt($expire)
        ->setCustomer(array(
            'cell_phone' => '01234567910'
            'key' => 'customer@provider.tld'
            'email' => 'customer@provider.tld'
            'language' => 'de_DE'
        ));
```

#### Example Response
Representation of current slip status. (Content depends on sent parameters.)

```json
{
  "id": "slp-d90ab05c-69f2-4e87-9972-97b3275a0ccd",
  "slip_type": "payment",
  "division_id": "1234",
  "reference_key": "O64737X",
  "hook_url": "https://psp.example.com/hook",
  "expires_at": "2016-01-10T12:34:56Z",
  "customer": {
    "key": "LDFKHSLFDHFL",
    "cell_phone_last_4_digits": "6789",
    "email": "john@example.com",
    "language": "de-DE"
  },
  "checkout_token": "djF8Y2hrdHxzbHAtMTM4ZWI3NzUtOWY5Yy00NzYwLWI4ZTAtYTNlZWNmYjQ5M2IxfElBSThZMnd6SFYwbjJpMm9aSUpvREpnYnhNS3c5Z2x3elJOanlLblZJeFk9",
  "metadata": {
    "order_id": "1234",
    "invoice_no": "A123"
  },
  "transactions": [
    {
      "id": "4729294329",
      "currency": "EUR",
      "amount": "123.34",
      "state": "pending"
    }
  ],
  "nearest_stores": [
    {
      "title": "mobilcom-debitel",
      "logo": {
        "id": "17077"
      },
      "distance_m": 1160,
      "address": {
        "city": "Berlin",
        "country": "DE",
        "street_and_no": "Grunerstraße 20",
        "zipcode": "10179"
      },
      "opening_hours": {
        "days": [
          { "day": "sun", "open": [] },
          { "day": "mon", "open": [{"begin": "10:00", "end": "21:00"}] },
          { "day": "tue", "open": [{"begin": "10:00", "end": "21:00"}] },
          { "day": "wed", "open": [{"begin": "10:00", "end": "21:00"}] },
          { "day": "thu", "open": [{"begin": "10:00", "end": "21:00"}] },
          { "day": "fri", "open": [{"begin": "10:00", "end": "21:00"}] },
          { "day": "sat", "open": [{"begin": "10:00", "end": "21:00"}] }
        ]
      }
    },
    {
      "title": "dm-drogerie markt",
      "logo": {
        "id": "13045"
      },
      "distance_m": 1220,
      "address": {
        "city": "Berlin",
        "country": "DE",
        "street_and_no": "Alexanderplatz 1",
        "zipcode": "10178"
      },
      "opening_hours": {
        "days": [
          { "day": "sun", "open": [] },
          { "day": "mon", "open": [{"begin": "09:00", "end": "22:00"}] },
          { "day": "tue", "open": [{"begin": "09:00", "end": "22:00"}] },
          { "day": "wed", "open": [{"begin": "09:00", "end": "22:00"}] },
          { "day": "thu", "open": [{"begin": "09:00", "end": "22:00"}] },
          { "day": "fri", "open": [{"begin": "09:00", "end": "22:00"}] },
          { "day": "sat", "open": [{"begin": "09:00", "end": "22:00"}] }
        ]
      }
    },
    {
      "title": "dm-drogerie markt",
      "logo": {
        "id": "13045"
      },
      "distance_m": 1280,
      "address": {
        "city": "Berlin",
        "country": "DE",
        "street_and_no": "Henriette-Herz-Platz 4",
        "zipcode": "10178"
      },
      "opening_hours": {
        "days": [
          { "day": "sun", "open": [] },
          { "day": "mon", "open": [{"begin": "08:30", "end": "21:00"}] },
          { "day": "tue", "open": [{"begin": "08:30", "end": "21:00"}] },
          { "day": "wed", "open": [{"begin": "08:30", "end": "21:00"}] },
          { "day": "thu", "open": [{"begin": "08:30", "end": "21:00"}] },
          { "day": "fri", "open": [{"begin": "08:30", "end": "21:00"}] },
          { "day": "sat", "open": [{"begin": "09:00", "end": "21:00"}] }
        ]
      }
    }
  ]
}
```

### UpdateRequest
To change slip parameters afterwards initiate a new UpdateRequest using the slip id. Use setters, an array or a json string to set your new or updated parameter(s). Only pending slips can be updated. For more information please read the [Barzahlen API v2 Documentation](https://docs.barzahlen.de/api/v2/).

```php
$request = new UpdateRequest('slp-f26bcd0b-556b-4285-b0b3-ba54052df97f');
$request->setCustomer(array(
    'email' => 'customer@provider.tld',
    'cell_phone' => '012345678910'
));
$request->setExpiresAt('2016-01-10T12:34:56Z');
$request->setTransaction('4729294329', '150.00');
$request->setReferenceKey('NEWKEY');
```

The expiresAt() method can be used with a DateTime object and chaining the setters is also possible. The response will contain a json with updated information.

### RetrieveRequest, ResendRequest, InvalidateRequest
The last three requests don't require any additional parameters via setters, array or json. They can be initiate with the slip id (and message type) before they're sent with the client.

```php
// get current information on the slip
$request = new RetrieveRequest('slp-f26bcd0b-556b-4285-b0b3-ba54052df97f');

// resend email / text message to customer
$request = new ResendRequest('slp-f26bcd0b-556b-4285-b0b3-ba54052df97f', 'email');
$request = new ResendRequest('slp-f26bcd0b-556b-4285-b0b3-ba54052df97f', 'text_message');

// invalidate slip immediately
$request = new InvalidateRequest('slp-f26bcd0b-556b-4285-b0b3-ba54052df97f');
```

## Webhook
When the state of a slip changes (e.g. the customer payed at a retail partner) and a hook url is set, Barzahlen will send a POST request to this hook url to let you know about the change. Initiate the Webhook class with the payment key and use it to verify the incoming request's header and body.

```php
use \Barzahlen\Webhook;

$header = $_SERVER;
$body = file_get_contents('php://input');
$webhook = new Webhook('f2a173a210c7c8e7e439da7dc2b8330b6c06fc04');

if ($webhook->verify($header, $body)) {
    $stdClass = json_decode($body);
    $array = json_decode($body, true);
    // @TODO: send 200 status code, update order
} else {
    // @TODO: send 400 status code, log error
}
```

## Support
The Barzahlen Team will happily assist you with any problems or questions.

Send us an email to support@barzahlen.de or use the contact form at https://integration.barzahlen.de/en/support.