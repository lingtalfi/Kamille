Template and rendering
=======================
2017-08-03



The goal of a template is to display a given model.


Sometimes, a template is re-used at multiple locations.

For instance imagine you are creating an e-commerce website, then you have to display some product list
at different locations.


When this happens, you should factorize your template code in classes (template also can use classes),
so that:

- the next time you need to display a product list you just call a method (time saver)
- if you want to upgrade your model later on, you can change just the class and it will 
        update every instance at once 
        
        
Those are the obvious benefits of centralization, hopefully you didn't learn anything new here.




Renderers could be provided by the modules (class-modules directory), or by the maintainer (class).



Models are provided by module authors.


A Renderer is an object that has a render method, which takes a model as its argument 
and returns an html code to display as its output.

At the kamille level, there is a TemplateRendererInterface, that you can use if it makes you feel safer.


The css style sheets could be put in css/elements if it helps visualizing the file structure.


So the right question will be: will your template be RE-USED?        



