<?php

namespace Prodigious\Sonata\MenuBundle\Adapter;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Prodigious\Sonata\MenuBundle\Model\MenuItemInterface;
use Prodigious\Sonata\MenuBundle\Manager\MenuManager;

/**
 * Class KnpMenuAdapter
 *
 * Warning !
 * Using or calling this adapter require to install knplabs/knp-menu-bundle :
 * `composer require knplabs/knp-menu-bundle`
 *
 * @author Joseph LEMOINE <j.lemoine@ludi.cat>
 */
class KnpMenuAdapter
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var MenuManager
     */
    protected $menuManager;

    /**
     * KnpMenuAdapter constructor.
     *
     * @param MenuManager $menuManager
     */
    public function __construct(
        FactoryInterface $factory,
        MenuManager $menuManager
    ) {
        $this->factory = $factory;
        $this->menuManager = $menuManager;
    }

    /**
     * @param string $alias
     * @return \Knp\Menu\ItemInterface
     */
    public function createMenu($alias)
    {
        $knp = $this->factory->createItem('root');

        $menu = $this->menuManager->loadByAlias($alias);
        // Get root list
        $items = $this->menuManager->getRootItems($menu, MenuManager::STATUS_ENABLED);

        foreach ($items as $item) {
            $this->recursiveAddItem($knp, $item);
        }

        return $knp;
    }

    /**
     * @param ItemInterface $menu
     * @param MenuItemInterface      $menuItemInterface
     * @return ItemInterface
     */
    protected function recursiveAddItem(ItemInterface $menu, MenuItemInterface $menuItem)
    {
        $nbChildren = $menuItem->getActiveChildren()->count();
        $childMenu = $menu->addChild(sprintf('%s.%d', $menu->getName(), $menuItem->getId()), [
            'route' => $menuItem->getPage() ?? null,
            'uri' => $menuItem->getUrl(),
            'label' => $menuItem->getName(),
            'attributes' => [
                'dropdown' => $nbChildren > 0,
            ],
            'linkAttributes' => [
                'target' => $menuItem->getTarget() ? '_blank' : null,
            ],
        ])
            ->setLinkAttribute('class', $menuItem->getClassAttribute())
        ;

        if ($nbChildren) {
            foreach ($menuItem->getActiveChildren() as $childPage) {
                $this->recursiveAddItem($childMenu, $childPage);
            }
        }

        return $menu;
    }
}
