Kamille Assets Organization
===============================
2017-05-02



The goal of this document is to define the recommended location for every asset in a kamille application.





This can also be better summarized with the following schema:
  
```txt
- app/www
----- theme/$themeName/
--------- controllers/$ControllerName.js                                    # init code for a specific controller, provided by the core Core_lazyJsInit service, used by the LawsUtil service
--------- layouts/$layoutName/$layoutName.$variationId.css                  # see laws
--------- widgets/$WidgetName/$WidgetName.$variationId.css                  # see laws
----- modules/$ModuleName/                                                  # for non theme specific module assets
```  
  

  