<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Cashier;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::session()->with('courses')->first();
        if(!$cartItems){
            $cartItems = Cart::current();
        }
        return view('cart.index', get_defined_vars());
    }

    public function addToCart(Course $course)
    {
        $userId = null;

        if (Auth::check()) {
            $userId = Auth::id();
        }

        $cart = Cart::firstOrCreate([
            'session_id' => session()->getId(),
            'user_id' => $userId,
        ]);

        if ($cart->courses()->where('course_id', $course->id)->exists()) {
            return redirect()->back()->with('success', 'Course is already in your cart.');
        }

        $cart->courses()->syncWithoutDetaching($course);
        return redirect()->back()->with('success', 'Course added to cart successfully!');
    }

    public function removeFromCart(Course $course)
    {
        $cart = Cart::session()->with('courses')->first();

        if (!$cart) {
            abort_unless($cart, 404, 'Cart not found.');
        }

        $cart->courses()->detach($course);
        return redirect()->back()->with('success', 'Course removed from cart successfully!');
    }
}
