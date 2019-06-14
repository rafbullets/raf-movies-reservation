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
        $response = $this->request('get', 'api/projections/'.$projectionId);

        return [
            'id' => $response['id'],
            'start_at' => $response['start_at'],
            'price' => $response['ticket_price'],
            'currency' => 'USD',
            'hall' => [
                'id' => $response['cinema_hall']['id'],
                'rowCount' => $response['cinema_hall']['number_of_rows'],
                'seatsCount' => $response['cinema_hall']['seats_in_row']
            ]
        ];
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
