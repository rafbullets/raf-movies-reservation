<?php


namespace App\Libraries\User;


use GuzzleHttp\Client;

class UserLibraryFake extends UserLibrary
{


    public function __construct(Client $client = null)
    {
        parent::__construct();

        if(!is_null($client)) {
            $this->client = $client;
        }
    }

}
