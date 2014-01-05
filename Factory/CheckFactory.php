<?php

namespace Jiabin\HolterBundle\Factory;

use Jiabin\HolterBundle\Model\Result;
use Jiabin\HolterBundle\Model\Status;
use Jiabin\HolterBundle\Check\CheckInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CheckFactory
{
    /**
     * @var array
     */
    protected $types = array();

    /**
     * @var array
     */
    protected $checks = array();

    /**
     * @var ObjectManager
     */
    protected $om;

    /**
     * @var string
     */
    public $resultClass;

    /**
     * @var string
     */
    public $checkClass;

    /**
     * Class constructor
     *
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, $resultClass, $checkClass)
    {
        $this->om = $om;
        $this->resultClass = $resultClass;
        $this->checkClass = $checkClass;
    }

    /**
     * Get objectManager
     * 
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->om;
    }

    /**
     * Add type
     * 
     * @param string $type
     * @param string $class
     */
    public function addType($type, $class)
    {
        $this->types[$type] = $class;
    }

    /**
     * Get checkTypes
     * 
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Get typeClass
     * 
     * @return array
     */
    public function getTypeClass($type)
    {
        return $this->types[$type];
    }

    /**
     * Load checks
     */
    public function loadChecks()
    {
        foreach ($this->getAvailableChecks() as $check) {
            $type = $check->getType();
            if (!$type or !array_key_exists($type, $this->getTypes())) {
                throw new \Exception('Invalid check type given');
            }

            $className = $this->getTypeClass($type);
            $class = new $className($check->getName(), $check->getOptions());
            $class->setCheckFactory($this);
            $this->addCheck($class);
        }
    }

    /**
     * Add check
     * 
     * @param string         $name
     * @param CheckInterface $check
     */
    public function addCheck(CheckInterface $check)
    {
        if (array_key_exists($check->getName(), $this->checks)) {
            throw new \Exception('Check names must be unique!');
        }
        
        $this->checks[$check->getName()] = $check;
    }

    /**
     * Get checks
     * 
     * @return array
     */
    public function getChecks()
    {
        return $this->checks;
    }

    /**
     * Get available checks
     * 
     * @return array
     */
    public function getAvailableChecks()
    {
        return $this->om->getRepository($this->checkClass)->findAll();
    }

    /**
     * Create status
     * 
     * @return Status
     */
    public function createStatus()
    {
        $status = new Status();

        // Get results for each check
        foreach ($this->getChecks() as $name => $check) {
            $result = $this->getResult($name);
            $status->addResult($result);
        }

        // Last minor & major
        $resultRepository = $this->om->getRepository($this->resultClass);
        $minor = $resultRepository->findBy(array('status' => Result::MINOR), array('createdAt' => 'DESC'));
        if ($minor->hasNext()) {
            $status->setLastMinor($minor->getNext()->getCreatedAt());
        }
        $major = $resultRepository->findBy(array('status' => Result::MAJOR), array('createdAt' => 'DESC'));
        if ($major->hasNext()) {
            $status->setLastMajor($major->getNext()->getCreatedAt());
        }

        return $status;
    }

    /**
     * Create result
     *
     * @param  string  $checkName
     * @param  string  $message
     * @param  integer $status
     * @return Result
     */
    public function createResult($checkName, $message, $status)
    {
        $resultRepository = $this->om->getRepository($this->resultClass);
        $className = $resultRepository->getClassName();
        
        return new $className($checkName, $message, $status);
    }

    /**
     * Get result
     * 
     * @return Result
     */
    public function getResult($checkName)
    {
        $resultRepository = $this->om->getRepository($this->resultClass);
        $results = $resultRepository->findBy(array('checkName' => $checkName), array('createdAt' => 'DESC'), 1);
        if ($results->hasNext()) {
            return $results->getNext();
        }

        return $this->createResult($checkName, 'n/a', Result::UNKNOWN);
    }
}
