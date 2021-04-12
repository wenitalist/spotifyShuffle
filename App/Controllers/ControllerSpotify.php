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
        self::getToken();
        header("Location: /");
        exit();
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
    }

    public function getTracks()
    {
        $offset = 0;
        $mass = [];

        $response = json_decode(self::getTotal($offset), true);
        $total = $response['total'];

        while (count($mass) < $total) {
            $id = 0;
            while ($id < count($response['items'])) {
                $mass[] = [
                    'artists' => $response['items'][$id]['track']['artists'][0]['name'],
                    'name' => $response['items'][$id]['track']['name'],
                    'id' => $response['items'][$id]['track']['id'],
                ];
                $id++;
            }
            $offset += 50;
            $response = json_decode(self::getTotal($offset), true);
        }

        shuffle($mass);

        if (file_exists("list.json")) // Создание json файла со всеми треками
        {
            unlink("list.json"); // Удаляет файл

            $fp = fopen("list.json", "a");
            $json = json_encode($mass, JSON_PRETTY_PRINT);
            fwrite($fp, $json);
            fclose($fp);
        } else {
            $fp = fopen("list.json", "a");
            $json = json_encode($mass, JSON_PRETTY_PRINT);
            fwrite($fp, $json);
            fclose($fp);
        }

        return $mass;
    }

    public function shuffleTracks()
    {
        self::getTracks();
        if (file_exists("list.json")) {
            self::deleteTracks();
        }

        echo("<h1>Готово</h1>");
    }

    public function getTotal(int $offset) // Запрос возвращающий массив с треками
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.spotify.com/v1/me/tracks' . '?' . 'offset=' . $offset . '&' . 'limit=50',
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $_SESSION['accessToken'],
            ],
        ]);
        return $result = curl_exec($curl);
    }

    public function deleteTracks() // Тут я удаляю все любимые треки
    {
        $json = file_get_contents("list.json");
        $mass = json_decode($json, TRUE);

        $query = 'https://api.spotify.com/v1/me/tracks?ids=';
        $counter = 0;
        $id = 0;
        $count = count($mass);

        foreach ($mass as $row) // Цикл для удаления всех любимых треков по 50 за раз
        {
            $query .= $row['id'] . ',';
            $id++;

            if ($id == 50)
            {
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $query,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'DELETE',
                    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $_SESSION['accessToken'],],
                ]);
                curl_exec($curl);
                $id = 0;
                $query = 'https://api.spotify.com/v1/me/tracks?ids=';
            }
            $counter++;
        }
        if ($count == $counter and $query != 'https://api.spotify.com/v1/me/tracks?ids=') // Если осталось удалить треков меньше 50
        {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $query,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'DELETE',
                CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $_SESSION['accessToken'],],
            ]);
            curl_exec($curl);
            $query = 'https://api.spotify.com/v1/me/tracks?ids=';
        }

        self::addTracks(); // После удаления всех треков, запускается функция добавления перемешанных треков
    }

    public function addTracks() // Тут я добавляю треки в "Любимую музыку"
    {
        //set_time_limit ( 1500 );

        $json = file_get_contents("list.json");
        $mass = json_decode($json, TRUE);

        foreach ($mass as $row) // Цикл для добавления треков
        {
            $query = 'https://api.spotify.com/v1/me/tracks?ids=';
            $query .= $row['id'];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $query,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_PUT => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $_SESSION['accessToken'],
                    'Accept: application/json',
                    'Content-Type: application/json',
                ],
            ]);
            curl_exec($curl);
            sleep(1);
        }
    }
}