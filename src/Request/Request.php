<?php

namespace Barzahlen\Request;

abstract class Request
{
    /**
     * @var boolean
     */
    protected $idempotence = false;

    /**
     * @var string
     */
    protected $path = '';

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * @var string
     */
    protected $method = '';


    /**
     * @return boolean
     */
    public function getIdempotence()
    {
        return $this->idempotence;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return vsprintf($this->path, $this->parameters);
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return null
     */
    public function getBody()
    {
        return null;
    }
}
