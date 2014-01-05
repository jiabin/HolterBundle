<?php

namespace Jiabin\HolterBundle\Check;

use Jiabin\HolterBundle\Model\Result;
use Symfony\Component\Form\FormBuilderInterface;

class MongoCheck extends Check
{
    /**
     * {@inheritdoc}
     */
    static $type = 'mongo';

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        $auth = array('timeout' => $this->options['timeout']);
        if ($this->options['username']) {
            $auth = array_merge($auth, array("username" => $this->options['username'], "password" => $this->options['password']));
        }

        try {
            $m = new \MongoClient("mongodb://".$this->options['host'], $auth);
            $result = $this->buildResult('Mongo is up and running', Result::GOOD);
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Operation timed out') !== false) {
                $result = $this->buildResult('Mongo connection timed-out', Result::MAJOR);
            } else {
                $result = $this->buildResult('Mongo not responding', Result::MAJOR);
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
                'required' => true
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
