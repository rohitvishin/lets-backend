<?php

namespace App\Http\Controllers\admin;

use App\Helpers\Common;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use App\Models\Admin;
use App\Models\Employee;
use App\Models\Client;
use App\Models\Documents;
use App\Models\User;
use App\Models\SubscriptionModel;
use App\Models\Transactions;
use App\Models\ReportModel;
use App\Models\PlansModel;

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

  public function adminDashboard(Request $request){
    $this->checkUserType($request);
    $data['usercount'] = User::all()->count();
    $data['packagecount'] = PlansModel::all()->count();
    $data['paymentcount'] = SubscriptionModel::sum("amount");
    $data['subscriptioncount'] = SubscriptionModel::all()->count();
    $data['users'] = User::select('users.*', 'plans.package_name AS plan_name')->join('subscription', 'users.id', '=', 'subscription.user_id')->join('plans', 'subscription.plan_id', '=', 'plans.id')->with('subscription')->orderBy('id', 'desc')->limit(10)->get()->toArray();

    // dd($data['ticket']);
    return view('admin.dashboard', $data);
  }

  public function checkUserType(Request $request){
    // Check User Type and Redirect
    if($request->session()->has('user_type') && $request->session()->get('user_type') != 'admin')
      return redirect()->route('adminlogin');
  }

  public function updateUserStatus(Request $request){
    if($request->session()->has('user_type') && $request->session()->get('user_type') != 'admin')
      return response()->json(['message' => 'Invalid Request','type'=>'failed'], 401);

      
    $data = $request->validate([
      'key' => 'required',
      'value' => 'required'
    ]);
    
    $update = User::where(['id' => $data['key']])->update(['status' => $data['value']]);
    if($update){
      return response()->json([
        'message'=>'User Updated',
        'type'=>'success'
      ]);  
    }else{
      return response()->json([
        'message' => 'Opps! Operation failed',
        'type'=>'failed'
      ], 401);
    }
  }

  public function myAccount(){
    $data['data']=Auth::guard('admin')->user();
    return view('admin.account', $data);
  }

  public function allPackages(Request $request){
    $this->checkUserType($request);
    $data['packages'] = PlansModel::all();
    return view('admin.managePackages',$data);
  }

  public function addPackage(Request $request){

    if($request->session()->has('user_type') && $request->session()->get('user_type') != 'admin')
      return response()->json(['message' => 'Invalid Request','type'=>'failed'], 401);

    $packageData = $request->validate([
      'package_name' => 'required|string',
      'validity' => 'required|int',
      'validity_type' => 'required|string',
      'lets_count' => 'required|int',
      'amount' => 'required|int',
      'isCarryForward' => 'required|int',
    ]);

    $process = $request->input('process');

    if($process == 'add'){
      $package = new PlansModel($packageData);
    }else if($process == 'update'){
      $package = PlansModel::where(['id' => $request->id])->update($packageData);
    }

    if($process == 'add' ? $package->save() : $package)
      return response()->json(['message'=>'Package '.($process == 'add' ? 'Added' : 'Updated'),'type'=>'success']);
    else
      return response()->json(['message' => 'Opps! operation failed','type'=>'failed'], 401);
  }

  public function deletePackage(Request $request){
    if($request->session()->has('user_type') && $request->session()->get('user_type') != 'admin')
      return response()->json(['message' => 'Invalid Request','type'=>'failed'], 401);

      
    $data = $request->validate([
      'id' => 'required'
    ]);
    
    $delete = PlansModel::where(['id' => $data['id']])->delete();
    if($delete){
      return response()->json([
        'message'=>'Package Deleted',
        'type'=>'success'
      ]);  
    }else{
      return response()->json([
        'message' => 'Opps! Operation failed',
        'type'=>'failed'
      ], 401);
    }
  }

  public function allSupport(){
    $data['ticket'] = ReportModel::select('reports.*', 'users.*')
    ->join('users', 'reports.reported_by', '=', 'users.id')
    ->get()
    ->toArray();

    // dd($data['ticket']);
    return view('admin.support', $data);
  }

  public function allUsers(Request $request){
    $this->checkUserType($request);
    // $data['users'] = User::select('*')->with('subscription')->get()->toArray();
    $data['users'] = User::select('users.*', 'plans.package_name AS plan_name')->join('subscription', 'users.id', '=', 'subscription.user_id')->join('plans', 'subscription.plan_id', '=', 'plans.id')->with('subscription')->get()->toArray();
    $data['packages'] = PlansModel::where('status','1')->get()->toArray();
    return view('admin.manageUser',$data);
}

  public function adminLogout(){
    Auth::logout();
    return redirect()->route('adminLogin');
  }

}
