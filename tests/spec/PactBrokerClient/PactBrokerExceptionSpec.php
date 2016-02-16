<?php

namespace spec\Madkom\PactBrokerClient;

use Madkom\PactBrokerClient\PactBrokerException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class PactBrokerExceptionSpec
 * @package spec\Madkom\PactBrokerClient
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin PactBrokerException
 */
class PactBrokerExceptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(\Exception::class);
    }
}
