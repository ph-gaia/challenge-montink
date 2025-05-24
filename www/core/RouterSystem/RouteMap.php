<?php

namespace Core\RouterSystem;

use Exception;

class RouteMap
{
    /**
     * Route map storage
     * Structure: HTTP_METHOD => [ROUTE_LENGTH => [ROUTE_DEFINITIONS]]
     */
    private array $routeMap = [];

    /**
     * Validates the route definition structure
     * 
     * @param array $routeDefinition Route definition array
     * @return self
     * @throws Exception
     */
    private function validateRouteStructure(array $routeDefinition): self
    {
        if (count($routeDefinition) !== 3) {
            throw new Exception("Invalid route definition structure");
        }

        return $this;
    }

    /**
     * Registers a GET route
     * 
     * @param array $routeDefinition [route, controller@action, rules]
     * @return self
     */
    public function get(array $routeDefinition): self
    {
        return $this->registerRoute('GET', $routeDefinition);
    }

    /**
     * Registers a POST route
     * 
     * @param array $routeDefinition [route, controller@action, rules]
     * @return self
     */
    public function post(array $routeDefinition): self
    {
        return $this->registerRoute('POST', $routeDefinition);
    }

    /**
     * Registers a PUT route
     * 
     * @param array $routeDefinition [route, controller@action, rules]
     * @return self
     */
    public function put(array $routeDefinition): self
    {
        return $this->registerRoute('PUT', $routeDefinition);
    }

    /**
     * Registers a DELETE route
     * 
     * @param array $routeDefinition [route, controller@action, rules]
     * @return self
     */
    public function delete(array $routeDefinition): self
    {
        return $this->registerRoute('DELETE', $routeDefinition);
    }

    /**
     * Registers a route for the specified HTTP method
     * 
     * @param string $method HTTP method
     * @param array $routeDefinition Route definition
     * @return self
     */
    private function registerRoute(string $method, array $routeDefinition): self
    {
        $this->validateRouteStructure($routeDefinition);
        $routeParts = explode('/', $routeDefinition[0]);
        
        $this->routeMap[$method][count($routeParts)][] = $this->createRouteProperties($routeDefinition, $routeParts);
        
        return $this;
    }

    /**
     * Creates the route properties array
     * 
     * @param array $routeDefinition Route definition
     * @param array $routeParts Exploded route parts
     * @return array Route properties
     */
    private function createRouteProperties(array $routeDefinition, array $routeParts): array
    {
        return [
            'route' => $routeDefinition[0],
            'exec' => $routeDefinition[1],
            'rules' => $routeDefinition[2],
            'routeParts' => $routeParts
        ];
    }

    /**
     * Returns the complete route map
     * 
     * @return array Route map
     */
    public function getRouteMap(): array
    {
        return $this->routeMap;
    }
}
