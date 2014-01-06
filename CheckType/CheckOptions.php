<?php

namespace Jiabin\HolterBundle\CheckType;

class CheckOptions
{
    /**
     * @var array
     */
    protected $options;

    /**
     * Class constructor
     */
    public function __construct(array $options = array())
    {
        $this->options = $options;
    }

    /**
     * Get option
     *
     * @param  string $option
     * @param  mixed  $default
     * @return mixed
     */
    public function get($option, $default = null)
    {
        if (!array_key_exists($option, $this->options)) {
            return $default;
        } 

        return $this->options[$option];
    }
}