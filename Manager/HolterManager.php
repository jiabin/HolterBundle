<?php

namespace Jiabin\HolterBundle\Manager;

use Jiabin\HolterBundle\Model\CheckInterface;
use Jiabin\HolterBundle\Model\Status;
use Jiabin\HolterBundle\Engine\EngineInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;

class HolterManager
{
    /**
     * @var ObjectManager
     */
    protected $om;

    /**
     * @var ArrayCollection
     */
    protected $engines;

    /**
     * @var string
     */
    public $resultClass;

    /**
     * @var string
     */
    public $checkClass;

    /**
     * @var string
     */
    public $configClass;

    /**
     * @var string
     */
    public $userClass;

    /**
     * Class constructor
     *
     * @param ObjectManager $om
     * @param string        $resultClass
     * @param string        $checkClass
     * @param string        $configClass
     * @param string        $userClass
     */
    public function __construct(ObjectManager $om, $resultClass, $checkClass, $configClass, $userClass)
    {
        $this->om          = $om;
        $this->engines     = new ArrayCollection();
        $this->resultClass = $resultClass;
        $this->checkClass  = $checkClass;
        $this->configClass = $configClass;
        $this->userClass   = $userClass;
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
     * Add engine
     *
     * @param EngineInterface $engine
     */
    public function addEngine(EngineInterface $engine)
    {
        $this->engines->set($engine->getId(), $engine);
    }

    /**
     * Get engines
     *
     * @return ArrayCollection
     */
    public function getEngines()
    {
        return $this->engines;
    }

    /**
     * Get engine
     *
     * @param  string $id
     * @return EngineInterface
     */
    public function getEngine($id)
    {
        return $this->engines->get($id);
    }

    /**
     * Get checks
     *
     * @return Collection
     */
    public function getChecks()
    {
        return $this->om->getRepository('JiabinHolterBundle:Check')->findAll();
    }

    /**
     * Get check
     *
     * @param  mixed          $id
     * @return CheckInterface
     */
    public function getCheck($id)
    {
        return $this->om->getRepository('JiabinHolterBundle:Check')->find($id);
    }

    /**
     * Get config
     *
     * @return ConfigInterface
     */
    public function getConfig()
    {
        $config = $this->om->getRepository('JiabinHolterBundle:Config')->findOneBy(array());
        if (is_null($config)) {
            $class  = $this->configClass;
            $config = new $class();
        }

        return $config;
    }

    /**
     * Execute check
     *
     * @param  CheckInterface $check
     * @return Result
     */
    public function check(CheckInterface $check)
    {
        $engine = $this->getEngine($check->getEngine());

        $result = $engine->check($check->getOptions());
        $result->setCheck($check);

        return $result;
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
        $minor = $resultRepository->findBy(array('status' => Status::MINOR), array('createdAt' => 'DESC'));
        if ($minor) {
            $status->setLastMinor(current($minor)->getCreatedAt());
        }
        $major = $resultRepository->findBy(array('status' => Status::MAJOR), array('createdAt' => 'DESC'));
        if ($major) {
            $status->setLastMinor(current($major)->getCreatedAt());
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
            $result = $this->createResult('n/a', Status::UNKNOWN, $check);
        }

        return $result;
    }

}
