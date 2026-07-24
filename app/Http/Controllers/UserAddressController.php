<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{UserAddress,User};


class UserAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $id=auth()->user()->id;
        $items=UserAddress::where(['user_id'=> $id])->latest()->paginate(20);
        
        return view('user_dashboards.address', compact('items'));
        
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('checkouts.user_address_modal');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data=$request->validate([
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
        ]);
        $data['user_id']=auth()->user()->id;
        UserAddress::Create($data);
        $url=route('front.user-address.index');
        return response()->json(['success'=>true,'msg'=>'Adderess Create successfully!','url'=>$url,'modal'=>'#addressModal']);


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
    public function edit($id)
    {
        $item=UserAddress::find($id);
        return view('checkouts.user_address_edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $item=UserAddress::find($id);

        $data=$request->validate([
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required',
        ]);
        $item->update($data);
        $url=route('front.user-address.index');
        return response()->json(['success'=>true,'msg'=>'Adderess Update successfully!','url'=>$url,'modal'=>'#addressModal']);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
