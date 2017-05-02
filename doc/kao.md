Kamille Assets Organization
===============================
2017-05-02



The goal of this document is to define the recommended location for every asset in a kamille application.






Provider    |   Type of asset         |       Location                                                          |       Loaded via        
----------- | ----------------------- | ----------------------------------------------------------------------- | -------------------------
Module      |  js specific lib        |  /modules/$ModuleName                                                   |  HtmlPageHelper::js("/your/js/your.js");
Widget      |  css sheet              |  /theme/$themeName/widgets/$widgetName/$widgetName.$variationId.css     |  HtmlPageHelper::js("/your/js/your.js");
  
  
  
