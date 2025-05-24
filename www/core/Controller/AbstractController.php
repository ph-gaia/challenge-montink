<?php

namespace Core\Controller;

use App\Config\ConfigApp as Config;
use stdClass;

abstract class AbstractController
{
    protected stdClass $view;
    protected string $page;
    protected array $parameters = [];

    /**
     * AbstractController constructor
     * 
     * @param array $parameters Route parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
        $this->view = new stdClass();
    }

    /**
     * Render the page with optional layout
     * 
     * @param string $page Name of the file to be rendered
     * @param bool $useLayout Whether to use a layout
     * @param string $alternativeLayout Name of the alternative layout
     * @throws \RuntimeException If layout file doesn't exist
     */
    protected function render(string $page, bool $useLayout = true, string $alternativeLayout = 'default'): void
    {
        $this->page = $page;
        $layoutFile = $this->getLayoutPath($alternativeLayout);

        if ($useLayout && !file_exists($layoutFile)) {
            throw new \RuntimeException("Layout file not found: {$layoutFile}");
        }

        if ($useLayout) {
            require_once $layoutFile;
        } else {
            echo $this->getContent();
        }
    }

    /**
     * Get the content of the current page
     * 
     * @return string The rendered content
     * @throws \RuntimeException If view file doesn't exist
     */
    public function getContent(): string
    {
        $viewFile = $this->getViewFilePath();

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View file not found: {$viewFile}");
        }

        ob_start();
        require_once $viewFile;
        return ob_get_clean();
    }

    /**
     * Get a parameter from the route
     * 
     * @param string|null $index Parameter name
     * @return mixed Parameter value or null if not found
     */
    protected function getParam(?string $index = null): mixed
    {
        if ($index === null) {
            return $this->parameters;
        }

        return $this->parameters[$index] ?? null;
    }

    /**
     * Get the current controller name
     * 
     * @return string Controller name
     */
    protected function getControllerName(): string
    {
        $currentClass = get_class($this);
        $controller = strtolower(str_replace("App\\Controllers\\", "", $currentClass));
        return str_replace("controller", "", $controller);
    }

    /**
     * Get the layout file path
     * 
     * @param string $layoutName Layout name
     * @return string Full path to layout file
     */
    private function getLayoutPath(string $layoutName): string
    {
        return getcwd() . Config::VIEWS_DIR . "Layout/{$layoutName}.php";
    }

    /**
     * Get the view file path
     * 
     * @return string Full path to view file
     */
    private function getViewFilePath(): string
    {
        $controllerName = $this->getControllerName();
        return getcwd() . Config::VIEWS_DIR . ucfirst($controllerName) . '/' . $this->page . '.php';
    }

    /**
     * Send a JSON response
     * 
     * @param mixed $data Data to be sent as JSON
     * @param int $statusCode HTTP status code
     * @return void
     */
    protected function jsonResponse($data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
