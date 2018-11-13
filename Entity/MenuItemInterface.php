<?php

namespace Prodigious\Sonata\MenuBundle\Entity;


interface MenuItemInterface
{
	public function hasChild();

	public function hasParent();
	
	public function getActiveChildren();

    public function __toString();
}
