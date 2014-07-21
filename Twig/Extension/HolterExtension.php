<?php

namespace Jiabin\HolterBundle\Twig\Extension;

use Jiabin\HolterBundle\Manager\HolterManager;

/**
 * Holter extension
 */
class HolterExtension extends \Twig_Extension
{
    /**
     * @var HolterManager
     */
    protected $manager;

    /**
     * Set manager
     *
     * @param HolterManager $manager
     */
    public function setManager(HolterManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function getGlobals()
    {
        return array(
            'config' => $this->manager->getConfig()->toArray()
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'holter_extension';
    }
}
