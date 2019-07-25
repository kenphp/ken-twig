<?php

namespace Ken\View\TwigEngine;

use Twig;

use Ken\View\BaseEngine;

use Psr\Http\Message\ResponseInterface;

/**
 * @author Juliardi <ardi93@gmail.com>
 */
class TwigEngine extends BaseEngine
{
    /**
     * @var \Twig\Environment
     */
    protected $engine;

    /**
     * {@inheritdoc}
     */
    protected function initEngine()
    {
        $loader = new \Twig\Loader\FilesystemLoader($this->viewPath);

        if (!empty($this->cachePath)) {
            $this->engine = new \Twig\Environment($loader, [
                'cache' => $this->cach,
            ]);
        } else {
            $this->engine = new \Twig\Environment($loader);
        }

        $this->registerViewFunctions();
    }

    /**
     * Registers custom functions.
     */
    protected function registerViewFunctions()
    {
        foreach ($this->viewFunctions as $function) {
            if (isset($function['name']) && isset($function['callable'])) {
                if (!is_numeric($function['name']) && is_callable($function['callable'])) {
                    $twigFunction = new Twig\TwigFunction($function['name'], $function['callable']);
                    $this->engine->addFunction($twigFunction);
                }
            }
        }
    }

    /**
    * @inheritDoc
    */
    protected function getFileExtension() {
        return 'twig';
    }

    /**
     * @inheritDoc
     */
    public function render(ResponseInterface $response, $view, array $params = []) {
        $template = $this->fetch($view, $params);
        $response->getBody()->write($template);

        return $response;
    }
    /**
     * @inheritDoc
     */
    public function fetch($view, array $params = []) {
        return $this->engine->render($view, $params);
    }
}
