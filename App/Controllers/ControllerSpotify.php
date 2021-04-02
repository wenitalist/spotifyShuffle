<?php

namespace App\Controllers;

class ControllerSpotify extends BasicController
{
    public function getCode()
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

        /*$curl = curl_init();
        curl_setopt_array( $curl, [
            CURLOPT_URL => '',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => ['client_id' => 'a0ae2c9ce97e4fb09acfca1fc0c7aef2', 'response_type' => 'code', 'redirect_uri' => 'http://spotify.sshkpp.ru/zxc'],
        ]);
        $result = curl_exec($curl);

        echo $result;*/

        /*$_SESSION['token'] = null;

        $client_id = '';
        $client_secret = '';

        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL, 'https://accounts.spotify.com/api/token' );
        curl_setopt($curl,CURLOPT_POST, true );
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true );
        curl_setopt($curl,CURLOPT_POSTFIELDS, 'grant_type=client_credentials' );
        curl_setopt($curl,CURLOPT_HTTPHEADER, array('Authorization: Basic '.base64_encode($client_id.':'.$client_secret)));
        $result = curl_exec($curl);

        $token = substr($result, 17, 83);
        $_SESSION['token'] = $token;

        return $this->render('spotify.twig', ['session' => $_SESSION]);*/
    }

    public function script()
    {
        $curl = curl_init();
        curl_setopt_array( $curl, [
            CURLOPT_URL => 'https://api.spotify.com/v1/me/episodes',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => array('Authorization: Bearer '.$_SESSION['token']),
        ]);
        $result = curl_exec($curl);

        dump($result);
    }
}