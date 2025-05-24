<?php

namespace Core\Interfaces;

interface ControllerInterface
{
    public function __construct(array $parameters);
    public function index();
}
