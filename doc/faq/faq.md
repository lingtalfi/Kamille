FAQ
=============
2017-04-20




- [FAQ](#faq)
- [Developers](#developers)
  * [How to access application dir?](#how-to-access-application-dir-)
  * [How to create links?](#how-to-create-links-)
  * [How to create uri?](#how-to-create-uri-)
  * [Whether or not to show the debug trace?](#whether-or-not-to-show-the-debug-trace-)
  * [Pack the module/widget you are currently working on](#pack-the-module-widget-you-are-currently-working-on)
  * [Routsy route identifier to uri?](#routsy-route-identifier-to-uri-)
  * [Application logs](#application-logs)
  
  
<small><i><a href='http://ecotrust-canada.github.io/markdown-toc/'>Table of contents generated with markdown-toc</a></i></small>
  
  
  
  
Developers
==============






How to access application dir?
------------------------

```php
$appDir = ApplicationParameters::get("app_dir");
```




How to create links?
------------------------

Using the routsy system, you can use the ApplicationLinkGenerator service, like this:

```php 
ApplicationLinkGenerator::getUri("Core_myRouteId5", [
      'dynamic' => 46,
]);
```

How to create uri?
------------------------

You can use the Z helper for that.
The following examples demonstrate how to use it.

```php 
// the url is: http://kaminos/no.php?pou=6


a(Z::uri()); // same uri, no query string                                       /no.php
a(Z::uri(null, [], false)); // same uri, with query string                      /no.php?pou=6
a(Z::uri(null, ["doo" => 7], false)); // same uri, merging params               /no.php?pou=6&doo=7
a(Z::uri(null, ["doo" => 7], true)); // same uri, replacing params              /no.php?doo=7
a(Z::uri(null, ["doo" => 7], true, true)); // prefix with host                  http://kaminos/no.php?doo=7
a(Z::uri("/myown", ['foo'], true, true));  // own uri                           http://kaminos/myown?0=foo
```


Whether or not to show the debug trace?
---------------------------

```php
if (true === XConfig::get("Core.showExceptionTrace")) {
    XLog::trace("$e");
}

```


Pack the module/widget you are currently working on
------------------------

Use the **kpack** alias to pack a module, a **kwpack** for a widget.

The aliases looks like this on my computer:

```bash
alias kpack='php -f /mytasks/kamille/pack-modules.php'
alias kwpack='php -f /mytasks/kamille/pack-widgets.php'
```

See those tasks for more info: https://github.com/lingtalfi/task-manager/tree/master/tasks/ling-personal-tasks/kamille


Routsy route identifier to uri?
---------------------------------

```php
$uri = RoutsyUtil::routeIdentifierToUri($routeIdentifier);
```


Application logs
---------------------------------

Use my klog alias.

```php
alias klog='tail -f -n 100 /myphp/kaminos/app/logs/kamille.log.txt'
```
