<?php

namespace Prodigious\Sonata\MenuBundle\Admin;

use Prodigious\Sonata\MenuBundle\Model\MenuInterface;
use Prodigious\Sonata\MenuBundle\Model\MenuItemInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MenuItemAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'sonata/menu/menu-item';

    /**
     * @var string
     */
    protected $menuClass;

    public function __construct(string $code, string $class, string $baseControllerName, string $menuClass)
    {
        parent::__construct(
            $code,
            $class,
            $baseControllerName
        );

        $this->menuClass = $menuClass;
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist($object)
    {
        $this->rewriteUrl($object);
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate($object)
    {
        $this->rewriteUrl($object);
    }

    public function rewriteUrl($object)
    {
        if ($this->getConfigurationPool()->getContainer()->hasParameter('sonata.page.page.class')) {
            $data = $this->getForm()->get('page')->getData();
            if (!empty($data)) {
                $object->setUrl($data);
            }
        }
        $this->updateUrl($object);
    }

    /**
     * Update url
     *
     * @param Menuitem $object
     */
    public function updateUrl($object)
    {
        $url = $object->getUrl();

        if (empty($url)) {
            $parent = $object->getParent();

            $container = $this->getConfigurationPool()->getContainer();

            $slugify = $container->get('sonata.core.slugify.cocur');

            $url = $slugify->slugify(strip_tags($object->getName()));

            if ($object->hasParent()) {
                $parent = $object->getParent();
                $url = $parent->getUrl() . '/' . $url;
            } else {
                $url = '/' . $url;
            }

            $object->setUrl($url);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toString($object)
    {
        return $object instanceof MenuItemInterface ? $object->getName() : $this->getTranslator()->trans('config.label_menu_item', [], 'ProdigiousSonataMenuBundle');
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('toggle', $this->getRouterIdParameter() . '/toggle');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();

        $menu = $subject->getMenu();

        if (!$menu) {
            $request = $this->getRequest();

            $id = $request->get('menu', '');

            if (!empty(intval($id))) {
                $menuManager = $this->getConfigurationPool()->getContainer()->get('prodigious_sonata_menu.manager');

                $menu = $menuManager->load($id);
            }
        }

        $formMapper
            ->with('config.label_menu_item', ['class' => 'col-md-6', 'translation_domain' => 'ProdigiousSonataMenuBundle'])
                ->add('name', TextType::class,
                    [
                        'label' => 'config.label_name',
                    ],
                    [
                        'translation_domain' => 'ProdigiousSonataMenuBundle',
                    ]
                )
                ->add('parent', ModelType::class,
                    [
                        'label'       => 'config.label_parent',
                        'required'    => false,
                        'btn_add'     => false,
                        'placeholder' => 'config.label_select',
                    ],
                    [
                        'translation_domain' => 'ProdigiousSonataMenuBundle',
                    ]
                )
                ->add('classAttribute', TextType::class,
                    [
                        'label'    => 'config.label_class_attribute',
                        'required' => false,
                    ],
                    [
                        'translation_domain' => 'ProdigiousSonataMenuBundle',
                    ]
                )
                ->add('enabled', null,
                    [
                        'label'    => 'config.label_enabled',
                        'required' => false,
                    ],
                    [
                        'translation_domain' => 'ProdigiousSonataMenuBundle',
                    ]
                )
            ->end()

            ->with('config.label_menu_link', ['class' => 'col-md-6', 'translation_domain' => 'ProdigiousSonataMenuBundle'])
                ->add('menu', ModelType::class,
                    [
                        'label'       => 'config.label_menu',
                        'required'    => false,
                        'btn_add'     => false,
                        'data'        => $menu,
                        'placeholder' => 'config.label_select',
                    ],
                    [
                        'translation_domain' => 'ProdigiousSonataMenuBundle',
                    ]
                )
            ->end();

        if ($this->getConfigurationPool()->getContainer()->hasParameter('sonata.page.page.class')) {
            $pageClass = $this->getConfigurationPool()->getContainer()->getParameter('sonata.page.page.class');

            $em = $this->modelManager->getEntityManager($pageClass);
            $builder = $em->createQueryBuilder('p');

            $query = $builder->select('p.name, p.url')
                       ->from($pageClass, 'p')
                       ->getQuery();

            $pages = $query->getResult();

            $choices = [];

            $subject = $this->getSubject();
            $url = $subject->getUrl();

            if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0', '<=')) {
                $choices[0] = 'config.label_select';
                foreach ($pages as $page) {
                    $choices[$page['url']] = ucfirst($page['name']);
                }
            } else {
                foreach ($pages as $page) {
                    $choices['config.label_select'] = null;
                    $choices[ucfirst($page['name'])] = $page['url'];
                }
            }
            $formMapper
                ->with('config.label_menu_link', ['class' => 'col-md-6', 'translation_domain' => 'ProdigiousSonataMenuBundle'])
                    ->add('page', ChoiceType::class,
                        [
                            'label'      => 'config.label_page',
                            'required'   => false,
                            'choices'    => $choices,
                            'data'       => $url,
                            'empty_data' => null,
                        ],
                        [
                            'translation_domain' => 'ProdigiousSonataMenuBundle',
                        ]
                    )
                ->end();
        }

        $formMapper
            ->with('config.label_menu_link', ['class' => 'col-md-6', 'translation_domain' => 'ProdigiousSonataMenuBundle'])
                ->add('url', TextType::class,
                    [
                        'label'    => 'config.label_custom_url',
                        'required' => false,
                    ],
                    [
                        'translation_domain' => 'ProdigiousSonataMenuBundle',
                    ]
                )
                ->add('target', null,
                    [
                        'label'    => 'config.label_target',
                        'required' => false,
                    ],
                    [
                        'translation_domain' => 'ProdigiousSonataMenuBundle',
                    ]
                )
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('name', null, ['label' => 'config.label_name', 'translation_domain' => 'ProdigiousSonataMenuBundle']);

        if (version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, '3.0', '<')) {
            $listMapper->add('menu', null, [], EntityType::class,
                [
                    'class'    => $this->menuClass,
                    'property' => 'name',
                ]
            );
        } else {
            $listMapper->add('menu', null, [], EntityType::class,
                [
                    'class'        => $this->menuClass,
                    'choice_label' => 'name',
                ]
            );
        }

        $listMapper->add('_action', 'actions', [
            'label'              => 'config.label_modify',
            'translation_domain' => 'ProdigiousSonataMenuBundle',
            'actions'            => [
                'edit'   => [],
                'delete' => [],
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name')
            ->add('menu', null, [], EntityType::class,
                [
                    'class' => $this->menuClass,
                ]
            );
    }
}
