<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Transaction,User};
use Hash;
class DashboardController extends Controller
{
    public function index()
    {
        $user=auth()->user();
        $items=Transaction::where(['user_id'=> $user->id,'type'=>'sell','is_new'=>0])->latest()->take(8)->get();
        
        return view('user_dashboards.index', compact('items','user'));
    }
    public function orders(Request $request)
    {
        $id=auth()->user()->id;

        $status=$request->status;
        $q=$request->q;
        $items=Transaction::where('user_id', $id)->latest()->paginate(30);
        return view('user_dashboards.orders','items','q','status');
    }
    public function create(){
        $user=auth()->user();
        return view('user_dashboards.profile', compact('user'));
    }
    public function wishlists(){
        return view('user_dashboards.wishlists');
    }
    
    public function confirmOrder($id){
        $order=Transaction::find($id);
        return view('user_dashboards.thank_you', compact('order'));
    }
    
    public function confirmOrderlanding($id){
        $order=Transaction::find($id);
        $info=Information::first();
        return view('user_dashboards.thank_you_landing', compact('order', 'info'));
    }

    public function oredrDetails($id) 
    {
        $item=Transaction::find($id);
        return view("user_dashboards.order_show", compact('item'));

    }

    public function update(Request $request, $id){

        $user=auth()->user();

        $data=$request->validate([
            'name' => 'required',
            'dob' => '',
            'gender' => 'required',
            'mobile' => 'required|unique:users,mobile,'.$user->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        if($request->hasFile('image')){
            $image = $request->file('image');
    
            // folder create if not exists
            $destinationPath = public_path('images/profile');
            if(!file_exists($destinationPath)){
                mkdir($destinationPath, 0755, true);
            }
    
            // unique image name
            $imageName = time() . '_' . $image->getClientOriginalName();
    
            // move file to public/images/profile
            $image->move($destinationPath, $imageName);
    
            // update data array
            $data['image'] = $imageName;
        }
        
        $user->update($data);
        return redirect()->back()->with('success', 'User Successfully Updated!');

        // return response()->json(['success'=>true,'msg'=>'User  Successfully Updated!','url'=>route('front.dashboards.create')]);
        

    }


    public function passwordUpdate(Request $request)
    {
        $user = auth()->user();
    
        // Validation rules
        $rules = [
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ];
    
        // If user already has a password, require currentPassword
        if (!empty($user->password)) {
            $rules['currentPassword'] = 'required';
        }
    
        $request->validate($rules);
    
        // If user already has a password, check current password
        if (!empty($user->password)) {
            if (!Hash::check($request->currentPassword, $user->password)) {
                return redirect()->back()->with('error', 'Current password does not match!');
            }
    
            if (Hash::check($request->password, $user->password)) {
                return redirect()->back()->with('error', 'New password cannot be the old password!');
            }
        }
    
        // Update password
        $user->password = Hash::make($request->password);
        $user->save();
    
        return redirect()->back()->with('success', 'Password updated successfully!');
    }
    
    public function cancelRequest(Request $request)
    {
        $request->validate([
            'sell_id' => 'required|integer',
            'note' => 'nullable|string',
            'agree' => 'required|boolean',
        ]);
    
        $order = Transaction::findOrFail($request->sell_id);

        $order->cancel_request = 1;
        $order->cancel_note = $request->note;
        $order->save();
    
        return response()->json([
            'message' => 'Cancel request submitted successfully!'
        ]);
    }

}
