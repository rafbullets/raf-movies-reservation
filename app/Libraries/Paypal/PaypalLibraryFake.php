<?php


namespace App\Libraries\Paypal;


use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Payer;
use PayPal\Api\PayerInfo;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;

class PaypalLibraryFake extends PayPalLibrary
{
    public function executePayment(string $paymentId, string $payerId, float $price, string $currency): Payment
    {
        $payment = new Payment();
        $payer = new Payer();
        $payer->setPayerInfo(new PayerInfo())->getPayerInfo()->setPayerId($payerId);

        $payment->setPayer($payer);
        $payment->setId($paymentId);
        $payment->setState('approved');

        return $payment;
    }
}
