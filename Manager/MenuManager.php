<?php

namespace Prodigious\Sonata\MenuBundle\Manager;

use Doctrine\ORM\EntityManager;
use Prodigious\Sonata\MenuBundle\Repository\MenuRepository;
use Prodigious\Sonata\MenuBundle\Repository\MenuitemRepository;
use Prodigious\Sonata\MenuBundle\Entity\Menu;
use Prodigious\Sonata\MenuBundle\Entity\MenuInterface;
use Prodigious\Sonata\MenuBundle\Entity\MenuItem;

/**
 * Menu manager
 */
class MenuManager
{
    /**
     *
     * @var EntityManager 
     */
    protected $em;

    /**
     * @var MenuRepository
     */
    protected $menuRepository;

    /**
     * @var MenuItemRepository
     */
    protected $menuItemRepository;

    /**
     * Constructor
     *
     * @param MenuRepository $menuRepository
     */
    // public function __construct(EntityManager $em, MenuRepository $menuRepository, MenuitemRepository $menuItemRepository)
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->menuRepository = $em->getRepository('ProdigiousSonataMenuBundle:Menu');
        $this->menuItemRepository = $em->getRepository('ProdigiousSonataMenuBundle:MenuItem');
    }

    /**
     * Load menu by id
     *
     * @param int $id
     * @return Menu
     */
    public function load($id)
    {
        $menu = $this->menuRepository->findById($id);

        return $menu;
    }

    /**
     * Remove a menu
     *
     * @param mixed $menu
     */
    public function remove($menu)
    {
        $menu = $this->menuRepository->remove($menu);
    }

    /**
     * Save a menu
     *
     * @param Menu $menu
     */
    public function save(Menu $menu)
    {
        $this->menuRepository->save($menu);
    }

    /**
     * @return Menu[]
     */
    public function findAll()
    {
        return $this->menuRepository->findAll();
    }

    /**
     * Get first level menu items
     *
     * @param Menu $menu
     * @return MenuItems[]
     */
    public function getRootItems(Menu $menu, $status)
    {
        return $this->getMenuItems($menu, true, $status);
    }

    /**
     * Get enabled menu items
     *
     * @param Menu $menu
     * @return MenuItems[]
     */
    public function getEnabledItems(Menu $menu)
    {
        return $this->getMenuItems($menu, false, true);
    }

    /**
     * Get disabled menu items
     *
     * @param Menu $menu
     * @return MenuItems[]
     */
    public function getDisabledItems(Menu $menu)
    {
        return $this->getMenuItems($menu, false, false);
    }

    /**
     * Get menu items
     *
     * @return MenuItem[]
     */
    public function getMenuItems(Menu $menu, $root=false, $status="all")
    {
        $items = array();
        
        $menuItems = $menu->getMenuItems();

        if(count($menuItems) > 0) {
            if($status == true) {
                // Get active menu items
                foreach ($menuItems as $menuItem) {
                    if($menuItem->getEnabled()) {
                        if($root) {
                            if(is_null($menuItem->getParent())) {
                                array_push($items, $menuItem);
                            }
                        } else {
                            array_push($items, $menuItem);
                        }
                    }
                }
            } elseif($status == false) {

                // Get disabled menu items
                foreach ($menuItems as $menuItem) {
                    if(!$menuItem->getEnabled()) {
                        if($root) {
                            if(is_null($menuItem->getParent())) {
                                array_push($items, $menuItem);
                            }
                        } else {
                            array_push($items, $menuItem);
                        }
                    }
                }

            } elseif($status == "all") {
                foreach ($menuItems as $menuItem) {
                    if($root) {
                        if(is_null($menuItem->getParent())) {
                            array_push($items, $menuItem);
                        }
                    } else {
                        array_push($items, $menuItem);
                    }
                }
            } 

            
        }

        return $items;
    }

    /**
     * Update menu tree
     *
     * @param mixed $menu
     * @param array $items
     *
     * @return bool
     */
    public function updateMenuTree($menu, $items, $parent=null)
    {
        $update = false;

        if(!($menu instanceof MenuInterface)) {
            $menu = $this->load($menu);
        }

        if(!empty($items) && $menu) {

            foreach ($items as $pos => $item) {
                $menuItem = $this->menuItemRepository->findOneBy(array('id' => $item->id, 'menu' => $menu));
                
                if($menuItem) {
                    $menuItem->setPosition($pos);

                    $menuItem->setParent($parent);

                    $this->em->persist($menuItem);
                }

                if(isset($item->children) && !empty($item->children)) {
                    $this->updateMenuTree($menu, $item->children, $menuItem);
                }
            }

            $this->em->flush();

            $update = true;
        }

        return $update;
    }

}
