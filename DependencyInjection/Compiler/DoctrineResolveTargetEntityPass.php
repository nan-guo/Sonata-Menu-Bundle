<?php

namespace Prodigious\Sonata\MenuBundle\DependencyInjection\Compiler;

use Doctrine\ORM\Version;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Prodigious\Sonata\MenuBundle\Model\MenuInterface;
use Prodigious\Sonata\MenuBundle\Model\MenuItemInterface;
use Prodigious\Sonata\MenuBundle\Entity\Menu;
use Prodigious\Sonata\MenuBundle\Entity\MenuItem;
use Prodigious\Sonata\MenuBundle\Admin\MenuAdmin;
use Prodigious\Sonata\MenuBundle\Admin\MenuItemAdmin;

class DoctrineResolveTargetEntityPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition('doctrine.orm.listeners.resolve_target_entity');

        $menuTarget = $container->getParameter('sonata_menu.entity.menu'); 
        $menuItemTarget = $container->getParameter('sonata_menu.entity.menu_item'); 

        $definition
            ->addMethodCall('addResolveTargetEntity',[
                    MenuInterface::class,
                    $menuTarget,
                    [],
                ]
            )
            ->addMethodCall('addResolveTargetEntity',[
                    MenuItemInterface::class,
                    $menuItemTarget,
                    [],
                ]
            );

        if(version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, "4.0", ">=")) {

            if ($menuTarget !== Menu::class) {
                $definition
                    ->addMethodCall('addResolveTargetEntity',[
                            Menu::class,
                            $menuTarget,
                            [],
                        ]
                    );
            }

            if ($menuItemTarget !== MenuItem::class) {
                $definition
                    ->addMethodCall('addResolveTargetEntity',[
                            MenuItem::class,
                            $menuItemTarget,
                            [],
                        ]
                    );
            }

        } else {
            $definitionDriver = $container->findDefinition('doctrine.orm.default_metadata_driver');

            $definitionDriver->addMethodCall('addDriver',[
                        new Reference('doctrine.orm.default_xml_metadata_driver'),
                        'Prodigious\Sonata\MenuBundle\Entity',
                        [],
                    ]
                );
        }

        if (version_compare(Version::VERSION, '2.5.0-DEV') < 0) {
            $definition->addTag('doctrine.event_listener', array('event' => 'loadClassMetadata'));
        } else {
            $definition->addTag('doctrine.event_subscriber', array('connection' => 'default'));
        }
    }
}