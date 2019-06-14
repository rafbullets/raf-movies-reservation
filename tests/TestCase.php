<?php

namespace Tests;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use DatabaseTransactions;

    use CreatesApplication;

    protected function generateJwt($userId = 1)
    {
        $key = "my_secret_key";
        $token = array(
            "exp" => Carbon::now()->addHour()->timestamp,
            "id" => $userId
        );

        return JWT::encode($token, $key);
    }

}
