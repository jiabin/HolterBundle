<?php

namespace Jiabin\HolterBundle;

use Jiabin\HolterBundle\DependencyInjection\Compiler\CheckCompilerPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class JiabinHolterBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CheckCompilerPass());
    }
}
