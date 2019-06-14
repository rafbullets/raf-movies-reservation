<?php


namespace App\Libraries\Projection;


use GuzzleHttp\Client;


class ProjectionLibrary
{
    protected $client;

    protected $url;

    public function __construct()
    {
        $this->client = new Client();
        $this->url = 'https://raf-movies-cinema.herokuapp.com/';
    }

    public function getProjection($projectionId)
    {
        //TODO:
        return [
            'id' => $projectionId,
            'price' => 5,
            'currency' => 'USD',
            'hall' => [
                'id' => 1,
                'rowCount' => 50,
                'seatsCount' => 50
            ]
        ];
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
