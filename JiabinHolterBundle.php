<?php

namespace Jiabin\HolterBundle;

use Jiabin\HolterBundle\DependencyInjection\Compiler\EngineCompilerPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class JiabinHolterBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new EngineCompilerPass());
    }
}


