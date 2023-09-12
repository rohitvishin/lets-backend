<?php

namespace App\Http\Controllers\API;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\PlansModel;
use App\Models\SubscriptionModel;

class SubscriptionController extends Controller
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
        $planid = $request->plan_id;
        $payment_mode = $request->payment_mode;
        $transaction_id = $request->transaction_id;

        $user = Auth::user();

        if (is_null($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $planData = PlansModel::where('id', $planid)->first();

        $start = now();

        $start_date = $start->format('Y-m-d H:i:s');

        $validity = now()->addDays($planData['validity']);

        $end_date = $validity->format('Y-m-d H:i:s');

        $numberOfDays = $start->diffInDays($validity);

        $subscription = SubscriptionModel::create([
            'user_id' => $user->id,
            'plan_id' => $planData['id'],
            'amount' => $planData['amount'],
            'payment_mode' => $payment_mode,
            'transaction_id' => $transaction_id,
            'lets_count' => $planData['lets_count'],
            'remaining_days_count' => $numberOfDays,
            'validity' => $planData['validity'],
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);

        Common::logSystemActivity('User Purchased Subscription', 'Subscription', 'API');

        $res = [
            'subscription' => $subscription,
            'msg' => 'Subscription Purchased Successfully'
        ];
        return response($res, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
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
}
