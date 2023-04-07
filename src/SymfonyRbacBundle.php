<?php

namespace SymfonyRbacBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\Console\Application;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use SymfonyRbacBundle\DependencyInjection\Compiler\DoctrineResolveTargetEntityPass;

final class SymfonyRbacBundle extends AbstractBundle
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
