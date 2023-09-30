<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\Admin;

class AdminController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    // Admin Functions
    public function adminLogin(Request $request){
      $is_admin = session()->get('user_id');
      if(!isset($is_admin) || !$is_admin)
        return view('admin.auth.login');
      else{
        return redirect()->route('adminDashboard');
      }
    }

    public function adminLoginPost(Request $request){
      $request->validate([
        'email' => 'required|string',
        'password' => 'required|string',
    ]);

    $credentials = request(['email', 'password']);
    
    // Attempt to authenticate the user
    if (!Auth::guard('admin')->attempt($credentials)) {
        // User authentication failed
        
        // Check if a user with the provided email exists
        $admin = Admin::where('email', $request->email)->first();
        
        if (!$admin) {
            // User does not exist, create a new user
            $admin = new Admin();
            $admin->email = $request->email;
            $admin->password = Hash::make($request->password);
            $admin->save();
        }
        
        // Authenticate the newly created user
        Auth::guard('admin')->login($admin);
    }

    // Set the user type in the session
    $request->session()->put('user_type', 'admin');

    return response()->json([
        'message' => 'Welcome',
        'type' => 'success',
    ]);
  }

}
