<?php

namespace Jiabin\HolterBundle\Check;

use Jiabin\HolterBundle\Model\Result;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HttpCheck extends Check
{
    /**
     * {@inheritdoc}
     */
    public function check()
    {
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
            
            if ($this->options['statusCode'] && strpos($str, "HTTP/1.1 ".$this->options['statusCode']) !== 0) {
                $result = $this->buildResult("Status code ".$this->options['statusCode']." does not match in response from ".$this->options['host'].":".$this->options['port'].$this->options['path'], Result::MAJOR);
            } elseif ($this->options['content'] && !strpos($str, $this->options['content'])) {
                $result = $this->buildResult("Content ".$this->options['content']." not found in response from ".$this->options['host'].":".$this->options['port'].$this->options['path'], Result::MAJOR);
            } else {
                $result = $this->buildResult('Host is up and running', Result::GOOD);
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
            'port' => 80,
            'path' => '/',
            'statusCode' => 200,
            'content' => null
        ));
    }
}
