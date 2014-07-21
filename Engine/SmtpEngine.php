<?php

namespace Jiabin\HolterBundle\Engine;

use Jiabin\HolterBundle\Model\Status;
use Symfony\Component\Form\FormBuilderInterface;

class SmtpEngine extends AbstractEngine
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'smtp';
    }

    /**
     * {@inheritdoc}
     */
    public function check($options)
    {
        try {
            $mailer = $this->createMailer();
            $logger = new \Swift_Plugins_Loggers_ArrayLogger();
            $mailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($logger));
        } catch (\Exception $e) {
            return $this->buildResult('No connection', Status::MAJOR);
        }

        try {
            $message = \Swift_Message::newInstance('Holter SMTP check')
                ->setFrom(array('john@doe.com' => 'John Doe'))
                ->setTo('receiver@domain.org')
                ->setBody($this->getEmailTemplate())
            ;
            $receipt = $mailer->send($message);
        } catch (\Exception $e) {
            return $this->buildResult('No connection', Status::MAJOR);
        }

        return $this->buildResult('Fully working', Status::GOOD);
    }

    protected function createMailer()
    {
        // Create the Transport
        $transport = \Swift_SmtpTransport::newInstance($options['host'], $options['port']);
        if ( ($username = $options['username']) && ($password = $options['password']) ) {
            $transport
                ->setUsername($username)
                ->setPassword($password)
            ;
        }

        return \Swift_Mailer::newInstance($transport);
    }

    protected function getEmailTemplate()
    {
        return sprintf('
Holter SMTP check
=================
Host: %s
IP  : %s
OS  : %s %s
Date: %s

', gethostname(), gethostbyname(gethostname()), php_uname('s'), php_uname('r'), date('Y-m-d H:i:s T'));
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
                'data' => 25
            ))
            ->add('username', 'text', array(
                'required' => false
            ))
            ->add('password', 'text', array(
                'required' => false
            ))
        ;
    }
}
