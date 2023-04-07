<?php

namespace Triforce\RBACBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Triforce\RBACBundle\Entity\PermissionInterface;
use Triforce\RBACBundle\Entity\RoleInterface;

class DoctrineResolveTargetEntityPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('doctrine.orm.listeners.resolve_target_entity');

        $definition->addMethodCall('addResolveTargetEntity', [PermissionInterface::class, $container->getParameter('triforce_rbac.resolve_target_entities.permission'), []]);
        $definition->addMethodCall('addResolveTargetEntity', [RoleInterface::class, $container->getParameter('triforce_rbac.resolve_target_entities.role'), []]);

        $definition->addTag('doctrine.event_subscriber');
    }
}