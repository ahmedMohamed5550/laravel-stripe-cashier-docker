<?php

namespace App\Services\Checkout;

use App\Models\Cart;
use App\Models\Course;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Cashier\Cashier;

class CheckoutService
{
    public function checkout()
    {
        return DB::transaction(function () {
            $cart = Cart::session()->with('courses')->first() ?? Cart::current();

            if (!$cart || $cart->courses->isEmpty()) {
                return redirect()->route('home', ['message' => 'Cart is empty.']);
            }

            try {
                $prices = $cart->courses()->pluck('stripe_price_id')->toArray();
                $sessionOptions = $this->getSessionOptions($cart->id);
                $customerOptions = $this->getCustomerOptions();

                return Auth::user()
                ->allowPromotionCodes()
                ->checkout($prices, $sessionOptions, $customerOptions);
            }
            catch (\Exception $e) {
                return redirect()->route('home', ['message' => 'Checkout error. Please try again.']);
            }
        });
    }

    public function lineItemsCheckout()
    {
        return DB::transaction(function () {
            $cart = Cart::session()->with('courses')->first() ?? Cart::current();

            if (!$cart || $cart->courses->isEmpty()) {
                return redirect()->route('home', ['message' => 'Cart is empty.']);
            }

            try {
                $courses = $cart->courses()->get()->map(function ($course) {
                    return [
                        'price_data' => [
                            'currency' => env('CASHIER_CURRENCY', 'usd'),
                            'product_data' => [
                                'name' => $course->name,
                            ],
                            'unit_amount' => $course->price,
                        ],
                        'quantity' => 1,
                        'adjustable_quantity' => [
                            'enabled' => true,
                            'minimum' => 1,
                            'maximum' => 10,
                        ],
                    ];
                })->toArray();

                if (empty($courses)) {
                    return redirect()->route('home', ['message' => 'No valid courses found in cart.']);
                }

                $sessionOptions = $this->getSessionOptions($cart->id, $courses);
                $customerOptions = $this->getCustomerOptions();

                return Auth::user()
                    ->allowPromotionCodes()
                    ->checkout($courses, $sessionOptions, $customerOptions);

            } catch (\Exception $e) {
                return redirect()->route('home', ['message' => 'Checkout error. Please try again.']);
            }
        });
    }

    public function getSessionOptions($cartId,$lineItems = null)
    {
        return [
            'success_url' => route('checkout.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel').'?session_id={CHECKOUT_SESSION_ID}',
            'payment_method_types' => ['card','amazon_pay'],
            'metadata' => [
                'cart_id' => $cartId ?? null,
                'user_id' => Auth::id(),
            ],
            'line_items' => $lineItems,
            'allow_promotion_codes' => true,
            // 'billing_address_collection' => 'required',
            // 'phone_number_collection' => [
            //     'enabled' => true,
            // ],
        ];
    }

    public function getCustomerOptions()
    {
        return [
            'name' => Auth::user()->name ?? 'Guest User',
            'email' => Auth::user()->email ?? null,
            'metadata' => [
                'user_id' => Auth::id() ?? null,
            ],
        ];
    }

    public function success($request)
    {
        $session = $request->user()->stripe()->checkout->sessions->retrieve($request->get('session_id'), []);

        if($session->payment_status == 'paid'){
            $cart = Cart::findOrFail($session->metadata->cart_id);
            $order = $this->createOrder($cart);
            return redirect()->route('home', ['message' => 'Payment Successful.']);
        } else {
            return redirect()->route('home', ['message' => 'Payment Status: ' . $session->payment_status . '.']);
        }
    }

    public function createOrder($cart)
    {
        $order = Order::create([
            'user_id' => Auth::id(),
        ]);

        $courseIds = $cart->courses()->pluck('courses.id')->toArray();

        if (!empty($courseIds)) {
            $order->courses()->syncWithoutDetaching($courseIds);
        }

        $this->clearUserCart();

        return $order;
    }


    public function attachCoursesToOrder($order, $session)
    {
        $cart = Cart::findOrFail($session->metadata->cart_id);
        $courseIds = $cart->courses()->pluck('courses.id')->toArray();

        if (!empty($courseIds)) {
            $order->courses()->syncWithoutDetaching($courseIds);
        }

        return true;
    }

    public function clearUserCart()
    {
        $cart = Cart::session()->with('courses')->first() ?? Cart::current();
        if ($cart) {
            $cart->courses()->detach();
            $cart->delete();
        }
        return true;
    }

    public function cancel(Request $request)
    {
        $session = $request->user()->stripe()->checkout->sessions->retrieve($request->get('session_id'), [] );
        return redirect()->route('home',['message' => 'Payment canceled.']);
    }
}
