<?php

namespace othillo\BroadwayReplayer\EventHandling;

use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainMessage;
use Broadway\Domain\Metadata;
use Broadway\EventHandling\EventListenerInterface;
use othillo\BroadwayReplayer\ReadModel\ReplayAwareProjectorInterface;

class ReplayAwareSimpleEventBusTest extends \PHPUnit_Framework_TestCase
{
    private $eventBus;

    public function setUp()
    {
        $this->eventBus = new ReplayAwareSimpleEventBus();
    }

    /**
     * @test
     */
    public function it_subscribes_replay_aware_event_listeners()
    {
        $eventListener1 = $this->prophesize(ReplayAwareProjectorInterface::class);
        $eventListener2 = $this->prophesize(ReplayAwareProjectorInterface::class);
        $eventListener3 = $this->prophesize(EventListenerInterface::class);

        $this->eventBus->subscribe($eventListener1->reveal());
        $this->eventBus->subscribe($eventListener2->reveal());
        $this->eventBus->subscribe($eventListener3->reveal());

        // subscription of replay aware event listeners is indirectly tested in self::it_calls_before_replay_method_on_registered_replay_aware_event_listeners

        // subscription of non replay aware event listeners is indirectly tested in self::it_publishes_domain_messages
    }

    /**
     * @test
     */
    public function it_calls_before_replay_method_on_registered_replay_aware_event_listeners()
    {
        $eventListener1 = $this->prophesize(ReplayAwareProjectorInterface::class);
        $eventListener2 = $this->prophesize(ReplayAwareProjectorInterface::class);

        $this->eventBus->subscribe($eventListener1->reveal());
        $this->eventBus->subscribe($eventListener2->reveal());

        $eventListener1
            ->beforeReplay()
            ->shouldBeCalled();

        $eventListener2
            ->beforeReplay()
            ->shouldBeCalled();

        $this->eventBus->beforeReplay();
    }

    /**
     * @test
     */
    public function it_calls_after_replay_method_on_registered_replay_aware_event_listeners()
    {
        $eventListener1 = $this->prophesize(ReplayAwareProjectorInterface::class);
        $eventListener2 = $this->prophesize(ReplayAwareProjectorInterface::class);

        $this->eventBus->subscribe($eventListener1->reveal());
        $this->eventBus->subscribe($eventListener2->reveal());

        $eventListener1
            ->afterReplay()
            ->shouldBeCalled();

        $eventListener2
            ->afterReplay()
            ->shouldBeCalled();

        $this->eventBus->afterReplay();
    }

    /**
     * @test
     */
    public function it_publishes_domain_messages()
    {
        $eventListener1 = $this->prophesize(ReplayAwareProjectorInterface::class);
        $eventListener2 = $this->prophesize(ReplayAwareProjectorInterface::class);
        $eventListener3 = $this->prophesize(EventListenerInterface::class);

        $this->eventBus->subscribe($eventListener1->reveal());
        $this->eventBus->subscribe($eventListener2->reveal());
        $this->eventBus->subscribe($eventListener3->reveal());

        $event1 = $this->createDomainMessage(['foo' => 'bar']);
        $event2 = $this->createDomainMessage(['bar' => 'baz']);

        $eventStream = new DomainEventStream([$event1, $event2]);

        $eventListener1
            ->handle($event1)
            ->shouldBeCalled();

        $eventListener1
            ->handle($event2)
            ->shouldBeCalled();

        $eventListener2
            ->handle($event1)
            ->shouldBeCalled();

        $eventListener2
            ->handle($event2)
            ->shouldBeCalled();

        $eventListener3
            ->handle($event1)
            ->shouldBeCalled();

        $eventListener3
            ->handle($event2)
            ->shouldBeCalled();

        $this->eventBus->publish($eventStream);
    }

    private function createDomainMessage($payload)
    {
        return DomainMessage::recordNow(1, 1, new Metadata([]), $payload);
    }
}
