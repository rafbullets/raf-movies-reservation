<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('/', function () {
    $apiContext = new \PayPal\Rest\ApiContext(
        new \PayPal\Auth\OAuthTokenCredential(
            'ASpq36RsyxQXRY6a1ogeoB-PGAOQwIr_EOBYMlCft3oYFMBHMzJlhhV4066PW5BdH2irWCH_LlUOxNn_',     // ClientID
            'EDMb7x1fQL9MemvZ-NFJlvevOdOkfkjKtAe-QgF_JGGlq2y2IGs3r7o4rN5rJ5NNQjHyfm6H5oMvkOmf'      // ClientSecret
        )
    );


    // After Step 2
    $payer = new \PayPal\Api\Payer();
    $payer->setPaymentMethod('paypal');

    $amount = new \PayPal\Api\Amount();
    $amount->setTotal('1000.00');
    $amount->setCurrency('USD');

    $transaction = new \PayPal\Api\Transaction();
    $transaction->setAmount($amount);

    $redirectUrls = new \PayPal\Api\RedirectUrls();
    $redirectUrls->setReturnUrl("http://raf-movies-reservation.local/execute")
        ->setCancelUrl("https://example.com/your_cancel_url.html");

    $payment = new \PayPal\Api\Payment();
    $payment->setIntent('sale')
        ->setPayer($payer)
        ->setTransactions(array($transaction))
        ->setRedirectUrls($redirectUrls);

    // After Step 3
    try {
        $created = $payment->create($apiContext);
//        dd($created->getId());
//        \PayPal\Api\Payment::get($created->getId());
//        echo $payment;

        return '<a href="'.$payment->getApprovalLink().'">Trosi pare po Bahamima!</a>';

        echo "\n\nRedirect user to approval_url: " . $payment->getApprovalLink() . "\n";
    }
    catch (\PayPal\Exception\PayPalConnectionException $ex) {
        // This will print the detailed information on the exception.
        //REALLY HELPFUL FOR DEBUGGING
        echo $ex->getData();
    }


    return view('welcome');
});

Route::get('/execute', function () {
    $apiContext = new \PayPal\Rest\ApiContext(
        new \PayPal\Auth\OAuthTokenCredential(
            'ASpq36RsyxQXRY6a1ogeoB-PGAOQwIr_EOBYMlCft3oYFMBHMzJlhhV4066PW5BdH2irWCH_LlUOxNn_',     // ClientID
            'EDMb7x1fQL9MemvZ-NFJlvevOdOkfkjKtAe-QgF_JGGlq2y2IGs3r7o4rN5rJ5NNQjHyfm6H5oMvkOmf'      // ClientSecret
        )
    );

    $paymentId = request('paymentId');
    $payment = \PayPal\Api\Payment::get($paymentId, $apiContext);

    $execution = new \PayPal\Api\PaymentExecution();
    $execution->setPayerId(request('PayerID'));


    $amount = new \PayPal\Api\Amount();
    $details = new \PayPal\Api\Details();
//    $details->setShipping(2.2)
//        ->setTax(1.3)
//        ->setSubtotal(17.50);

    $amount->setCurrency('USD');
    $amount->setTotal(1000);
    $amount->setDetails($details);


//    $execution->addTransaction($transaction);

    $result = $payment->execute($execution, $apiContext);
    return "Potroseno";



    return view('welcome');
});
