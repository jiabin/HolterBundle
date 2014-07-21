<?php

namespace Jiabin\HolterBundle\Model;

interface StatusInterface
{
    const GOOD    = 0;
    const MINOR   = 1;
    const MAJOR   = 2;
    const UNKNOWN = 3;
}