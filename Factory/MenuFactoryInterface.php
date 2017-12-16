<?php

namespace Prodigious\Sonata\MenuBundle\Factory;

/**
 * Interface implemented by the factory to create menu
 */
interface MenuFactoryInterface
{
    /**
     * Create a menu
     *
     * @param string $name
     *
     * @return MenuInterface
     */
    public function create($name);
}
