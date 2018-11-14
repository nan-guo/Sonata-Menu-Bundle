<?php

namespace Prodigious\Sonata\MenuBundle\Model;


interface MenuItemInterface
{
	public function hasChild();

	public function hasParent();

	public function getActiveChildren();

	public function setEnabled($enabled);

	public function getEnabled();

    public function __toString();
}
