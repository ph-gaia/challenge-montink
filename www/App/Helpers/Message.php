<?php

namespace App\Helpers;

class Message
{

    public static function showMsg($message, $type, $exit = true)
    {
        echo "<div class='alert alert-{$type} alert-dismissible' role='alert'>
            <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
                {$message}
            </div>";

        if ($exit) {
            exit;
        }
    }
}
