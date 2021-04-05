<?php

namespace App;

class Router
{
    public const massUrl = [
        "/" => [\App\Controllers\ControllerSpotify::class, 'index'],
        "/getCode/" => [\App\Controllers\ControllerSpotify::class, 'getCode'],
        "/getToken/" => [\App\Controllers\ControllerSpotify::class, 'getToken'],
        "/shuffle/" => [\App\Controllers\ControllerSpotify::class, 'getTracks'],
        "/add/" => [\App\Controllers\ControllerSpotify::class, 'addTracks'],
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