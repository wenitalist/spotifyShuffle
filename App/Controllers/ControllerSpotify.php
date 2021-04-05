<?php

namespace App\Controllers;

class ControllerSpotify extends BasicController
{
    public function getCode() // Тут я получаю код авторизации
    {
        $url = ((!empty($_SERVER['HTTPS'])) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $result = parse_url($url);
        parse_str($result['query'], $output);
        $_SESSION['code'] = $output['code'];
        //$_SESSION['time'] = date('l jS \of F Y h:i:s A');
        header("Location: /");
    }

    public function index()
    {
        return $this->render('index.twig', ['session' => $_SESSION]);
    }

    public function getToken() // Тут я получаю токен доступа
    {
        $clientIdSecret = "a0ae2c9ce97e4fb09acfca1fc0c7aef2:fd959446bb4a41d894001095dc653468";

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://accounts.spotify.com/api/token',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Authorization: Basic ' . base64_encode($clientIdSecret)],
            CURLOPT_POSTFIELDS => http_build_query([
                'grant_type' => 'authorization_code',
                'code' => $_SESSION['code'],
                'redirect_uri' => 'http://spotify.sshkpp.ru/getCode/',
            ]),
        ]);
        $result = curl_exec($curl);
        $response = json_decode($result, true);
        $_SESSION['accessToken'] = $response['access_token']; // Токен доступа
        $_SESSION['refreshToken'] = $response['refresh_token']; // Для обновления токена
        header("Location: /");
    }

    public function getAlbums()
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.spotify.com/v1/me/tracks',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $_SESSION['accessToken']],
        ]);
        $result = curl_exec($curl);
        $response = json_decode($result, true);
        dump($response);
    }
}