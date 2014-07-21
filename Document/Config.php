<?php

namespace Jiabin\HolterBundle\Document;

use Jiabin\HolterBundle\Model\Config as BaseConfig;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document
 */
class Config extends BaseConfig
{
    /**
     * @ODM\Id(strategy="AUTO")
     */
    protected $id;

    /**
     * @ODM\Hash
     */
    protected $config;
}
