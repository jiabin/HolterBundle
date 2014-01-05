<?php

namespace Jiabin\HolterBundle\Form\Type;

use Jiabin\HolterBundle\Factory\CheckFactory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CheckType extends AbstractType
{
    /**
     * @var CheckFactory
     */
    protected $cf;

    /**
     * Class constructor
     */
    public function __construct(CheckFactory $cf)
    {
        $this->cf = $cf;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        switch ($options['flow_step']) {
            case 1:
                $types = array();
                foreach ($this->cf->getTypes() as $type => $class) {
                    $types[$type] = ucfirst($type);
                }

                $builder->add('name', 'text');
                $builder->add('type', 'choice', array(
                    'choices' => $types
                ));
                break;
            case 2:
                $optionsBuilder = $builder->create('options', null, array(
                    'compound' => true
                ));

                $type = $options['data']->getType();
                $className = $this->cf->getTypeClass($type);
                $className::buildOptionsForm($optionsBuilder, $options);

                $builder->add($optionsBuilder);
                break;
        }
    }

    public function getName()
    {
        return 'check';
    }

}