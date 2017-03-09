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

Otherwise, we could just inspect/inject the right parameters to the controller, like in symfony,
as it saves some time for the developer and it does not really eat a lot of computer power.
However, I will not implement that, because I'm not sure if it's the right thing to do.

Let me explain.

So, the Request comes into the application.
As said in kam doc, php provides all those super arrays: $_GET, $_POST, $_FILES, $_COOKIE,
$_SESSION.

In kam, we don't attach them to the request, but we might agree on the fact that semantically, 
at least $_GET, $_POST and $_FILES could be part of the request.

In addition to that, the Router adds its own params to the request: let's call those parameters
the urlParams, since they are guessed from the url (but are different from the $_GET).

The urlParams can be thought as params that help making an url prettier.

My doubt is this: if the controller has some params, from which array should they be taken?

- urlParams?
- why not a merged get, post, files, urlParams?
- why not a merged get, post, urlParams?
- why not all params from the request?

Do you see why I'm not sure about auto-injecting params in the controller? That's because I don't know
from which array they should come, and I don't want to induce a conception error that later could be revealed
as a flaw (and everybody using this framework would have to pay my conception error).

So, let the lazy work a little on this one, I will stay neutral: a controller doesn't receive any parameter.
End of the discussion.

Super arrays are easily accessible ($_GET, $_POST, ..., plus the $_SESSION and $_COOKIE, and even $_SERVER).
Now the only missing variables with this approach are the urlParams.

Well, they will be accessible via the WebApplication.

I will make the WebApplication a singleton, since I believe that only one instance of an application should
be available at any moment during a request life cycle.

And, before the request is dispatched, I will say that the Request is always attached to the WebApplication,
so that any object from that point (request listener, controllers, and almost every object in fact) can simply
access the request by doing something like:

```php
$req = WebApplication::inst()->get('request'); 
```

Or this, with a helper, to get auto-completion for the rest of the code (assumging your ide does it):

```php
$req = Z::request(); // returns a HttpRequestInterface 
```





But then I asked myself: rather than passing no arguments to the controller, wouldn't it be better to pass the request?
And the answer is no.
That's because we use super arrays ($_GET, $_POST), and so in this case passing the request is only useful to access
the urlParams, but not everybody wants pretty url. I often prefer "ugly" urls for small internal projects, because it's
faster to develop, and nobody cares about the url (in those kind of projects). So, no: YOU DON'T PASS ANYTHING.

--A CONTROLLER IS JUST A CALLABLE WITHOUT ARGUMENTS, WHICH RETURNS A RESPONSE--



Note: concretely, here is what I get inside a test Controller:

 
```php
$page = Z::getUrlParam('page', null, true); 
```

I created a Z class (in class/Ling) which I intend to use as my goto helper class.
Basically, I try to reduce common developer tasks to one line in this class.




So, now a global picture starts to emerge:

- the router requestListener chooses the Controller and prepares it
- the controller requestListener execute the Controller


One might wonder why we need to separate those request listeners (why not combine them in one)?

That's because if I change my mind and say that a Controller could be set as a string instead of a callable, I can:
I just need to change the convention, not the system. In other words, I anticipate some evolution of the object.


So, again, here is how it works in kamille:

- Router (RequestListener):   Request.controller, ?Request.urlParams
- Controller (RequestListener):   Request.response (uses the controller previously set to generate the response)
- Response (RequestListener):   just execute the response previously set






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
        
        
