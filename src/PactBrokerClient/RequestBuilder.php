<?php

namespace Madkom\PactBrokerClient;

use GuzzleHttp\Psr7\Request;

/**
 * Class RequestBuilder
 * @package Madkom\PactBrokerClient
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 */
class RequestBuilder
{

    /**
     * Creates request for publish new pact file
     *
     * @param string            $baseUrl
     * @param string            $consumerName
     * @param string            $providerName
     * @param string            $version
     * @param \JsonSerializable $jsonObject
     *
     * @return Request
     */
    public function createPublishPactRequestFromObject($baseUrl, $consumerName, $providerName, $version, \JsonSerializable $jsonObject)
    {
        $url = $baseUrl . '/pacts/provider/' . $providerName . '/consumer/' . $consumerName . '/version/' . $version;

        $request = new Request('PUT', $url, [
            "Content-Type" => "application/json"
        ], json_encode($jsonObject));

        return $request;
    }

    /**
     * Creates request for publish new pact file
     *
     * @param string $baseUrl
     * @param string $consumerName
     * @param string $providerName
     * @param string $version
     * @param string $filePath
     *
     * @return Request
     * @throws PactBrokerException
     */
    public function createPublishPactRequestFromFile($baseUrl, $consumerName, $providerName, $version, $filePath)
    {
        if (!file_exists($filePath)) {
            throw new PactBrokerException('File at ' . $filePath . ' does not exists');
        }

        $url = $baseUrl . '/pacts/provider/' . $providerName . '/consumer/' . $consumerName . '/version/' . $version;

        $request = new Request('PUT', $url, [
            "Content-Type" => "application/json"
        ], file_get_contents($filePath));

        return $request;
    }

    /**
     * Creates request for tagging version
     *
     * @param string $baseUrl
     * @param string $consumerName
     * @param string $providerName
     * @param string $version
     * @param string $tagName
     *
     * @return Request
     */
    public function createTagVersionRequest($baseUrl, $consumerName, $providerName, $version, $tagName)
    {
        $url = $baseUrl . '/pacts/provider/' . $providerName . '/consumer/' . $consumerName . '/versions/' . $version . '/tags/' . $tagName;

        $request = new Request('PUT', $url, [
            "Content-Type" => "application/json"
        ]);

        return $request;
    }

    /**
     * Creates request for retrieving pact file
     *
     * @param string $baseUrl
     * @param string $consumerName
     * @param string $providerName
     * @param string $version
     * @param string $tagName
     *
     * @return Request
     */
    public function createRetrievePactRequest($baseUrl, $consumerName, $providerName, $version, $tagName = null)
    {
        $url    = $baseUrl . '/pacts/provider/' . $providerName . '/consumer/' . $consumerName;
        $suffix = '';

        if ($version === 'latest') {
            $suffix .= '/latest';
        }else {
            $suffix .= '/versions/' . $version;
        }

        if ($tagName) {
            $suffix .= '/' . $tagName;
        }

        $url .= $suffix;

        $request = new Request('GET', $url, [
            "Content-Type" => "application/json"
        ]);

        return $request;
    }

    /**
     * Creates request for retrieving last added pact file
     *
     * @param $baseUrl
     *
     * @return Request
     */
    public function createRetrieveLastAddedPact($baseUrl)
    {
        $url = $baseUrl . '/pacts/latest';

        $request = new Request('GET', $url, [
            "Content-Type" => "application/json"
        ]);

        return $request;

    }

}
