<?php

namespace Tests\Feature;

use App\Facades\Paypal;
use App\Facades\Projection;
use App\Facades\User;
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

        User::fake($this->mockClient([
            new Response(200, [], json_encode([
                "user" => [
                    "id" => $reservation->user_id,
                    "email" => "jela@jelica.com",
                ],
                "status" => [
                    "discount" => 0.05,
                ]
            ])),
            new Response(200, [], json_encode([]))
        ]));

        $response = $this->get("/api/reserve?payment_id={$payPalPayment->payment_id}&payer_id={$payerId}", [
            'Authorization' => 'Bearer '.$this->generateJwt($reservation->user_id)
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

    public function testReservationRequest()
    {
        $user_id = random_int(1, 100);
        $projectionId = random_int(1, 50);
        $ticketPrice = random_int(1, 100);

        $row_number_1 = random_int(1, 10);
        $seat_number_1 = random_int(1, 10);
        $row_number_2 = random_int(1, 10);
        $seat_number_2 = random_int(1, 10);

        $mockedProjection = [
            "id" => $projectionId,
            "movie_id" => 1,
            "cinema_hall_id" => 1,
            "start_at" => "2019-06-14 02:25:48",
            "ticket_price" => $ticketPrice,
            "created_at" => "2019-06-14 02:35:09",
            "updated_at" => "2019-06-14 02:38:10",
            "cinema_hall" => [
                "id" => 1,
                "hall_name" => "sala 1",
                "number_of_rows" => random_int(10, 50),
                "seats_in_row" => random_int(10, 50),
                "created_at" => null,
                "updated_at" => null
            ]

        ];

        Projection::fake($this->mockClient([
            new Response(200, [], json_encode($mockedProjection)),
            new Response(200, [], json_encode($mockedProjection))
        ]));
        $response = $this->post('/api/reserve', [
	        "seats" => [
		        ["row_number" => $row_number_1, "seat_number" => $seat_number_1],
		        ["row_number" => $row_number_2, "seat_number" => $seat_number_2]
	        ],
	        "projection_id" => $projectionId,
	        "return_url" => "http://example1.com",
            "cancel_url" => "http://example2.com"
        ], [
            'Authorization' => 'Bearer '.$this->generateJwt($user_id)
        ]);

        $response->assertStatus(200);
        $this->assertTrue(isset($response->json()['redirect_to']));

        $this->assertDatabaseHas('reservations', [
            'user_id' => $user_id,
            'projection_id' => $projectionId,
            'price' => 2*$ticketPrice,
            'currency' => 'USD'
        ]);

        $this->assertDatabaseHas('seats', [
            'row_number' => $row_number_1,
            'seat_number' => $seat_number_1,
        ]);
        $this->assertDatabaseHas('seats', [
            'row_number' => $row_number_2,
            'seat_number' => $seat_number_2,
        ]);
    }

    public function testReservationMatrix()
    {
        $projectionId = random_int(1, 10);
        $reservation = factory(Reservation::class)->create([
            'projection_id' => $projectionId
        ]);

        $row_number_1 = random_int(1, 10);
        $seat_number_1 = random_int(1, 10);
        $row_number_2 = random_int(1, 10);
        $seat_number_2 = random_int(1, 10);

        factory(Seat::class)->create([
            'reservation_id' => $reservation->id,
            'row_number' => $row_number_1,
            'seat_number' => $seat_number_1
        ]);
        factory(Seat::class)->create([
            'reservation_id' => $reservation->id,
            'row_number' => $row_number_2,
            'seat_number' => $seat_number_2
        ]);

        Projection::fake($this->mockClient([
            new Response(200, [], json_encode([
                "id" => $projectionId,
                "movie_id" => 1,
                "cinema_hall_id" => 1,
                "start_at" => "2019-06-14 02:25:48",
                "ticket_price" => 5,
                "created_at" => "2019-06-14 02:35:09",
                "updated_at" => "2019-06-14 02:38:10",
                "cinema_hall" => [
                    "id" => 1,
                    "hall_name" => "sala 1",
                    "number_of_rows" => random_int(10, 50),
                    "seats_in_row" => random_int(10, 50),
                    "created_at" => null,
                    "updated_at" => null
                ]

            ]))
        ]));

        $response = $this->get('/api/seats/'.$projectionId, [
            'Authorization' => 'Bearer '.$this->generateJwt()
        ]);

        $responseJson = $response->json();
        $this->assertEquals(1, $responseJson[$row_number_1][$seat_number_1]);
        $this->assertEquals(1, $responseJson[$row_number_2][$seat_number_2]);
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
