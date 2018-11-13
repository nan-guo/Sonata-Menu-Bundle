<?php

namespace Prodigious\Sonata\MenuBundle\Factory;

use Prodigious\Sonata\MenuBundle\Entity\Menu;

/**
 * Factory to create a menu
 */
class MenuFactory implements MenuFactoryInterface
{

    /**
     * Create a menu
     *
     * @param string $name
     *
     * @return \Prodigious\Sonata\MenuBundle\Entity\Menu
     */
    public function create($name)
    {
        $menu = new Menu($name);

        return $menu;
    }

}
