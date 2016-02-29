<?php

namespace Madkom\PactBrokerClient\Formatter;

use Madkom\PactBrokerClient\PactBrokerException;
use Madkom\PactBrokerClient\ResponseFormatter;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ToPactContractFormatter
 * @package Madkom\PactBrokerClient\Formatter
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class ToPactContractFormatter implements ResponseFormatter
{

    /**
     * @inheritDoc
     */
    public function format(ResponseInterface $response)
    {
        $responseArray = json_decode($response->getBody()->getContents(), true);
        $formattedResponse = [];

        if (
               !array_key_exists('consumer', $responseArray)
            || !array_key_exists('provider', $responseArray)
            || !array_key_exists('interactions', $responseArray)
            || !array_key_exists('metadata', $responseArray)
        ) {
            throw new PactBrokerException('Response data is malformed.');
        }

        $formattedResponse['consumer'] = $responseArray['consumer'];
        $formattedResponse['provider'] = $responseArray['provider'];
        $formattedResponse['interactions'] = $responseArray['interactions'];
        $formattedResponse['metadata'] = $responseArray['metadata'];

        return json_encode($formattedResponse);
    }

}
