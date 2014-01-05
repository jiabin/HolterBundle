<?php

namespace Jiabin\HolterBundle\Check;

use Symfony\Component\Form\FormBuilderInterface;

interface CheckInterface
{
    /**
     * Check
     *
     * @return Result
     */
    public function check();

    /**
     * Build options form
     */
    static function buildOptionsForm(FormBuilderInterface $builder, array $options);
}