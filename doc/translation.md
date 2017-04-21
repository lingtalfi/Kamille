Kamille translation
======================
2017-04-09



In kamille, the default translation mechanism is located in:

- app/functions/main-functions.php


The two relevant functions are double underscore (__) and triple underscore (___),
and they alone handle the default translation mechanism in kamille.


What's the default translation mechanism in kamille
======================================

You create translation files in the lang directory, using any sub-organization you want,
and the file must end with the ".trans.php" extension.


By convention, we try to organize the translation files by the type of the item that provide them
(modules, widgets, controllers, ...), then followed by the item name.



For instance:

```php
- app
----- lang
--------- en
------------- widgets
----------------- MyWidget
--------------------- MyWidget.trans.php
--------- fr
------------- common
----------------- form.trans.php
------------- widgets
----------------- MyWidget
--------------------- MyWidget.trans.php
------------- controllers
----------------- Authenticate
--------------------- AuthenticateController.trans.php
------------- modules
----------------- ModuleOne
--------------------- ModuleOne.trans.php


```

There is also the special common directory, as you can see above.
By convention, this directory is reserved for messages that might be re-used by more than one module.
A kamille application will provide default messages that you might want to check.
You can also add your own if you want.

It's recommended that you encapsulate your message identifiers and translations with double quotes
exclusively (not single quotes), so that it's easier to build tool to manipulate those files later.

You've been warned, use single quotes at your own risks!






Inside the translation file, you just create a $defs variable containing your definitions,
like so (example from the loginForm widget):

```php 
<?php


$defs = [
    'loginForm' => "Login Form",
    'username' => "Username",
    'password' => "Password",
    'submit' => "Log in",
    'passwordLost' => "Lost your password?",
    'createAccount' => "Create Account",
];
```


That's it.
If something wrong happens, you will be able to look at the errors in the log,
and the string won't be translated, but it will never stop your application.






