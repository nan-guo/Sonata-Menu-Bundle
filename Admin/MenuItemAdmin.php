<?php

namespace Prodigious\Sonata\MenuBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Prodigious\Sonata\MenuBundle\Entity\MenuItem;
use Prodigious\Sonata\MenuBundle\Entity\MenuItemInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class MenuItemAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'sonata/menu/menu-item';

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {

        $subject = $this->getSubject();

        $menu = $subject->getMenu();

        if(!$menu) {

            $request = $this->getRequest();
            
            $id = $request->get('menu', '');


            if(!empty(intval($id))) {
            
                $menuManager = $this->getConfigurationPool()->getContainer()->get('prodigious_sonata_menu.manager');

                $menu = $menuManager->load($id);
            }
        }        

        $formMapper
            ->with('config.label_menu_item', array('class' => 'col-md-6', 'translation_domain' => 'ProdigiousSonataMenuBundle'))
                ->add('name', 'text',
                    array(
                        'label' => 'config.label_name'
                    ),
                    array(
                        'translation_domain' => 'ProdigiousSonataMenuBundle'
                    )
                )
                ->add('parent', 'sonata_type_model',
                    array(
                        'label' => 'config.label_parent',
                        'required' => false,
                        'btn_add' => false,
                        'placeholder' => 'config.label_select',
                    ),
                    array(
                        'translation_domain' => 'ProdigiousSonataMenuBundle'
                    )
                )
                ->add('classAttribute', 'text',
                    array(
                        'label' => 'config.label_class_attribute',
                        'required' => false,
                    )
                    ,
                    array(
                        'translation_domain' => 'ProdigiousSonataMenuBundle'
                    )
                )
                ->add('enabled', null,
                    array(
                        'label' => 'config.label_enabled',
                        'required' => false,
                    ),
                    array(
                        'translation_domain' => 'ProdigiousSonataMenuBundle'
                    )
                )
            ->end()

            ->with('config.label_menu_link', array('class' => 'col-md-6', 'translation_domain' => 'ProdigiousSonataMenuBundle'))
                ->add('menu', 'sonata_type_model',
                    array(
                        'label' => 'config.label_menu',
                        'required' => false,
                        'btn_add' => false,
                        'data' => $menu,
                        'placeholder' => 'config.label_select',
                    ),
                    array(
                        'translation_domain' => 'ProdigiousSonataMenuBundle'
                    )
                )
            ->end();

        if($this->getConfigurationPool()->getContainer()->hasParameter('sonata.page.page.class')){
            $pageClass = $this->getConfigurationPool()->getContainer()->getParameter('sonata.page.page.class');
            
            $em = $this->modelManager->getEntityManager($pageClass);
            $builder = $em->createQueryBuilder('p');

            $query = $builder->select('p.name, p.url')
                       ->from($pageClass, 'p')
                       ->getQuery();

            $pages = $query->getResult();

            $choices = array();

            $subject = $this->getSubject();
            $url = $subject->getUrl();
           
            if(version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, "3.0", "<=")){
                $choices[0] = 'config.label_select';
                foreach ($pages as $page) {
                    $choices[$page['url']] = ucfirst($page['name']);
                }
            }else {
                foreach ($pages as $page) {
                    $choices['config.label_select'] = null;
                    $choices[ucfirst($page['name'])] = $page['url'];
                }
            }
            $formMapper
                ->with('config.label_menu_link', array('class' => 'col-md-6', 'translation_domain' => 'ProdigiousSonataMenuBundle'))
                    ->add('page', ChoiceType::class,
                        array(
                            'label' => 'config.label_page',
                            'required' => false,
                            'choices' => $choices,
                            'data' => $url,
                            'empty_data' => null,
                        ),
                        array(
                            'translation_domain' => 'ProdigiousSonataMenuBundle'
                        )
                    )
                ->end();
        }
        

        $formMapper
            ->with('config.label_menu_link', array('class' => 'col-md-6', 'translation_domain' => 'ProdigiousSonataMenuBundle'))
                ->add('url', 'text', 
                    array(
                        'label' => 'config.label_custom_url',
                        'required' => false,
                    ),
                    array(
                        'translation_domain' => 'ProdigiousSonataMenuBundle'
                    )
                )
                ->add('target', null,
                    array(
                        'label' => 'config.label_target',
                        'required' => false,
                    ),
                    array(
                        'translation_domain' => 'ProdigiousSonataMenuBundle'
                    )
                )
            ->end();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {   
        $listMapper->addIdentifier('name', null, array('label' => 'config.label_name', 'translation_domain' => 'ProdigiousSonataMenuBundle'));

        if(version_compare(\Symfony\Component\HttpKernel\Kernel::VERSION, "3.0", "<")){
            $listMapper->add('menu', null, array(), 'entity',
                array(
                    'class'    => 'Application\Sonata\MenuBundle\Entity\Menu',
                    'property' => 'name',
                )
            );
        }else{
            $listMapper->add('menu', null, array(), 'entity',
                array(
                    'class'    => 'Application\Sonata\MenuBundle\Entity\Menu',
                    'choice_label' => 'name',
                )
            );
        }

        $listMapper->add('_action', 'actions', array('label' => 'config.label_modify', 'translation_domain' => 'ProdigiousSonataMenuBundle', 'actions' => array('edit' => array(), 'delete' => array())));
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name')
            ->add('menu', null, array(), 'entity',
                array(
                    'class'    => 'Prodigious\Sonata\MenuBundle\Entity\Menu',
                    'property' => 'name',
                )
            );
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
        if($this->getConfigurationPool()->getContainer()->hasParameter('sonata.page.page.class')){      
            $data = $this->getForm()->get('page')->getData();
            if(!empty($data)){
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

        if(empty($url)) {

            $parent = $object->getParent();

            $container = $this->getConfigurationPool()->getContainer();

            $slugify = $container->get('sonata.core.slugify.cocur');

            $url = $slugify->slugify(strip_tags($object->getName()));

            if($object->hasParent()) {
                $parent = $object->getParent();
                $url = $parent->getUrl().'/'.$url;
            }else {
                $url = '/'.$url;
            }

            $object->setUrl($url);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toString($object)
    {
        return $object instanceof MenuItemInterface ? $object->getName() : $this->getTranslator()->trans("config.label_menu_item", array(), 'ProdigiousSonataMenuBundle');
    }

}
