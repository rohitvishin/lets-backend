<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Helpers\Common;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Models\SubscriptionModel;
use App\Models\PlansModel;

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

    public function getOrderId(Request $request) {
        try {
            $data = $request->validate([
                'package_id' => 'required|numeric'
            ]);
            
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 400);
        }

        $user = Auth::user();

        if (is_null($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $planData = PlansModel::where('id', $data['package_id'])->first();

        $start = now();

        $start_date = $start->format('Y-m-d H:i:s');

        $validity = now()->addDays($planData['validity']);

        $end_date = $validity->format('Y-m-d H:i:s');

        $numberOfDays = $start->diffInDays($validity);

        $subscription = SubscriptionModel::create([
            'user_id' => $user->id,
            'plan_id' => $planData['id'],
            'amount' => $planData['amount'],
            'lets_count' => $planData['lets_count'],
            'remaining_days_count' => $numberOfDays,
            'validity' => $planData['validity'],
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);

        $subscriptionId = $subscription->id;

        $api_key = env('RAZORPAY_APP_KEY');
        $api_secret = env('RAZORPAY_APP_SECRET');

        $api = new Api($api_key, $api_secret);

        $order = $api->order->create([
            'amount' => $planData['amount'], // Replace with the actual amount
            'currency' => 'INR', // Replace with the desired currency
            'payment_capture' => 1, // Auto-capture payment
        ]);

        return response()->json(['msg' => 'Order Id Generated Successfully', 'subscription_id' => $subscriptionId, 'order_id' => $order->id, 'amount' => $order->amount_paid], 200);
    }

    public function updatePaymentStatus(Request $request) {

        try {
            $data = $request->validate([
                'subscription_id' => 'required|numeric',
                'order_id' => 'required|string',
                'transaction_id' => 'required|string'
            ]);
            
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 400);
        }

        $subscription_data = SubscriptionModel::where('id', $data['subscription_id'])->latest()->first();

        if ($subscription_data) {
            // Update the subscription record based on the retrieved data
            $subscription_data->update([
                'order_id' => $data['order_id'],
                'transaction_id' => $data['transaction_id'],
                'status' => '1',
            ]);
        }

        return response()->json(['msg' => 'Subscription Id Updated Successfully'], 200);
    }
}
