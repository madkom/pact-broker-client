<?php

namespace Madkom\PactBrokerClient;

use Psr\Http\Message\ResponseInterface;

/**
 * Interface ResponseFormatter
 * @package Madkom\PactBrokerClient
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
interface ResponseFormatter
{

    /**
     * Format response to specified type
     *
     * @param ResponseInterface $response
     *
     * @return FormattedResponse
     * @throws PactBrokerException
     */
    public function format(ResponseInterface $response);

}