<?php

namespace Jiabin\HolterBundle\Event;

use Jiabin\HolterBundle\Model\Result;
use Symfony\Component\EventDispatcher\Event;

class CheckEvent extends Event
{
    /**
     * @var Result
     */
    protected $result;

    /**
     * Class constructor
     *
     * @param Result $result
     */
    public function __construct(Result $result)
    {
        $this->result = $result;
    }

    /**
     * Get result
     * 
     * @return Result
     */
    public function getResult()
    {
        return $this->result;
    }
}
