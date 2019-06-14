<?php


namespace App\Libraries\Paypal;


use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;

class PayPalLibrary
{
    private $apiContext;

    public function __construct()
    {
        $this->apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                'ASpq36RsyxQXRY6a1ogeoB-PGAOQwIr_EOBYMlCft3oYFMBHMzJlhhV4066PW5BdH2irWCH_LlUOxNn_',     // ClientID
                'EDMb7x1fQL9MemvZ-NFJlvevOdOkfkjKtAe-QgF_JGGlq2y2IGs3r7o4rN5rJ5NNQjHyfm6H5oMvkOmf'      // ClientSecret
            )
        );
    }

    public function requestPayment(float $price, string $currency, string $returnUrl, string $cancelUrl): Payment
    {
        // After Step 2
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $amount = new Amount();
        $amount->setTotal($price);
        $amount->setCurrency($currency);

        $transaction = new Transaction();
        $transaction->setAmount($amount);

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($returnUrl)
            ->setCancelUrl($cancelUrl);

        $payment = new \PayPal\Api\Payment();
        $payment->setIntent('sale')
            ->setPayer($payer)
            ->setTransactions(array($transaction))
            ->setRedirectUrls($redirectUrls);

        $payment->create($this->apiContext);

        return $payment;
    }

    public function executePayment(string $paymentId, string $payerId, float $price, string $currency): Payment
    {
        $payment = Payment::get($paymentId, $this->apiContext);

        $execution = new PaymentExecution();
        $execution->setPayerId($payerId);

        $amount = new Amount();
        $details = new Details();

        $amount->setCurrency($currency);
        $amount->setTotal($price);
        $amount->setDetails($details);

        $result = $payment->execute($execution, $this->apiContext);

        return $result;
    }

}
