<?php

namespace App;

class Config {
    static public function get()
    {
        $txt = file_get_contents(__DIR__."/../.env");
        $config = [];
        foreach (explode("\n", $txt) as $row) {
            [$key, $value] = array_merge(explode("=", $row), [""]);
            if ($key == ""){ continue; }
            $config[$key] = $value;
        }
        return $config;
    }
}
