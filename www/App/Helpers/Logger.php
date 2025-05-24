<?php

namespace App\Helpers;

class Logger
{

    const DS = DIRECTORY_SEPARATOR;

    public function __construct()
    {
        $this->createFile();
    }

    private function createFile()
    {
        if (!file_exists(__DIR__ . "/../Logs")) {
            mkdir(__DIR__ . "/../Logs");
        }
    }

    public static function info($message)
    {
        self::addRecord(self::validateMessage($message, "INFO"));
    }

    public static function warning($message)
    {
        self::addRecord(self::validateMessage($message, "WARNING"));
    }

    public static function error($message)
    {
        self::addRecord(self::validateMessage($message, "ERROR"));
    }

    private static function validateMessage($message, $level)
    {
        if (empty($message)) {
            return false;
        }

        $data = "";
        if (!empty($params)) {
            $data = join(", ");
            $message . $data;
        }

        $date = date('Y-m-d H:i:s');

        $msg = sprintf("[%s] [%s]: %s %s", $date, $level, $message, PHP_EOL);

        return $msg;
    }

    private static function addRecord($message)
    {
        $filename = __DIR__ . "/../Logs/challenge-montink.txt";

        file_put_contents($filename, $message, FILE_APPEND);
    }
}
