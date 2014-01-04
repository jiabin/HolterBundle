<?php

namespace Jiabin\HolterBundle\Check;

use Jiabin\HolterBundle\Model\Result;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MongoCheck extends Check
{
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
            $result = $this->buildResult('Mongodb is up & running', Result::GOOD);
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Operation timed out') !== false) {
                $result = $this->buildResult('Mongodb connection timed-out', Result::MAJOR);
            } else {
                $result = $this->buildResult('Mongodb not responding', Result::MAJOR);
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('host'));

        $resolver->setDefaults(array(
            'username' => null,
            'password' => null,
            'timeout'  => 1000
        ));
    }
}
