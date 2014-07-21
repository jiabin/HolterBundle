<?php

namespace Jiabin\HolterBundle\Document;

use Jiabin\HolterBundle\Model\Check as BaseCheck;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document
 */
class Check extends BaseCheck
{
    /**
     * @ODM\Id(strategy="AUTO")
     */
    protected $id;

    /**
     * @ODM\String
     */
    protected $name;

    /**
     * @ODM\String
     */
    protected $displayGroup;

    /**
     * @ODM\String
     */
    protected $engine;

    /**
     * @ODM\String
     */
    protected $token;

    /**
     * @ODM\Hash
     */
    protected $options = array();

    /**
     * @ODM\Int
     */
    protected $interval;

    /**
     * @ODM\Date
     */
    protected $createdAt;
}
