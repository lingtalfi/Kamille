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


For instance:

```php
- app
----- lang
--------- en
------------- widgets
----------------- myWidget
--------------------- myWidget.trans.php
--------- fr
------------- widgets
----------------- myWidget
--------------------- myWidget.trans.php

```


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






