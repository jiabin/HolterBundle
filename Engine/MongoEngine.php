<?php

namespace Jiabin\HolterBundle\Engine;

use Jiabin\HolterBundle\Model\Status;
use Symfony\Component\Form\FormBuilderInterface;

class MongoEngine extends AbstractEngine
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'mongo';
    }

    /**
     * {@inheritdoc}
     */
    public function check($options)
    {
        $auth = array('connectTimeoutMS' => $options['timeout']);
        if ($options['username']) {
            $auth = array_merge($auth, array("username" => $options['username'], "password" => $options['password']));
        }

        try {
            $m = new \MongoClient("mongodb://".$options['host'], $auth);
            $result = $this->buildResult('Mongo is up and running', Status::GOOD);
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Operation timed out') !== false) {
                $result = $this->buildResult('Mongo connection timed-out', Status::MAJOR);
            } else {
                $result = $this->buildResult('Mongo not responding', Status::MAJOR);
            }
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
                'data' => 'localhost:27017'
            ))
            ->add('username', 'text', array(
                'required' => false,
                'data' => null
            ))
            ->add('password', 'text', array(
                'required' => false,
                'data' => null
            ))
            ->add('timeout', 'integer', array(
                'required' => false,
                'data' => 1000
            ))
        ;
    }
}
