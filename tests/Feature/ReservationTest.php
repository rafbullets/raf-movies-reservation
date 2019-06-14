<?php

namespace Tests\Feature;

use App\Facades\Paypal;
use App\Facades\Projection;
use App\Mail\TicketReserved;
use App\PaypalPayment;
use App\Reservation;
use App\Seat;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReservationTest extends TestCase
{
    public function testReservationVerification()
    {
        Paypal::fake();
        Mail::fake();

        $payerId = 'B9W3ZCXR236YQ';
        $projectionId = random_int(1, 10);
        $reservation = factory(Reservation::class)->create([
            'projection_id' => $projectionId
        ]);

        factory(Seat::class)->create([
            'reservation_id' => $reservation->id,
            'row_number' => 1,
            'seat_number' => 1
        ]);
        $payPalPayment = factory(PaypalPayment::class)->create([
            'reservation_id' => $reservation->id,
            'payment_state' => 'created'
        ]);

        $response = $this->get("/api/reserve?payment_id={$payPalPayment->payment_id}&payer_id={$payerId}", [
            'Authorization' => 'Bearer '.$this->generateJwt()
        ]);

        $responseJson = $response->json();
        $this->assertEquals($responseJson['user_id'], $reservation->user_id);
        $this->assertEquals($responseJson['projection_id'], $projectionId);
        $this->assertEquals($responseJson['price'], $reservation->price);
        $this->assertEquals($responseJson['currency'], $reservation->currency);
        $this->assertEquals($responseJson['status'], Reservation::VERIFIED_STATUS);

        $this->assertDatabaseHas('reservations', [
            'user_id' => $reservation->user_id,
            'projection_id' => $projectionId,
            'price' => $reservation->price,
            'currency' => $reservation->currency,
            'status' => Reservation::VERIFIED_STATUS
        ]);

        $this->assertDatabaseHas('paypal_payments', [
            'id' => $payPalPayment->id,
            'payment_state' => 'approved',
            'payer_id' => $payerId,
        ]);

        Mail::assertSent(TicketReserved::class, 1);
    }

    public function testReservation()
    {
        $projectionId = random_int(1, 50);
        $response = $this->post('/api/reserve', [
            "user_id" => 1,
	        "seats" => [
		        ["row_number" => 7, "seat_number" => 1],
		        ["row_number" => 7, "seat_number" => 2]
	        ],
	        "projection_id" => $projectionId,
	        "return_url" => "http://example1.com",
            "cancel_url" => "http://example2.com"
        ], [
            'Authorization' => 'Bearer '.$this->generateJwt()
        ]);

        $response->assertStatus(200);
        $this->assertTrue(isset($response->json()['redirect_to']));

        $this->assertDatabaseHas('reservations', [
            'user_id' => 1,
            'projection_id' => $projectionId,
            'price' => 10, //TODO
            'currency' => 'USD' // TODO
        ]);

        $this->assertDatabaseHas('seats', [
            'row_number' => 7,
            'seat_number' => 1,
        ]);
    }

    public function testReservationMatrix()
    {
        $reservation = factory(Reservation::class)->create([
            'projection_id' => 1
        ]);

        factory(Seat::class)->create([
            'reservation_id' => $reservation->id,
            'row_number' => 1,
            'seat_number' => 1
        ]);
        factory(Seat::class)->create([
            'reservation_id' => $reservation->id,
            'row_number' => 1,
            'seat_number' => 2
        ]);

        Projection::fake($this->mockClient());

        $response = $this->get('/api/seats/1');

        $responseJson = $response->json();
        $this->assertEquals(1, $responseJson[1][1]);
        $this->assertEquals(1, $responseJson[1][2]);
    }

    private function mockClient($responses)
    {
        $mock = new MockHandler(
            $responses
//            [new Response(200, [], json_encode($mockedResponse))]
        );
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        return $client;
    }
}
