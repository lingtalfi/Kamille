Services
===========
2017-08-03



Service container is one of the backbone of the kamille framework.


Services are brought by modules,
and the user can configure only what the module author allows her to configure.

The module configuration file is where the services params reside.


However, this is not recommended to use this technique, because even if it's lazily loaded,
the module configuration is a php array loaded in memory, and if every module loads even a small array,
at the end of the page load, depending on the number of loaded modules, we could potentially have
a noticeable memory consumption impact.


This can be avoided if the user overrides the module service directly.

When we install a module, the installer takes the snapshot of every module service and copy them
into the container.

This method is optimal because it's lazy, and so it just consume the memory it needs at the execution moment.

The idea is to have a directory in the application where the user can replace the module's default service
entirely.

Then, upon installation (or re-installation), the installer would take the user service snapshot instead
of the module author's snapshot, and thus with this technique we yields both benefits:

- the user can configure the service completely
- the delivery of the service is optimum in terms of perfs and memory consumption


The only drawback being that the user needs to re-install the module in order to apply the changes.


Note: as the time of writing, this technique is not implemented.


