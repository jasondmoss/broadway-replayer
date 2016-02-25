<?php

namespace othillo\BroadwayReplayer\EventStore;

use othillo\BroadwayReplayer\EventHandling\ReplayAwareEventBusInterface;
use othillo\BroadwayReplayer\ReplayAwareInterface;

class ReplayAwareEventBusPublishingVisitor extends EventBusPublishingVisitor implements ReplayAwareInterface
{
    public function __construct(ReplayAwareEventBusInterface $eventBus)
    {
        parent::__construct($eventBus);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeReplay()
    {
        $this->eventBus->beforeReplay();
    }

    /**
     * {@inheritdoc}
     */
    public function afterReplay()
    {
        $this->eventBus->afterReplay();
    }
}
