<?php

namespace Jiabin\HolterBundle\Form\Type;

use Jiabin\HolterBundle\Manager\HolterManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EngineType extends AbstractType
{
    /**
     * @var HolterManager
     */
    protected $manager;

    /**
     * Class constructor
     */
    public function __construct(HolterManager $manager)
    {
        $this->manager = $manager;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices'  => $this->getEngineChoices(),
            'multiple' => false,
            'expanded' => false,
            'required' => true
        ));
    }

    protected function getEngineChoices()
    {
        $choices = array();
        foreach ($this->manager->getEngines() as $engine) {
            $choices[$engine->getId()] = $engine->getLabel();
        }

        return $choices;
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'holter_engine';
    }

}