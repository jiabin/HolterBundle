<?php

namespace Jiabin\HolterBundle\Event;

use Jiabin\HolterBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Common\Persistence\ObjectManager;

class EventSubscriber implements EventSubscriberInterface
{
    /**
     * @var ObjectManager
     */
    protected $om;

    /**
     * Class constructor
     *
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::HOLTER_CHECK => array('onHolterCheck')
        );
    }

    /**
     * Check result
     * 
     * @param CheckEvent $event
     */
    public function onHolterCheck(CheckEvent $event)
    {
        $result = $event->getResult();

        $this->om->persist($result);
        $this->om->flush($result);
    }
}
