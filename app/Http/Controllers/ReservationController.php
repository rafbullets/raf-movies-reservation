<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationRequest;
use App\Http\Requests\ReservationVerifyRequest;
use App\Libraries\PayPalLibrary;
use App\PaypalPayment;
use App\Reservation;
use App\Seat;
use App\Services\Reservation\ReservationServiceInterface;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /** @var ReservationServiceInterface $reservationService*/
    private $reservationService;

    /**
     * ReservationController constructor.
     * @param ReservationServiceInterface $reservationService
     */
    public function __construct(ReservationServiceInterface $reservationService)
    {
        $this->reservationService = $reservationService;
    }

    /**
     * @param $projectionId
     * @return array
     */
    public function getSeats($projectionId)
    {
        return $this->reservationService->getSeatsMatrix($projectionId);
    }

    /**
     * @param ReservationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reserve(ReservationRequest $request)
    {
        $reservation = $this->reservationService->reserve($request->all());
        if(is_null($reservation)) {
            return response()->json([
                'message' => 'Try another seat'
            ], 400);
        }
        return response()->json(['redirect_to' => $reservation->paypalPayment->approval_link]);
    }

    /**
     * @param ReservationVerifyRequest $request
     * @return mixed
     */
    public function verify(ReservationVerifyRequest $request)
    {
        return $this->reservationService->verifyReservation($request->all());
    }
}
