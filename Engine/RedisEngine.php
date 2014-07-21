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
            $result = $this->buildResult('I ain\'t got time to bleed.', Status::GOOD);
            $redis->info();
        } catch (\Exception $e) {
            switch ($e->getMessage()) {
                case 'Redis server went away.':
                    $message = $e->getMessage();
                    break;
                default:
                    $message = 'Lost connection to server.';
                    break;
            }
            $result = $this->buildResult($message, Status::MAJOR);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function buildOptionsForm(FormBuilderInterface $builder, array $options)
    {
        $engineOptions = $options['data']->getOptions();
        $builder
            ->add('host', 'text', array(
                'required' => true,
                'data' => array_key_exists('host', $engineOptions) ? $engineOptions['host'] : 'localhost'
            ))
            ->add('port', 'integer', array(
                'required' => false,
                'data' => array_key_exists('port', $engineOptions) ? $engineOptions['port'] : 6379
            ))
            ->add('database', 'integer', array(
                'required' => false,
                'data' => array_key_exists('database', $engineOptions) ? $engineOptions['database'] : 0
            ))
        ;
    }
}
