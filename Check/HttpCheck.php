<?php

namespace Jiabin\HolterBundle\Check;

use Jiabin\HolterBundle\Model\Result;
use Symfony\Component\Form\FormBuilderInterface;

class HttpCheck extends Check
{
    /**
     * {@inheritdoc}
     */
    public static $type = 'http';

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        $start = microtime(true);

        $fp = @fsockopen($this->options['host'], $this->options['port'], $errno, $errstr, 10);
        if (!$fp) {
            $result = $this->buildResult(sprintf('No http service running at host %s on port %s', $this->options['host'], $this->options['port']), Result::MAJOR);
        } else {
            $header = "GET ".$this->options['path']." HTTP/1.1\r\n";
            $header .= "Host: ".$this->options['host']."\r\n";
            $header .= "Connection: close\r\n\r\n";
            fputs($fp, $header);
            $str = '';
            while (!feof($fp)) {
                $str .= fgets($fp, 1024);
            }
            fclose($fp);
            
            $stop = microtime(true);
            $duration = round(($stop - $start) * 1000, 2);
            
            if ($this->options['statusCode'] && strpos($str, "HTTP/1.1 ".$this->options['statusCode']) !== 0) {
                $result = $this->buildResult("Status code ".$this->options['statusCode']." does not match in response from ".$this->options['host'].":".$this->options['port'].$this->options['path'], Result::MAJOR);
            } elseif ($this->options['content'] && !strpos($str, $this->options['content'])) {
                $result = $this->buildResult("Content ".$this->options['content']." not found in response from ".$this->options['host'].":".$this->options['port'].$this->options['path'], Result::MAJOR);
            } elseif ($this->options['warning_threshold'] && $duration > $this->options['warning_threshold']) {
                $result = $this->buildResult('Requests are taking longer than usual', Result::MINOR);
            } else {
                $result = $this->buildResult('Host is up and running', Result::GOOD);
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
            ->add('port', 'integer', array(
                'required' => false,
                'data' => 80
            ))
            ->add('warning_threshold', 'integer', array(
                'required' => false,
                'data' => 1000
            ))
            ->add('path', 'text', array(
                'required' => false,
                'data' => '/'
            ))
            ->add('statusCode', 'integer', array(
                'required' => false,
                'data' => 200
            ))
            ->add('content', 'text', array(
                'required' => false,
                'data' => null
            ))
        ;
    }
}
