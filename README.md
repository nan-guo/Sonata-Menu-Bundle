
# Sonata Menu Bundle
 
This bundle provides menu management by sonata admin bundle (compatible with sonata page bundle).

# Compatibility
- For Symfony 2, please install version 2.x
- For Symfony 3 and Symfony 4 , please install version 3.x

# Prerequisites
- SonataAdminBundle
- SonataPageBundle (Optional)

# Screenshots

![screenshot](https://github.com/nan-guo/Sonata-Menu-Bundle/blob/master/Resources/public/imgs/screenshot-1.png)

![screenshot](https://github.com/nan-guo/Sonata-Menu-Bundle/blob/master/Resources/public/imgs/screenshot-3.png)

Check out [more screenshots](https://github.com/nan-guo/Sonata-Menu-Bundle/tree/master/Resources/public/imgs).

# Installation

```

composer require prodigious/sonata-menu-bundle

```

# Configuration
```
// Symfony 2
// app/AppKernel.php
new Prodigious\Sonata\MenuBundle\ProdigiousSonataMenuBundle(),

php app/console cache:clear
php app/console doctrine:schema:update --force
php app/console assets:install
```

```
// Symfony 3
// app/AppKernel.php
new Prodigious\Sonata\MenuBundle\ProdigiousSonataMenuBundle(),

php bin/console cache:clear
php bin/console doctrine:schema:update --force
php bin/console assets:install
```

```
// Symfony 4
// config/bundles.php
Prodigious\Sonata\MenuBundle\ProdigiousSonataMenuBundle::class => ['all' => true],

php bin/console cache:clear
php bin/console doctrine:migration:diff
php bin/console doctrine:migration:migrate
php bin/console assets:install
```

### sonata_admin.yml

Add menu to your sonata admin menu list.
For symfony 4, you can also let it empty, menu will be added automatically

```
sonata_admin:
    dashboard:
        groups:     
            // Optional for symfony 4
            sonata.admin.group.menu_builder:
                label:           config.label_menu
                label_catalogue: ProdigiousSonataMenuBundle
                icon:            '<i class="fa fa-magic"></i>'
                items:
                    - prodigious_sonata_menu.admin.menu

            // Sonata page menu
            # sonata.admin.group.site_builder:
            #     label:           site
            #     label_catalogue: SonataPageBundle
            #     icon:            '<i class="fa fa-puzzle-piece"></i>'
            #     items:
            #         - sonata.page.admin.site
            #         - sonata.page.admin.page
```

# Advanced configurations ( Symfony 3 / Syfmony 4 )
#### Create custom entities

Edit the configuration

* prodigious_sonata_menu.yaml

```
prodigious_sonata_menu:
    entities:
        menu: AppBundle\Entity\Menu
        menu_item: AppBundle\Entity\MenuItem
```

Then create the related entity menu and menu item.
You can add extra fields

* Menu

```
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Prodigious\Sonata\MenuBundle\Model\Menu as BaseMenu;

/**
 * Class Menu
 *
 * @ORM\Table(name="sonata_menu")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Menu\MenuRepository")
 */
class Menu extends BaseMenu
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }
}
```

* MenuItem

```
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Prodigious\Sonata\MenuBundle\Model\MenuItem as BaseMenuItem;

/**
 * Class MenuItem
 *
 * @ORM\Table(name="sonata_menu_item")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MenuItemRepository")
 */
class MenuItem extends BaseMenuItem
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $icon;

    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     * @return $this
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }
}
```
Clear cache and update database
```
// Symfony 3

php bin/console cache:clear
php bin/console doctrine:schema:update --force

// Symfony 4

php bin/console cache:clear
php bin/console doctrine:migration:diff
php bin/console doctrine:migration:migrate

```

Remember to update admin classes by extending the original ones :

Edit the configuration

* prodigious_sonata_menu.yaml

```
prodigious_sonata_menu:
    entities:
        menu: AppBundle\Entity\Menu
        menu_item: AppBundle\Entity\MenuItem
    admins:
        menu: AppBundle\Admin\MyMenuAdmin
        menu_item: AppBundle\Admin\MyMenuItemAdmin
```
And create your admin class
```
namespace AppBundle\Admin;

use Prodigious\Sonata\MenuBundle\Admin\MenuAdmin as BaseAdmin;
use Sonata\AdminBundle\Form\FormMapper;

class MyMenuAdmin extends BaseAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);
    }
}
```

```
namespace AppBundle\Admin;

use Prodigious\Sonata\MenuBundle\Admin\MenuItemAdmin as BaseAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MyMenuItemAdmin extends BaseAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);
        
        $formMapper
            ->with('config.label_menu_item')
                ->add('icon', TextType::class, [
                        'label' => 'config.label_icon'
                    ]
                )
            ->end()
        ->end();
    }
}
```

# Getting Started with Menu Manager

### Controller

```
$mm = $this->container->get('prodigious_sonata_menu.manager');

$menuId = 1; // Example

$menu = $mm->load($menuId);

// $status = true (Get enabled menu items)
// $status = false (Get disabled menu items)
// getMenuItems($menu, $root = MenuManager::ITEM_CHILD, $status = MenuManager::STATUS_ALL)

$menuItems = $mm->getMenuItems($menu, true);

return  $this->render('menu/menu.html.twig', [
            '$menuItems' => $menuItems,
        ]);

```

#### And then, it's your showtime. For the frontend, you can output the results as you want. 

#### For example :

* menu.html.twig

```
{% if menuItems is not null and menuItems|length > 0 %}

{% import 'AppBundle:Menu:menu_tree_macro.html.twig' as tree %}

{% set currentPath = app.request.requestUri %}

{{ tree.menu(menuItems, currentPath) }}

{% endif %}
```

* menu_tree_macro.html.twig

```
{% macro menu(items, currentPath) %}
    
    {% import _self as self %}

        <ul>
            {% for menuItem in items %}
    
            {% set url = menuItem.url %}
            {% set attributes = "menu-item" %}
            {% if menuItem.classAttribute %}
            {% set attributes = attributes ~ ' ' ~ menuItem.classAttribute %}
            {% endif %}
            {% if menuItem.hasChild() %}
            
            {% set attributes = attributes ~ ' has-child' %}
            
            {% for childItem in menuItem.children %}

            {% set childUrl = childItem.url %}

            {% if childUrl == currentPath %}
            {% set attributes = attributes ~ ' current-parent' %}
            {% endif %}
            
            {% endfor %}
            
            {% endif %}

            <li class="{{ attributes }}" role="menu-item">
                {% if menuItem.hasChild() %}
                <a href="{{ url }}" class="parent" {% if currentPath == url %} class="current"{% endif %}" {% if menuItem.target %} target="_blank"{% endif %}>{{ menuItem.name }}</a>
                {{ self.menu(menuItem.children, currentPath) }}
                {% else %}
                <a href="{{ url }}" {% if currentPath == url %} class="current"{% endif %}" {% if menuItem.target %} target="_blank"{% endif %}>{{ menuItem.name }}</a>
                {% endif %}
            </li>
            {% endfor %}
        </ul>

{% endmacro %}
```

# Changelog
### 3.0.0
- Add Symfony 4 support

### 2.0.4
- Fix Symfony 3 compability bugs
- Remove custom routing config
  
### 2.0.5
- Fix Menu Item Admin bug

### 2.0.6
- Add column to Menu Admin

# Additional info
Author: [Nan GUO](https://github.com/nan-guo/)
 
Company : [Prodigious](http://www.prodigious.com/)
