<?php

namespace spec\Madkom\PactBrokerClient;

use Http\Client\HttpClient;
use Madkom\PactBrokerClient\Contract;
use Madkom\PactBrokerClient\HttpBrokerClient;
use Madkom\PactBrokerClient\PactBrokerException;
use Madkom\PactBrokerClient\RequestBuilder;
use Madkom\PactBrokerClient\ResponseFormatter;
use Madkom\Stub\JsonSimpleObject;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class HttpClientSpec
 * @package spec\Madkom\PactBrokerClient
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin HttpBrokerClient
 */
class HttpBrokerClientSpec extends ObjectBehavior
{

    /** @var  string */
    private $baseUrl;

    /** @var  HttpClient */
    private $client;

    /** @var  RequestBuilder */
    private $requestBuilder;

    function let(HttpClient $client, RequestBuilder $requestBuilder)
    {
        $this->baseUrl        = 'http://localhost:3000';
        $this->client         = $client;
        $this->requestBuilder = $requestBuilder;
        $this->beConstructedWith($this->baseUrl, $client, $requestBuilder);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Madkom\PactBrokerClient\HttpBrokerClient');
    }

    function it_should_publish_pact_by_file(RequestInterface $request, ResponseInterface $response)
    {
        $consumerName = 'consumerA';
        $providerName = 'providerB';
        $version      = '1.0.0';
        $contract    = (new JsonSimpleObject())->getPath();

        $this->requestBuilder->createPublishPactRequestFromFile($this->baseUrl, $consumerName, $providerName, $version, $contract)->willReturn($request);
        $this->client->sendRequest($request)->willReturn($response);
        $response->getStatusCode()->willReturn(200);

        $this->publishPact($providerName, $consumerName, $version, $contract)->shouldReturn($response);
    }

    function it_should_publish_pact_by_object(RequestInterface $request, ResponseInterface $response)
    {
        $consumerName = 'consumerA';
        $providerName = 'providerB';
        $version      = '1.0.0';
        $contract    = new JsonSimpleObject();

        $this->requestBuilder->createPublishPactRequestFromObject($this->baseUrl, $consumerName, $providerName, $version, $contract)->willReturn($request);
        $this->client->sendRequest($request)->willReturn($response);
        $response->getStatusCode()->willReturn(200);

        $this->publishPact($providerName, $consumerName, $version, $contract)->shouldReturn($response);
    }

    function it_should_throw_exception_if_wrong_type_passed()
    {
        $consumerName = 'consumerA';
        $providerName = 'providerB';
        $version      = '1.0.0';
        $contract    = new \stdClass();

        $this->shouldThrow(PactBrokerException::class)->during('publishPact', [$this->baseUrl, $providerName, $consumerName, $version, $contract]);
    }

    function it_should_tag_version(RequestInterface $request, ResponseInterface $response)
    {
        $consumerName = 'consumerA';
        $version      = '1.0.0';
        $tagName      = 'prod';

        $this->requestBuilder->createTagVersionRequest($this->baseUrl, $consumerName, $version, $tagName)->willReturn($request);
        $this->client->sendRequest($request)->willReturn($response);
        $response->getStatusCode()->willReturn(200);

        $this->tagVersion($consumerName, $version, $tagName)->shouldReturn($response);
    }

    function it_should_retrieve_contract_by_tag(RequestInterface $request, ResponseInterface $response)
    {
        $providerName = 'providerB';
        $consumerName = 'consumerA';
        $version      = 'latest';
        $tagName      = 'prod';

        $this->requestBuilder->createRetrievePactRequest($this->baseUrl, $consumerName, $providerName, $version, $tagName)->willReturn($request);
        $this->client->sendRequest($request)->willReturn($response);
        $response->getStatusCode()->willReturn(200);
        $response->getHeader('X-Pact-Consumer-Version')->willReturn([]);

        $this->retrievePact($providerName, $consumerName, $version, $tagName)->data()->shouldReturn($response);
    }

    function it_should_retrieve_contract_by_version(RequestInterface $request, ResponseInterface $response)
    {
        $providerName = 'providerB';
        $consumerName = 'consumerA';
        $version      = 'latest';

        $this->requestBuilder->createRetrievePactRequest($this->baseUrl, $consumerName, $providerName, $version, null)->willReturn($request);
        $this->client->sendRequest($request)->willReturn($response);
        $response->getStatusCode()->willReturn(200);
        $response->getHeader('X-Pact-Consumer-Version')->willReturn([]);

        $this->retrievePact($providerName, $consumerName, $version)->data()->shouldReturn($response);
    }

    function it_should_retrieve_last_added_pact_for_all_providers(RequestInterface $request, ResponseInterface $response)
    {
        $this->requestBuilder->createRetrieveLastAddedPact($this->baseUrl)->willReturn($request);
        $this->client->sendRequest($request)->willReturn($response);
        $response->getStatusCode()->willReturn(200);
        $response->getHeader('X-Pact-Consumer-Version')->willReturn([]);

        $this->retrieveLastAddedPact()->data()->shouldReturn($response);
    }

    function it_should_remove_participant(RequestInterface $request, ResponseInterface $response)
    {
        $participantName = 'Service 1';

        $this->requestBuilder->createRemoveParticipantRequest($this->baseUrl, $participantName)->willReturn($request);
        $this->client->sendRequest($request)->willReturn($response);
        $response->getStatusCode()->willReturn(200);

        $this->removeParticipant($participantName)->shouldReturn($response);
    }

    function it_should_throw_exception_if_there_was_an_error_while_publishing_pact(RequestInterface $request, ResponseInterface $response, StreamInterface $stream)
    {
        $consumerName = 'consumerA';
        $providerName = 'providerB';
        $version      = '1.0.0';
        $contract    = (new JsonSimpleObject())->getPath();

        $this->requestBuilder->createPublishPactRequestFromFile($this->baseUrl, $consumerName, $providerName, $version, $contract)->willReturn($request);
        $this->client->sendRequest($request)->willReturn($response);

        $response->getStatusCode()->willReturn(500);
        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn('Error');

        $this->shouldThrow(PactBrokerException::class)->during('publishPact', [$providerName, $consumerName, $version, $contract]);
    }

    function it_should_throw_exception_while_tagging_if_error_response(RequestInterface $request, ResponseInterface $response, StreamInterface $stream)
    {
        $consumerName = 'consumerA';
        $version      = '1.0.0';
        $tagName      = 'prod';

        $this->requestBuilder->createTagVersionRequest($this->baseUrl, $consumerName, $version, $tagName)->willReturn($request);
        $this->client->sendRequest($request)->willReturn($response);

        $response->getStatusCode()->willReturn(500);
        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn('Error');

        $this->shouldThrow(PactBrokerException::class)->during('tagVersion', [$consumerName, $version, $tagName]);
    }

    function it_should_throw_exception_while_retrieving_by_tag_if_error_response(RequestInterface $request, ResponseInterface $response, StreamInterface $stream)
    {
        $providerName = 'providerB';
        $consumerName = 'consumerA';
        $version      = 'latest';
        $tagName      = 'prod';

        $this->requestBuilder->createRetrievePactRequest($this->baseUrl, $consumerName, $providerName, $version, $tagName)->willReturn($request);
        $this->client->sendRequest($request)->willReturn($response);
        $response->getStatusCode()->willReturn(500);
        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn('Error');

        $this->shouldThrow(PactBrokerException::class)->during('retrievePact', [$providerName, $consumerName, $version, $tagName]);
    }

    function it_should_throw_exception_while_retrieving_last_added_contract(RequestInterface $request, ResponseInterface $response, StreamInterface $stream)
    {
        $this->requestBuilder->createRetrieveLastAddedPact($this->baseUrl)->willReturn($request);
        $this->client->sendRequest($request)->willReturn($response);
        $response->getStatusCode()->willReturn(500);
        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn('Error');

        $this->shouldThrow(PactBrokerException::class)->during('retrieveLastAddedPact');
    }

    function it_should_throw_exception_while_removing_participant_if_error_occurred(RequestInterface $request, ResponseInterface $response, StreamInterface $stream)
    {
        $participantName = 'Service 1';

        $this->requestBuilder->createRemoveParticipantRequest($this->baseUrl, $participantName)->willReturn($request);
        $this->client->sendRequest($request)->willReturn($response);
        $response->getStatusCode()->willReturn(500);
        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn('Error');

        $this->shouldThrow(PactBrokerException::class)->during('removeParticipant', [$participantName]);
    }

    function it_should_format_response_for_last_added_pact(RequestInterface $request, ResponseInterface $response, ResponseFormatter $responseFormatter)
    {
        $this->requestBuilder->createRetrieveLastAddedPact($this->baseUrl)->willReturn($request);
        $this->client->sendRequest($request)->willReturn($response);
        $response->getStatusCode()->willReturn(200);
        $response->getHeader('X-Pact-Consumer-Version')->willReturn(['1.2.1']);

        $responseFormatter->format($response)->willReturn('some');
        $contract = $this->retrieveLastAddedPact($responseFormatter);
        $contract->shouldHaveType(Contract::class);
        $contract->data()->shouldReturn('some');
        $contract->version()->shouldReturn('1.2.1');
    }

    function it_should_return_empty_string_if_no_version_passed(RequestInterface $request, ResponseInterface $response, ResponseFormatter $responseFormatter)
    {
        $this->requestBuilder->createRetrieveLastAddedPact($this->baseUrl)->willReturn($request);
        $this->client->sendRequest($request)->willReturn($response);
        $response->getStatusCode()->willReturn(200);
        $response->getHeader('X-Pact-Consumer-Version')->willReturn([]);

        $responseFormatter->format($response)->willReturn('some');
        $contract = $this->retrieveLastAddedPact($responseFormatter);
        $contract->shouldHaveType(Contract::class);
        $contract->data()->shouldReturn('some');
        $contract->version()->shouldReturn('');
    }

    function it_should_format_response_for_pact_retrieve(RequestInterface $request, ResponseInterface $response, ResponseFormatter $responseFormatter)
    {
        $providerName = 'providerB';
        $consumerName = 'consumerA';
        $version      = 'latest';
        $tagName      = 'prod';

        $this->requestBuilder->createRetrievePactRequest($this->baseUrl, $consumerName, $providerName, $version, $tagName)->willReturn($request);
        $this->client->sendRequest($request)->willReturn($response);
        $response->getStatusCode()->willReturn(200);
        $response->getHeader('X-Pact-Consumer-Version')->willReturn(['1.2.0']);

        $responseFormatter->format($response)->willReturn('a');
        $contract = $this->retrievePact($providerName, $consumerName, $version, $tagName, $responseFormatter, $responseFormatter);
        $contract->shouldHaveType(Contract::class);
        $contract->data()->shouldReturn('a');
        $contract->version()->shouldReturn('1.2.0');
    }
    
}
