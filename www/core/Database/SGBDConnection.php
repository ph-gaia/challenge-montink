<?php

namespace Core\Database;

use App\Config\ConfigDatabase;
use Core\Database\SGBD\SGBDAbstract;

class SGBDConnection
{

    private $config;
    private $instance;

    public function __construct()
    {
        $configDatabase = new ConfigDatabase();

        $this->config = $configDatabase->db;
        $this->rules();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    private function rules()
    {
        if (!isset($this->config['sgbd'])) {
            throw new \Exception("It was not possible to detect the SGBD. "
            . "Check the database connection configuration file.");
        }

        $className = "\Core\Database\SGBD\\" . ucfirst(strtolower($this->config['sgbd']));

        if (!class_exists($className)) {
            throw new \Exception("It was not possible to find the class {$className}. "
            . "Check the database connection configuration file.");
        }

        $this->instance = new $className($this->config);

        if (!($this->instance instanceof SGBDAbstract)) {
            throw new \Exception("The class {$this->config['sgbd']} is not a child of SGBDAbstract. "
            . "Check the database connection configuration file.");
        }

        return $this;
    }

    private function run()
    {
        $this->instance->run();
    }

    final public function getConnection()
    {
        $this->run();

        return $this->instance->getConnection();
    }
}
