<?php

namespace Jiabin\HolterBundle\Document;

use Jiabin\HolterBundle\Check\Check as BaseCheck;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * @ODM\Document
 */
class Check
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

    public function __construct()
    {
        $this->token = $this->generateToken();
    }

    private function generateToken()
    {
        if (function_exists('openssl_random_pseudo_bytes')) {
            return bin2hex(openssl_random_pseudo_bytes(16));;
        } else {
            return md5(uniqid(mt_rand(), true));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;   
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->name = $name;   

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;   
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;   

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;   
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        return $this->token;   
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions($options)
    {
        $this->options = $options;   

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->options;   
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;   

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;   
    }
}
