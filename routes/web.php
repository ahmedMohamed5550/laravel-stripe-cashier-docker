<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ProfileController;
use App\Models\Cart;
use App\Models\Course;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $cart = Cart::session()->with('courses')->first();
    $courses = Course::all();
    return view('home' , get_defined_vars());
})->name('home');

Route::prefix('courses')->group(function () {
    Route::get('/{course:slug}', [CourseController::class, 'show'])->name('courses.show');
});

Route::prefix('checkout')->middleware('auth')->group(function () {
    Route::get('/', [CheckoutController::class, 'checkout'])->name('checkout');
    Route::get('/line-items', [CheckoutController::class, 'lineItemsCheckout'])->name('checkout.lineItems');
    Route::get('/direct/payment-method', [CheckoutController::class, 'directPaymentMethod'])->name('checkout.directPaymentMethod');
    Route::post('/direct/payment-method', [CheckoutController::class, 'storeDirectPaymentMethod'])->name('checkout.storeDirectPaymentMethod');
    Route::get('/direct/payment-intent', [CheckoutController::class, 'directPaymentIntent'])->name('checkout.directPaymentIntent');
    Route::post('/direct/payment-intent', [CheckoutController::class, 'storeDirectPaymentIntent'])->name('checkout.storeDirectPaymentIntent');
    Route::get('/direct/setup-intent', [CheckoutController::class, 'directSetupIntent'])->name('checkout.directSetupIntent');
    Route::post('/direct/setup-intent', [CheckoutController::class, 'storeDirectSetupIntent'])->name('checkout.storeDirectSetupIntent');
    Route::get('/direct/payment-method/one-click-checkout', [CheckoutController::class, 'directPaymentMethodOneClickCheckout'])->name('checkout.directPaymentMethodOneClickCheckout')->middleware('protect.one.click.checkout');
    Route::get('/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
});

Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::get('add-to-cart/{course:slug}', [CartController::class, 'addToCart'])->name('cart.add');
    Route::get('remove-from-cart/{course:slug}', [CartController::class, 'removeFromCart'])->name('cart.remove');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
