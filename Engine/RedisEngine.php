<?php

namespace Jiabin\HolterBundle\Engine;

use Jiabin\HolterBundle\Model\Status;
use Symfony\Component\Form\FormBuilderInterface;

class RedisEngine extends AbstractEngine
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'redis';
    }

    /**
     * {@inheritdoc}
     */
    public function check($options)
    {
        $redis = new \Redis();
        try {
            $redis->connect($options['host'], $options['port'], 1);
            $result = $this->buildResult('Redis is up and running.', Status::GOOD);
            $redis->info();
        } catch (\Exception $e) {
            $result = $this->buildResult('Can not connect to Redis server.', Status::MAJOR);
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
                'required' => true,
                'data' => 'localhost'
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
