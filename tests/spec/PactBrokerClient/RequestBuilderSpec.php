<?php

namespace spec\Madkom\PactBrokerClient;

use Madkom\PactBrokerClient\PactBrokerException;
use Madkom\PactBrokerClient\RequestBuilder;
use Madkom\Stub\JsonSimpleObject;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;

/**
 * Class RequestBuilderSpec
 * @package spec\Madkom\PactBrokerClient
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin RequestBuilder
 */
class RequestBuilderSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType('Madkom\PactBrokerClient\RequestBuilder');
    }

    function it_should_build_publish_pact_request_via_object()
    {
        $url                = 'localhost:80';
        $consumerName       = 'TestConsumer1';
        $providerName       = 'TestProvider1';
        $version            = '1.0.0';
        $jsonObject         = new JsonSimpleObject();

        $request = $this->createPublishPactRequestFromObject($url, $consumerName, $providerName, $version, $jsonObject);

        $request->shouldHaveType(RequestInterface::class);

        $request->getBody()->getContents()->shouldReturn(json_encode(new JsonSimpleObject()));
        $request->getHeaders()->shouldReturn([
            'Host' => ['localhost:80'],
            'Content-Type' => ['application/json']
        ]);
        $request->getRequestTarget()->shouldReturn('/pacts/provider/' . $providerName . '/consumer/' . $consumerName . '/version/' . $version);
        $request->getMethod()->shouldReturn('PUT');
    }

    function it_should_build_publish_pact_request_via_file()
    {
        $url                = 'localhost:80';
        $consumerName       = 'TestConsumer1';
        $providerName       = 'TestProvider1';
        $version            = '1.0.0';
        $filePath           = (new JsonSimpleObject())->getPath();

        $request = $this->createPublishPactRequestFromFile($url, $consumerName, $providerName, $version, $filePath);

        $request->shouldHaveType(RequestInterface::class);
        $json = json_encode(json_decode($request->getBody()->getContents()->getWrappedObject()));
        \PHPUnit_Framework_Assert::assertEquals(json_encode(new JsonSimpleObject()), $json);

        $request->getHeaders()->shouldReturn([
            'Host' => ['localhost:80'],
            'Content-Type' => ['application/json']
        ]);
        $request->getRequestTarget()->shouldReturn('/pacts/provider/' . $providerName . '/consumer/' . $consumerName . '/version/' . $version);
        $request->getMethod()->shouldReturn('PUT');
    }

    function it_should_throw_exception_if_file_found()
    {
        $url                = 'localhost:80';
        $consumerName       = 'TestConsumer1';
        $providerName       = 'TestProvider1';
        $version            = '1.0.0';
        $filePath           = (new JsonSimpleObject())->getPath() . 'x';

        $this->shouldThrow(PactBrokerException::class)->during('createPublishPactRequestFromFile', [$url, $consumerName, $providerName, $version, $filePath]);
    }

    function it_should_create_tag_version_request()
    {
        $url                = 'localhost:80';
        $consumerName       = 'TestConsumer1';
        $version            = '1.0.0';
        $tagName            = 'prod';

        $request = $this->createTagVersionRequest($url, $consumerName, $version, $tagName);

        $request->shouldHaveType(RequestInterface::class);
        $request->getHeaders()->shouldReturn([
            'Host' => ['localhost:80'],
            'Content-Type' => ['application/json']
        ]);
        $request->getRequestTarget()->shouldReturn('/pacticipants/' . $consumerName . '/versions/' . $version . '/tags/' . $tagName);
        $request->getMethod()->shouldReturn('PUT');
    }

    function it_should_create_retrieve_pact_request_for_version()
    {
        $url                = 'localhost:80';
        $consumerName       = 'TestConsumer1';
        $providerName       = 'TestProvider1';
        $version            = '1.0.0';

        $request = $this->createRetrievePactRequest($url, $consumerName, $providerName, $version);

        $request->shouldHaveType(RequestInterface::class);
        $request->getHeaders()->shouldReturn([
            'Host' => ['localhost:80'],
            'Content-Type' => ['application/json']
        ]);
        $request->getRequestTarget()->shouldReturn('/pacts/provider/' . $providerName . '/consumer/' . $consumerName . '/versions/' . $version);
        $request->getMethod()->shouldReturn('GET');
    }

    function it_should_create_retrieve_latest_pact_request()
    {
        $url                = 'localhost:80';
        $consumerName       = 'TestConsumer1';
        $providerName       = 'TestProvider1';
        $version            = 'latest';

        $request = $this->createRetrievePactRequest($url, $consumerName, $providerName, $version);

        $request->shouldHaveType(RequestInterface::class);
        $request->getHeaders()->shouldReturn([
            'Host' => ['localhost:80'],
            'Content-Type' => ['application/json']
        ]);
        $request->getRequestTarget()->shouldReturn('/pacts/provider/' . $providerName . '/consumer/' . $consumerName . '/latest');
        $request->getMethod()->shouldReturn('GET');
    }

    function it_should_create_retrieve_latest_pact_with_tag()
    {
        $url                = 'localhost:80';
        $consumerName       = 'TestConsumer1';
        $providerName       = 'TestProvider1';
        $version            = 'latest';
        $tag                = 'prod';

        $request = $this->createRetrievePactRequest($url, $consumerName, $providerName, $version, $tag);

        $request->shouldHaveType(RequestInterface::class);
        $request->getHeaders()->shouldReturn([
            'Host' => ['localhost:80'],
            'Content-Type' => ['application/json']
        ]);
        $request->getRequestTarget()->shouldReturn('/pacts/provider/' . $providerName . '/consumer/' . $consumerName . '/latest/prod');
        $request->getMethod()->shouldReturn('GET');
    }

    function it_should_request_for_retrieve_last_added_pact_file()
    {
        $url                = 'localhost:80';
        $request = $this->createRetrieveLastAddedPact($url);

        $request->shouldHaveType(RequestInterface::class);
        $request->getHeaders()->shouldReturn([
            'Host' => ['localhost:80'],
            'Content-Type' => ['application/json']
        ]);
        $request->getRequestTarget()->shouldReturn('/pacts/latest');
        $request->getMethod()->shouldReturn('GET');
    }

}
