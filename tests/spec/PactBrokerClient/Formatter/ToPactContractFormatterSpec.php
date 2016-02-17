<?php

namespace spec\Madkom\PactBrokerClient\Formatter;

use Madkom\PactBrokerClient\Formatter\ToPactContractFormatter;
use Madkom\PactBrokerClient\PactBrokerException;
use Madkom\PactBrokerClient\ResponseFormatter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class ToPactContractFormatterSpec
 * @package spec\Madkom\PactBrokerClient\Formatter
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin ToPactContractFormatter
 */
class ToPactContractFormatterSpec extends ObjectBehavior
{

    function it_is_initializable()
    {
        $this->shouldHaveType(ResponseFormatter::class);
    }

    function it_should_format_to_pact(ResponseInterface $response, StreamInterface $stream)
    {
        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn("{\"consumer\":{\"name\":\"TestConsumer1\"},\"provider\":{\"name\":\"TestProvider1\"},\"interactions\":[{\"description\":\"A request for foo\",\"provider_state\":\"Foo exists\",\"request\":{\"method\":\"get\",\"path\":\"/foo\"},\"response\":{\"status\":200,\"headers\":{\"Content-Type\":\"application/json\"},\"body\":{\"foo\":\"bar\"}}}],\"metadata\":{\"pactSpecificationVersion\":\"2.0.0\"}}");

        $this->format($response)->shouldReturn(json_encode(json_decode("{\"consumer\":{\"name\":\"TestConsumer1\"},\"provider\":{\"name\":\"TestProvider1\"},\"interactions\":[{\"description\":\"A request for foo\",\"provider_state\":\"Foo exists\",\"request\":{\"method\":\"get\",\"path\":\"/foo\"},\"response\":{\"status\":200,\"headers\":{\"Content-Type\":\"application/json\"},\"body\":{\"foo\":\"bar\"}}}],\"metadata\":{\"pactSpecificationVersion\":\"2.0.0\"}}")));
    }

    function it_should_throw_exception_if_missing_key_occurred(ResponseInterface $response, StreamInterface $stream)
    {
        $response->getBody()->willReturn($stream);
        $stream->getContents()->willReturn("{\"provider\":{\"name\":\"TestProvider1\"},\"interactions\":[{\"description\":\"A request for foo\",\"provider_state\":\"Foo exists\",\"request\":{\"method\":\"get\",\"path\":\"/foo\"},\"response\":{\"status\":200,\"headers\":{\"Content-Type\":\"application/json\"},\"body\":{\"foo\":\"bar\"}}}],\"metadata\":{\"pactSpecificationVersion\":\"2.0.0\"}}");
        $this->shouldThrow(PactBrokerException::class)->during('format', [$response]);

        $stream->getContents()->willReturn("{\"consumer\":{\"name\":\"TestConsumer1\"},\"interactions\":[{\"description\":\"A request for foo\",\"provider_state\":\"Foo exists\",\"request\":{\"method\":\"get\",\"path\":\"/foo\"},\"response\":{\"status\":200,\"headers\":{\"Content-Type\":\"application/json\"},\"body\":{\"foo\":\"bar\"}}}],\"metadata\":{\"pactSpecificationVersion\":\"2.0.0\"}}");
        $this->shouldThrow(PactBrokerException::class)->during('format', [$response]);

        $stream->getContents()->willReturn("{\"consumer\":{\"name\":\"TestConsumer1\"},\"provider\":{\"name\":\"TestProvider1\"},\"metadata\":{\"pactSpecificationVersion\":\"2.0.0\"}}");
        $this->shouldThrow(PactBrokerException::class)->during('format', [$response]);

        $stream->getContents()->willReturn("{\"consumer\":{\"name\":\"TestConsumer1\"},\"provider\":{\"name\":\"TestProvider1\"},\"interactions\":[{\"description\":\"A request for foo\",\"provider_state\":\"Foo exists\",\"request\":{\"method\":\"get\",\"path\":\"/foo\"},\"response\":{\"status\":200,\"headers\":{\"Content-Type\":\"application/json\"},\"body\":{\"foo\":\"bar\"}}}]}");
        $this->shouldThrow(PactBrokerException::class)->during('format', [$response]);
    }

}
