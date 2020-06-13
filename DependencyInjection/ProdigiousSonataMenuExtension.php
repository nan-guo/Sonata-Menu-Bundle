<?php

namespace Prodigious\Sonata\MenuBundle\DependencyInjection;

use Prodigious\Sonata\MenuBundle\Admin\MenuAdmin;
use Prodigious\Sonata\MenuBundle\Admin\MenuItemAdmin;
use Prodigious\Sonata\MenuBundle\Entity\Menu;
use Prodigious\Sonata\MenuBundle\Entity\MenuItem;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @see http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class ProdigiousSonataMenuExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $this->registerEntities($container, $config);
        $this->registerAdmins($container, $config);
    }

    /**
     * @param Configuration $configuration
     *
     * @return $this
     */
    protected function registerEntities(ContainerBuilder $container, array $config)
    {
        if (isset($config['entities'])) {
            if (isset($config['entities']['menu'])) {
                $container->setParameter('sonata_menu.entity.menu', $config['entities']['menu']);
            } else {
                $container->setParameter('sonata_menu.entity.menu', Menu::class);
            }

            if (isset($config['entities']['menu_item'])) {
                $container->setParameter('sonata_menu.entity.menu_item', $config['entities']['menu_item']);
            } else {
                $container->setParameter('sonata_menu.entity.menu_item', MenuItem::class);
            }
        }

        return $this;
    }

    /**
     * @param Configuration $configuration
     *
     * @return $this
     */
    protected function registerAdmins(ContainerBuilder $container, array $config)
    {
        if (isset($config['admins'])) {
            if (isset($config['admins']['menu'])) {
                $container->setParameter('sonata_menu.admins.menu', $config['admins']['menu']);
            } else {
                $container->setParameter('sonata_menu.admins.menu', MenuAdmin::class);
            }

            if (isset($config['admins']['menu_item'])) {
                $container->setParameter('sonata_menu.admins.menu_item', $config['admins']['menu_item']);
            } else {
                $container->setParameter('sonata_menu.admins.menu_item', MenuItemAdmin::class);
            }
        }

        return $this;
    }
}
