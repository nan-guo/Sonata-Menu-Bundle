<?php

namespace Prodigious\Sonata\MenuBundle\Controller;

use Prodigious\Sonata\MenuBundle\Manager\MenuManager;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;

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

        /** @var MenuManager $menuManager */
        $menuManager = $this->container->get('prodigious_sonata_menu.manager');

        if (null !== $request->get('btn_update') && $request->getMethod() == 'POST') {
            $menuId = $request->get('menu', null);
            $items = $request->get('items', null);

            if (!empty($items) && !empty(intval($menuId))) {
                $items = json_decode($items);

                $update = $menuManager->updateMenuTree($menuId, $items);
                /** @var TranslatorInterface $translator */
                $translator = $this->get('translator');

                $request->getSession()->getFlashBag()->add('notice',
                    $translator->trans(
                        $update ? 'config.label_saved' : 'config.label_error',
                        [],
                        'ProdigiousSonataMenuBundle'
                    )
                );

                return new RedirectResponse($this->get('sonata.admin.route.default_generator')
                    ->generateUrl(
                        $this->get('prodigious_sonata_menu.admin.menu'),
                        'items',
                        ['id' => $menuId]
                    )
                );
            }
        }

        $menuItemsEnabled = $menuManager->getRootItems($object, MenuManager::STATUS_ENABLED);
        $menuItemsDisabled = $menuManager->getDisabledItems($object);

        $menus = $menuManager->findAll();

        return $this->renderWithExtraParams('@ProdigiousSonataMenu/Menu/menu_edit_items.html.twig', [
            'menus'             => $menus,
            'menu'              => $object,
            'menuItemsEnabled'  => $menuItemsEnabled,
            'menuItemsDisabled' => $menuItemsDisabled,
        ]);
    }
}
