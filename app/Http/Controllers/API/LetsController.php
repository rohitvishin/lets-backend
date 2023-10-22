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
use Illuminate\Support\Facades\Storage;

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

    public function letsCreator(Request $request)
    {
        $user = Auth::user();

        if (is_null($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user_details = User::find($user->id);

        $radius = $user_details->radius_filter;

        try {
            $data = $request->validate([
                'event_name' => 'required|string',
                'duration' => 'numeric'
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

        foreach ($all_users_in_same_location as $users_list) {


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
            if ($subscription->lets_count !== 0) {
                $event_name = $data['event_name'];
                $duration = $data['duration'];
                $creator_longitude = $user_details->longitude;
                $creator_latitude = $user_details->latitude;

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

                    // Notify User
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
            } else {
                return response()->json(['message' => 'You are out of limits to create New Lets'], 401);
            }
        }
    }
    public function letsNoUser()
    {
        $user = Auth::user();

        if (is_null($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $lets_data = LetsModel::where(['user_id' => $user->id])->latest()->first();
        if (!empty($lets_data->id)) {
            LetsReceiverLogModel::where('lets_id', $lets_data->id)->update(['action' => 'reject']);
            return response()->json(['message' => 'Lets updated'], 200);
        }
        return response()->json(['message' => 'Lets not found'], 400);
    }
    public function letsAcceptor(Request $request)
    {
        $user = Auth::user();

        if (is_null($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $lets_id = $request->lets_id;
        $action = $request->action;

        if ($request->has('acceptor_selfie')) {
            $base64Image = $request->input('acceptor_selfie');
            list($type, $base64Data) = explode(';', $base64Image);
            list(, $base64Data) = explode(',', $base64Data);
            $imageData = base64_decode($base64Data);
            $extension = explode('/', explode(':', substr($base64Image, 0, strpos($base64Image, ';')))[1])[1];
            $fileName = time() . '.' . $extension;
            $fullPath = Storage::disk('public')->put('image/' . $fileName, $imageData);
            $acceptor_selfie_path = "image/" . $fileName;
        } else {
            $acceptor_selfie_path = NULL;
        }

        $letsReceiverDetails = LetsReceiverLogModel::where(['lets_id' => $lets_id, 'user_id' => $user->id])
            ->select(['user_longitude', 'user_latitude', 'action', 'user_id', 'id', 'lets_id'])
            ->first();
        if (!$letsReceiverDetails) {
            return response()->json(['message' => 'Lets record not found'], 401);
        }
        // Common::print_r_custom($letsReceiverDetails);

        if ($letsReceiverDetails['action'] !== null) {
            return response()->json(['message' => 'OOPS! You Just missed it'], 200);
        } else {
            if ($letsReceiverDetails) {
                // Update the 'action' attribute with the new value
                $letsReceiverDetails->action = $action;

                // Save the updated record
                $letsReceiverDetails->save();

                response()->json(['message' => 'Lets Receiver action updated successfully'], 200);
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
            $lets_details->handshake_status = 0;
            $lets_details->updated_at = date('Y-m-d H:i:s', time());

            // Save the updated record
            $lets_details->save();

            // delete all other request sent
            LetsReceiverLogModel::where('lets_id', $lets_id)->where('user_id', '!=', $user->id)->delete();

            response()->json(['message' => 'Lets record updated successfully'], 200);
        } else {
            return response()->json(['message' => 'Lets record not found'], 404);
        }

        $subscription = SubscriptionModel::where('user_id', $lets_details->user_id)
            ->select('id')
            ->latest()
            ->first();

        // Decrement lets_count in SubscriptionModel
        SubscriptionModel::where('user_id', $lets_details->user_id)->where('id', $subscription->id)->decrement('lets_count');

        // Common::logSystemActivity('User Accepted Lets', 'Lets Accepted', 'API');

        return response()->json(['message' => 'Lets Accepted Successfully'], 200);
    }

    public function getLetsDetails()
    {
        $user = Auth::user();

        if (is_null($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $creator = [
            'name' => $user['name'],
            'age' => $user['age'],
            'profile' => $user['profile1'],
            'longitude' => $user['longitude'],
            'latitude' => $user['latitude'],
        ];
        $lets_data = LetsModel::where(['user_id' => $user->id])->latest()->first();
        if (!empty($lets_data->acceptor_id))
            $acceptor = User::select('name', 'age', 'longitude', 'latitude')->where('id', $lets_data->acceptor_id)->first();
        else
            $acceptor = [];
        return response()->json(['message' => 'Lets Data', 'list' => $lets_data, 'acceptor' => $acceptor, 'creator' => $creator], 200);
    }

    public function getLetsDetailRequests()
    {
        $user = Auth::user();

        if (is_null($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }
        // get incoming request
        $lets_receiver_logs = LetsReceiverLogModel::select('lets_id')->where(['user_id' => $user->id, 'action' => null])->latest()->get();
        $letsArr = [];
        foreach ($lets_receiver_logs as $lets) {
            // get lets details of request
            $letsEvt = LetsModel::select('id', 'user_id', 'event_name', 'duration')->where(['id' => $lets->lets_id, 'acceptor_id' => null])
                ->first();
            if ($letsEvt) {
                $lets_data['lets'] = $letsEvt;
                // get last 3 activities of lets shooter
                $lets_data['activities'] = LetsModel::select('event_name')->where(['user_id' => $letsEvt->user_id, 'handshake_status' => 2])
                    ->latest()
                    ->take(3)
                    ->get();
                $lets_data['shooter'] = User::select('name', 'age', 'gender', 'profile1', 'profile2')->where('id', $letsEvt->user_id)
                    ->first();
                array_push($letsArr, $lets_data);
            }
        }
        return response()->json(['message' => 'Lets Request Data', 'lets_data' => $letsArr], 200);
    }

    public function getMatchLocation(Request $request)
    {
        $lets_data = LetsModel::where(['id' => $request->id])->first();
        if ($lets_data)
            return response()->json(['message' => 'Lets Request Data', 'lets_data' => $lets_data], 200);
        else
            return response()->json(['message' => 'Lets Not found'], 400);
    }

    public function updateMatchDetails(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string',
            'longitude' => 'required',
            'latitude' => 'required',
        ]);
        if ($data['type'] == 'creator')
            $query = LetsModel::where(['id' => $request->id])->update(['creator_longitude' => $data['longitude'], 'creator_latitude' => $data['latitude']]);
        if ($data['type'] == 'acceptor')
            $query = LetsModel::where(['id' => $request->id])->update(['acceptor_longitude' => $data['longitude'], 'acceptor_latitude' => $data['latitude']]);
        if ($request->status == 'complete') {
            $query = LetsModel::where(['id' => $request->id])->update(['status' => 2, 'handshake_status' => 2]);
        }
        if ($query)
            return response()->json(['message' => 'Lets updated'], 200);
        else
            return response()->json(['message' => 'Lets Not found'], 400);
    }
}
