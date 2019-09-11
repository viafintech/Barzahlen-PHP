<?php

namespace Barzahlen\Request;

class CreateRequest extends Request
{
    /**
     * @var boolean
     */
    protected $idempotence = true;

    /**
     * @var string
     */
    protected $path = '/slips';

    /**
     * @var string
     */
    protected $method = 'POST';

    /**
     * @var null|string
     */
    protected $body = null;

    /**
     * @var string
     */
    private $slipType;

    /**
     * @var string
     */
    private $forSlipId;

    /**
     * @var string
     */
    private $referenceKey;

    /**
     * @var string
     */
    private $hookUrl;

    /**
     * @var string
     */
    private $expiresAt;

    /**
     * @var array
     */
    private $customer = array();

    /**
     * @var string
     */
    private $address;

    /**
     * @var array
     */
    private $transactions = array();

    /**
     * @var array
     */
    private $metadata = array();


    /**
     * @param array|string $body
     * @return CreateRequest
     */
    public function setBody($body)
    {
        if (is_array($body)) {
            $this->body = json_encode($body);
        } else {
            $this->body = $body;
        }

        return $this;
    }

    /**
     * @param mixed $slipType
     * @return CreateRequest
     */
    public function setSlipType($slipType)
    {
        $this->slipType = $slipType;

        return $this;
    }

    /**
     * @param mixed $forSlipId
     * @return CreateRequest
     */
    public function setForSlipId($forSlipId)
    {
        $this->forSlipId = $forSlipId;

        return $this;
    }

    /**
     * @param mixed $referenceKey
     * @return CreateRequest
     */
    public function setReferenceKey($referenceKey)
    {
        $this->referenceKey = $referenceKey;

        return $this;
    }

    /**
     * @param mixed $hookUrl
     * @return CreateRequest
     */
    public function setHookUrl($hookUrl)
    {
        $this->hookUrl = $hookUrl;

        return $this;
    }

    /**
     * @param \DateTime|string $expiresAt
     * @return CreateRequest
     */
    public function setExpiresAt($expiresAt)
    {
        if ($expiresAt instanceof \DateTime) {
            $this->expiresAt = $expiresAt->format('c');
        } else {
            $this->expiresAt = $expiresAt;
        }

        return $this;
    }

    /**
     * @param array $customer
     * @return CreateRequest
     */
    public function setCustomer(array $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @param mixed $customerKey
     * @return CreateRequest
     */
    public function setCustomerKey($customerKey)
    {
        $this->customer['key'] = $customerKey;

        return $this;
    }

    /**
     * @param mixed $customerCellPhone
     * @return CreateRequest
     */
    public function setCustomerCellPhone($customerCellPhone)
    {
        $this->customer['cell_phone'] = $customerCellPhone;

        return $this;
    }

    /**
     * @param mixed $customerEmail
     * @return CreateRequest
     */
    public function setCustomerEmail($customerEmail)
    {
        $this->customer['email'] = $customerEmail;

        return $this;
    }

    /**
     * @param mixed $customerLanguage
     * @return CreateRequest
     */
    public function setCustomerLanguage($customerLanguage)
    {
        $this->customer['language'] = $customerLanguage;

        return $this;
    }

    /**
     * @param mixed $address
     * @return CreateRequest
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @param string $amount
     * @param string $currency
     * @return CreateRequest
     */
    public function setTransaction($amount, $currency = 'EUR')
    {
        $this->transactions[0] = array(
            'amount' => $amount,
            'currency' => $currency
        );

        return $this;
    }

    /**
     * @param mixed $amount
     * @return CreateRequest
     */
    public function setAmount($amount)
    {
        $this->transactions[0]['amount'] = $amount;

        return $this;
    }

    /**
     * @param string $currency
     * @return CreateRequest
     */
    public function setCurrency($currency)
    {
        $this->transactions[0]['currency'] = $currency;

        return $this;
    }

    /**
     * @param string $key
     * @param string $value
     * @return CreateRequest
     */
    public function addMetadata($key, $value)
    {
        $this->metadata[$key] = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        if ($this->body !== null) {
            return $this->body;
        }

        $body = array(
            'slip_type' => $this->slipType,
            'transactions' => $this->transactions,
        );

        if (count($this->customer)) {
            $body['customer'] = $this->customer;
        }

        if ($this->forSlipId && $this->slipType == 'refund') {
            $body['refund'] = array('for_slip_id' => $this->forSlipId);
        }

        if ($this->referenceKey) {
            $body['reference_key'] = $this->referenceKey;
        }

        if ($this->hookUrl) {
            $body['hook_url'] = $this->hookUrl;
        }

        if ($this->expiresAt) {
            $body['expires_at'] = $this->expiresAt;
        }

        if ($this->address) {
            $body['show_stores_near'] = array('address' => $this->address);
        }

        if (count($this->metadata)) {
            $body['metadata'] = $this->metadata;
        }

        return json_encode($body);
    }

    public function validate() {

	}

	public function autocorrect() {

	}

	public function sanitize() {

	}

}
