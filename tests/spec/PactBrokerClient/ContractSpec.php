<?php

namespace spec\Madkom\PactBrokerClient;

use Madkom\PactBrokerClient\Contract;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class ContractSpec
 * @package spec\Madkom\PactBrokerClient
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin Contract
 */
class ContractSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('1.12.1', 'someContractJson');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Madkom\PactBrokerClient\Contract');
    }

    function it_should_return_values_it_was_constructed_with()
    {
        $this->version()->shouldReturn('1.12.1');
        $this->data()->shouldReturn('someContractJson');
    }
}
