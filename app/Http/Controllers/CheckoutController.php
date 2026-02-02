<?php

namespace App\Http\Controllers;

use App\Services\Checkout\CheckoutDirectIntegrationService;
use App\Services\Checkout\CheckoutService;
use Illuminate\Http\Request;

// card success : 4242 4242 4242 4242
// card cancel : 4000 0000 0000 0002
class CheckoutController extends Controller
{
    protected $checkoutService;
    protected $checkoutDirectIntegrationService;

    public function __construct(CheckoutService $checkoutService, CheckoutDirectIntegrationService $checkoutDirectIntegrationService)
    {
        $this->checkoutService = $checkoutService;
        $this->checkoutDirectIntegrationService = $checkoutDirectIntegrationService;
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

    public function directPaymentMethodOneClickCheckout()
    {
        return $this->checkoutDirectIntegrationService->oneClickCheckout();
    }
}
