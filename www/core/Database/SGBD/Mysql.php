<?php

/**
 * Biblioteca de Conexao com o MySQL
 */

namespace Core\Database\SGBD;

use Core\Database\SGBD\SGBDAbstract;

class Mysql extends SGBDAbstract
{

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function run()
    {
        $this->rules();
        $this->connect();
    }

    /**
     * Regras de Conexao com o banco de Dados
     * @return boolean
     * @throws \Exception
     */
    protected function rules()
    {
        if (is_array($this->config)) {

            if (empty($this->config['server'])) {
                throw new \Exception('You did not inform the server!');
            }

            if (empty($this->config['dbname'])) {
                throw new \Exception('You did not inform the database!');
            }

            if (empty($this->config['username'])) {
                throw new \Exception('You did not inform the user!');
            }

            if (!isset($this->config['password'])) {
                throw new \Exception('You did not inform the password!');
            }

            if (!isset($this->config['options']) or !is_array($this->config['options'])) {
                throw new \Exception('You did not inform the options or it is not an array, you need to inform it even if it is empty!');
            }

            return true;
        }

        throw new \Exception('Invalid database connection configuration.');
    }

    /**
     * Conecta com o Banco de Dados
     * @return boolean
     * @throws \Exception
     */
    protected function connect()
    {
        if ($this->connection) {
            return true;
        }

        try {

            $this->connection = new \PDO(
                'mysql:host=' . $this->config['server']
                    . ';dbname=' . $this->config['dbname'],
                $this->config['username'],
                $this->config['password'],
                $this->config['options']
            );
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new \Exception('Error connecting to the database. Code: ' . $e->getCode() . '! Message: ' . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
