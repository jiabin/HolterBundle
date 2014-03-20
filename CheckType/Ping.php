<?php

namespace Jiabin\HolterBundle\CheckType;

use Jiabin\HolterBundle\Model\Status;
use Symfony\Component\Process\Process;
use Symfony\Component\Form\FormBuilderInterface;

class Ping extends CheckType
{
    /**
     * {@inheritdoc}
     */
    static $name = 'ping';

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        $command = sprintf('ping -c 2 %s', $this->options->get('host'));
        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful()) {
            return $this->buildResult('Connection failed', Status::MAJOR);
        }

        $output = $process->getOutput();
        preg_match_all('/time=(.*?) .*/i', $output, $matches);

        if (empty($matches[1])) {
            return $this->buildResult('Connection failed', Status::MAJOR);
        }

        $avg = 0;
        foreach ($matches[1] as $match) {
            $avg = ($avg + $match) / 2;
        }

        if ($this->options->get('timeout') && $avg > $this->options->get('timeout')) {
            return $this->buildResult('Connection to host is taking more than usual', Status::MINOR);
        } else {
            return $this->buildResult('Host is reachable', Status::GOOD);
        }
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
            ->add('timeout', 'integer', array(
                'required' => false,
                'data' => 1000
            ))
        ;
    }
}
