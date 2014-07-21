<?php

namespace Jiabin\HolterBundle\Form\Type;

use Jiabin\HolterBundle\Manager\HolterManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConfigType extends AbstractType
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

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'empty_data' => 'Holter',
                'help_block' => 'Application title'
            ))
            ->add('description', 'textarea', array(
                'required' => false,
                'help_block' => 'Application description (You can use HTML)',
                'attr' => array(
                    'class' => 'codemirror'
                )
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'label' => 'Configuration form'
        ));
    }

    public function getName()
    {
        return 'holter_config';
    }

}
