<?php


namespace App\Libraries\Projection;


use GuzzleHttp\Client;

class ProjectionLibraryFake extends ProjectionLibrary
{

    public function __construct(Client $client = null)
    {
        parent::__construct();

        if(!is_null($client)) {
            $this->client = $client;
        }
    }

}
