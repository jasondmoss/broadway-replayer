<?php

namespace othillo\BroadwayReplayer\Replayer;

use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use Broadway\EventStore\Management\Criteria;
use Broadway\EventStore\Management\EventStoreManagementInterface;
use othillo\BroadwayReplayer\EventStore\ReplayAwareEventBusPublishingVisitor;

class ReplayerTest extends \PHPUnit_Framework_TestCase
{
    private $eventStore;
    private $eventVisitor;
    private $replayer;

    public function setUp()
    {
        $this->eventStore   = $this->prophesize(EventStoreManagementInterface::class);
        $this->eventVisitor = $this->prophesize(ReplayAwareEventBusPublishingVisitor::class);

        $this->replayer     = new Replayer($this->eventStore->reveal(), $this->eventVisitor->reveal());
    }

    /**
     * @test
     */
    public function it_replays_events()
    {
        $criteria    = new Criteria();
        $event1      = $this->createDomainMessage(['foo' => 'bar']);
        $event2      = $this->createDomainMessage(['bar' => 'baz']);
        $eventStream = new DomainEventStream([$event1, $event2]);

        $this->eventStore
            ->visitEvents($criteria, $this->eventVisitor)
            ->willReturn($eventStream);

        $this->eventVisitor
            ->beforeReplay()
            ->shouldBeCalled();

        $this->eventVisitor
            ->doWithEvent($event1)
            ->shouldBeCalled();

        $this->eventVisitor
            ->doWithEvent($event2)
            ->shouldBeCalled();

        $this->eventVisitor
            ->afterReplay()
            ->shouldBeCalled();

        $this->replayer->replay($criteria);
    }

    /**
     * @test
     */
    public function it_calls_before_and_after_replay_methods_on_replay_aware_event_visitor()
    {
        $this->eventVisitor
            ->beforeReplay()
            ->shouldBeCalled();

        $this->eventVisitor
            ->afterReplay()
            ->shouldBeCalled();

        $this->replayer->replay(new Criteria());
    }

    private function createDomainMessage($payload)
    {
        return DomainMessage::recordNow(1, 1, new Metadata([]), $payload);
    }
}
