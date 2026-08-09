<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Contact;
use App\Utils\UserType;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Auth;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    

    
    
    public function register(Request $request)
    {
      
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email|email:rfc,dns',
            'mobile'    => 'required|unique:users,mobile|regex:/^01[3-9]\d{8}$/',
            'password' => 'required|min:6|confirmed',
        ]);

        // Create User
        
        $contact=Contact::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'mobile'=>$request->mobile,
                'type'=>'customer',
                'add_from'=> UserType::CUSTOMER_ADDED_FROM_ECOMMERCE, // add_from 1 is for direct customer registration
        ]);
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'mobile'    => $request->mobile,
            'contact_id'    => $contact->id,
            'password' => Hash::make($request->password),
        ]);
    
        // Login the newly registered user
        Auth::login($user);

        // Redirect to intended URL or dashboard
        $redirectUrl = session('url.intended', url('/'));

        session()->forget('url.intended'); // clear intended URL

        return redirect()->to($redirectUrl);
    }
    

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
