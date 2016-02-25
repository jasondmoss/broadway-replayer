<?php

namespace othillo\BroadwayReplayer\ReadModel;

use Broadway\ReadModel\ProjectorInterface;
use othillo\BroadwayReplayer\ReplayAwareInterface;

/**
 * Handles events and projects to a read model and allows for replaying
 */
interface ReplayAwareProjectorInterface extends ProjectorInterface, ReplayAwareInterface
{
}
