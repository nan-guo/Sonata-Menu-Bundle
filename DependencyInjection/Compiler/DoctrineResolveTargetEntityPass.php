<?php

namespace Prodigious\Sonata\MenuBundle\DependencyInjection\Compiler;

use Doctrine\ORM\Version;
use Prodigious\Sonata\MenuBundle\Model;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class DoctrineResolveTargetEntityPass
 *
 * @author Joseph LEMOINE <j.lemoine@ludi.cat>
 */
class DoctrineResolveTargetEntityPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        // resolve_target_entities
        $definition = $container->findDefinition('doctrine.orm.listeners.resolve_target_entity');

        $definition
            ->addMethodCall('addResolveTargetEntity', [
                Model\MenuInterface::class,
                $container->getParameter('sonata_menu.entity.menu'),
                [],
            ])
            ->addMethodCall('addResolveTargetEntity', [
                Model\MenuItemInterface::class,
                $container->getParameter('sonata_menu.entity.menu_item'),
                [],
            ])
        ;

        if (version_compare(Version::VERSION, '2.5.0-DEV') < 0) {
            $definition->addTag('doctrine.event_listener', array('event' => 'loadClassMetadata'));
        } else {
            $definition->addTag('doctrine.event_subscriber', array('connection' => 'default'));
        }
    }
}
