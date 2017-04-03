<?php

namespace Ken\View\TwigEngine;

use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_SimpleFunction;
use Ken\View\BaseEngine;

/**
 * @author Juliardi <ardi93@gmail.com>
 */
class TwigEngine extends BaseEngine
{
    /**
     * @var Twig_Environment
     */
    protected $engine;

    /**
     * @var \Ken\TwigEngine\FunctionGenerator
     */
    protected $functionGenerator;

    public function __construct($config)
    {
        parent::__construct($config);
        if (!isset($config['functionGenerator'])) {
            $config['functionGenerator'] = __NAMESPACE__.'\FunctionGenerator';
        }
        $this->initFunctionGenerator($config['functionGenerator']);
        $this->initEngine();
    }

    /**
     * Inits custom function generator.
     */
    protected function initFunctionGenerator($generatorClass)
    {
        $this->functionGenerator = $generatorClass::build();
    }

    /**
     * {@inheritdoc}
     */
    protected function initEngine()
    {
        $loader = new Twig_Loader_Filesystem($this->viewPath);

        if (!empty($this->cachePath)) {
            $this->engine = new Twig_Environment($loader, array(
                'cache' => $this->cachePath,
            ));
        } else {
            $this->engine = new Twig_Environment($loader);
        }

        $this->registerCustomFunctions();
    }

    /**
     * Registers custom functions.
     */
    protected function registerCustomFunctions()
    {
        $functionList = $this->functionGenerator->getFunctionList();

        foreach ($functionList as $function) {
            if (isset($function['name']) && isset($function['callable'])) {
                $twigFunction = new Twig_SimpleFunction($function['name'], $function['callable']);
                $this->engine->addFunction($twigFunction);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function render($view, array $params = [])
    {
        $view = $this->suffixExtension($view);

        echo $this->engine->render($view, $params);
    }

    /**
     * {@inheritdoc}
     */
    protected function getFileExtension()
    {
        return 'twig';
    }
}
