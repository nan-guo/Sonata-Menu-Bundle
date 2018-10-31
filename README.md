# Sonata Menu Bundle
 
This bundle provides menu management by sonata admin bundle (compatible with sonata page bundle).

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
// app/AppKernel.php
new Prodigious\Sonata\MenuBundle\ProdigiousSonataMenuBundle(),

// Symfony 2
php app/console doctrine:schema:update --force
php app/console assets:install

// Symfony 3
php bin/console doctrine:schema:update --force
php bin/console assets:install
```

### sonata_admin.yml

Add menu to your sonata admin menu list

```
sonata_admin:
    dashboard:
        groups:
            // Sonata page menu
            # sonata.admin.group.site_builder:
            #     label:           site
            #     label_catalogue: SonataPageBundle
            #     icon:            '<i class="fa fa-puzzle-piece"></i>'
            #     items:
            #         - sonata.page.admin.site
            #         - sonata.page.admin.page
            
            sonata.admin.group.menu_builder:
                label:           config.label_menu
                label_catalogue: ProdigiousSonataMenuBundle
                icon:            '<i class="fa fa-magic"></i>'
                items:
                    - prodigious_sonata_menu.admin.menu
```

You can also let it empty, menu will be added automatically

### Create custom entities

Edit the configuration

* prodigious_sonata_menu.yaml

```
prodigious_sonata_menu:
    entities:
        menu: My\App\Entity\MyMenu
        menu_item: My\App\Entity\MyMenuItem
```

Then create the related entity menu and menu item.
You can add extra fields

* Menu

```
use Prodigious\Sonata\MenuBundle\Model\MenuAbstract;
// Annotations
use Doctrine\ORM\Mapping as ORM;

/**
 * Class MenuItem
 *
 * @ORM\Table
 * @ORM\Entity(repositoryClass="App\Repository\Menu\MenuRepository")
 */
class Menu extends MenuAbstract
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
}
```

* MenuItem

```
use Prodigious\Sonata\MenuBundle\Model\MenuItemAbtract;
// Annotations
use Doctrine\ORM\Mapping as ORM;

/**
 * Class MenuItem
 *
 * @ORM\Table
 * @ORM\Entity(repositoryClass="App\Repository\MenuItemRepository")
 */
class MenuItem extends MenuItemAbtract
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
     * @ORM\Column(type="string")
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

Remember to update admin classes by extending the original ones :

```
use Prodigious\Sonata\MenuBundle\Admin\MenuAdmin as BaseAdmin;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MyMenuAdmin extends BaseAdmin
{
    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        parent::configureFormFields($formMapper);
        $formMapper
            ->with('config.label_menu', [])
                ->add('icon', TextType::class, [
                        'label' => 'config.label_icon'
                    ],
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
// getMenuItems($menu, $root=MenuManager::ITEM_CHILD, $status=MenuManager::STATUS_ALL)

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

### KnpMenuBundle integration

#### Install bundle

* composer

```
composer require knplabs/knp-menu-bundle "^2.0"

```

#### Enable integration in config.yml or prodigious_sonata_menu.yaml

* prodigious_sonata_menu.yaml

```
prodigious_sonata_menu:
    knp_menu_integration: true

```

Clear cache

```
php bin/console cache:clear
```

#### Use in twig

* my_template.html.twig

Use the menu alias to retrieve menu

```
{{ knp_menu_render(sonata_menu('test')) }}
```

#### Limitations

You have to create a knp menu template that can handle multi level trees.
To handle bootstrap and font awsome bundle you should override the menuitem entity.

#### Override

* Services

You can override some service just by replacing the service alias.
Exemple:

```
parameters:
    prodigious_sonata_menu.twig.knp.class: My\Custom\MenuBuilder
```

# Changelog
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
