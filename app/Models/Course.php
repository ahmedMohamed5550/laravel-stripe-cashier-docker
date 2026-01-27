<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Cashier;

class Course extends Model
{
    protected $table = 'courses';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $fillable = [
        'name',
        'slug',
        'desc',
        'price',
        'stripe_price_id',
    ];

    public function carts()
    {
        return $this->belongsToMany(Cart::class, 'cart_course', 'course_id', 'cart_id');
    }

    public function formatPrice()
    {
        return $this->price ? Cashier::formatAmount($this->price, env('CASHIER_CURRENCY')) : '$0.00';
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'course_order', 'course_id', 'order_id');
    }

}
