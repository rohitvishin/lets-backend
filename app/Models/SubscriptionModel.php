<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionModel extends Model
{
    use HasFactory;

    protected $table = 'subscription';

    protected $fillable =['user_id', 'plan_id', 'amount', 'payment_mode', 'transaction_id', 'order_id', 'lets_count', 'remaining_days_count', 'validity', 'start_date', 'end_date','razorpay_response', 'status', 'created_at', 'updated_at'];
}
