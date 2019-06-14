<?php


namespace App\Facades;


use App\Libraries\Paypal\PaypalLibraryFake;
use App\Libraries\Projection\ProjectionLibraryFake;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Facade;
use PayPal\Api\Payment;

/**
 * Class Paypal
 *
 * @method static Payment requestPayment(float $price, string $currency, string $returnUrl, string $cancelUrl)
 * @method static Payment executePayment(string $paymentId, string $payerId, float $price, string $currency)
 * @see PaypalLibraryFake
 */
class Paypal extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'paypal';
    }

    public static function fake()
    {
        static::swap(new PaypalLibraryFake());
    }
}
