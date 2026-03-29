<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Services\Checkout\CheckoutDirectIntegrationService;
use App\Services\Checkout\CheckoutService;
use App\Services\Checkout\PaymentIntentService;
use App\Services\Checkout\SetupIntentService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// card success : 4242 4242 4242 4242
// card cancel : 4000 0000 0000 0002
class CheckoutController extends Controller
{
    protected $checkoutService;
    protected $checkoutDirectIntegrationService;
    protected $PaymentIntentService;
    protected $SetupIntentService;

    public function __construct(CheckoutService $checkoutService, CheckoutDirectIntegrationService $checkoutDirectIntegrationService,PaymentIntentService $PaymentIntentService, SetupIntentService $SetupIntentService)
    {
        $this->checkoutService = $checkoutService;
        $this->checkoutDirectIntegrationService = $checkoutDirectIntegrationService;
        $this->PaymentIntentService = $PaymentIntentService;
        $this->SetupIntentService = $SetupIntentService;
    }

    public function checkout()
    {
        return $this->checkoutService->checkout();
    }

    public function lineItemsCheckout()
    {
        return $this->checkoutService->lineItemsCheckout();
    }

    public function success(Request $request)
    {
        return $this->checkoutService->success($request);
    }

    public function directPaymentMethod()
    {
        return view ('cart.directPaymentMethod');
    }

    public function storeDirectPaymentMethod(Request $request)
    {
        return $this->checkoutDirectIntegrationService->store($request);
    }

    public function directPaymentIntent()
    {
        $cart = Cart::session()->with('courses')->first();
        $amount = $cart->courses->sum('price');
        $payment = Auth::user()->pay($amount);
        return view ('cart.paymentIntent',get_defined_vars());
    }

    public function storeDirectPaymentIntent(Request $request)
    {
        return $this->PaymentIntentService->store($request);
    }

    public function directSetupIntent()
    {
        $setupIntent = Auth::user()->createSetupIntent();
        return view ('cart.setupIntent',get_defined_vars());
    }

    public function storeDirectSetupIntent(Request $request)
    {
        return $this->SetupIntentService->store($request);
    }


    public function directPaymentMethodOneClickCheckout()
    {
        return $this->checkoutDirectIntegrationService->oneClickCheckout();
    }
}
