<?php

namespace Prodigious\Sonata\MenuBundle\Admin;

use Prodigious\Sonata\MenuBundle\Model\MenuInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MenuAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'sonata/menu';

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('config.label_menu', ['translation_domain' => 'ProdigiousSonataMenuBundle'])
                ->add('name', TextType::class,
                    [
                        'label' => 'config.label_name'
                    ],
                    [
                        'translation_domain' => 'ProdigiousSonataMenuBundle'
                    ]
                )
                ->add('alias', TextType::class,
                    [
                        'label' => 'config.label_alias'
                    ],
                    [
                        'translation_domain' => 'ProdigiousSonataMenuBundle'
                    ]
                )
            ->end()
        ->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->add('id', null, ['label' => 'config.label_id', 'translation_domain' => 'ProdigiousSonataMenuBundle'])
            ->addIdentifier('alias', null, ['label' => 'config.label_alias', 'translation_domain' => 'ProdigiousSonataMenuBundle'])
            ->addIdentifier('name', null, ['label' => 'config.label_name', 'translation_domain' => 'ProdigiousSonataMenuBundle'])
        ;

        $listMapper->add('_action', 'actions', [
            'label' => 'config.label_modify',
            'translation_domain' => 'ProdigiousSonataMenuBundle',
            'actions' => [
                'edit' => [],
                'delete' => [],
                'items' => ['template' => '@ProdigiousSonataMenu/CRUD/list__action_edit_items.html.twig', 'route' => 'items']
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('name')
            ->add('alias')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->add('items', $this->getRouterIdParameter().'/items');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->setTemplate('edit', '@ProdigiousSonataMenu/CRUD/edit.html.twig');
    }

    /**
     * {@inheritdoc}
     */
    public function toString(object $object): string
    {
        return $object instanceof MenuInterface ? $object->getName() : $this->getTranslator()->trans("config.label_menu", array(), 'ProdigiousSonataMenuBundle');
    }

    /**
     * @inheritdoc
     */
    protected function prePersist(object $object): void
    {
        parent::prePersist($object);
        foreach ($object->getMenuItems() as $menuItem) {
            $menuItem->setMenu($object);
        }
    }

    /**
     * @inheritdoc
     */
    protected function preUpdate(object $object): void
    {
        parent::prePersist($object);
        foreach ($object->getMenuItems() as $menuItem) {
            $menuItem->setMenu($object);
        }
    }

}
