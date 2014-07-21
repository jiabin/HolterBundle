<?php

namespace Jiabin\HolterBundle\Engine;

use Symfony\Component\Form\FormBuilderInterface;

interface EngineInterface
{
    public function getName();

    public function check($options);

    public function buildOptionsForm(FormBuilderInterface $builder, array $options);
}