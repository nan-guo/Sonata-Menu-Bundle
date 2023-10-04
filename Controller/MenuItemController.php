<?php

namespace Prodigious\Sonata\MenuBundle\Controller;

use Prodigious\Sonata\MenuBundle\Model\MenuItemInterface;
use Prodigious\Sonata\MenuBundle\Manager\MenuManager;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Sonata\AdminBundle\Route\DefaultRouteGenerator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\ORM\EntityManagerInterface;

class MenuItemController extends Controller
{

    private $menuManager;
    private $translator;
    private $routeGenerator;
    private $adminPool;
    private $entityManager;

    public function __construct(MenuManager $menuManager,
                                TranslatorInterface $translator,
                                DefaultRouteGenerator $routeGenerator,
                                Pool $adminPool,
                                EntityManagerInterface $entityManager)
    {
        $this->menuManager = $menuManager;
        $this->translator = $translator;
        $this->routeGenerator = $routeGenerator;
        $this->adminPool = $adminPool;
        $this->entityManager = $entityManager;
    }
    /**
     * @param integer $id
     */
    public function toggleAction($id)
    {
        
        /** @var MenuItemInterface $object */
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }

        $object->setEnabled(!$object->getEnabled());

        $m = $this->entityManager;
        $m->persist($object);
        $m->flush();

        return new RedirectResponse($this->routeGenerator->generateUrl(
            $this->adminPool->getAdminByAdminCode('prodigious_sonata_menu.admin.menu'),
                'items',
                ['id' => $object->getMenu()->getId()]
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function redirectTo(Request $request, object $object): RedirectResponse
    {
        $response = parent::redirectTo($request, $object);

        if (null !== $request->get('btn_update_and_list') || null !== $request->get('btn_create_and_list') || null !== $request->get('btn_update_and_edit') || strtoupper($request->getMethod()) === 'DELETE') {
            $url = $this->admin->generateUrl('list');

            if(!empty($object) && $object instanceof MenuItemInterface) {
                $menu = $object->getMenu();

                if($menu && $this->admin->isChild()) {
                    $url = $this->admin->getParent()->generateObjectUrl('items', $menu, array('id' => $menu->getId()));
                }
            }

            $response->setTargetUrl($url);
        }

        return $response;
    }


}
