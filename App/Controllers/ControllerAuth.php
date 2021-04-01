<?php

namespace App\Controllers;

class ControllerAuth extends BasicController
{
    public function authorization()
    {
        /*$_SESSION['token'] = null;

        $client_id = 'a0ae2c9ce97e4fb09acfca1fc0c7aef2';
        $client_secret = 'fd959446bb4a41d894001095dc653468';

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
        echo 1;
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