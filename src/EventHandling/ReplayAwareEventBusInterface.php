<?php

namespace othillo\BroadwayReplayer\EventHandling;

use Broadway\EventHandling\EventBusInterface;
use othillo\BroadwayReplayer\ReplayAwareInterface;

interface ReplayAwareEventBusInterface extends EventBusInterface, ReplayAwareInterface
{
}
