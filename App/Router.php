<?php

namespace App;

class Router
{
    public const massUrl = [
        "/" => [\App\Controllers\ControllerSpotify::class, 'index'],
        "/getCode/" => [\App\Controllers\ControllerSpotify::class, 'getCode'],
        "/shuffle/" => [\App\Controllers\ControllerSpotify::class, 'shuffleTracks'],
    ];

    public function checkUrl()
    {
        $url = $_SERVER['REQUEST_URI'];
        $url = explode('?', $url);
        $url = $url[0];

        if(isset(self::massUrl[$url]))
        {
            $controllerClass = self::massUrl[$url][0];
            $method = self::massUrl[$url][1];
            $controller = new $controllerClass();
            echo $controller->$method();
        }
        else
        {
            $constrError = new \App\Controllers\ControllerError404();
            echo $constrError->inputError();
        }
    }
}