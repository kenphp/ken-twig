<?php

namespace Ken\View\TwigEngine;

use Ken\Base\Buildable;
use Ken\Helper\Url;

/**
 * This class is used to add simple function to Twig Template Engine.
 * Extends this class to add custom function.
 */
class FunctionGenerator implements Buildable
{
    protected $functionList = [];

    private function __construct()
    {
        $this->addDefaultFunction();
        $this->addCustomFunction();
    }

    /**
     * {@inheritdoc}
     */
    public static function build($config = array())
    {
        return new static();
    }

    /**
     * Adds function to twig engine.
     *
     * @param string   $functionName Function name to be called from template file
     * @param callable $callback     Callback function to be called
     */
    public function addFunction($functionName, $callback)
    {
        if (is_string($functionName) && is_callable($callback)) {
            $functions = [
                'name' => $functionName,
                'callable' => $callback,
            ];

            array_push($this->functionList, $functions);
        }
    }

    /**
     * Retrieves function list.
     *
     * @return array
     */
    public function getFunctionList()
    {
        return $this->functionList;
    }

    /**
     * Adds default function for twig template.
     */
    private function addDefaultFunction()
    {
        $this->addFunction('app', function () {
            return app();
        });

        $this->addFunction('url', function ($url, $absolute = false, array $params = array()) {
            if ($absolute) {
                return Url::createAbsolute($url, $params);
            } else {
                return Url::create($url, $params);
            }
        });

        $this->addFunction('assets', function ($path) {
            return Url::createAbsolute($path);
        });
    }

    /**
     * Adds custom function for Twig Template Engine.
     * You must extends this method to add your custom function.
     */
    protected function addCustomFunction()
    {
        // ADD YOUR CUSTOM FUNCTION HERE
    }
}
