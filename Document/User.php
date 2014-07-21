<?php

namespace Jiabin\HolterBundle\Document;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique as MongoDBUnique;

/**
 * @ODM\Document
 * @MongoDBUnique("email")
 */
class User extends BaseUser
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
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        $this->setUsername($email);

        return parent::setEmail($email);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailCanonical($emailCanonical)
    {
        $this->setUsernameCanonical($emailCanonical);

        return parent::setEmailCanonical($emailCanonical);
    }

    /**
     * Set name
     *
     * @param  string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
