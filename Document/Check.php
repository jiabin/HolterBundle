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
     * @ODM\Id
     */    
    protected $id;

    /**
     * @ODM\String
     */
    protected $name;

    /**
     * @ODM\String
     */
    protected $type;

    /**
     * @ODM\String
     */
    protected $token;

    /**
     * @ODM\Hash
     */
    protected $options = array();

    /**
     * @ODM\Date
     */
    protected $createdAt;
}
