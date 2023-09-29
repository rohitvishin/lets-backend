<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Helpers\Common;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Models\SubscriptionModel;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getOrderId() {
        $user = Auth::user();

        if (is_null($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $subscription_data = SubscriptionModel::where(['status' => '1', 'user_id' => $user->id])->latest()->first();

        $api_key = env('RAZORPAY_APP_KEY');
        $api_secret = env('RAZORPAY_APP_SECRET');

        $api = new Api($api_key, $api_secret);

        $order = $api->order->create([
            'amount' => $subscription_data->amount, // Replace with the actual amount
            'currency' => 'INR', // Replace with the desired currency
            'payment_capture' => 1, // Auto-capture payment
        ]);

        return response()->json(['msg' => 'Order Id Generated Successfully', 'order_id' => $order->id, 'amount' => $order->amount_paid], 200);
    }
}
