<?php

namespace App\Services\checkout;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentIntentService
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
                $cart = Cart::session()->with('courses')->first();

                if (!$cart || $cart->courses->isEmpty()) {
                    return redirect()->route('home', ['message' => 'Cart is empty.']);
                }

                $paymentIntentId = $request->payment_intent_id;
                $payment = Auth::user()->findPayment($paymentIntentId);

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

    protected function successFromPayment($cart)
    {
        $this->checkoutService->createOrder($cart);
        return redirect()->route('home',['message' => 'Payment Successful.']);
    }
}
