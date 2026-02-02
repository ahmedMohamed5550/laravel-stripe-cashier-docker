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
            $cart = Cart::session()->with('courses')->first();

            if (!$cart || $cart->courses->isEmpty()) {
                return redirect()->route('home', ['message' => 'Cart is empty.']);
            }

            $amount = $cart->courses->sum('price');
            $paymentMethod = $request->payment_method;

            try
            {
                $payment = Auth::user()->charge($amount, $paymentMethod, [
                    'return_url' => route('home', ['message' => 'Payment Successful.'])
                ]);

                if ($payment->status === 'succeeded') {
                    return $this->successFromPayment($cart);
                } else {
                    return redirect()->route('home', ['message' => 'Payment failed.']);
                }
            }
            catch (\Exception $e) {
                return redirect()->route('home', ['message' => 'Payment processing error. Please try again.']);
            }
        });
    }

    public function successFromPayment($cart)
    {
        $this->checkoutService->createOrder($cart);
        return redirect()->route('home',['message' => 'Payment Successful.']);
    }
}
