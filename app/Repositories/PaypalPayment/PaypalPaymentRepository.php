<?php


namespace App\Repositories\PaypalPayment;


use App\PaypalPayment;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Model;

class PaypalPaymentRepository extends Repository implements PaypalPaymentRepositoryInterface
{
    public function __construct(PaypalPayment $paypalPayment)
    {
        parent::__construct($paypalPayment);
    }
}
