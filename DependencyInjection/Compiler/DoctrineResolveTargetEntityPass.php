<?php

namespace Prodigious\Sonata\MenuBundle\DependencyInjection\Compiler;

use Doctrine\ORM\Version;
use Prodigious\Sonata\MenuBundle\Admin\MenuAdmin;
use Prodigious\Sonata\MenuBundle\Admin\MenuItemAdmin;
use Prodigious\Sonata\MenuBundle\Entity\Menu;
use Prodigious\Sonata\MenuBundle\Entity\MenuItem;
use Prodigious\Sonata\MenuBundle\Model\MenuInterface;
use Prodigious\Sonata\MenuBundle\Model\MenuItemInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DoctrineResolveTargetEntityPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('doctrine.orm.listeners.resolve_target_entity');
        $definitionDriver = $container->findDefinition('doctrine.orm.default_metadata_driver');

        $menuTarget = $container->getParameter('sonata_menu.entity.menu');
        $menuItemTarget = $container->getParameter('sonata_menu.entity.menu_item');

        $definition
            ->addMethodCall('addResolveTargetEntity', [
                    MenuInterface::class,
                    $menuTarget,
                    [],
                ]
            )
            ->addMethodCall('addResolveTargetEntity', [
                    MenuItemInterface::class,
                    $menuItemTarget,
                    [],
                ]
            );

        if ($menuTarget !== Menu::class) {
            if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '4.0', '>=')) {
                $this->removeEntityMappingV4($definition, Menu::class, $menuTarget);
            } else {
                $this->removeEntityMappingV3($definitionDriver);
            }
        }

        if ($menuItemTarget !== MenuItem::class) {
            if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '4.0', '>=')) {
                $this->removeEntityMappingV4($definition, MenuItem::class, $menuItemTarget);
            } else {
                $this->removeEntityMappingV3($definitionDriver);
            }
        }

        if (version_compare(Version::VERSION, '2.5.0-DEV') < 0) {
            $definition->addTag('doctrine.event_listener', ['event' => 'loadClassMetadata']);
        } else {
            $definition->addTag('doctrine.event_subscriber', ['connection' => 'default']);
        }
    }

    // Ignore orm objects in Entity folder
    protected function removeEntityMappingV3($definition, $target = 'Prodigious\\Sonata\\MenuBundle\\Entity')
    {
        $definition->addMethodCall('addDriver', [
                new Reference('doctrine.orm.default_xml_metadata_driver'),
                $target,
                [],
            ]
        );
    }

    // Ignore orm objects in Entity folder
    protected function removeEntityMappingV4($definition, $origin, $target)
    {
        $definition->addMethodCall('addResolveTargetEntity', [
                $origin,
                $target,
                [],
            ]
        );
    }
}
