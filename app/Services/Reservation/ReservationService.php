<?php


namespace App\Services\Reservation;


use App\Facades\Paypal;
use App\Facades\Projection;
use App\Facades\User;
use App\Libraries\Paypal\PayPalLibrary;
use App\Mail\TicketReserved;
use App\PaypalPayment;
use App\Repositories\PaypalPayment\PaypalPaymentRepositoryInterface;
use App\Repositories\Reservation\ReservationRepositoryInterface;
use App\Repositories\Seat\SeatRepositoryInterface;
use App\Reservation;
use App\Seat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class ReservationService implements ReservationServiceInterface
{
    private $seatRepository;

    private $reservationRepository;

    private $paypalPaymentRepository;

    public function __construct(SeatRepositoryInterface $seatRepository,
                                ReservationRepositoryInterface $reservationRepository,
                                PaypalPaymentRepositoryInterface $paypalPaymentRepository)
    {
        $this->seatRepository = $seatRepository;
        $this->reservationRepository = $reservationRepository;
        $this->paypalPaymentRepository = $paypalPaymentRepository;
    }

    public function getSeatsMatrix(int $projectionId): array
    {
        $projection = Projection::getProjection($projectionId);
        $rowCount = $projection['hall']['rowCount'];
        $seatsCount = $projection['hall']['seatsCount'];

        // Get all reserved seats
        $takenSeats = $this->seatRepository->getReservedSeats($projectionId);

        $takenSeatsMatrix = [];
        foreach ($takenSeats as $takenSeat) {
            if(!isset($takenSeatsMatrix[$takenSeat->row_number])) {
                $takenSeatsMatrix[$takenSeat->row_number] = [];
            }
            $takenSeatsMatrix[$takenSeat->row_number][$takenSeat->seat_number] = 1;
        }

        // Create matrix of seats
        $allSeatsMatrix = [];
        for($i=1; $i <= $rowCount; $i++) {
            for($j=1; $j <= $seatsCount; $j++) {
                $allSeatsMatrix[$i][$j] = 0;

                if(isset($takenSeatsMatrix[$i]) && isset($takenSeatsMatrix[$i][$j])) {
                    $allSeatsMatrix[$i][$j] = 1;
                }
            }
        }

        // Return matrix
        return $allSeatsMatrix;
    }

    private function checkSeats($projectionId, $seats) {
        $allSeats = $this->getSeatsMatrix($projectionId);

        foreach ($seats as $seat) {
            if(!isset($allSeats[$seat['row_number']]) || !isset($allSeats[$seat['row_number']][$seat['seat_number']])) {
                return false;
            }
            if ($allSeats[$seat['row_number']][$seat['seat_number']]==1) {
                return false;
            }
        }

        return true;
    }

    public function reserve($data)
    {
        $projection = Projection::getProjection($data['projection_id']);

        $requestedSeats = $data['seats'];

        if(! $this->checkSeats($projection['id'], $requestedSeats)) {
            return null;
        }
        if (Carbon::now()->greaterThan($projection['start_at'])) {
            return false;
        }

        // Calculate price
        $price = $projection['price'] * count($requestedSeats);
        $currency = $projection['currency'];
        $user = User::getUser(request()->user()['id']);
        $price = $price * (1 - $user['status']['discount']);

        // Create reservation
        $reservation = $this->reservationRepository
            ->createReservationWithSeats(request()->user()['id'], $data['projection_id'], $price, $currency, $requestedSeats);

        // Request to paypal payment
        $requestedPayment = Paypal::requestPayment($price, $projection['currency'], $data['return_url'], $data['cancel_url']);

        $this->reservationRepository
            ->attachPaypalPayment($reservation, $requestedPayment->getId(),
                $requestedPayment->getState(), $price, $currency, $requestedPayment->getApprovalLink());

        return $reservation;
    }

    public function verifyReservation($data)
    {
        $user = User::getUser(request()->user()['id']);

        /** @var PaypalPayment $paypalPayment */
        $paypalPayment = $this->paypalPaymentRepository->firstBy('payment_id', $data['payment_id']);

        if($paypalPayment->payment_state=='approved') {
            return $paypalPayment->reservation;
        }

        // Execute paypal payment
        $verifiedPayment = Paypal::executePayment($paypalPayment->payment_id, $data['payer_id'], $paypalPayment->price, $paypalPayment->currency);

        // Update PaypalPayment status
        $this->paypalPaymentRepository->updateWhereId($paypalPayment->id, [
            'payment_state' => $verifiedPayment->getState(),
            'payer_id' => $verifiedPayment->getPayer()->getPayerInfo()->getPayerId()
        ]);

        // Update Reservation status
        if($verifiedPayment->getState() == 'approved') {
            $this->reservationRepository
                ->updateWhere(['id' => $paypalPayment->reservation->id], ['status' => Reservation::VERIFIED_STATUS]);

            User::increasePoints(request()->user()['id']);

            Mail::to($user['user']['email'])->send(new TicketReserved($paypalPayment->reservation));
        }


        // Return reservation id
        return $paypalPayment->reservation()->first();
    }
}
