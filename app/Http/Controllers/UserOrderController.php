<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Transaction,User};


class UserOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request){
        
        $shipping_status=$request->shipping_status;
        $payment_status=$request->payment_status;
        $id=auth()->user()->id;
        $query=Transaction::where(['user_id'=> $id,'type'=>'sell','is_new'=>0]);
        
                if($shipping_status){
                    $query->where('shipping_status',$shipping_status);
                }
                
                if($payment_status){
                    $query->where('payment_status',$payment_status);
                }
                
        $data['items']=$query->latest()->paginate(8);
        $data['title']='All Order';
        $data['order']=1;
        $data['status']=[
    		'pending'=>'New Order',
    		'on_hold'=>'On Hold',
    		
    		'processing'=>'Processing',
    		'shipped'=>'Shipped',
    		'delivered'=>'Delivered',
    	];
    	
    	$data['pstatus']=[
    		'due'=>'Due',
    		'paid'=>'Paid',
    		'partial'=>'Partial',
    	];
    	
        
        return view('user_dashboards.orders', $data);
        
    }
    
    public function cancelledOrder(){
        
        $id=auth()->user()->id;
        $data['items']=Transaction::where(['user_id'=> $id,'type'=>'sell','is_new'=>0,'shipping_status'=>'cancelled','is_pos'=>0])->latest()->paginate(8);
        $data['title']='Cacelled Order';
        
        return view('user_dashboards.orders', $data);
        
    }
    
    public function returnOrder(){
        
        $id=auth()->user()->id;
        $data['items']=Transaction::where(['user_id'=> $id,'type'=>'sell','is_new'=>0,'shipping_status'=>'return','is_pos'=>0])->latest()->paginate(8);
        $data['title']='Return Order';
        return view('user_dashboards.orders', $data);
        
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
        $userid=auth()->user()->id;
        $item=Transaction::where(['user_id'=> $userid,'id'=>$id])->first();
        if(empty($item)){
            abort(401);
        }
        return view('user_dashboards.order_details', compact('item'));
        
        
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
