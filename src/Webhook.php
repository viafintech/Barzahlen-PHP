<?php

namespace Barzahlen;

class Webhook
{
    /**
     * @var string
     */
    private $paymentKey;


    /**
     * @param string $paymentKey
     */
    public function __construct($paymentKey)
    {
        $this->paymentKey = $paymentKey;
    }

    /**
     * @param array $header
     * @param string $body
     * @return boolean
     */
    public function verify($header, $body)
    {
        if (isset($header['HTTP_X_FORWARDED_HOST'])) {
            $host = $header['HTTP_X_FORWARDED_HOST'] . ':' . $header['HTTP_X_FORWARDED_PORT'];
        } else {
            $host = $header['HTTP_HOST'] . ':' . $header['SERVER_PORT'];
        }

        $signature = Middleware::generateSignature(
            $host,
            $header['REQUEST_METHOD'],
            strtok($header['REQUEST_URI'], '?'),
            $header['QUERY_STRING'],
            $header['HTTP_DATE'],
            '',
            $body,
            $this->paymentKey
        );

        return Middleware::stringsEqual($header['HTTP_BZ_SIGNATURE'], 'BZ1-HMAC-SHA256 ' . $signature);
    }
}