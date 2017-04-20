FAQ
=============
2017-04-20



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


How to know whether or not to show the debug trace?
---------------------------

```php
if (true === XConfig::get("Core.showExceptionTrace")) {
    XLog::trace("$e");
}

```


How to pack the module you are currently working on?
------------------------

Use the **kpack** alias.

```bash
alias kpack='php -f /mytasks/kamille/pack-modules.php'
```

See those tasks for more info: https://github.com/lingtalfi/task-manager/tree/master/tasks/ling-personal-tasks/kamille



