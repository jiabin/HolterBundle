<?php

namespace Jiabin\HolterBundle\CheckType;

use Jiabin\HolterBundle\Factory\CheckFactory;

abstract class CheckType implements CheckTypeInterface
{
    /**
     * @var string
     */
    static $name;

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
    public function __construct($options = array())
    {
        $options = new CheckOptions($options);
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
        return $this->cf->createResult($message, $status);
    }
}
