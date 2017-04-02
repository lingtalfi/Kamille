KamilleModule
===============
2017-04-02


A module in kamille has only two methods: install and uninstall.



Rather than implementing everything yourself, you can rely on the KamilleModule class
to help you with standard install/uninstall tasks.


To use the KamilleModule class, just make sure your module extends the KamilleModule class,
and then continue reading to understand how you can benefit of the KamilleModule features.




Features provided by KamilleModule
======================================

- make your module config parameters available via the XConfig class
- make your module provide hooks via the Hooks class
- make your module subscribe to desired hooks of the Hooks class 
- make your module inject services to the X container 
- make your module inject files into the application 




Config
=========

To access a module parameter, the recommended way is to use the XConfig class, because
it handles the case of installed/uninstalled modules (i.e. if a module is not installed,
you won't be able to access its parameters).


The XConfig class looks for files in the /app/config/modules/$module.conf.php directory.
 
So if you are creating a Hamburger module, and you call the XConfig get method, like so:

```php
XConfig::get("Hamburger.myKey");
```

then the XConfig class would look for the myKey defined in a $conf array inside 
the **/app/config/modules/Hamburger.conf.php** file
of your application.


But instead of doing that yourself, KamilleModule provides an easier way, continue reading...


How to
------------
Create a **conf.php** file at the root of your module directory.
Inside that conf file, create a $conf variable which contains all the configuration keys of your module.

That's it. 
The KamilleModule class will do the rest for you upon the installation of the module. 


