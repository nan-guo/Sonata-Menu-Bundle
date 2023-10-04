<?php

namespace Prodigious\Sonata\MenuBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * MenuItem
 *
 * @ORM\Table(name="sonata_menu_item")
 * @ORM\MappedSuperclass
 * @ORM\InheritanceType("SINGLE_TABLE")
 */
abstract class MenuItem implements MenuItemInterface
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
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    protected $url;

    /**
     * @var string
     *
     * @ORM\Column(name="class_attribute", type="string", length=255, nullable=true)
     */
    protected $classAttribute;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="smallint", options={"unsigned"=true}, nullable=true)
     */
    protected $position;

    /**
     * @var bool
     *
     * @ORM\Column(name="target", type="boolean", nullable=true, options={"default":false})
     */
    protected $target;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", nullable=true, options={"default":true})
     */
    protected $enabled;

    /**
     * @var \stdClass
     *
     */
    protected $page;

    /**
     * @var MenuItemInterface
     *
     * @ORM\ManyToOne(targetEntity="\Prodigious\Sonata\MenuBundle\Model\MenuItemInterface", inversedBy="children", cascade={"persist"})
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    protected $parent;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="\Prodigious\Sonata\MenuBundle\Model\MenuItemInterface", mappedBy="parent", cascade={"all"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $children;

    /**
     * @ORM\ManyToOne(targetEntity="\Prodigious\Sonata\MenuBundle\Model\MenuInterface", inversedBy="menuItems")
     * @ORM\JoinColumn(name="menu", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    protected $menu;

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->position = 999;
        $this->enabled = true;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return MenuItem
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
     * Set url
     *
     * @param string $url
     * @return MenuItem
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set classAttribute
     *
     * @param string $classAttribute
     * @return MenuItem
     */
    public function setClassAttribute($classAttribute)
    {
        $this->classAttribute = $classAttribute;

        return $this;
    }

    /**
     * Get classAttribute
     *
     * @return string 
     */
    public function getClassAttribute()
    {
        return $this->classAttribute;
    }

    /**
     * Set position
     *
     * @param int $position
     * @return MenuItem
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return int 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set target
     *
     * @param boolean $enabled
     * @return MenuItem
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * Get target
     *
     * @return boolean 
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return MenuItem
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        if(!$enabled && $this->hasChild()) {
            foreach ($this->children as $child) {
                if($child->enabled) {
                    $child->setEnabled(false);
                    $child->setParent(null);
                }
            }
            $this->children = new ArrayCollection();
        }

        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean 
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Get page
     *
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set page
     *
     * @param $page
     *
     * @return MenuItem
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get parent
     *
     * @return MenuItemInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set parent
     *
     * @param MenuItemInterface $parent
     *
     * @return MenuItem
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
        
        if(!is_null($parent))
            $parent->addChild($this);

        return $this;
    }

    /**
     * Add child
     *
     * @param MenuItemInterface $child
     *
     * @return $this
     */
    public function addChild(MenuItemInterface $child)
    {
        $this->children[] = $child;

        return $this;
    }

    /**
     * Remove child
     *
     * @param MenuItemInterface $child
     */
    public function removeChild(MenuItemInterface $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * Set children
     *
     * @param ArrayCollection $children
     *
     * @return MenuItem
     */
    public function setChildren(ArrayCollection $children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Get children
     *
     * @return Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set menu
     *
     * @param MenuInterface $menu
     *
     * @return MenuItem
     */
    public function setMenu(MenuInterface $menu)
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Get menu
     *
     * @return MenuInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Has child
     */
    public function hasChild()
    {
        return count($this->children) > 0;
    }

    /**
     * Has parent
     */
    public function hasParent()
    {
        return !is_null($this->parent);
    }

    public function getActiveChildren()
    {
        $children = array();

        foreach ($this->children as $child) {
            if($child->enabled) {
                array_push($children, $child);
            }
        }

        return $children;
    }

    public function __toString()
    {
        return $this->name ?? "";
    }
}
