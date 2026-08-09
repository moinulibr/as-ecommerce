<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Contact;
use App\Utils\UserType;
use Illuminate\Support\Facades\Auth;

class SocialitLogineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Socialite::driver('google')
                ->scopes(['openid','profile','email'])
                ->redirect();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(){

        $googleUser = Socialite::driver('google')->stateless()->user();
        
        
        $contact=Contact::firstOrCreate([
                
                 'email' => $googleUser->getEmail()],
                [
                    'name'=>$googleUser->getName(),
                    'type'=>'customer',
                    'add_from'=>UserType::CUSTOMER_ADDED_FROM_ECOMMERCE_SOCIALITE, //add_from 2 is for socialite registration
            ]);
            
            
        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'contact_id'    => $contact->id,
                // 'password' => bcrypt(str()->random(16)),
            ]
        );

        Auth::login($user);

        return redirect('/');
        
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
}
