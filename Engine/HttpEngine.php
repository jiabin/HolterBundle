<?php

namespace Jiabin\HolterBundle\Engine;

use Jiabin\HolterBundle\Model\Status;
use Symfony\Component\Form\FormBuilderInterface;

class HttpEngine extends AbstractEngine
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'http';
    }

    /**
     * {@inheritdoc}
     */
    public function check($options)
    {
        $start = microtime(true);

        $fp = @fsockopen($options['host'], $options['port'], $errno, $errstr, 10);
        if (!$fp) {
            $result = $this->buildResult(sprintf('No http service running at host %s on port %s', $options['host'], $options['port']), Status::MAJOR);
        } else {
            $header = "GET ".$options['path']." HTTP/1.1\r\n";
            $header .= "Host: ".$options['host']."\r\n";
            $header .= "Connection: close\r\n\r\n";
            fputs($fp, $header);
            $str = '';
            while (!feof($fp)) {
                $str .= fgets($fp, 1024);
            }
            fclose($fp);

            $stop = microtime(true);
            $duration = round(($stop - $start) * 1000, 2);

            if ($options['statusCode'] && strpos($str, "HTTP/1.1 ".$options['statusCode']) !== 0) {
                $result = $this->buildResult("Status code ".$options['statusCode']." does not match in response from ".$options['host'].":".$options['port'].$options['path'], Status::MAJOR);
            } elseif ($options['content'] && !strpos($str, $options['content'])) {
                $result = $this->buildResult("Content ".$options['content']." not found in response from ".$options['host'].":".$options['port'].$options['path'], Status::MAJOR);
            } elseif ($options['warning_threshold'] && $duration > $options['warning_threshold']) {
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
