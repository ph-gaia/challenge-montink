#!/usr/bin/php
<?php
require_once __DIR__ . '/vendor/autoload.php';

$import = new Setup();
$db = new App\Config\ConfigDatabase();
$import->run();


class Setup
{

    private $connection;
    const PATH_DUMP_SQL = __DIR__ . '/public/dump.sql';

    public function run()
    {
        $this->connectDatabase();
        $this->createDataBase();
    }

    /**
     * Try creating the database schemas according the dump.sql file
     * @throws \Exception
     */
    private function createDataBase()
    {
        try {
            $sqlFile = file_get_contents(self::PATH_DUMP_SQL);
            $this->connection->pdo()->exec($sqlFile);
            echo '> Banco de Dados criado com sucesso' . PHP_EOL;
        } catch (\PDOException $ex) {
            throw new \Exception(""
                . "Não foi possível executar o dump.sql" . PHP_EOL
                . "Log:" . $ex->getMessage()
                . "" . PHP_EOL);
        }
    }

    /**
     * Try connect with database and returns the connection reference
     * @return \PDO
     * @throws \Exception
     */
    private function connectDatabase()
    {
        try {
            if (!$this->connection) {
                $this->connection = new Core\Database\ModelAbstract;
            }
            return $this->connection;
        } catch (\Exception $ex) {
            throw new \Exception(""
                . "ERRO!"
                . PHP_EOL
                . "Não foi possível connectar ao banco de dados"
                . PHP_EOL
                . $ex->getMessage()
                . PHP_EOL
                . "" . PHP_EOL);
        }
    }
};
