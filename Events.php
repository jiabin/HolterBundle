<?php

namespace Jiabin\HolterBundle;

final class Events
{
    /**
     * The holter.check event is thrown 
     * each time a check is run.
     * 
     * The event listener receives an
     * Jiabin\HolterBundle\Event\CheckEvent instance.
     *
     * @var string
     */
    const HOLTER_CHECK = 'holter.check';
}
