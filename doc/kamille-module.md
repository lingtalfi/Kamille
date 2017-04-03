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





Hooks
=========

Hooks is a way for modules to collect information from other modules.
A hook binds two modules together, one being the provider, and the other the subscriber.

There can be many subscribers modules for a given provider module.


How to
-------------

Create a $moduleName"Hooks" class at the top of your module directory.

Now to create a provider hook, create a protected static method which name follows this convention:

- $moduleName_$hookMethodName

As for now, you can only have ONE parameter (if you need one), so use it wisely (this was done for performances reasons,
and it might change based on pragmatic requirements). 
That's it.


Now to create a subscriber hook, do the same as above, but replace the moduleName with the moduleName of the module
you want to subscribe to.

That's it.



Services
=========

Services is the primary way for modules to bring functionality with the outer world.
A service could be anything, which makes the X container a swiss army knife for the developers. 




How to
-------------

Create a $moduleName"Services" class at the top of your module directory.

Now to create a service, create a protected static method which name follows this convention:

- $moduleName_$hookMethodName

As for now, services don't accept argument (this was done for simplicity reasons),
but it might change based on pragmatic requirements). 
That's it.




Files
=========

A module might provide files to the application (web assets, email templates, and so on...).
Rather than copying the files manually, you can use the KamilleModule feature for files.


How to
-------------

Create a files directory at the top of your module directory.

Then create an app file inside your files directory; this app files represents the application:
every file within that directory will be mapped exactly as is in the application.


So for instance, if you create this file at the top of your module's directory:

- files/app/doo/doo.txt

Then upon your module installation, the following file will be created:

- /app/doo/doo.txt


Files are removed when you uninstall the module.





