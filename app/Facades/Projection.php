<?php


namespace App\Facades;


use App\Libraries\Projection\ProjectionLibrary;
use App\Libraries\Projection\ProjectionLibraryFake;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Facade;

/**
 * Class Projection
 *
 * @method static mixed getProjection($projectionId)
 * @see ProjectionLibrary
 */
class Projection extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'projection';
    }

    public static function fake(Client $client)
    {
        static::swap(new ProjectionLibraryFake($client));
    }

}
