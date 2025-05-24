<?php
namespace Core\RouterSystem;

use Core\RouterSystem\RouteSystemConfig;
use Core\RouterSystem\RouteMap;
use App\Route\Route;
use Core\Http\Header;

class Router
{
    private RouteSystemConfig $routeConfig;
    private Route $route;
    private ?string $exec = null;
    private array $parameters = [];

    public function __construct()
    {
        $this->routeConfig = new RouteSystemConfig();
        $routeMap = new RouteMap();
        $this->route = new Route($routeMap);
        $this->route->register();

        $this->findRoute();
    }

    /**
     * Finds and validates the requested route
     * 
     * @throws \RuntimeException When route is not found
     */
    private function findRoute(): void
    {
        $method = $this->routeConfig->getMethod();
        $uriParts = $this->routeConfig->getUriParts();
        $routeMap = $this->route->getRouteMap()->getRouteMap();

        $requestedRouteLength = count($uriParts);

        if (!isset($routeMap[$method])) {
            $this->send404Response();
        }

        if (!isset($routeMap[$method][$requestedRouteLength])) {
            $this->send404Response();
        }

        foreach ($routeMap[$method][$requestedRouteLength] as $route) {
            if (empty(array_diff_assoc($route['routeParts'], $uriParts))) {
                $this->exec = $route['exec'];
                break;
            }

            if ($this->hasParameters($route['route'])) {
                if ($this->validateParameters($route, $uriParts)) {
                    $this->exec = $route['exec'];
                }
            }
        }

        if (!$this->exec) {
            $this->send404Response();
        }
    }

    /**
     * Checks if the route contains parameters
     */
    private function hasParameters(string $route): bool
    {
        return str_contains($route, '{');
    }

    /**
     * Validates the parameters from the URI against the route rules
     */
    private function validateParameters(array $route, array $uriParts): bool
    {
        $routeParts = $route['routeParts'];

        for ($i = 0; $i < count($routeParts); $i++) {
            if ($this->hasParameters($routeParts[$i])) {
                $parameterName = $this->extractParameterName($routeParts[$i]);
                $matches = [];

                if (!preg_match($route['rules'][$parameterName], $uriParts[$i], $matches)) {
                    return false;
                }

                $this->parameters[$parameterName] = $uriParts[$i];
                continue;
            }

            if ($routeParts[$i] !== $uriParts[$i]) {
                return false;
            }
        }

        return true;
    }

    /**
     * Extracts the parameter name from the route definition
     */
    private function extractParameterName(string $value): string
    {
        return trim($value, '{}');
    }

    /**
     * Sends a 404 response
     */
    private function send404Response(): void
    {
        $header = new Header();
        $header->error404();
    }

    /**
     * Returns the controller and action to be executed
     */
    public function getExec(): ?string
    {
        return $this->exec;
    }

    /**
     * Returns the parameters passed in the URI
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
