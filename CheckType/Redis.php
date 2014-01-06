<?php

namespace Jiabin\HolterBundle\CheckType;

use Jiabin\HolterBundle\Model\Result;
use Symfony\Component\Process\Process;
use Symfony\Component\Form\FormBuilderInterface;

class Redis extends CheckType
{
    /**
     * {@inheritdoc}
     */
    static $name = 'redis';

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        $redis = new \Redis();
        try {
            $redis->connect($this->options->get('host'), $this->options->get('port'), 1);
            $result = $this->buildResult('Redis is up and running', Result::GOOD);
        } catch (\Exception $e) {
            $result = $this->buildResult('Can not connect to Redis server', Result::MAJOR);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public static function buildOptionsForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('host', 'text', array(
                'required' => true
            ))
            ->add('port', 'integer', array(
                'required' => false,
                'data' => 6379
            ))
            ->add('database', 'integer', array(
                'required' => false,
                'data' => 0
            ))
        ;
    }
}
