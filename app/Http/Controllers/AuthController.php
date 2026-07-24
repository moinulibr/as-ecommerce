<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Models\User;
use App\Models\VendorAddress;
use Hash;
use DB;

class AuthController extends Controller
{
    public function sellerRegister(){

        return view('auth.seller_register');
    }
    
    
    public function userLogin(Request $request)
    {
        if ($request->filled('redirect')) {
            session(['url.intended' => $request->redirect]);
        }
        
        
        
        return view('auth.login');
    }
    
    public function sellerRegisterPost(Request $request)
    {
        // dd($request);
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'mobile'    => 'required|unique:users,mobile|regex:/^(?:\+88|88)?(01[3-9]\d{8})$/',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',

    
            // vendor address table fields
            'shop_name'     => 'required|string|max:255',
            'trade_license' => 'required|string|max:255',
            'fax'           => 'nullable|string|max:255',
            'website'       => 'nullable|string|max:255',
            'address'       => 'nullable|string',
            'our_mission'   => 'nullable|string',
            'our_vision'    => 'nullable|string',
        ]);
        
        DB::beginTransaction();

        try {
           
           // Create user
            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'mobile'    => $request->mobile,
                'password'  => Hash::make($request->password),
                // 'is_seller' => 1,
                // 'status'    => 1,
            ]);
            
            DB::table('model_has_roles')->insert([
                'role_id'    => 2,
                'model_type' => 'App\\Models\\User',
                'model_id'   => $user->id
            ]);
    
            // Generate slug from shop_name
            $slug = Str::slug($request->shop_name);
    
            // Ensure slug is unique in VendorAddress table
            $count = VendorAddress::where('slug', 'LIKE', "{$slug}%")->count();
            if ($count > 0) {
                $slug = $slug . '-' . ($count + 1);
            }
        
            VendorAddress::create([
                'user_id'       => $user->id,
                'shop_name'     => $request->shop_name,
                'slug'          => $slug,
                'trade_license' => $request->trade_license,
                'fax'           => $request->fax,
                'website'       => $request->website,
                'address'       => $request->address,
                'our_mission'   => $request->our_mission,
                'our_vision'    => $request->our_vision,
            ]);
        
        
            DB::commit();
            
            $url=route('front.home');
            return response()->json(['status'=>true,'msg'=>'Successfully Created Seller Account!', 'url'=>$url]);
        
            // all good
        } catch (\Exception $e) {
            DB::rollback();
            // something went wrong
            $url=route('front.becomeSeller');
        return response()->json(['status'=>false,'msg'=> $e->getMessage()]);
        }

    }

  
    public function login(Request $request){

        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $url=trim(session()->get('url'));

        if (empty($url)) {
            
            $url= route('front.home');
        }

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return response()->json(['success'=>true,'msg'=>'Successfully Register !', 'url'=>$url]);
        }
        return response()->json(['success'=>false,'msg'=>'Oppes! You have entered invalid credentials !']);


    
    }
  
    public function getOpt(){

        $user_data=session()->get('user_data');
        
        if(empty($user_data)){
            return redirect()->route('login');
        }
     
        return view('auth.otp_verify');
    }
  
  
  
  public function optVerify(){
        
        $user_data=session()->get('user_data');
        date_default_timezone_set("Asia/Dhaka");
        
        
        if(empty($user_data)){
            return redirect()->route('login');
        }
        
        $exp_date = date('Y-m-d H:i:s');
        if(request('button')=='Save'){
            request()->validate([
                'otp_verify' => 'required',
            ]);
            
            if($user_data['otp_verify'] != request('otp_verify')){
                return back()->with('error_msg', 'PIN Is Not Match. please try again !');
            }
            
            if($user_data['exp_time']<$exp_date){
                return back()->with('error_msg', 'Time Is Expired!');
            }
            
            $user=User::where('mobile', $user_data['phone'])->first();
            if($user){
                Auth::loginUsingId($user->id);
                session()->put('user_data',[]);
                
                if(auth()->user()->type=='1'){
                    session()->put('cart',[]);
                }
                return redirect(url('/checkouts'))->with('success_msg', 'Login Success!');
            }else{
                $user=$this->createUser($user_data);
                if($user){
                    Auth::loginUsingId($user->id);
                    session()->put('user_data',[]);
                    return redirect(url('/checkouts'))->with('success_msg', 'Login Success!');
                }else{
                    
                    return back()->with('error_msg', 'Something Went Wrong . try again !');
                }
            }
        } else if(request('button')=='Resend'){
            $date = date('Y-m-d H:i:s');
            $date = strtotime($date);
            $date = strtotime("+3 minute", $date);
            $new_date=date('Y-m-d H:i:s', $date);
            $otp=rand(100000,999999);
            $user_data['exp_time']=$new_date;
            $user_data['otp_verify']=$otp;
            session()->put('user_data', $user_data);
            $number=$user_data['phone'];
            $msg='Your One-Time PIN is '.$otp.'. It will expire in 3 minutes.Visit softitsecurity.com';
    
            $success=sendSMS($number ,$msg);
            $res=json_decode($success);
            if (isset($res->Status) && ($res->Status =='0')) {
                return redirect()->route('front.getOpt')->with('status','PIN Is Send Check Your Phone');
            }else{
              return back()->with('error_msg','OTP Is Send Check Your Phone');
            }
            
            
            
        }
    }
  
    private function createUser($data){
        $user=User::create([
            'mobile' => $data['phone'],
        ]);
        return $user;
    }
  
  


    public function register(Request $request){
        
        $request->validate([
            'email' => 'nullable|email|unique:users',
            'name' => 'required',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required',
        ]);

        $data = $request->all();
        $user = $this->create($data);

        $url=session()->get('url');

        if (empty($url)) {
            $url= route('front.home');
        }

        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            return response()->json(['success'=>true,'msg'=>'Successfully Register !', 'url'=>route('front.home')]);
        }
        return response()->json(['success'=>false,'msg'=>'Oppes! You have entered invalid credentials !']);
        
    }



    public function create(array $data)
    {

      return User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password'])
      ]);
    }

}
