<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\SubscriptionModel;
use App\Models\LetsModel;
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

        $letsCount = SubscriptionModel::where('user_id', $user->id)
            ->orderBy('created_at', 'desc') // Assuming you want to order by the 'created_at' column
            ->value('lets_count');

        $subId = SubscriptionModel::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->value('id');

        if($letsCount !== 0) {
            $event_name = $request->event_name;
            $duration = $request->duration;
            $creator_longitude = $request->creator_longitude;
            $creator_latitude = $request->creator_latitude;

            if ($request->hasFile('creator_selfie')) {
                $creator_selfie_path = $request->file('creator_selfie')->store('image', 'public');
            } else {
                $creator_selfie_path = NULL;
            }

            $newLetsRecord = LetsModel::create([
                'user_id' => $user->id,
                'event_name' => $event_name,
                'duration' => $duration,
                'creator_selfie' => $creator_selfie_path,
                'creator_longitude' => $creator_longitude,
                'creator_latitude' => $creator_latitude
            ]);

            // Get the ID of the newly inserted record
            $newLetsRecordId = $newLetsRecord->id;

            // Decrement lets_count in SubscriptionModel
            SubscriptionModel::where('user_id', $user->id)->where('id', $subId)->decrement('lets_count');

            return response()->json(['message' => 'Lets Created Successfully'], 200);


        }else {
            return response()->json(['message' => 'You are out of limits to create New Lets'], 401);
        }
    }

    public function letsAcceptor(Request $request) {
        $user = Auth::user();

        if (is_null($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $lets_id = $request->lets_id;
        $acceptor_longitude = $request->acceptor_longitude;
        $acceptor_latitude = $request->acceptor_latitude;

        // if ($request->hasFile('acceptor_selfie')) {
        //     $creator_selfie_path = $request->file('creator_selfie')->store('image', 'public');
        // } else {
        //     $creator_selfie_path = NULL;
        // }

        $userData = User::find($user->id);

        $newLetsRecord = LetsModel::create([
            'acceptor_id' => $user->id,
            // 'acceptor_selfie' => $acceptor_selfie,
            'acceptor_longitude' => $acceptor_longitude,
            'acceptor_latitude' => $acceptor_latitude
        ]);

        return response()->json(['message' => 'Lets Accepted Successfully'], 200);
    }
}
