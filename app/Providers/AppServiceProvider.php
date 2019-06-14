<?php

namespace App\Providers;

use App\Libraries\Paypal\PayPalLibrary;
use App\Libraries\Projection\ProjectionLibrary;
use App\Libraries\User\UserLibrary;
use App\Repositories\PaypalPayment\PaypalPaymentRepository;
use App\Repositories\PaypalPayment\PaypalPaymentRepositoryInterface;
use App\Repositories\Reservation\ReservationRepository;
use App\Repositories\Reservation\ReservationRepositoryInterface;
use App\Repositories\Seat\SeatRepository;
use App\Repositories\Seat\SeatRepositoryInterface;
use App\Services\Reservation\ReservationService;
use App\Services\Reservation\ReservationServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('paypal', PayPalLibrary::class);
        $this->app->bind('projection', ProjectionLibrary::class);
        $this->app->bind('user', UserLibrary::class);

        $this->app->bind(ReservationRepositoryInterface::class, ReservationRepository::class);
        $this->app->bind(SeatRepositoryInterface::class, SeatRepository::class);
        $this->app->bind(PaypalPaymentRepositoryInterface::class, PaypalPaymentRepository::class);

        $this->app->bind(ReservationServiceInterface::class, ReservationService::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
