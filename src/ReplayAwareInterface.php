<?php

namespace othillo\BroadwayReplayer;

interface ReplayAwareInterface
{
    /**
     * hook for preparing for replaying, like clearing database
     */
    public function beforeReplay();

    /**
     * hook for finalizing replaying
     */
    public function afterReplay();
}
