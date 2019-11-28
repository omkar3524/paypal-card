<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Omnipay\Omnipay;

class PaypalPaymentController extends Controller
{
    public function paypalPayment(Request $request)
    {
        $gateway = Omnipay::create('PayPal_Pro');
        $gateway->setUsername(config('paypal-omnipay.paypal_username'));
        $gateway->setPassword(config('paypal-omnipay.paypal_password'));
        $gateway->setSignature(config('paypal-omnipay.paypal_signature'));
        $gateway->setTestMode(config('paypal-omnipay.paypal_test_mode')); // here 'true' is for sandbox. Pass 'false' when go live

        $card_details = [
            'firstName' => $request->card_details['first_name'],
            'lastName' => $request->card_details['last_name'],
            'number' => $request->card_details['card_number'],
            'expiryMonth' => $request->card_details['card_month'],
            'expiryYear' => $request->card_details['card_year'],
            'cvv' => $request->card_details['cvv'],
        ];

        try {
            // Send purchase request
            $response = $gateway->purchase(
                [
                    'amount' => $request->amount,
                    'currency' => 'USD',
                    'card' => $card_details,
                ]
            )->send();

            // Process response
            if ($response->isSuccessful()) {

                // Payment was successful
                echo "Payment is successful. Your Transaction ID is: " . $response->getTransactionReference();

            } else {
                // Payment failed
                echo "Payment failed. " . $response->getMessage();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }

    }
}
