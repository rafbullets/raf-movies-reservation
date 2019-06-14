<?php


namespace App\Repositories\Reservation;


use App\Repositories\Repository;
use App\Repositories\RepositoryInterface;
use App\Reservation;
use Illuminate\Database\Eloquent\Model;

class ReservationRepository extends Repository implements ReservationRepositoryInterface
{

    public function __construct(Reservation $reservation)
    {
        parent::__construct($reservation);
    }

    public function createReservationWithSeats($userId, $projectionId, $price, $currency, $requestedSeats)
    {
        /** @var Reservation $reservation */
        $reservation = Reservation::query()->create([
            'user_id' => $userId,
            'projection_id' => $projectionId,
            'price' => $price,
            'currency' => $currency
        ]);

        // Create seats
        $reservation->seats()->createMany($requestedSeats);

        return $reservation;
    }

    public function attachPaypalPayment(Reservation $reservation, $paymentId, $paymentState, $price, $currency, $approvalLink)
    {
        return $reservation->paypalPayment()->create([
            'payment_id' => $paymentId,
            'payment_state' => $paymentState,
            'price' => $price,
            'currency' => $currency,
            'approval_link' => $approvalLink
        ]);
    }
}
