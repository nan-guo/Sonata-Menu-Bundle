<?php

namespace Prodigious\Sonata\MenuBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Prodigious\Sonata\MenuBundle\Model\MenuInterface;
use Prodigious\Sonata\MenuBundle\Model\MenuItemInterface;
use Prodigious\Sonata\MenuBundle\Repository\MenuitemRepository;
use Prodigious\Sonata\MenuBundle\Repository\MenuRepository;

/**
 * Menu manager
 */
class MenuManager
{
    const STATUS_ENABLED = true;
    const STATUS_DISABLED = false;
    const STATUS_ALL = null;

    const ITEM_ROOT = true;
    const ITEM_CHILD = false;
    const ITEM_ALL = null;

    /**
     * @var EntityManagerInterface
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
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->menuRepository = $em->getRepository(MenuInterface::class);
        $this->menuItemRepository = $em->getRepository(MenuItemInterface::class);
    }

    /**
     * Load menu by id
     *
     * @param int $id
     *
     * @return Menu
     */
    public function load($id)
    {
        $menu = $this->menuRepository->find($id);

        return $menu;
    }

    /**
     * Load menu by alias
     *
     * @param string $alias
     *
     * @return Menu
     */
    public function loadByAlias($alias)
    {
        $menu = $this->menuRepository->findOneByAlias($alias);

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
    public function save(MenuInterface $menu)
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
     * @param Menu  $menu
     * @param mixed $status
     *
     * @return MenuItems[]
     */
    public function getRootItems(MenuInterface $menu, $status)
    {
        return $this->getMenuItems($menu, static::ITEM_ROOT, $status);
    }

    /**
     * Get enabled menu items
     *
     * @param Menu $menu
     *
     * @return MenuItems[]
     */
    public function getEnabledItems(MenuInterface $menu)
    {
        return $this->getMenuItems($menu, static::ITEM_ALL, static::STATUS_ENABLED);
    }

    /**
     * Get disabled menu items
     *
     * @param Menu $menu
     *
     * @return MenuItems[]
     */
    public function getDisabledItems(MenuInterface $menu)
    {
        return $this->getMenuItems($menu, static::ITEM_ALL, static::STATUS_DISABLED);
    }

    /**
     * Get menu items
     *
     * @param mixed $root
     * @param mixed $status
     *
     * @return MenuItem[]
     */
    public function getMenuItems(MenuInterface $menu, $root = self::ALL_ELEMENTS, $status = self::STATUS_ALL)
    {
        $menuItems = $menu->getMenuItems()->toArray();

        return array_filter($menuItems, function (MenuItemInterface $menuItem) use ($root, $status) {
            // Check root parameter
            if ($root === static::ITEM_ROOT && null !== $menuItem->getParent()
             || $root === static::ITEM_CHILD && null === $menuItem->getParent()
            ) {
                return;
            }

            // Check status parameter
            if ($status === static::STATUS_ENABLED && !$menuItem->getEnabled()
             || $status === static::STATUS_DISABLED && $menuItem->getEnabled()
            ) {
                return;
            }

            return $menuItem;
        });
    }

    /**
     * Update menu tree
     *
     * @param mixed      $menu
     * @param array      $items
     * @param mixed|null $parent
     *
     * @return bool
     */
    public function updateMenuTree($menu, $items, $parent = null)
    {
        $update = false;

        if (!($menu instanceof MenuInterface)) {
            $menu = $this->load($menu);
        }

        if (!empty($items) && $menu) {
            foreach ($items as $pos => $item) {
                /** @var MenuItem $menuItem */
                $menuItem = $this->menuItemRepository->findOneBy(['id' => $item->id, 'menu' => $menu]);

                if ($menuItem) {
                    $menuItem
                        ->setPosition($pos)
                        ->setParent($parent)
                    ;

                    $this->em->persist($menuItem);
                }

                if (isset($item->children) && !empty($item->children)) {
                    $this->updateMenuTree($menu, $item->children, $menuItem);
                }
            }

            $this->em->flush();

            $update = true;
        }

        return $update;
    }
}
