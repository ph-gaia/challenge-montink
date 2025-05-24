<?php

namespace App\Config;

class ConfigDatabase
{

    public $db = array(
        'sgbd' => 'mysql',
        'server' => 'montink-db',
        'dbname' => 'montink',
        'username' => 'root',
        'password' => 'root',
        'options' => array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"),
    );

}