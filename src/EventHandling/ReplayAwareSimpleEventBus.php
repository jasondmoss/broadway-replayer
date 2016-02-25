<?php

namespace othillo\BroadwayReplayer\EventHandling;

use Broadway\EventHandling\EventListenerInterface;
use Broadway\EventHandling\SimpleEventBus;
use othillo\BroadwayReplayer\ReplayAwareInterface;

class ReplayAwareSimpleEventBus extends SimpleEventBus implements ReplayAwareEventBusInterface
{
    private $replayAwareEventListeners = [];

    /**
     * {@inheritDoc}
     */
    public function subscribe(EventListenerInterface $eventListener)
    {
        parent::subscribe($eventListener);

        if ($eventListener instanceof ReplayAwareInterface) {
            $this->replayAwareEventListeners[] = $eventListener;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function beforeReplay()
    {
        foreach ($this->replayAwareEventListeners as $eventListener) {
            $eventListener->beforeReplay();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function afterReplay()
    {
        foreach ($this->replayAwareEventListeners as $eventListener) {
            $eventListener->afterReplay();
        }
    }
}
