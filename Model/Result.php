<?php

namespace Jiabin\HolterBundle\Model;

abstract class Result implements ResultInterface
{
    /**
     * Class constructor
     */
    public function __construct($message, $status)
    {
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
     * Set checkId
     *
     * @param  CheckInterface $check
     * @return self
     */
    public function setCheck(CheckInterface $check)
    {
        $this->check = $check;

        return $this;
    }

    /**
     * Get checkId
     *
     * @return CheckInterface
     */
    public function getCheck()
    {
        return $this->check;
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
        $list = Status::getStatusList();

        if (!isset($list[$this->getStatus()])) {
            return 'n/a';
        }

        return $list[$this->getStatus()];
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
            'name'        => $this->check->getName(),
            'message'     => $this->message,
            'status'      => $this->status,
            'status_name' => $this->getStatusName()
        );
    }
}