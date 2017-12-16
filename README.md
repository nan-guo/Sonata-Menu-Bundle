# Sonata Menu Bundle
 
This bundle provides menu management by sonata admin bundle (compatible with sonata page bundle).

# Prerequisites
- SonataAdminBundle
- SonataPageBundle (Optional)

# Screenshots

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

### routing.yml

Replace admin_url by your admin prefix

```
prodigious_sonata_menu:
    resource: "@ProdigiousSonataMenuBundle/Resources/config/routing.yml"
    prefix:   /{admin_url}/sonata/menu
```

### config.yml or sonata.yml

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

# Getting Started with Menu Manager

### Controller

```
$mm = $this->container->get('prodigious_sonata_menu.manager');

$menuId = 1; // Example

$menu = $mm->load($menuId);

// $stauts = true (Get enabled menu items)
// $stauts = false (Get disabled menu items)
// getMenuItems($menu, $status="all")

$menuItems = $mm->getMenuItems($menu, true);

return  $this->render('menu/menu.html.twig', [
            '$menuItems' => $menuItems,
        ]);

```

### Twig View

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
            {% set attributes = "menu-itme" %}
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

# Additional info
Author: [Nan GUO](https://github.com/nan-guo/)
 
Company : [Prodigious](http://www.prodigious.com/)
