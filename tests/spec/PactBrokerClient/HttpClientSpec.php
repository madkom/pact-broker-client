<?php

namespace spec\Madkom\PactBrokerClient;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class HttpClientSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Madkom\PactBrokerClient\HttpClient');
    }
}
