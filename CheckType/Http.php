<?php

namespace Jiabin\HolterBundle\CheckType;

use Jiabin\HolterBundle\Model\Status;
use Symfony\Component\Form\FormBuilderInterface;

class Http extends CheckType
{
    /**
     * {@inheritdoc}
     */
    static $name = 'http';

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        $start = microtime(true);

        $fp = @fsockopen($this->options->get('host'), $this->options->get('port'), $errno, $errstr, 10);
        if (!$fp) {
            $result = $this->buildResult(sprintf('No http service running at host %s on port %s', $this->options->get('host'), $this->options->get('port')), Status::MAJOR);
        } else {
            $header = "GET ".$this->options->get('path')." HTTP/1.1\r\n";
            $header .= "Host: ".$this->options->get('host')."\r\n";
            $header .= "Connection: close\r\n\r\n";
            fputs($fp, $header);
            $str = '';
            while (!feof($fp)) {
                $str .= fgets($fp, 1024);
            }
            fclose($fp);
            
            $stop = microtime(true);
            $duration = round(($stop - $start) * 1000, 2);
            
            if ($this->options->get('statusCode') && strpos($str, "HTTP/1.1 ".$this->options->get('statusCode')) !== 0) {
                $result = $this->buildResult("Status code ".$this->options->get('statusCode')." does not match in response from ".$this->options->get('host').":".$this->options->get('port').$this->options->get('path'), Status::MAJOR);
            } elseif ($this->options->get('content') && !strpos($str, $this->options->get('content'))) {
                $result = $this->buildResult("Content ".$this->options->get('content')." not found in response from ".$this->options->get('host').":".$this->options->get('port').$this->options->get('path'), Status::MAJOR);
            } elseif ($this->options->get('warning_threshold') && $duration > $this->options->get('warning_threshold')) {
                $result = $this->buildResult('Requests are taking longer than usual', Status::MINOR);
            } else {
                $result = $this->buildResult('Host is up and running', Status::GOOD);
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
