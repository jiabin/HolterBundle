<?php

namespace Jiabin\HolterBundle\Model;

interface StatusInterface
{
    const GOOD    = 0;
    const MINOR   = 10;
    const MAJOR   = 20;
    const UNKNOWN = 30;
}