<?php


namespace App\Libraries\User;


use GuzzleHttp\Client;

class UserLibrary
{
    protected $client;
    protected $url;

    public function __construct()
    {
        $this->client = new Client();
        $this->url = 'https://raf-movies-user.herokuapp.com/';
    }

    public function getUser($userId)
    {
        $response = $this->request('get', 'api/users/'.$userId);
        return $response;
    }

    public function increasePoints($userId)
    {
        $this->request('post', 'api/users/inc/'.$userId);
    }


    protected function request($method, $uri)
    {
        $url = $this->url;

        $options = [
            'headers' => [
                'content-type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '.request()->user()['jwt']
            ],
        ];

        $response = $this->client->$method($url.$uri, $options);

        $response = $response->getBody();

        $response = json_decode($response, true);

        return $response;
    }
}
