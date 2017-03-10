<?php


namespace Mvc\Renderer;


interface RendererInterface
{

    public function render($uninterpretedContent, array $variables);
}