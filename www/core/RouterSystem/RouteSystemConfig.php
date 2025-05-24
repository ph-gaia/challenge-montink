<?php

namespace Core\RouterSystem;

class RouteSystemConfig
{

    private $uriRaw;

    private $method;

    private $uriParts;

    public function __construct()
    {
        $this->setUriRaw()
            ->setMethod()
            ->handleUri();
    }

    /**
     * Obtem o valor de REQUEST_URI
     * @return $this
     */
    private function setUriRaw(): self
    {
        $this->uriRaw = $_SERVER['REQUEST_URI'];
        return $this;
    }

    /**
     * Retorna o valor de uriRaw
     * @return string
     */
    public function getUriRaw(): string
    {
        return $this->uriRaw;
    }

    /**
     * Obtem o valor de REQUEST_METHOD
     * @return $this
     */
    private function setMethod(): self
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        return $this;
    }

    /**
     * Returns the HTTP method of the request
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    private function handleUri(): self
    {
        $this->uriParts = explode('/', $this->uriRaw);
        return $this;
    }

    public function getUriParts(): array
    {
        return empty($this->uriParts) ? ['index'] : $this->uriParts;
    }
}
