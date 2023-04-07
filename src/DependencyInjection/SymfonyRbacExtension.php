<?php

namespace SymfonyRbacBundle\DependencyInjection;

use SymfonyRbacBundle\Core\RbacInterface;
use Symfony\Component\Config\FileLocator;
use SymfonyRbacBundle\EventSubscriber\AccessControlDriver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class SymfonyRbacExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');

        $definition = $container->getDefinition(AccessControlDriver::class);
        $definition->addMethodCall('load', [$config]);

        $container->setParameter('symfony_rbac.resolve_target_entities.permission', $config['resolve_target_entities']['permission']);
        $container->setParameter('symfony_rbac.resolve_target_entities.role', $config['resolve_target_entities']['role']);
    }

    public function getAlias(): string
    {
        return 'symfony_rbac';
    }
}
