<?php

namespace Jiabin\HolterBundle\Form\Type;

use Jiabin\HolterBundle\Manager\HolterManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CheckType extends AbstractType
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
        switch ($options['flow_step']) {
            case 1:
                $builder->add('name', 'text', array(
                    'help_block' => 'Human visible name'
                ));
                $builder->add('displayGroup', 'text', array(
                    'help_block' => 'Group checks together',
                    'required' => false
                ));
                $builder->add('interval', 'number', array(
                    'data' => 30,
                    'help_block' => 'Expressed in seconds'
                ));
                $builder->add('engine', 'holter_engine', array(
                    'help_block' => 'Check engine'
                ));
                break;
            case 2:
                $optionsBuilder = $builder->create('options', null, array(
                    'compound' => true
                ));

                $id = $options['data']->getEngine();
                $engine = $this->manager->getEngine($id);
                $engine::buildOptionsForm($optionsBuilder, $options);

                $builder->add($optionsBuilder);
                break;
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'label' => 'Check form'
        ));
    }

    public function getName()
    {
        return 'check';
    }

}
