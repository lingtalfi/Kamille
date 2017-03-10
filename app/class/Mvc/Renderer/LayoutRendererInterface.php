<?php


namespace Mvc\Renderer;


use Mvc\Layout\LayoutInterface;

interface LayoutRendererInterface extends RendererInterface
{

    public function setLayout(LayoutInterface $layout);
}