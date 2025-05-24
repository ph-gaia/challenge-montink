<?php

namespace Core\Database;

use Core\Database\SGBDConnection;

class ModelAbstract
{

    private $sgbdConnection;

    public function __construct()
    {
        $this->sgbdConnection = new SGBDConnection();
    }

    /**
     * Reference to the PDO object
     * @return \PDO
     */
    public function pdo()
    {
        return $this->sgbdConnection->getConnection();
    }

    protected function toJson($json)
    {
        echo json_encode($json);
    }

    protected function getInput()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    protected function contentTypeJSON()
    {
        header('Content-type:application/json;charset=utf-8');
        return $this;
    }
}
