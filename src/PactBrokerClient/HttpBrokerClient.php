<?php

namespace Madkom\PactBrokerClient;

use Http\Client\HttpClient;
use Psr\Http\Message\ResponseInterface;

/**
 * Class HttpClient
 * @package Madkom\PactBrokerClient
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class HttpBrokerClient
{
    /**
     * @var string
     */
    private $baseUrl;
    /**
     * @var HttpClient
     */
    private $client;
    /**
     * @var RequestBuilder
     */
    private $requestBuilder;

    /**
     * HttpBrokerClient constructor.
     *
     * @param string         $baseUrl
     * @param HttpClient     $client
     * @param RequestBuilder $requestBuilder
     */
    public function __construct($baseUrl, HttpClient $client, RequestBuilder $requestBuilder)
    {
        $this->baseUrl = $baseUrl;
        $this->client = $client;
        $this->requestBuilder = $requestBuilder;
    }

    /**
     * Publishes pact to pact broker
     *
     * @param string                   $providerName
     * @param string                   $consumerName
     * @param string                   $version
     * @param string|\JsonSerializable $contract Path to file, or object with return json contract
     *
     * @return \Psr\Http\Message\ResponseInterface
     * @throws PactBrokerException
     */
    public function publishPact($providerName, $consumerName, $version, $contract)
    {
        $request = null;
        if ($contract instanceof \JsonSerializable) {
            $request = $this->requestBuilder->createPublishPactRequestFromObject($this->baseUrl, $consumerName, $providerName, $version, $contract);
        }else if (is_string($contract)) {
            $request = $this->requestBuilder->createPublishPactRequestFromFile($this->baseUrl, $consumerName, $providerName, $version, $contract);
        }

        if (!$request) {
            throw new PactBrokerException("Can't publish contract file. Contract in wrong type.");
        }

        $response = $this->client->sendRequest($request);
        $this->checkIfResponseIsCorrect($response);

        return $response;
    }

    /**
     * Tags version with specific name
     *
     * @param string $consumerName
     * @param string $version
     * @param string $tagName
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function tagVersion($consumerName, $version, $tagName)
    {
        $request = $this->requestBuilder->createTagVersionRequest($this->baseUrl, $consumerName, $version, $tagName);

        $response = $this->client->sendRequest($request);
        $this->checkIfResponseIsCorrect($response);

        return $response;
    }

    /**
     * Retrieve contract by version or tag name
     *
     * @param string $providerName
     * @param string $consumerName
     * @param string $version Pass 'latest' for last added contract
     * @param string $tagName Optional. If not not passed retrieving by version
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function retrievePact($providerName, $consumerName, $version, $tagName = null)
    {
        $request = $this->requestBuilder->createRetrievePactRequest($this->baseUrl, $consumerName, $providerName, $version, $tagName);

        $response = $this->client->sendRequest($request);
        $this->checkIfResponseIsCorrect($response);

        return $response;
    }

    /**
     * Retrieves last added contract to the broker for all providers.
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function retrieveLastAddedPact()
    {
        $request = $this->requestBuilder->createRetrieveLastAddedPact($this->baseUrl);

        $response = $this->client->sendRequest($request);
        $this->checkIfResponseIsCorrect($response);

        return $response;
    }

    /**
     * Removes participant (provider, consumer) from pact-broker
     *
     * @param $participantName
     *
     * @return ResponseInterface
     * @throws PactBrokerException
     */
    public function removeParticipant($participantName)
    {
        $request = $this->requestBuilder->createRemoveParticipantRequest($this->baseUrl, $participantName);

        $response = $this->client->sendRequest($request);
        $this->checkIfResponseIsCorrect($response);

        return $response;
    }

    /**
     * Check if response is correct
     *
     * @param ResponseInterface $response
     *
     * @throws PactBrokerException
     */
    private function checkIfResponseIsCorrect(ResponseInterface $response)
    {
        if (!in_array($response->getStatusCode(), [200, 201, 202, 204])) {
            throw new PactBrokerException('Response_code: ' . $response->getStatusCode() . ' Response_data:' . $response->getBody()->getContents());
        }
    }

}
