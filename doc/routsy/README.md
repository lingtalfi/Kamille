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


Routsy route identifier
==========================

A routsy route identifier is either a route id, or an array containing two
entries: the route id, and the route params.

Here is a more formal definition:

```txt
- routsyRouteIdentifier: routeId | routeIdAndParams
- routeId: string, the route id 
- routeIdAndParams: [routeId, routeParams]
- routeParams: array, the parameters for generating the route uri
```

The ApplicationLinkGenerator object is generally used in 
the kamille framework to convert the route identifier to an uri. 
