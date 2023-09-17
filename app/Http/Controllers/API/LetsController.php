<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use App\Helpers\Common;
use App\Models\SubscriptionModel;
use App\Models\LetsModel;
use App\Models\LetsReceiverLogModel;
use App\Models\User;

class LetsController extends Controller
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

    public function letsCreator(Request $request) {
        $user = Auth::user();

        if (is_null($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user_details = User::find($user->id);

        $radius = $user_details->radius_filter;

        try {
            $data = $request->validate([
                'event_name' => 'required|string',
                'duration' => 'numeric',
                'creator_longitude' => 'string',
                'creator_latitude' => 'string',
            ]);
            
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 400);
        }

        $all_users_in_same_location = User::where('state', $user_details->state)
            ->where('city', $user_details->city)
            ->where('id', '!=', $user_details->id)
            ->where('status', '1')
            ->whereBetween('age', [$user_details->from_age_filter, $user_details->to_age_filter])
            ->get()->toArray();


        $usersNearBy = [];

        foreach($all_users_in_same_location as $users_list) {
            $distance = Common::distance_meters($user_details->latitude, $user_details->longitude, $users_list['latitude'], $users_list['longitude'], $radius);

            if ($distance !== null && $distance <= $radius) {
                // If the distance is within the specified radius, add the user to the $usersNearBy array
                $usersNearBy[] = $users_list;
            }
        }

        $subscription = SubscriptionModel::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->select(['lets_count', 'id', 'end_date'])
            ->first();

        $hasExpired = Carbon::now()->isAfter($subscription->end_date);

        if ($hasExpired) {
            return response()->json(['message' => 'Your Let\'s Subscription is Expired'], 401);
        } else {
            if($subscription->lets_count !== 0) {
                $event_name = $data['event_name'];
                $duration = $data['duration'];
                $creator_longitude = $data['creator_longitude'];
                $creator_latitude = $data['creator_latitude'];
    
                $newLetsRecord = LetsModel::create([
                    'user_id' => $user->id,
                    'event_name' => $event_name,
                    'duration' => $duration,
                    // 'creator_selfie' => $creator_selfie_path,
                    'creator_longitude' => $creator_longitude,
                    'creator_latitude' => $creator_latitude
                ]);
    
                // Get the ID of the newly inserted record
                $newLetsRecordId = $newLetsRecord->id;

                foreach ($usersNearBy as $userNearByList) {

                    $newLetsReceiverRecord = LetsReceiverLogModel::create([
                        'user_id' => $userNearByList['id'],
                        'lets_id' => $newLetsRecordId,
                        'user_longitude' => $userNearByList['longitude'],
                        'user_latitude' => $userNearByList['latitude']
                    ]);
                }
    
                // Decrement lets_count in SubscriptionModel
                // SubscriptionModel::where('user_id', $user->id)->where('id', $subscription->id)->decrement('lets_count');

                Common::logSystemActivity('User Created Lets', 'Lets Created', 'API');
    
                return response()->json(['message' => 'Lets Created Successfully'], 200);
    
    
            }else {
                return response()->json(['message' => 'You are out of limits to create New Lets'], 401);
            }
        }
    }

    public function letsAcceptor(Request $request) {
        $user = Auth::user();

        if (is_null($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $lets_id = $request->lets_id;
        $action = $request->action;

        if ($request->hasFile('acceptor_selfie')) {
            $acceptor_selfie_path = $request->file('acceptor_selfie')->store('image', 'public');
        } else {
            $acceptor_selfie_path = NULL;
        }

        $userData = User::find($user->id);

        $letsReceiverDetails = LetsReceiverLogModel::where('lets_id', $lets_id)
    ->orderBy('created_at', 'desc')
    ->select(['user_longitude', 'user_latitude', 'action', 'user_id', 'id', 'lets_id'])
    ->get()->toArray();

        // Common::print_r_custom($letsReceiverDetails);

        foreach($letsReceiverDetails as $details) {
            if($details['action'] == 'accept') {
                return response()->json(['message' => 'OOPS! You Just missed it'], 200);
            }
            else {
                $letsReceiverDetails = LetsReceiverLogModel::where('user_id', $user->id)
                    ->where('lets_id', $lets_id)
                    ->orderBy('created_at', 'desc')
                    ->select(['user_longitude', 'user_latitude', 'action', 'user_id', 'id'])
                    ->first();

                if ($letsReceiverDetails) {
                    // Update the 'action' attribute with the new value
                    $letsReceiverDetails->action = $action;

                    // Save the updated record
                    $letsReceiverDetails->save();

                    response()->json(['message' => 'Lets Receiver action updated successfully'], 200);
                }
            }
        }

        $lets_details = LetsModel::where('id', $lets_id)->first();

        if ($lets_details) {
            // Update the attributes as needed
            // $record = $letsReceiverDetailsMissed->first();

            $lets_details->acceptor_id = $user->id;
            $lets_details->acceptor_longitude = $letsReceiverDetails->user_longitude;
            $lets_details->acceptor_latitude = $letsReceiverDetails->user_latitude;
            $lets_details->acceptor_selfie = $acceptor_selfie_path;
            $lets_details->handshake_status = '1';
            $lets_details->updated_at = date('Y-m-d H:i:s', time());
        
            // Save the updated record
            $lets_details->save();
        
            response()->json(['message' => 'Lets record updated successfully'], 200);
        } else {
            return response()->json(['message' => 'Lets record not found'], 404);
        }

        $subscription = SubscriptionModel::where('user_id', $lets_details->user_id)
            ->orderBy('created_at', 'desc')
            ->select(['id'])
            ->first();

        // Decrement lets_count in SubscriptionModel
        SubscriptionModel::where('user_id', $lets_details->user_id)->where('id', $subscription->id)->decrement('lets_count');

        Common::logSystemActivity('User Accepted Lets', 'Lets Accepted', 'API');

        return response()->json(['message' => 'Lets Accepted Successfully'], 200);
    }

    public function getLetsDetails() {
        $user = Auth::user();

        if (is_null($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $lets_data = LetsModel::where('user_id', $user->id)->where('status', '1')->get();

        return response()->json(['message' => 'Lets Data', 'list' => $lets_data], 200);
    }
}
