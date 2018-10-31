<?php

namespace Prodigious\Sonata\MenuBundle\Factory;

use Prodigious\Sonata\MenuBundle\Model\MenuInterface;

/**
 * Factory to create a menu
 */
class MenuFactory implements MenuFactoryInterface
{
    /**
     * @var MenuInterface
     */
    protected $menuClass;

    public function __construct($menuClass)
    {
        $this->menuClass = $menuClass;
    }

    /**
     * Create a menu
     *
     * @param string $name
     *
     * @return MenuInterface
     */
    public function create($name)
    {
        $type = $this->menuClass;
        /** @var MenuInterface $menu */
        $menu = new $type($name);

        return $menu;
    }
}
