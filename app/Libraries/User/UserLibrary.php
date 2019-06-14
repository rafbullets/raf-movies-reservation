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


    }

    public function increasePoints($userId)
    {

    }


    protected function request($method, $uri)
    {
        $url = $this->url;

        $options = [
            'headers' => ['content-type' => 'application/json', 'Accept' => 'application/json'],
        ];

        $response = $this->client->$method($url.$uri, $options);

        return $response;
    }
}
