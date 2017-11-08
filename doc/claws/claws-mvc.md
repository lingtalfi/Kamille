Claws mvc
===============
2017-10-31



Claw mvc is a redefined form of MVC.

[![claws-mvc-displaying-page.png](https://s19.postimg.org/d2wzewzwz/claws-mvc-displaying-page.png)](https://postimg.org/image/a8tu1gxqn/)


The three main components are:

- M: the model, which goal is to provide an array (sometimes called the model too, which I agree is confusing) to the controller
        The model generally communicates with some api provided by the application and/or its modules,
        and it is recommended that only the model can communicates with those apis (the other components V and C 
        shouldn't communicate with those apis).
- V: the view, which goal is to display the array passed by the controller.
        The view has the responsibility to decide every thing that relates to design and appearance.
        It has the power to translate some strings that are left over by the model.
        It is recommended that the view use not apis, but tools available to the application, like
        a translator function, or a assets loader tool (view related tools).
- C: the controller, it just branches the model's array to a template path.
        Therefore, its code should be very slim.
        
        Another thing the controller can do is pass application environment/context variables to the model.
        Note: if the model uses super php arrays directly (like $_GET, $_POST, etc), then there is no need
        for the controller to pass them to the model.
        
        In short, the controller can verify that the branching variables are ok, and potentially
        change the View depending if something unexpected occur.
         
        
        
        
        
Using the clawsMvc model promotes thin readable controllers, which makes it easier to create new pages or re-use pages.
                 
From now on, it is recommended that users of the kamille framework adopt the clawsMvc system in their applications.                
        
        

Suggested organization for your apps
----------------------------------------

- class-modules
    - $moduleName
        - Model
            - Front
                - $yourFrontModelName.php
                
- class-controllers
    - $moduleName
            - Front
                - $yourFrontControllerName.php
                
- theme
    - $someRelativePath
        - $moduleName                
            - Front                
                - $whatever.tpl.php                
                
