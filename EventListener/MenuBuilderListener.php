<?php

namespace Prodigious\Sonata\MenuBundle\EventListener;

use Sonata\AdminBundle\Event\ConfigureMenuEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuBuilderListener
{
    private $requestStack;

    public function __construct($requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function addMenuItems(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        $request = $this->requestStack->getCurrentRequest();

        switch ($request->get('_route')) {
            case 'admin_sonata_menu_menuitem_list':
            case 'admin_sonata_menu_menuitem_edit':
            case 'admin_sonata_menu_menuitem_delete':
            case 'admin_sonata_menu_menuitem_create':
                $menu['sonata.admin.group.menu_builder']->setCurrent(true);

                break;
            default:
                break;
        }
    }
}
