<?php


namespace App\Facades;


use App\Libraries\Projection\ProjectionLibrary;
use App\Libraries\Projection\ProjectionLibraryFake;
use App\Libraries\User\UserLibrary;
use App\Libraries\User\UserLibraryFake;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Facade;

/**
 * Class User
 *
 * @method static mixed getUser($userId)
 * @method static mixed increasePoints($userId)
 * @see UserLibrary
 */
class User extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'user';
    }

    public static function fake(Client $client)
    {
        static::swap(new UserLibraryFake($client));
    }

}
