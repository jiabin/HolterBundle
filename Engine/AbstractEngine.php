<?php

namespace Jiabin\HolterBundle\Engine;

use Jiabin\HolterBundle\Exception\Exception;
use Jiabin\HolterBundle\Manager\HolterManager;
use Symfony\Component\Form\FormBuilderInterface;

abstract class AbstractEngine implements EngineInterface
{
    /**
     * @var HolterManager
     */
    protected $manager;

    /**
     * {@inheritdoc}
     */
    abstract public function getName();

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return ucfirst($this->getName());
    }

    /**
     * {@inheritdoc}
     */
    abstract public function check($options);

    /**
     * {@inheritdoc}
     */
    abstract public function buildOptionsForm(FormBuilderInterface $builder, array $options);

    /**
     * Set id
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set manager
     */
    public function setManager(HolterManager $manager)
    {
        $this->manager = $manager;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function buildResult($message, $status)
    {
        return $this->manager->createResult($message, $status);
    }
}