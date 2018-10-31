<?php

namespace Prodigious\Sonata\MenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Prodigious\Sonata\MenuBundle\Entity\MenuItem;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Menu
 *
 * @ORM\Table(name="sonata_menu", indexes={
 *     @ORM\Index(name="alias_idx", columns={"alias"})
 * })
 * @ORM\Entity(repositoryClass="Prodigious\Sonata\MenuBundle\Repository\MenuRepository")
 * @UniqueEntity("alias")
 */
class Menu implements MenuInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * Technical alias to fetch menu
     *
     * @var string
     *
     * @ORM\Column(name="alias", type="string", length=255, unique=true)
     */
    protected $alias;

    /**
     * @ORM\OneToMany(targetEntity="Prodigious\Sonata\MenuBundle\Entity\MenuItem", mappedBy="menu", cascade={"persist"})
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
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
     * @param string $alias
     *
     * @return $this
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * Add menuItem
     *
     * @param \Prodigious\Sonata\MenuBundle\Entity\MenuItem $menuItem
     *
     * @return Menu
     */
    public function addMenuItem(\Prodigious\Sonata\MenuBundle\Entity\MenuItem $menuItem)
    {
        $this->menuItems[] = $menuItem;

        $menuItem->setMenu($this);

        return $this;
    }

    /**
     * Remove menuItem
     *
     * @param \Prodigious\Sonata\MenuBundle\Entity\MenuItem $menuItem
     */
    public function removeMenuItem(\Prodigious\Sonata\MenuBundle\Entity\MenuItem $menuItem)
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
        return isset($this->name) ? $this->name : "";
    }
}
