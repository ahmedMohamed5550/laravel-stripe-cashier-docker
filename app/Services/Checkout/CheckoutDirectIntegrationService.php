<?php

namespace App\Services\Checkout;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutDirectIntegrationService
{
    protected $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            try{
                if($request->payment_method) {
                    $this->handleNewCustomerPaymentMethod($request->payment_method);
                }

                $cart = Cart::session()->with('courses')->first();

                if (!$cart || $cart->courses->isEmpty()) {
                    return redirect()->route('home', ['message' => 'Cart is empty.']);
                }

                $amount = $cart->courses->sum('price');
                $paymentMethod = $request->payment_method;

                $payment = Auth::user()->charge($amount, $paymentMethod, [
                    'return_url' => route('home', ['message' => 'Payment Successful.']),
                    'metadata' => [
                        'cart_id' => $cart->id,
                        'user_id' => Auth::id(),
                    ],
                ]);

                return redirect()->route('home', ['message' => 'Payment Successful.']);

                // if ($payment->status === 'succeeded') {
                //     return $this->successFromPayment($cart);
                // } else {
                //     return redirect()->route('home', ['message' => 'Payment failed.']);
                // }
            }
            catch (\Exception $e) {
                return redirect()->route('home', ['message' => 'Payment processing error. Please try again.']);
            }
        });
    }

    public function oneClickCheckout()
    {
        return DB::transaction(function () {
            try{
                if(!Auth::user()->hasDefaultPaymentMethod()) {
                    return redirect()->route('home', ['message' => 'No default payment method set. Please add a payment method first.']);
                }

                else{
                    $cart = Cart::session()->with('courses')->first();

                    if (!$cart || $cart->courses->isEmpty()) {
                        return redirect()->route('home', ['message' => 'Cart is empty.']);
                    }

                    $amount = $cart->courses->sum('price');
                    $paymentMethod = Auth::user()->defaultPaymentMethod()->id;

                    $payment = Auth::user()->charge($amount, $paymentMethod, [
                        'return_url' => route('home', ['message' => 'Payment Successful.'])
                    ]);

                    if ($payment->status === 'succeeded') {
                        return $this->successFromPayment($cart);
                    } else {
                        return redirect()->route('home', ['message' => 'Payment failed.']);
                    }
                }
            }
            catch (\Exception $e) {
                return redirect()->route('home', ['message' => 'Payment processing error. Please try again.']);
            }
        });
    }

    protected function handleNewCustomerPaymentMethod($paymentMethod)
    {
        $user = Auth::user();

        $user->updateOrCreateStripeCustomer();
        $user->updateDefaultPaymentMethod($paymentMethod);
    }

    protected function successFromPayment($cart)
    {
        $this->checkoutService->createOrder($cart);
        return redirect()->route('home',['message' => 'Payment Successful.']);
    }
}
