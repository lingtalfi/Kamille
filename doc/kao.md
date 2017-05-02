Kamille Assets Organization
===============================
2017-05-02



The goal of this document is to define the recommended location for every asset in a kamille application.



The goal is to provide such an array:

Provider    |   Type of asset         |       Location                                                          |       Loaded via        
----------- | ----------------------- | ----------------------------------------------------------------------- | -------------------------
Module      |  js specific lib        |  /modules/$ModuleName                                                   |  HtmlPageHelper::js("/your/js/your.js");
Widget      |  css sheet              |  /theme/$themeName/widgets/$widgetName/$widgetName.$variationId.css     |  HtmlPageHelper::js("/your/js/your.js");
...         |  ...                    |  ...                                                                    |  ...
  
  
  
Module
=============

Js specific lib
------------------------

### Locations

- /modules/$ModuleName/$fileName.js
    - if the file isn't specific to a theme
- /theme/$themeName/modules/$ModuleName/$fileName.js
    - if the file is specific to a theme
    
    
### Usually loaded via

```php
HtmlPageHelper::js("/your/js/your.js");
```
    
    
Widget
=============

Css stylesheet
------------------------

### Locations

- /theme/$themeName/widgets/$widgetName/$widgetName.$variationId.css
    - this is the law location
    
### Usually loaded via

Automatically using the Core.useCssAutoload option,
which is recognized at the LawsUtil service level.

    




