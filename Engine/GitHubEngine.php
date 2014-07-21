<?php

namespace Jiabin\HolterBundle\Engine;

use Jiabin\HolterBundle\Model\Status;
use Symfony\Component\Form\FormBuilderInterface;

class GitHubEngine extends AbstractEngine
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'github';
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'GitHub';
    }

    /**
     * {@inheritdoc}
     */
    public function check($options)
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
    public function buildOptionsForm(FormBuilderInterface $builder, array $options)
    {
    }
}
