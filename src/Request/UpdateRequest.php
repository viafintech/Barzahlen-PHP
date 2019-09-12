<?php

namespace Barzahlen\Request;

use Barzahlen\Request\Validate;
use Barzahlen\Request\Autocorrect;
use Barzahlen\Request\Sanitize;

class UpdateRequest extends Request
{
    /**
     * @var string
     */
    protected $path = '/slips/%s';

    /**
     * @var string
     */
    protected $method = 'PATCH';

    /**
     * @var null|string
     */
    protected $body = null;

    /**
     * @var array
     */
    private $customer = array();

    /**
     * @var string
     */
    private $expiresAt;

    /**
     * @var string
     */
    private $referenceKey;

    /**
     * @var array
     */
    private $transactions = array();


    /**
     * @param string $slipId
     */
    public function __construct($slipId)
    {
        $this->parameters[] = $slipId;
    }

    /**
     * @param array|string $body
     * @return UpdateRequest
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
     * @param array $customer
     * @return UpdateRequest
     */
    public function setCustomer(array $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @param string $customerCellPhone
     * @return UpdateRequest
     */
    public function setCustomerCellPhone($customerCellPhone)
    {
        $this->customer['cell_phone'] = $customerCellPhone;

        return $this;
    }

    /**
     * @param string $customerEmail
     * @return UpdateRequest
     */
    public function setCustomerEmail($customerEmail)
    {
        $this->customer['email'] = $customerEmail;

        return $this;
    }

    /**
     * @param \DateTime|string $expiresAt
     * @return UpdateRequest
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
     * @param string $referenceKey
     * @return UpdateRequest
     */
    public function setReferenceKey($referenceKey)
    {
        $this->referenceKey = $referenceKey;

        return $this;
    }

    /**
     * @param string $id
     * @param string $amount
     * @return UpdateRequest
     */
    public function setTransaction($id, $amount)
    {
        $this->transactions[0] = array(
            'id' => $id,
            'amount' => $amount
        );

        return $this;
    }

    /**
     * @param string $transactionId
     * @return UpdateRequest
     */
    public function setTransactionId($transactionId)
    {
        $this->transactions[0]['id'] = $transactionId;

        return $this;
    }

    /**
     * @param string $amount
     * @return UpdateRequest
     */
    public function setAmount($amount)
    {
        $this->transactions[0]['amount'] = $amount;

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

        $body = array();

        if (count($this->customer)) {
            $body['customer'] = $this->customer;
        }

        if ($this->expiresAt) {
            $body['expires_at'] = $this->expiresAt;
        }

        if ($this->referenceKey) {
            $body['reference_key'] = $this->referenceKey;
        }

        if (count($this->transactions)) {
            $body['transactions'] = $this->transactions;
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
