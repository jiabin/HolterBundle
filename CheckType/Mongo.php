<?php

namespace Jiabin\HolterBundle\CheckType;

use Jiabin\HolterBundle\Model\Result;
use Symfony\Component\Form\FormBuilderInterface;

class Mongo extends CheckType
{
    /**
     * {@inheritdoc}
     */
    static $name = 'mongo';

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        $auth = array('timeout' => $this->options->get('timeout'));
        if ($this->options->get('username')) {
            $auth = array_merge($auth, array("username" => $this->options->get('username'), "password" => $this->options->get('password')));
        }

        try {
            $m = new \MongoClient("mongodb://".$this->options->get('host'), $auth);
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
