<?php



namespace Kamille\Mvc\EndSnippetsCollector;


/**
 *
 * Context:
 * a modular MVC architecture where widgets have their own assets.
 *
 * In such an architecture, we can have widgets that will need to put their javascript code at the end
 * of the page, just before the body end.
 *
 * The EndSnippetsCollector acts as a medium between the widgets (or any other object) and a client object.
 *
 * The "widgets" will pass the code snippets that they want, whereas the client will retrieve those snippets
 * and display them in an appropriate manner.
 *
 */
interface EndSnippetsCollectorInterface{

}