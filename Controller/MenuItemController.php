<?php

namespace Prodigious\Sonata\MenuBundle\Controller;

use Prodigious\Sonata\MenuBundle\Model\MenuItemInterface;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MenuItemController extends Controller
{

    /**
     * {@inheritdoc}
     */
    protected function redirectTo($object)
    {
        $request = $this->getRequest();
        $response = parent::redirectTo($object, $request);

        if (null !== $request->get('btn_update_and_list') || null !== $request->get('btn_create_and_list') || null !== $request->get('btn_update_and_edit') || $this->getRestMethod() === 'DELETE') {
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
