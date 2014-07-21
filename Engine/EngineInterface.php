<?php

namespace Jiabin\HolterBundle\Engine;

use Jiabin\HolterBundle\Event\PullEvent;
use Jiabin\HolterBundle\Event\PushEvent;

interface EngineInterface
{
    public function getName();

    public function check($options);
}