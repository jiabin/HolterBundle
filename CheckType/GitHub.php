<?php

namespace Jiabin\HolterBundle\CheckType;

use Jiabin\HolterBundle\Model\Status;
use Symfony\Component\Form\FormBuilderInterface;

class GitHub extends CheckType
{
    /**
     * {@inheritdoc}
     */
    static $name = 'github';

    /**
     * {@inheritdoc}
     */
    public function check()
    {
        $json = file_get_contents('https://status.github.com/api/last-message.json');
        $data = json_decode($json, true);

        if (!array_key_exists('body', $data) || !$data['body']) {
            $data['body'] = 'Status unknown';
        }

        switch ($data['status']) {
            case 'good':
                $code = Status::GOOD;
                break;
            case 'minor':
                $code = Status::MINOR;
                break;
            case 'major':
                $code = Status::MAJOR;
                break; 
            default:
                $code = Status::UNKNOWN;
                break;
        }

        return $this->buildResult($data['body'], $code);
    }

    /**
     * {@inheritdoc}
     */
    public static function buildOptionsForm(FormBuilderInterface $builder, array $options)
    {
    }
}
