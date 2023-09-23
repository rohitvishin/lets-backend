<?php

namespace App\Http\Controllers\API;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

use App\Helpers\Common;
use App\Models\LetsModel;

use function PHPUnit\Framework\isNull;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        if (is_null($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }
        
        $users = User::all(); // Assuming you have a User model

        $usersWithLast3Activities = [];
        
        foreach ($users as $user) {
            $last3Activities = LetsModel::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
        
            $usersWithLast3Activities[] = [
                'user' => $user,
                'last_3_activities' => $last3Activities,
            ];
        }
        
        return response()->json(['message' => 'Users Data', 'user_list' => $usersWithLast3Activities], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        if (is_null($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }

        try {
            $data = $request->validate([
                'name' => 'required|string',
                // 'email' => 'required|string|unique:users,email,' . $user->id, // Add the user's ID
                'username' => 'required|string|unique:users,username,' . $user->id, // Add the user's ID
                'password' => 'required|string',
                'age' => 'numeric',
                // 'phone' => 'numeric|unique:users,phone,' . $user->id, // Add the user's ID
                'dob' => 'date',
                'gender' => 'string',
                'profile1' => 'image|mimes:jpeg,png,jpg|max:2048',
                'profile2' => 'image|mimes:jpeg,png,jpg|max:2048',
                'selfie' => 'image|mimes:jpeg,png,jpg|max:2048'
            ]);
            
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 400);
        }

        if ($request->hasFile('profile1')) {
            $profile1_path = $request->file('profile1')->store('image', 'public');
        } else {
            $profile1_path = NULL;
        }

        if ($request->hasFile('profile2')) {
            $profile2_path = $request->file('profile2')->store('image', 'public');
        } else {
            $profile2_path = NULL;
        }

        if ($request->hasFile('selfie')) {
            $selfie_path = $request->file('selfie')->store('image', 'public');
        } else {
            $selfie_path = NULL;
        }

        $userData = User::find($user->id);

        // Update the user's information using the $user object
        $userData->name = $data['name'];
        // $userData->email = $data['email'];
        $userData->password = bcrypt($data['password']);
        $userData->username = $data['username'];
        $userData->age = $data['age'];
        // $userData->phone = $data['phone'];
        $userData->dob = $data['dob'];
        $userData->gender = $data['gender'];
        $userData->profile1 = $profile1_path;
        $userData->profile2 = $profile2_path;
        $userData->selfie = $selfie_path;

        // Save the updated user
        $userData->save();

        $res = [
            'user' => $userData,
            'msg' => 'User Updated Successful'
        ];
        return response($res, 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();

        if (is_null($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $userData = User::find($user->id);

        // Validate the request data
        try {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required',
                'confirm_password' => 'required|same:new_password'
            ]);
            
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 400);
        }

        // Check if the current password matches the user's password
        if (!Hash::check($request->input('current_password'), $userData['password'])) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }

        // Update the user's password
        $userData->password = Hash::make($request->input('confirm_password'));
        $userData->save();

        return response()->json(['message' => 'Password changed successfully'], 200);
    }

    public function update_location_api(Request $request)
    {
        $user = Auth::user();

        if (is_null($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }

        try {
            $data = $request->validate([
                'state' => 'required|string',
                'city' => 'required|string',
                'latitude' => 'required|string',
                'longitude' => 'required|string'
            ]);
            
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 400);
        }

        $userData = User::find($user->id);

        // Update the user's information using the $user object
        $userData->state = $data['state'];
        $userData->city = $data['city'];
        $userData->longitude = $data['longitude'];
        $userData->latitude = $data['latitude'];

        // Save the updated user
        $userData->save();

        $res = [
            'user' => $userData,
            'msg' => 'User\'s Location Updated Successfully'
        ];
        return response($res, 201);
    }

    public function update_filter_api(Request $request)
    {
        $user = Auth::user();

        if (is_null($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }

        try {
            $data = $request->validate([
                'gender_filter' => 'string',
                'radius_filter' => 'numeric',
                'from_age_filter' => 'numeric',
                'to_age_filter' => 'numeric',
            ]);
            
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 400);
        }

        $userData = User::find($user->id);

        $gender_filter = isset($data['gender_filter']) ? $data['gender_filter'] : null;
        $radius_filter = isset($data['radius_filter']) ? $data['radius_filter'] : 200;
        $from_age_filter = isset($data['from_age_filter']) ? $data['from_age_filter'] : null;
        $to_age_filter = isset($data['to_age_filter']) ? $data['to_age_filter'] : null;

        // Update the user's information using the $user object
        $userData->gender_filter = $gender_filter;
        $userData->radius_filter = $radius_filter;
        $userData->from_age_filter = $from_age_filter;
        $userData->to_age_filter = $to_age_filter;

        // Save the updated user
        $userData->save();

        $res = [
            'user' => $userData,
            'msg' => 'User\'s Filter Updated Successfully'
        ];
        return response($res, 201);
    }

    public function getUserDetails(Request $request)
    {
        $user = Auth::user();

        if (is_null($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }

        try {
            $data = $request->validate([
                'token' => 'required|string'
            ]);
            
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 400);
        }

        $userData = User::find($user->id);

        $res = [
            'user' => $userData,
            'msg' => 'Users Details as per Token'
        ];
        return response($res, 201);
    }
}
