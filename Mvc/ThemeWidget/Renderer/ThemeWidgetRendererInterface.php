<?php


namespace Kamille\Mvc\ThemeWidget\Renderer;


interface ThemeWidgetRendererInterface
{

    public function setModel(array $model);

    public function render();
}