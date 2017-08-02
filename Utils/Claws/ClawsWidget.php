<?php

namespace Kamille\Utils\Claws;


use Kamille\Utils\Claws\Exception\ClawsException;

class ClawsWidget
{

    private $template;
    private $conf;
    private $class;

    public function __construct()
    {
        $this->conf = [];
        $this->class = "Kamille\Mvc\Widget\Widget";
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * @return array
     */
    public function getConf()
    {
        return $this->conf;
    }

    public function setConf(array $conf)
    {
        $this->conf = $conf;
        return $this;
    }

    public function setConfVariable($key, $value)
    {
        $this->conf[$key] = $value;
        return $this;
    }

    public function removeConfVariable($key)
    {
        unset($this->conf[$key]);
        return $this;
    }

    public function getConfVariable($key, $default = null, $throwEx = false)
    {
        if (array_key_exists($key, $this->conf)) {
            return $this->conf[$key];
        }
        if (true === $throwEx) {
            throw new ClawsException("Undefined conf variable: $key");
        }
        return $default;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }


}