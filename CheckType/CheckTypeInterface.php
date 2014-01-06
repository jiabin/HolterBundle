<?php

namespace Jiabin\HolterBundle\CheckType;

use Symfony\Component\Form\FormBuilderInterface;

interface CheckTypeInterface
{
    /**
     * Execute check
     *
     * @return Result
     */
    public function check();

    /**
     * Build options form
     */
    static function buildOptionsForm(FormBuilderInterface $builder, array $options);
}