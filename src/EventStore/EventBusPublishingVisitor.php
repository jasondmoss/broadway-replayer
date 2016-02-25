<?php

namespace othillo\BroadwayReplayer\EventStore;

use Broadway\Domain\DomainEventStream;
use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventBusInterface;
use Broadway\EventStore\EventVisitorInterface;

class EventBusPublishingVisitor implements EventVisitorInterface
{
    protected $eventBus;

    public function __construct(EventBusInterface $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    /**
     * {@inheritdoc}
     */
    public function doWithEvent(DomainMessage $domainMessage)
    {
        $this->eventBus->publish(new DomainEventStream([$domainMessage]));
    }
}
