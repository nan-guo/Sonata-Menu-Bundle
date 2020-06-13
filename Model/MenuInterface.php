<?php

namespace Prodigious\Sonata\MenuBundle\Model;

interface MenuInterface
{
    public function __toString();

    public function getMenuItems();
}
