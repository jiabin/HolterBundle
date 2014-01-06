<?php

namespace Jiabin\HolterBundle\Factory;

use Jiabin\HolterBundle\Model\Result;
use Jiabin\HolterBundle\Model\Status;
use Jiabin\HolterBundle\Model\CheckInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CheckFactory
{
    /**
     * @var array
     */
    protected $checkTypes = array();

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
     * Add checkType
     * 
     * @param string $name
     * @param string $class
     */
    public function addCheckType($name, $class)
    {
        $this->checkTypes[$name] = $class;
    }

    /**
     * Get types
     * 
     * @return array
     */
    public function getCheckTypes()
    {
        return $this->checkTypes;
    }

    /**
     * Get typeClass
     *
     * @param  string $name
     * @return array
     */
    public function getCheckTypeClass($name)
    {
        return $this->checkTypes[$name];
    }

    /**
     * Execute check
     * 
     * @param  CheckInterface $check
     * @return Result
     */
    public function check(CheckInterface $check)
    {       
        $className = $this->getCheckTypeClass($check->getType());
        $class = new $className($check->getOptions());
        $class->setCheckFactory($this);

        $result = $class->check();
        $result->setCheck($check);

        return $result;
    }

    /**
     * Get checks
     * 
     * @return Collection
     */
    public function getChecks()
    {
        return $this->om->getRepository($this->checkClass)->findAll();
    }

    /**
     * Get check
     *
     * @param  mixed          $id
     * @return CheckInterface
     */
    public function getCheck($id)
    {
        return $this->om->getRepository($this->checkClass)->find($id);
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
        foreach ($this->getChecks() as $check) {
            $result = $this->getResult($check);
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
     * @param  string         $message
     * @param  integer        $status
     * @param  CheckInterface $check
     * @return Result
     */
    public function createResult($message, $status, CheckInterface $check = null)
    {
        $resultRepository = $this->om->getRepository($this->resultClass);
        $className = $resultRepository->getClassName();
        
        $result = new $className($message, $status);
        if ($check) {
            $result->setCheck($check);
        }

        return $result;
    }

    /**
     * Get result
     *
     * @param  CheckInterface $check
     * @return Result
     */
    public function getResult(CheckInterface $check)
    {
        $resultRepository = $this->om->getRepository($this->resultClass);
        $builder = $resultRepository->createQueryBuilder();
        $result = $builder->field('check')->references($check)->sort('createdAt', 'desc')->getQuery()->getSingleResult();
        
        if (!$result) {
            $result = $this->createResult('n/a', Result::UNKNOWN, $check);
        }

        return $result;
    }
}
