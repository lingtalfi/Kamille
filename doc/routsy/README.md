Routsy
============
2017-04-19



Another routing system.




Goals
========

- have all routes configurable by the user, and centralized in one location 
- the uris can be chosen by the user without affecting the logic of the application behind, so can be chosen the controllers  
- module authors can create links without affecting the users choices  




Structure
===============

```php
- app
----- class-modules
--------- $moduleName
------------- routsy
----------------- conf.php
----- config
--------- routsy
------------- routes.php  
```
