<?php

/**
 * Base da interface com as bibliotecas de conexao
 */
namespace Core\Database\SGBD;

abstract class SGBDAbstract
{

    protected $config;
    protected $connection;

    abstract public function run();

    abstract protected function rules();

    abstract protected function connect();

    abstract public function getConnection();
}