<?php

namespace Jiabin\HolterBundle\Model;

class Status implements StatusInterface
{
    /**
     * @var integer
     */
    protected $status;

    /**
     * @var array
     */
    protected $results = array();

    /**
     * @var DateTime
     */
    protected $lastMinor;

    /**
     * @var DateTime
     */
    protected $lastMajor;

    /**
     * @var DateTime
     */
    protected $lastUpdated;

    /**
     * Get status
     * 
     * @return integer
     */
    public function getStatus()
    {
        foreach ($this->getResults() as $result) {
            if ($result->getStatus() === Result::UNKNOWN) {
                continue;
            }
            if (!isset($status) || $result->getStatus() > $status) {
                $status = $result->getStatus();
            }
        }

        if (!isset($status)) {
            $status = Result::UNKNOWN;
        }

        return $status;
    }

    /**
     * @return string
     */
    public function getStatusName()
    {
        $list = Result::getStatusList();

        if (!isset($list[$this->getStatus()])) {
            return 'n/a';
        }

        return $list[$this->getStatus()];
    }

    /**
     * Add result
     * 
     * @param Result $result
     */
    public function addResult(Result $result)
    {
        $this->results[$result->getCheckName()] = $result;

        if (is_null($this->lastUpdated) or $this->lastUpdated < $result->getCreatedAt()) {
            $this->lastUpdated = $result->getCreatedAt();
        }
    }

    /**
     * Get results
     * 
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * Set lastMinor
     *
     * @param  DateTime $lastMinor
     * @return self
     */
    public function setLastMinor(\DateTime $lastMinor)
    {
        $this->lastMinor = $lastMinor;

        return $this;
    }  

    /**
     * Get lastMinor
     *
     * @return DateTime
     */
    public function getLastMinor()
    {
        return $this->lastMinor;
    }

    /**
     * Set lastMajor
     *
     * @param  DateTime $lastMajor
     * @return self
     */
    public function setLastMajor(\DateTime $lastMajor)
    {
        $this->lastMajor = $lastMajor;

        return $this;
    }  

    /**
     * Get lastMajor
     *
     * @return DateTime
     */
    public function getLastMajor()
    {
        return $this->lastMajor;
    }  

    /**
     * Get lastUpdated
     * 
     * @return DateTime
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = array(
            'status'       => $this->getStatus(),
            'status_name'  => $this->getStatusName(),
            'results'      => array()
        );

        // Last updated
        if ($lastUpdated = $this->getLastUpdated()) {
            $array['last_updated'] = $lastUpdated->format('c');
        }

        // Last minor & major
        if ($lastMinor = $this->getLastMinor()) {
            $array['last_minor'] = $lastMinor->format('c');
        }

        if ($lastMajor = $this->getLastMajor()) {
            $array['last_major'] = $lastMajor->format('c');
        }

        foreach ($this->getResults() as $result) {
            $array['results'][$result->getCheckName()] = $result->toArray();
        }

        return $array;
    }
}