<?php

namespace othillo\BroadwayReplayer\EventStore;

use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use Broadway\EventHandling\EventBusInterface;

class EventBusPublishingVisitorTest extends \PHPUnit_Framework_TestCase
{
    protected $eventBus;
    protected $visitor;

    public function setUp()
    {
        $this->eventBus = $this->prophesize(EventBusInterface::class);
        $this->visitor  = new EventBusPublishingVisitor($this->eventBus->reveal());
    }

    /**
     * @test
     */
    public function it_publishes_an_event_as_an_event_stream_on_the_event_bus()
    {
        $event = $this->createDomainMessage(['foo' => 'bar']);

        $this->eventBus
            ->publish(new DomainEventStream([$event]))
            ->shouldBeCalled();

        $this->visitor->doWithEvent($event);
    }

    private function createDomainMessage($payload)
    {
        return DomainMessage::recordNow(1, 1, new Metadata([]), $payload);
    }
}
