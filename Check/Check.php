<?php

namespace Jiabin\HolterBundle\Check;

use Jiabin\HolterBundle\Factory\CheckFactory;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

abstract class Check implements CheckInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $label;

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
        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);

        $this->name = $name;
        $this->label = $name;
        $this->options = $resolver->resolve($options);
    }

    /**
     * {@inheritdoc}
     */
    abstract public function check();

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    }

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
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * {@inheritdoc}
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIcon()
    {
        return $this->icon;
    }
}
