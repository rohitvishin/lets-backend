<?php

namespace App\Http\Controllers\API;

use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Mail;
use App\Mail\SendOTP;
use App\Models\PlansModel;
use App\Models\SubscriptionModel;

class AuthController extends Controller
{
    public function sign_up(Request $request){

        try {
            $data = $request->validate([
                'name' => 'required|string',
                'email' => 'required|string|unique:users,email',
                'username' => 'required|string|unique:users,username',
                'password' => 'required|string',
                'age' => 'numeric',
                'phone' => 'numeric|unique:users,phone',
                'dob' => 'date',
                'gender' => 'string',
                'profile1' => 'image|mimes:jpeg,png,jpg|max:2048',
                'profile2' => 'image|mimes:jpeg,png,jpg|max:2048',
                'selfie' => 'image|mimes:jpeg,png,jpg|max:2048',
                'ref_code' => 'string',
                // 'state' => 'required|string',
                // 'city' => 'required|string',
                // 'gender_filter' => 'required|string',
                // 'radius_filter' => 'numeric',
                // 'from_age_filter' => 'numeric',
                // 'to_age_filter' => 'numeric',
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

        $user_id = User::where('referral_code', $data['ref_code'])->value('id');

        // $gender_filter = isset($data['gender_filter']) ? $data['gender_filter'] : null;
        $radius_filter = isset($data['radius_filter']) ? $data['radius_filter'] : 200;
        $from_age_filter = isset($data['from_age_filter']) ? $data['from_age_filter'] : $data['age'] - 2;
        $to_age_filter = isset($data['to_age_filter']) ? $data['to_age_filter'] : $data['age'] + 2;

        $referralCode = Str::random(6);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'username' => $data['username'],
            'age' => $data['age'],
            'phone' => $data['phone'],
            'dob' => $data['dob'],
            'gender' => $data['gender'],
            'profile1' => $profile1_path,
            'profile2' => $profile2_path,
            'selfie' => $selfie_path,
            // 'state' => $data['state'],
            // 'city' => $data['city'],
            // 'gender_filter' => $data['gender_filter'],
            'radius_filter' => 500,
            'from_age_filter' => $data['age']-2,
            'to_age_filter' => $data['age']+2,
            'referral_code' => $referralCode,
            'ref_id' => $user_id
        ]);
        $planData = PlansModel::where('id', 3)->first();

        $start = now();

        $start_date = $start->format('Y-m-d H:i:s');

        $validity = now()->addDays($planData['validity']);

        $end_date = $validity->format('Y-m-d H:i:s');

        $numberOfDays = $start->diffInDays($validity);

        SubscriptionModel::create([
            'user_id' => $user->id,
            'plan_id' => $planData['id'],
            'amount' => $planData['amount'],
            'payment_mode' => 'free',
            'transaction_id' => 'free',
            'lets_count' => $planData['lets_count'],
            'remaining_days_count' => $numberOfDays,
            'validity' => $planData['validity'],
            'start_date' => $start_date,
            'end_date' => $end_date
        ]);

        $token = $user->createToken('apiToken')->plainTextToken;

        $res = [
            'user' => $user,
            'msg' => 'User Created Successful',
            'token' => $token
        ];
        return response($res, 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $data['email'])->where('status', '1')->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response([
                'msg' => 'incorrect username or password'
            ], 401);
        }

        $token = $user->createToken('apiToken')->plainTextToken;

        $res = [
            'user' => $user,
            'msg' => 'Logged-in Successfully',
            'token' => $token
        ];

        return response($res, 201);
    }

    public function verify_email(Request $request) {
        $data = $request->validate([
            'email' => 'required',
            'otp' => 'required'
        ]);

        $user = User::where('email', $data['email'])->first();
        if($user)
        return response()->json(['message' => 'Email already exist'], 409);

        $mailData = [
            'email' => $data['email'],
            'otp' => $data['otp']
        ];
        Mail::to($data['email'])->send(new SendOTP($mailData));
        return response()->json(['message' => 'Email Verified successfully'], 201);
    }

    public function logout()
    {
        $user = Auth::user();

        if ($user) {
            $userId = $user->id;
        } else {
            // Handle the case when no user is authenticated
            $userId = null;
        }

        if ($user) {
            $user->tokens->each(function ($token) {
                $token->delete();
            });

            return response()->json(['message' => 'Logged out successfully'], 201);
        }

        return response()->json(['message' => 'User not found'], 404);
    }


}
