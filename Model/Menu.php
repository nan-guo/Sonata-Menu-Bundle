<?php

namespace Prodigious\Sonata\MenuBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Menu
 *
 * @ORM\Table(name="sonata_menu")
 * @ORM\MappedSuperclass
 * @ORM\InheritanceType("SINGLE_TABLE")
 */
abstract class Menu implements MenuInterface
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255)
     */
    protected $alias;

    /**
     * @ORM\OneToMany(targetEntity="\Prodigious\Sonata\MenuBundle\Model\MenuItemInterface", mappedBy="menu", cascade={"persist"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $menuItems;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->menuItems = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Menu
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set alias
     *
     * @param string $alias
     * @return Menu
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string 
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Add menuItem
     *
     * @param MenuItemInterface $menuItem
     *
     * @return Menu
     */
    public function addMenuItem(MenuItemInterface $menuItem)
    {
        $this->menuItems[] = $menuItem;

        $menuItem->setMenu($this);

        return $this;
    }

    /**
     * Remove menuItem
     *
     * @param MenuItemInterface $menuItem
     */
    public function removeMenuItem(MenuItemInterface $menuItem)
    {
        $this->menuItems->removeElement($menuItem);
    }

    /**
     * Set menuItems
     *
     * @param ArrayCollection $menuItems
     *
     * @return Menu
     */
    public function setMenuItems(ArrayCollection $menuItems)
    {
        $this->menuItems = $menuItems;

        return $this;
    }

    /**
     * Get menuItems
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getMenuItems()
    {   
        return $this->menuItems;
    }

    public function __toString()
    {
        return $this->name ?? "";
    }
}
