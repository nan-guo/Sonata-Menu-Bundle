<?php

namespace Prodigious\Sonata\MenuBundle\Entity;


interface MenuInterface
{
    public function __toString();

    public function getMenuItems();
}
