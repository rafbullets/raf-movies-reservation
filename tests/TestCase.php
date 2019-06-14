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
    use DatabaseMigrations;
    use RefreshDatabase;
    use CreatesApplication;

    protected function generateJwt()
    {
        $key = "my_secret_key";
        $token = array(
            "username" => "http://example.org",
            "exp" => Carbon::now()->addHour()->timestamp,
            "user_id" => 1
        );

        return JWT::encode($token, $key);
    }

}
