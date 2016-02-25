<?php

namespace othillo\BroadwayReplayer\EventStore;

use othillo\BroadwayReplayer\EventHandling\ReplayAwareEventBusInterface;

class ReplayAwareEventBusPublishingVisitorTest extends EventBusPublishingVisitorTest
{
    public function setUp()
    {
        $this->eventBus = $this->prophesize(ReplayAwareEventBusInterface::class);
        $this->visitor  = new ReplayAwareEventBusPublishingVisitor($this->eventBus->reveal());
    }

    /**
     * @test
     */
    public function it_calls_before_replay_method_on_event_bus()
    {
        $this->eventBus
            ->beforeReplay()
            ->shouldBeCalled();

        $this->visitor->beforeReplay();
    }

    /**
     * @test
     */
    public function it_calls_after_replay_method_on_event_bus()
    {
        $this->eventBus
            ->afterReplay()
            ->shouldBeCalled();

        $this->visitor->afterReplay();
    }
}
