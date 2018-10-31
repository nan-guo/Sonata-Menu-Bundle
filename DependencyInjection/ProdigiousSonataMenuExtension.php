<?php

namespace Prodigious\Sonata\MenuBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
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
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $this->knpMenuIntegration($config, $loader);
    }

    /**
     * @param array                 $config
     * @param Loader\YamlFileLoader $loader
     * @throws \Exception
     */
    protected function knpMenuIntegration(array $config, Loader\YamlFileLoader $loader)
    {
        if ($config['knp_menu_integration']) {
            $loader->load('knp.yaml');
        }
    }
}
