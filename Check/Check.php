<?php

namespace Jiabin\HolterBundle\Check;

use Jiabin\HolterBundle\Factory\CheckFactory;

abstract class Check implements CheckInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    public static $type;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var CheckFactory
     */
    protected $cf;

    /**
     * Class constructor
     * 
     * @param string $name
     * @param array  $options
     */
    public function __construct($name, $options = array())
    {
        $this->name = $name;
        $this->label = $name;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function check();

    /**
     * {@inheritdoc}
     */
    public function setCheckFactory(CheckFactory $cf)
    {
        $this->cf = $cf;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function buildResult($message, $status)
    {
        return $this->cf->createResult($this->getName(), $message, $status);
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
    public function getType()
    {
        if (is_null(static::$type)) {
            throw new \Exception('Check type must be set');
        }

        return static::$type;
    }
}
