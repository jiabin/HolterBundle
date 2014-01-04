<?php

namespace Jiabin\HolterBundle\Check;

interface CheckInterface
{
    /**
     * Check
     *
     * @return Result
     */
    public function check();
}