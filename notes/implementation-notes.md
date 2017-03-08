Implementation notes
=======================
2017-03-08




A few notes while implementing the kam framework.



Architecture
================

The kam framework doesn't define (purposely) precisely what a Controller is.


Using the proposed model with three RequestListeners (Router, Controller, Response),
I found out that the Router's returned controller should be a callable (exactly like in symfony).

That's because of the instantiation params that a controller might have.
Imagine the Controller was just passed as a string, and the controller has special instantiation requirements, 
then we would need to create another system to allow that controller to be instantiated correctly.
 
This problem can be easily avoided by delegating the responsibility of instantiating the controller
at the Router (RequestListener) level.

That's we will do in kamille, in order to avoid an extra layer of complexity.

Then at the Controller request listener level, if the controller is not a valid callable, 
we should throw an exception (assuming that passing the controller via the Request is the norm).

Otherwise, we can just inspect/inject the right parameters to the controller, like in symfony,
as it saves some time for the developer and it does not really eat a lot of computer power.





Services, Modules - Hooks
================
... 
Another way to see it, is that a container is an empty shell, to which modules can attach services.

Developers can also attach services by hand (but then we need to be careful not to replace those methods by automated 
processes).

By attaching, I mean that the code is injected into the class.

So the Container class, in the end, is composed of multiple methods from various modules.


In kamille, I want to test this approach/idea where the hooks are symbolized by a "Hooks" class,
which is basically the same idea as a container: an shell that is fed by modules, except for one thing:

the module author creates the method in advance, and instead of creating whole methods, the subscriber
just composes the body of the method.

It seems only natural that it's the module author's responsibility to provide a way for "hookers" (sorry if that
doesn't translate well) to hook into their modules.
 
With the power of convention, we can provide ONE generalized way of hooking into a class.

For instance, a hook class should end with the "Hooks" suffix,
and a hook must only be a "public static method".


But then we can choose between at least two models:

- do we create only ONE "Hooks" class at the application level, to which all modules would subscribe
- or does every module have the ability to provide its own "Hooks" class

The first idea has been tried with Saas (nullos admin).
Now with kamille I want to try the idea of ONE centralized Hooks class, because I believe centralization is
a good thing. Here are a few things I have notice that occur with ONE centralized Hooks class:

- easier to list all hooks
- easier to find/create hooks
- (watch out for) collisions
- multiple providers can provide the same hook (although we probably won't need this ability, it's an interesting one)




Let me share the two models with an image:


[![comparison-of-two-hooks-systems.jpg](https://s19.postimg.org/p5vyfw2ub/comparison_of_two_hooks_systems.jpg)](https://postimg.org/image/qxoxasm73/)




So, in kamille we use the centralized model.
By convention, to avoid name collision, we will use this simple technique (which might avoid most
collisions, but is not the ultimate solution):

- the methods in this "Hooks" class are "public static"
- the method names follow this scheme: ClassName_methodName
        where ClassName represents the short name of the class, and
        methodName is the method name
        
        
