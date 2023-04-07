<?php

namespace RbacBundle;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use RbacBundle\DependencyInjection\Compiler\DoctrineResolveTargetEntityPass;

final class RbacBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(
            new DoctrineResolveTargetEntityPass(),
            PassConfig::TYPE_BEFORE_OPTIMIZATION,
            1000
        );
    }
}
