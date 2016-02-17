<?php

namespace Madkom\PactBrokerClient;

/**
 * Class FormattedResponse
 * @package Madkom\PactBrokerClient
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class FormattedResponse
{
    /**
     * @var mixed
     */
    private $data;

    /**
     * FormattedResponse constructor.
     *
     * @param mixed $data formatted data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function data()
    {
        return $this->data;
    }

}
