<?php

namespace Jiabin\HolterBundle\Document;

use Jiabin\HolterBundle\Model\Result as BaseResult;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document
 */
class Result extends BaseResult
{
    /**
     * @ODM\Id
     */    
    protected $id;

    /**
     * @ODM\String
     */
    protected $checkName;

    /**
     * @ODM\String
     */
    protected $message;

    /**
     * @ODM\Int
     */
    protected $status;

    /**
     * @ODM\Date
     */
    protected $createdAt;
}
