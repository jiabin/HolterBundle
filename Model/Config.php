<?php

namespace Jiabin\HolterBundle\Model;

class Config implements ConfigInterface
{
    public function __construct()
    {
        $this->config = array();
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->config;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $val = null)
    {
        $this->config[$key] = $val;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->config)) {
            return $this->config[$key];
        }

        return $default;
    }
}
