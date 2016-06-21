<?php

namespace Madkom\PactBrokerClient;

/**
 * Class Contract
 * @package Madkom\PactBrokerClient
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class Contract
{
    /**
     * @var string
     */
    private $version;
    /**
     * @var mixed
     */
    private $data;

    /**
     * Contract constructor.
     *
     * @param string $version
     * @param string $data
     */
    public function __construct($version, $data)
    {
        $this->version = $version;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function version()
    {
        return $this->version;
    }

    /**
     * @return mixed
     */
    public function data()
    {
        return $this->data;
    }
}
