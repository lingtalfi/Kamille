<?php


namespace Mvc\Layout;


use Mvc\Loader\LoaderInterface;
use Mvc\Renderer\LayoutRendererInterface;
use Mvc\Renderer\RendererInterface;
use Mvc\Widget\WidgetInterface;

/**
 * In this implementation, we use the following pattern:
 * https://github.com/lingtalfi/loader-renderer-pattern/blob/master/loader-renderer.pattern.md
 */
class Layout implements LayoutInterface
{
    private $templateName;
    private $widgets;

    /**
     * @var LoaderInterface
     */
    private $loader;

    /**
     * @var RendererInterface
     */
    private $renderer;

    public function __construct()
    {
        $this->widgets = [];
    }


    public function setTemplate($templateName)
    {
        $this->templateName = $templateName;
        return $this;
    }

    public function bindWidget($name, WidgetInterface $widget)
    {
        $this->widgets[$name] = $widget;
        return $this;
    }



    public function render(array $variables)
    {

        $uninterpretedTemplate = $this->loader->load($this->templateName);
        if (false !== $uninterpretedTemplate) {
            $this->prepareVariables($variables);

            if ($this->renderer instanceof LayoutRendererInterface) {
                $this->renderer->setLayout($this);
            }

            $renderedTemplate = $this->renderer->render($uninterpretedTemplate, $variables);
            $this->onRenderedTemplateReady($renderedTemplate);
            return $renderedTemplate;
        }
        return $this->onLoaderFailed($this->templateName);
    }



    //--------------------------------------------
    //
    //--------------------------------------------
    public function setLoader(LoaderInterface $loader)
    {
        $this->loader = $loader;
        return $this;
    }

    public function setRenderer(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }
    //--------------------------------------------
    //
    //--------------------------------------------
    /**
     * This method is an opportunity to return the uninterpreted content (or do something else), in
     * case the loader failed.
     *
     * @return string, the fallback uninterpreted content
     */
    protected function onLoaderFailed($templateName)
    {
        return "";
    }


    /**
     * This is the opportunity to decorate the renderered template.
     * Note: this method is experimental, I did not need it concretely.
     */
    protected function onRenderedTemplateReady(&$renderedTemplate)
    {

    }


    protected function prepareVariables(array &$variables)
    {

    }


}