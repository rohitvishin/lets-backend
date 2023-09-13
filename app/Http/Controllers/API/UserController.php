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

use function PHPUnit\Framework\isNull;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users_data = User::where('status', '1')->get();

        return response()->json(['message' => 'Users Data', 'list' => $users_data], 200);
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
                'email' => 'required|string|unique:users,email,' . $user->id, // Add the user's ID
                'username' => 'required|string|unique:users,username,' . $user->id, // Add the user's ID
                'password' => 'required|string',
                'age' => 'numeric',
                'phone' => 'numeric|unique:users,phone,' . $user->id, // Add the user's ID
                'dob' => 'date',
                'gender' => 'string',
                'profile1' => 'image|mimes:jpeg,png,jpg|max:2048',
                'profile2' => 'image|mimes:jpeg,png,jpg|max:2048',
                'selfie' => 'image|mimes:jpeg,png,jpg|max:2048',
                'state' => 'required|string',
                'city' => 'required|string',
                // 'gender_filter' => 'required|string',
                'radius_filter' => 'numeric',
                'from_age_filter' => 'numeric',
                'to_age_filter' => 'numeric',
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
        $userData->email = $data['email'];
        $userData->password = bcrypt($data['password']);
        $userData->username = $data['username'];
        $userData->age = $data['age'];
        $userData->phone = $data['phone'];
        $userData->dob = $data['dob'];
        $userData->gender = $data['gender'];
        $userData->profile1 = $profile1_path;
        $userData->profile2 = $profile2_path;
        $userData->selfie = $selfie_path;
        $userData->state = $data['state'];
        $userData->city = $data['city'];
        // $userData->gender_filter = $data['gender_filter'];
        $userData->radius_filter = $data['radius_filter'];
        $userData->from_age_filter = $data['from_age_filter'];
        $userData->to_age_filter = $data['to_age_filter'];

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
}
