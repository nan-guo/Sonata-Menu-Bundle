<?php

namespace Prodigious\Sonata\MenuBundle\Admin;

use Prodigious\Sonata\MenuBundle\Model\MenuInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MenuAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'sonata/menu';

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
        return $object instanceof MenuInterface ? $object->getName() : $this->getTranslator()->trans('config.label_menu', [], 'ProdigiousSonataMenuBundle');
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($object)
    {
        parent::prePersist($object);
        foreach ($object->getMenuItems() as $menuItem) {
            $menuItem->setMenu($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($object)
    {
        parent::prePersist($object);
        foreach ($object->getMenuItems() as $menuItem) {
            $menuItem->setMenu($object);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('config.label_menu', ['translation_domain' => 'ProdigiousSonataMenuBundle'])
                ->add('name', TextType::class,
                    [
                        'label' => 'config.label_name',
                    ],
                    [
                        'translation_domain' => 'ProdigiousSonataMenuBundle',
                    ]
                )
                ->add('alias', TextType::class,
                    [
                        'label' => 'config.label_alias',
                    ],
                    [
                        'translation_domain' => 'ProdigiousSonataMenuBundle',
                    ]
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
            ->add('id', null, ['label' => 'config.label_id', 'translation_domain' => 'ProdigiousSonataMenuBundle'])
            ->addIdentifier('alias', null, ['label' => 'config.label_alias', 'translation_domain' => 'ProdigiousSonataMenuBundle'])
            ->addIdentifier('name', null, ['label' => 'config.label_name', 'translation_domain' => 'ProdigiousSonataMenuBundle'])
        ;

        $listMapper->add('_action', 'actions', [
            'label'              => 'config.label_modify',
            'translation_domain' => 'ProdigiousSonataMenuBundle',
            'actions'            => [
                'edit'   => [],
                'delete' => [],
                'items'  => ['template' => '@ProdigiousSonataMenu/CRUD/list__action_edit_items.html.twig', 'route' => 'items'],
            ],
        ]);
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
        $collection->add('items', $this->getRouterIdParameter() . '/items');
    }
}
