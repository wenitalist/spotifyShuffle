<?php

namespace App\Controllers;

abstract class BasicController
{
    public function render(string $view, array $forRender = []): string
    {
        $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../../Views');
        $twig = new \Twig\Environment($loader);
        return $twig->load($view)->render($forRender);
    }

    public function clearMessagesSession()
    {

    }
}