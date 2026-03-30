<?php

namespace App\Listeners;

use App\Models\Cart;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Events\WebhookReceived;

class ChargeSucceededListener
{

    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(WebhookReceived $event) : void
    {
        if($event->payload['type'] === 'charge.succeeded') {
            $metadata = $event->payload['data']['object']['metadata'] ?? null;
            $cart = Cart::find($metadata['cart_id'] ?? null);
            $user = User::find($metadata['user_id'] ?? null);

            if($event->payload['data']['object']['status'] === 'succeeded') {

                $order = Order::create([
                    'user_id' => $user->id,
                ]);

                $order->courses()->attach($cart->courses->pluck('id')->toArray());
                $cart->delete();
            }
        }
    }
}
