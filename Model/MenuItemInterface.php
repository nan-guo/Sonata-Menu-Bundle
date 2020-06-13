<?php

namespace Prodigious\Sonata\MenuBundle\Model;

interface MenuItemInterface
{
    public function __toString();

    public function getName();

    public function getUrl();

    public function getClassAttribute();

    public function getPosition();

    public function getTarget();

    public function getPage();

    public function getEnabled();

    public function getParent();

    public function getChildren();

    public function hasChild();

    public function hasParent();

    public function getActiveChildren();

    public function getMenu();
}
