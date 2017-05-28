Laws
===============
2017-05-28


Laws stands for **LA**youts and **W**idgets **S**ystem.




Laws is a system that you can implement in your application to separate the data from 
its html representation (an html representation is called view in law).

The benefit of separating the data from the view is that you can display the data using different views.

In other words, you create the data once for all, and then you create as many views as you want.


Laws was first created as a helper for Controllers (for applications that use Controllers) to render 
html things (pages or fragments of pages).



What's a theme?
==================================

An application has a certain number of data to display.
To make things easier to understand, let's say the application is composed of 10 items.

Give the same application to Paul and Alice, both template authors.
 
Each author will create a view (html representation) for every item of the application.
So Paul will create his 10 views, and Alice will create another 10 views.

Paul's 10 views will be different from Alice's 10 views.

That's it: we have two themes: Paul's theme and Alice theme.

Now we can switch from Paul theme to Alice's theme and vice versa at will.

The same data is used (so the application dev is happy), but the look'n'feel is very different
depending on whether you browse Paul or Alice's theme.

That's the powerful concept of theme, and implementing laws in your application is one way to get there.





What about layouts and widgets?
==================================
 
A widget is an object that renders a portion of a page.

Typically, the weather widget on some websites.
But a widget can be as small as a title, or as big as a one page app.


The layout disposes widgets in an html structure.
It can do so using at least two ways:

- calling the widget's render method directly
- calling a position

A position is basically a pre-defined group of widgets.
When the layout calls a position, all the widgets at that position are rendered.







The main ideas of Laws
============================

The main idea of laws is to allow configuration of the Layouts and Widgets using a file rather than php code.


Rendering a widget: the model
-----------------------

In the end, it's the widget which is responsible for displaying the data.
As said earlier, Paul and Alice will create 2 different html representations of the widget, but the input of the widget is the same: it's the data.

In Laws, the data is passed to the widgets in a php array form.
We call that array the model.

The model keys (and values conventions if any) needs to be carefully established by the widget author,
because template authors like Paul and Alice will refer to that model as the documentation for creating their templates.



Three rendering tools: widget, position and includes
------------------------------------------------------

Layout and widgets should be able to call those three methods:

- widget ( widgetId )                   // return a rendered a widget
- position ( positionName )             // return a rendered position
- includes ( includePath )              // includes another file located in the includes directory (have a look at the file structure later in this document to see where the includes dir is located)





Laws config file: the building block of laws
---------------------------

Layouts and Widgets are php objects in the end, but as humans we prefer to deal with a simple configuration array.
That's the idea of a laws config file.

The laws config file allows anyone to change the widget configuration just by changing some properties in a php array (rather
than diving into some oop php code).


The laws config file structure is extensible, and its base form is the following:


```txt
- layout:
----- tpl: $layoutTemplateName
- widgets:
----- $widgetId: 
--------- tpl: $widgetTemplateName 

```

With:

- layoutTemplateName: $layoutName/$layoutVariationName
- widgetTemplateName: $widgetName/$widgetVariationName
- widgetId: ( $positionName . )? $widgetInternalName
- layoutName: string, the name of the layout, for instance sandwich_2c (see 
        related [layout naming conventions](https://github.com/lingtalfi/layout-naming-conventions#lnc_1) for ideas about naming layouts)
- layoutVariationName: string, the name of the layout variation. like default, prototype, myVar1, ...
- positionName: string, the name of the position (sidebar, main, bottom, ...)
- widgetInternalName: string, a widget identifier (like dashboard, customerAccountMenu, customBlock, ....)



Here is a concrete example in php of one of the simplest config file possible:


```php
<?php

$conf = [
    "layout" => [
        'tpl' => "sandwich_1c/raw",
    ],
    "widgets" => [
        'maincontent.login' => [
            "tpl" => 'Ekom/ForgotPassword/prototype',
            "conf" => [
                "key1" => "value1",
                // ...
            ],
        ],
        // other widgets here...
    ],
];
```

The config file is a key element in laws. 




Computing the laws config file
-------------------------


Here is how laws defines the computing of the config file:


Context: a view identifier $viewId is defined, and the application might or might not use a theme named $themeName.
Usually, the laws system is used by module authors, and the viewId is composed of the module name, followed by 
a slash, followed by an abstract identifier.

So for instance "MyModule/contact" is a typical viewId for a MyModule module.



- Step1: collecting the base config file
    - if the theme is defined, and if the following file exists: **app/config/laws/theme/$themeName/$viewId.conf.php**,
            then this is our base config file.
    - otherwise, if the following file exists: **app/config/laws/$viewId.conf.php**, then this is our base config file
    - otherwise, an error should be returned, because no config file could be found for the given $viewId
    
- Step2: searching for user config file and merging
    - if the theme is defined, and if the following file exists: **app/config/laws/theme/$themeName/$viewId.user.conf.php**,
            then this config is merged with the base config file.
    - otherwise, if the following file exists: **app/config/laws/$viewId.user.conf.php**, then this is merged with
            the base config file.
            

            
Note that step2 was created so that a user could overwrite the base configuration file via a gui.            
            
            
Note that the Controller has always the final word, as it is responsible for displaying the view in the first place.
And so we would typically see this one liner in Controller's code; notice the second optional argument which allow
the Controller to override the computed configuration.

```php

return $this->renderByViewId("MyModule/contact", LawsConfig::create()->replace([
    'widgets' => [
        'contactForm' => [
            'tpl' => "MyModule/contactForm/special",
        ],
    ],
]));

```


Shortcodes
------------

A shortcode is a string that can be processed and transformed into any value type (including arrays, objects, ...).

Remember step 2 of computing the laws config file? Now with the help of shortcodes, the user can not only
use the gui to creates strings or arrays, she now access some parts of the api and potentially call powerful methods.


The shortcode notation is the following:

- shortcode notation: <shortCodeProviderName> <:> <shortCodeFunction>

With:
- shortCodeProviderName: the name of the short code provider, in lowercase; for instance: ekom
- shortCodeFunction: <providerMethod> (<:> <providerArgs>)?
- providerMethod: the name of the method of the provider we want to call, for instance doActionAAA
- providerArgs: arguments of the function, using the shortcode notation.


The Shortcode notation is described in the source comments of the [ShortCodeTool](https://github.com/lingtalfi/BeeFramework/blob/master/Notation/String/ShortCode/Tool/ShortCodeTool.php) from the Bee planet.

Basically, the shortcode notation allows you to do things like this:

```php 
az(ShortCodeTool::parse("hello=6, pou=[a, [b, c]], e='po=po', f='[pou]', g=[po => 4, go => [1,2,3], mo]"));
```

Which would result in this:

```txt
array(5) {
  ["hello"] => int(6)
  ["pou"] => array(2) {
    [0] => string(1) "a"
    [1] => array(2) {
      [0] => string(1) "b"
      [1] => string(1) "c"
    }
  }
  ["e"] => string(5) "po=po"
  ["f"] => string(5) "[pou]"
  ["g"] => array(3) {
    ["po"] => int(4)
    ["go"] => array(3) {
      [0] => int(1)
      [1] => int(2)
      [2] => int(3)
    }
    [0] => string(2) "mo"
  }
}

```


Here is how a shortcode looks like in action (in a laws config file):

```php
<?php

$conf = [
    "layout" => [
        'tpl' => "sandwich_1c/default",
    ],
    "widgets" => [
        'maincontent.breadcrumbs' => [
            "tpl" => 'Ekom/BreadCrumbs/default',
            'conf' => [
                // this will return the breadcrumbs model so that the template can render the right categories
                // and links of the breadcrumbs
                'items' => 'ekom:getBreadCrumbs',  
            ],
        ],
    ],
];
```




Laws and the file structure
================================

Here is the structure you should implement to have a laws compliant system:


// config files
- $app/config/laws/$viewId.conf.php                         # the base configuration file if not overridden by the theme base configuration file
- $app/config/laws/$viewId.conf.user.php                    # the overriding configuration file if not overridden by the theme overriding configuration file
- $app/config/laws/theme/$themeName/$viewId.conf.php        # the theme base configuration file
- $app/config/laws/theme/$themeName/$viewId.conf.user.php   # the theme overriding configuration file


// theme files
- $app/theme/$themeName/layouts/$layoutName/$layoutVariationName.tpl.php        # the layout's template file    
- $app/theme/$themeName/widgets/$widgetName/$widgetVariationName.tpl.php        # the widget's template file    
- $app/theme/$themeName/includes/$includePath                                   # path to an included file    

// webserver
- $app/www/theme/$themeName             # directory containing the assets owned/used by your theme












