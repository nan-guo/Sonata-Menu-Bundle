<?php

namespace Prodigious\Sonata\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('prodigious_sonata_menu');

        $rootNode
            ->children()
                ->booleanNode('knp_menu_integration')->defaultFalse()->end()
                ->arrayNode('entities')
                    ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('menu')->defaultValue('Prodigious\Sonata\MenuBundle\Entity\Menu')->end()
                            ->scalarNode('menu_item')->defaultValue('Prodigious\Sonata\MenuBundle\Entity\MenuItem')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
