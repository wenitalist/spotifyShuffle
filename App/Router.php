<?php

namespace App;

class Router
{
    public const massUrl = [
        "/" => [\App\Controllers\ControllerAuth::class, 'authorization'],
    ];

    public function checkUrl()
    {
        if(isset(self::massUrl[$_SERVER['REQUEST_URI']]))
        {
            $controllerClass = self::massUrl[$_SERVER['REQUEST_URI']][0];
            $method = self::massUrl[$_SERVER['REQUEST_URI']][1];
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