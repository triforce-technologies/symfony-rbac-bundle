<?php

namespace Triforce\RBACBundle;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Triforce\RBACBundle\DependencyInjection\Compiler\DoctrineResolveTargetEntityPass;

final class TriforceRBACBundle extends Bundle
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
