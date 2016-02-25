<?php

namespace othillo\BroadwayReplayer\Replayer;

use Broadway\EventStore\EventVisitorInterface;
use Broadway\EventStore\Management\Criteria;
use Broadway\EventStore\Management\EventStoreManagementInterface;
use othillo\BroadwayReplayer\ReplayAwareInterface;

class Replayer
{
    private $eventStore;
    private $eventVisitor;
    private $isReplayAware = false;

    public function __construct(
        EventStoreManagementInterface $eventStore,
        EventVisitorInterface $eventVisitor
    ) {
        $this->eventStore    = $eventStore;
        $this->eventVisitor  = $eventVisitor;
        $this->isReplayAware = $eventVisitor instanceof ReplayAwareInterface;
    }

    public function replay(Criteria $criteria)
    {
        if ($this->isReplayAware) {
            $this->eventVisitor->beforeReplay();
        }

        $this->eventStore->visitEvents($criteria, $this->eventVisitor);

        if ($this->isReplayAware) {
            $this->eventVisitor->beforeReplay();
        }
    }
}
