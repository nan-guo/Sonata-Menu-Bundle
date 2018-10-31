<?php

namespace Prodigious\Sonata\MenuBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Prodigious\Sonata\MenuBundle\Entity\Menu;
use Prodigious\Sonata\MenuBundle\Model\MenuInterface;
use Prodigious\Sonata\MenuBundle\Entity\MenuItem;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MenuAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'sonata/menu';

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('config.label_menu', array('translation_domain' => 'ProdigiousSonataMenuBundle'))
                ->add('name', TextType::class,
                    array(
                        'label' => 'config.label_name'
                    ),
                    array(
                        'translation_domain' => 'ProdigiousSonataMenuBundle'
                    )
                )
                ->add('alias', TextType::class,
                        array(
                            'label' => 'config.label_alias'
                        ),
                        array(
                            'translation_domain' => 'ProdigiousSonataMenuBundle'
                        )
                    )
            ->end()
        ->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, array('label' => 'config.label_id', 'translation_domain' => 'ProdigiousSonataMenuBundle'))
            ->addIdentifier('alias', null, array('label' => 'config.label_alias', 'translation_domain' => 'ProdigiousSonataMenuBundle'))
            ->addIdentifier('name', null, array('label' => 'config.label_name', 'translation_domain' => 'ProdigiousSonataMenuBundle'))
        ;

        $listMapper->add('_action', 'actions', array('label' => 'config.label_modify',
            'translation_domain' => 'ProdigiousSonataMenuBundle',
            'actions' => array(
                'edit' => array(),
                'delete' => array(),
                'items' => array('template' => '@ProdigiousSonataMenu/CRUD/list__action_edit_items.html.twig', 'route' => 'items')
            )
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('alias')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('items', $this->getRouterIdParameter().'/items');
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setTemplate('edit', '@ProdigiousSonataMenu/CRUD/edit.html.twig');
    }

    /**
     * {@inheritdoc}
     */
    public function toString($object)
    {
        return $object instanceof MenuInterface ? $object->getName() : $this->getTranslator()->trans("config.label_menu", array(), 'ProdigiousSonataMenuBundle');
    }

    /**
     * @inheritdoc
     */
    public function prePersist($object)
    {
        parent::prePersist($object);
        foreach ($object->getMenuItems() as $menuItem) {
            $menuItem->setMenu($object);
        }
    }

    /**
     * @inheritdoc
     */
    public function preUpdate($object)
    {
        parent::prePersist($object);
        foreach ($object->getMenuItems() as $menuItem) {
            $menuItem->setMenu($object);
        }
    }

}
