<?php

namespace Barzahlen;

class Client
{
    const API_URL = 'https://api.barzahlen.de:443/v2';

    const API_SANDBOX_URL = 'https://api-sandbox.barzahlen.de:443/v2';

    /**
     * @var string
     */
    private $divisionId;

    /**
     * @var string
     */
    private $paymentKey;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var string
     */
    private $userAgent = 'PHP SDK v2.0.3';


    /**
     * @param string $divisionId
     * @param string $paymentKey
     * @param boolean $sandbox
     */
    public function __construct($divisionId, $paymentKey, $sandbox = false)
    {
        $this->divisionId = $divisionId;
        $this->paymentKey = $paymentKey;
        $this->apiUrl = $sandbox ? self::API_SANDBOX_URL : self::API_URL;
    }

    /**
     * @param string $userAgent
     * @return Client
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * @param Request\Request $request
     * @return string
     * @throws Exception\ApiException
     * @throws Exception\AuthException
     * @throws Exception\CurlException
     * @throws Exception\IdempotencyException
     * @throws Exception\InvalidFormatException
     * @throws Exception\InvalidParameterException
     * @throws Exception\InvalidStateException
     * @throws Exception\NotAllowedException
     * @throws Exception\RateLimitException
     * @throws Exception\ServerException
     * @throws Exception\TransportException
     */
    public function handle($request)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->apiUrl . $request->getPath());
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->buildHeader($request));
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->getMethod());
        curl_setopt($curl, CURLOPT_POSTFIELDS, $request->getBody());
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);

        $response = curl_exec($curl);

        $error = curl_error($curl);
        if ($error || false === $response) {
            throw new Exception\CurlException('Error during cURL: ' . $error . ' [' . curl_errno($curl) . ']');
        }

        $this->checkResponse($response);
        curl_close($curl);

        return $response;
    }

    /**
     * @param Request\Request $request
     * @return array
     */
    public function buildHeader($request)
    {
        $date = gmdate('D, d M Y H:i:s T');
        $idempotencyKey = $request->getIdempotence() ? md5(uniqid(rand(), true)) : '';

        $signature = Middleware::generateSignature(
            parse_url($this->apiUrl, PHP_URL_HOST) . ':' . parse_url($this->apiUrl, PHP_URL_PORT),
            $request->getMethod(),
            parse_url($this->apiUrl . $request->getPath(), PHP_URL_PATH),
            parse_url($this->apiUrl, PHP_URL_QUERY),
            $date,
            $idempotencyKey,
            $request->getBody(),
            $this->paymentKey
        );

        $header = array(
            "Host: " . parse_url($this->apiUrl, PHP_URL_HOST),
            "Date: " . $date,
            "User-Agent: " . $this->userAgent,
            "Authorization: BZ1-HMAC-SHA256 DivisionId=" . $this->divisionId . ", Signature=" . $signature
        );

        if ($idempotencyKey !== '') {
            $header[] = "Idempotency-Key: " . $idempotencyKey;
        }

        return $header;
    }

    /**
     * @param string $response
     * @throws Exception\ApiException
     * @throws Exception\AuthException
     * @throws Exception\IdempotencyException
     * @throws Exception\InvalidFormatException
     * @throws Exception\InvalidParameterException
     * @throws Exception\InvalidStateException
     * @throws Exception\NotAllowedException
     * @throws Exception\RateLimitException
     * @throws Exception\ServerException
     * @throws Exception\TransportException
     */
    public function checkResponse($response)
    {
        if (strpos($response, 'error_class') === false) {
            return;
        }

        $response = json_decode($response);
        switch ($response->error_class) {
            case 'auth':
                throw new Exception\AuthException($response->message, $response->request_id);
                break;
            case 'transport':
                throw new Exception\TransportException($response->message, $response->request_id);
                break;
            case 'idempotency':
                throw new Exception\IdempotencyException($response->message, $response->request_id);
                break;
            case 'rate_limit':
                throw new Exception\RateLimitException($response->message, $response->request_id);
                break;
            case 'invalid_format':
                throw new Exception\InvalidFormatException($response->message, $response->request_id);
                break;
            case 'invalid_state':
                throw new Exception\InvalidStateException($response->message, $response->request_id);
                break;
            case 'invalid_parameter':
                throw new Exception\InvalidParameterException($response->message, $response->request_id);
                break;
            case 'not_allowed':
                throw new Exception\NotAllowedException($response->message, $response->request_id);
                break;
            case 'server_error':
                throw new Exception\ServerException($response->message, $response->request_id);
                break;
            default:
                throw new Exception\ApiException($response->message, $response->request_id);
                break;
        }
    }
}
