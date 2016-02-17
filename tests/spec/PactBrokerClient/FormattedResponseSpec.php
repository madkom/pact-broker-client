<?php

namespace spec\Madkom\PactBrokerClient;

use Madkom\PactBrokerClient\FormattedResponse;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class FormattedResponseSpec
 * @package spec\Madkom\PactBrokerClient
 * @author  Dariusz Gafka <d.gafka@madkom.pl>
 * @mixin FormattedResponse
 */
class FormattedResponseSpec extends ObjectBehavior
{

    function let()
    {
        $this->beConstructedWith([]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Madkom\PactBrokerClient\FormattedResponse');
    }

    public function it_should_return_value_it_was_created_from()
    {
        $this->data()->shouldReturn([]);
    }

}
