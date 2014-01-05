<?php

namespace Jiabin\HolterBundle\Model;

abstract class Result implements ResultInterface
{
    const GOOD    = 0;
    const MINOR   = 10;
    const MAJOR   = 20;
    const UNKNOWN = 30;

    /**
     * Class constructor
     */
    public function __construct($checkName, $message, $status)
    {
        $this->checkName = $checkName;
        $this->message   = $message;
        $this->status    = $status;
        $this->createdAt = new \DateTime();
    }

    /**
     * Get id
     * 
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set checkName
     *
     * @param  string $checkName
     * @return self
     */
    public function setCheckName($checkName)
    {
        $this->checkName = $checkName;

        return $this;
    }

    /**
     * Get checkName
     * 
     * @return string
     */
    public function getCheckName()
    {
        return $this->checkName;
    }

    /**
     * Set message
     *
     * @param  string $message
     * @return self
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     * 
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set status
     *
     * @param  integer $status
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = intval($status);

        return $this;
    }

    /**
     * Get status
     * 
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        $list = self::getStatusList();

        if (!isset($list[$this->getStatus()])) {
            return 'n/a';
        }

        return $list[$this->getStatus()];
    }

    /**
     * Set createdAt
     *
     * @param  string $createdAt
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     * 
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'check_name'  => $this->checkName,
            'message'     => $this->message,
            'status'      => $this->status,
            'status_name' => $this->getStatusName(),
            'created_at'  => $this->createdAt->format('c')
        );
    }

    /**
     * @return array
     */
    public static function getStatusList()
    {
        return array(
            self::GOOD    => 'good',
            self::MINOR   => 'minor',
            self::MAJOR   => 'major',
            self::UNKNOWN => 'unknown',
        );
    }
}