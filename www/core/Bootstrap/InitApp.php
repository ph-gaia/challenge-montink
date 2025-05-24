<?php

namespace Core\Bootstrap;

use Core\RouterSystem\Router;
use Core\Interfaces\ControllerInterface;
use Exception;

class InitApp
{
    private Router $router;
    private string $controller;
    private string $action;
    private array $parameters = [];
    private array $executionParts = [];

    public function __construct()
    {
        $this->router = new Router();
        $this->initialize();
    }

    /**
     * Initialize the application by processing the route
     * @return void
     * @throws Exception
     */
    private function initialize(): void
    {
        $this->processExecutionParts()
            ->validateController()
            ->validateAction()
            ->setParameters();
    }

    /**
     * Set the parameters from the router
     * @return self
     */
    private function setParameters(): self
    {
        $this->parameters = $this->router->getParameters();
        return $this;
    }

    /**
     * Process the execution parts from the router
     * @return self
     */
    private function processExecutionParts(): self
    {
        $this->executionParts = explode('@', $this->router->getExec());
        return $this;
    }

    /**
     * Validate the controller from the route
     * @return self
     * @throws Exception
     */
    private function validateController(): self
    {
        if (!isset($this->executionParts[0])) {
            throw new Exception("Controller is not defined in the route registration.");
        }

        $controllerClass = '\App\Controllers\\' . $this->executionParts[0];
        if (!class_exists($controllerClass)) {
            throw new Exception("Controller class not found: {$controllerClass}");
        }

        $this->controller = $this->executionParts[0];
        return $this;
    }

    /**
     * Validate the action from the route
     * @return self
     */
    private function validateAction(): self
    {
        $this->action = isset($this->executionParts[1]) ? $this->executionParts[1] : 'index';
        return $this;
    }

    /**
     * Check if the controller implements the required interface
     * @param ControllerInterface $controller
     * @return self
     * @throws Exception
     */
    private function validateControllerInterface(ControllerInterface $controller): self
    {
        if (!$controller instanceof ControllerInterface) {
            throw new Exception(
                'Invalid Controller. All controllers must implement the ControllerInterface'
            );
        }
        return $this;
    }

    /**
     * Check if the action method exists in the controller
     * @param ControllerInterface $controller
     * @return self
     * @throws Exception
     */
    private function validateActionMethod(ControllerInterface $controller): self
    {
        if (!method_exists($controller, $this->action)) {
            throw new Exception(
                "Invalid Action. The action '{$this->action}' does not exist in controller " .
                '\App\Controllers\\' . $this->controller
            );
        }
        return $this;
    }

    /**
     * Run the application
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        $controllerClass = '\App\Controllers\\' . $this->controller;
        $controller = new $controllerClass($this->parameters);

        $this->validateControllerInterface($controller)
             ->validateActionMethod($controller);

        $action = $this->action;
        $controller->$action();
    }
}
