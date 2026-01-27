<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Cashier;

class Cart extends Model
{
    protected $guarded = [];

    public function scopeSession($query)
    {
        return $query->where('session_id', session()->getId());
    }

    public static function current()
    {
        if (Auth::check()) {
            return static::where('user_id', Auth::id())->with('courses')->first();
        }

        return static::session()->with('courses')->first();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'cart_course', 'cart_id', 'course_id');
    }

    public function totalPrice()
    {
        $price = $this->courses()->sum('price');
        return $price ? Cashier::formatAmount($price, env('CASHIER_CURRENCY')) : '$0';
    }
}
