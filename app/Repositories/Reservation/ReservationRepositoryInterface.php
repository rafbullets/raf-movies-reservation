<?php


namespace App\Repositories\Reservation;


use App\PaypalPayment;
use App\Repositories\RepositoryInterface;
use App\Reservation;

interface ReservationRepositoryInterface extends RepositoryInterface
{
    /**
     * @param $userId
     * @param $projectionId
     * @param $price
     * @param $currency
     * @param $requestedSeats
     * @return Reservation
     */
    public function createReservationWithSeats($userId, $projectionId, $price, $currency, $requestedSeats);


    /**
     * @param Reservation $reservation
     * @param $paymentId
     * @param $paymentState
     * @param $price
     * @param $currency
     * @param $approvalLink
     * @return PaypalPayment
     */
    public function attachPaypalPayment(Reservation $reservation, $paymentId, $paymentState, $price, $currency, $approvalLink);

}
