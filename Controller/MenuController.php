<?php

namespace Prodigious\Sonata\MenuBundle\Controller;

use Prodigious\Sonata\MenuBundle\Manager\MenuManager;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sonata\AdminBundle\Route\DefaultRouteGenerator;
use Sonata\AdminBundle\Admin\Pool;

class MenuController extends Controller
{

    private $menuManager;
    private $translator;
    private $routeGenerator;
    private $adminPool;

    public function __construct(MenuManager $menuManager,
                                TranslatorInterface $translator,
                                DefaultRouteGenerator $routeGenerator,
                                Pool $adminPool)
    {
        $this->menuManager = $menuManager;
        $this->translator = $translator;
        $this->routeGenerator = $routeGenerator;
        $this->adminPool = $adminPool;
    }

	/**
	 * Manager menu items
	 *
	 * @param $id
	 */
    public function itemsAction(Request $request, $id)
    {
    	$object = $this->admin->getSubject();


    	if (empty($object)) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        /** @var MenuManager $menuManager */
        $menuManager = $this->menuManager;

        if (null !== $request->get('btn_update') && $request->getMethod() == 'POST') {

            $menuId = $request->get('menu', null);
            $items = $request->get('items', null);

            if(!empty($items) && !empty(intval($menuId))) {
                $items = json_decode($items);

                $update = $menuManager->updateMenuTree($menuId, $items);
                /** @var TranslatorInterface $translator */
                //$translator = $this->get('translator');

                $request->getSession()->getFlashBag()->add('notice',
                    $this->translator->trans(
                        $update ? 'config.label_saved' : 'config.label_error',
                        array(),
                        'ProdigiousSonataMenuBundle'
                    )
                );

                return new RedirectResponse($this->routeGenerator->generateUrl(
                        $this->adminPool->getAdminByAdminCode('prodigious_sonata_menu.admin.menu'),
                        'items',
                        ['id' => $menuId]
                    )
                );
            }
        }

        $menuItemsEnabled = $menuManager->getRootItems($object, MenuManager::STATUS_ENABLED);
        $menuItemsDisabled = $menuManager->getDisabledItems($object);

        $menus = $menuManager->findAll();

    	return $this->renderWithExtraParams('@ProdigiousSonataMenu/Menu/menu_edit_items.html.twig', array(
            'menus' => $menus,
    		'menu' => $object,
            'menuItemsEnabled' => $menuItemsEnabled,
            'menuItemsDisabled' => $menuItemsDisabled
        ));
    }
}
