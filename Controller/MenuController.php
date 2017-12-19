<?php

namespace Prodigious\Sonata\MenuBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MenuController extends Controller
{

	/**
	 * Manager menu items
	 *
	 * @param $id
	 */
    public function itemsAction($id)
    {
    	$object = $this->admin->getSubject();
        $request = $this->getRequest();

    	if (empty($object)) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        $menuManager = $this->container->get('prodigious_sonata_menu.manager');

        if (null !== $request->get('btn_update') && $request->getMethod() == 'POST') {
            
            $menuId = $request->get('menu', null);
            $items = $request->get('items', null);

            if(!empty($items) && !empty(intval($menuId))) {
                
                $items = json_decode($items);

                $update = $menuManager->updateMenuTree($menuId, $items);

                $session = $request->getSession();

                $translator = $this->get('translator');

                if($update) {
                    $session->getFlashBag()->add('notice', $translator->trans('config.label_saved', array(), 'ProdigiousSonataMenuBundle'));
                } else {
                    $session->getFlashBag()->add('notice', $translator->trans('config.label_error', array(), 'ProdigiousSonataMenuBundle'));
                }

                return new RedirectResponse($this->generateUrl('admin_sonata_menu_menu_items', array('id' => $menuId)));                

            }

        }

        $menuItemsEnabled = $menuManager->getRootItems($object, true);

        $menuItemsDisabled = $menuManager->getDisabledItems($object);

        $menus = $menuManager->findAll();

    	return $this->render('ProdigiousSonataMenuBundle:Menu:menu_edit_items.html.twig', array(
            'menus' => $menus,
    		'menu' => $object,
            'menuItemsEnabled' => $menuItemsEnabled,
            'menuItemsDisabled' => $menuItemsDisabled
        ));
    }
}
