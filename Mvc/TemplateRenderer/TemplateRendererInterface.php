<?php


namespace Kamille\Mvc\TemplateRenderer;


/**
 * See planets/Kamille/doc/template-and-rendering.md
 *
 */
interface TemplateRendererInterface
{

    public function render(array $model = []);

}