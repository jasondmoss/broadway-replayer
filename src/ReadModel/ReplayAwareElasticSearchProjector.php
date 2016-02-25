<?php

namespace othillo\BroadwayReplayer\ReadModel;

use Broadway\ReadModel\Projector;

abstract class ReplayAwareElasticSearchProjector extends Projector implements ReplayAwareProjectorInterface
{
    /**
     * {@inheritdoc}
     */
    public function beforeReplay()
    {
        // @todo create new index
    }

    /**
     * {@inheritdoc}
     */
    public function afterReplay()
    {
        // @todo set alias to new index
    }
}
