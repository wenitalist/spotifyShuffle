<?php

namespace App\Controllers;

class ControllerError404 extends BasicController
{
    public function inputError()
    {
        return $this->render('error404.twig', ['session' => $_SESSION]);
    }
}