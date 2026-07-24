<?php
namespace App\Utils;

use App\Models\SmsSetting;
use App\Models\Subscription;
use App\Models\NotificationTamplate;
use App\Models\Business;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class Util {

    
    public function sendSms($number,$message){


        $item=SmsSetting::first();

        if($item){
        
            
            if($item->method=='get'){
                
                $response = Http::get($item->url, [
                    $item->send_to=>$number,
                    $item->message=>$message,
                    $item->key_1=>$item->key_value_1,
                    $item->key_2=>$item->key_value_2,
                    $item->key_3=>$item->key_value_3,
                    $item->key_4=>$item->key_value_4,
                ]);

            }else if($item->method=='post'){

                $response = Http::post($item->url, [
                    $item->send_to=>$number,
                    $item->message=>$message,
                    $item->key_1=>$item->key_value_1,
                    $item->key_2=>$item->key_value_2,
                    $item->key_3=>$item->key_value_3,
                    $item->key_4=>$item->key_value_4,
                ]);
            }
            return $response->successful();
        }
    
    }
    
    public function subscriptionCheck(){

        $item=Subscription::where('business_id',getBusinessId())->orderby('end_date','desc')->first();
        $date=date('Y-m-d');

        if(($item) && ($item->end_date >=$date)){
            return true;
        }else{
            return false;
        }

    }
    
    
    public function sendNotification($transaction){
        
    
        $user = $transaction->contact;
        
        if($user){
            
            $data = [
                'name' => $user->name,
                'order_no' => $transaction->invoice_no,
                'total' => $transaction->final_amount,
                'items' => $transaction->lines // Array or Collection of products
            ];
            
            if($transaction->mail_notification && $user->email){
                Mail::send('emails.order_confirmation', $data, function($message) use ($user, $data) {
                    $message->to($user->email, $user->name)
                            ->subject('Order Confirmed - #' . $data['order_no']);
                });
            }
            
            if($transaction->sms_notification && $user->mobile){
                $msg = "Hi {$user->name}. We've received your order #{$transaction->invoice_no} and it is now being processed";
                $this->sendSms($user->mobile,$msg);
                
            }
        
            
        }
        
        
    
        
    }

    public function sendNotificationOld($business_id, $transaction, $type){


        
        $template=NotificationTamplate::where([
                'business_id'=>$business_id,
                'type'=>$type,
            ])->first();
        $type_array = array("order", "sell", "order_receive", "order_deliver","receive_not_delivery");
        
        if ($template) {
            $message=$template->body;
                $number='';
            if($type=='salary_payment'){
                    if($transaction->employee){
                        $number=$transaction->employee->phone;
                    }

                }else if($type=='payment'){
                    if($transaction->worker){
                        $number=$transaction->worker->phone;
                    }else if($transaction->employee){
                        $number=$transaction->employee->phone;
                    }

                }else if(in_array($type,$type_array)) {
                    
                    if($transaction->contact){
                        $number = $transaction->contact->phone;
                    }
                    
                
                }else if($type=='worker_assign'){

                    if($transaction->worker){
                        $number=$transaction->worker->phone;
                    }

                }else if($type=='master_assign'){
                   
                    if($transaction->employee){
                        $number=$transaction->employee->phone;
                    }

                }

            $number = $number;
            $message=$this->replaceTags($business_id,$message,$transaction,$type);          
            $this->sendSms($business_id,$number,$message);
        }

    }

    public function replaceTags($business_id, $message,$transaction,$type){
        

        $type_array = array("order", "sell", "order_receive", "order_deliver","receive_not_delivery");
            $business = Business::findOrFail($business_id);
             //Replace contact name
            if (strpos($message, '{contact_name}') !== false) {
                $contact_name='';

                if($type=='salary_payment'){
                    if($transaction->employee){
                        $contact_name=$transaction->employee->name;
                    }
                    

                    if (strpos($message, '{total_payment}') !== false) {
                       $total_payment = $transaction->amount;
                       $message = str_replace('{total_payment}', $total_payment, $message);
                  }

                  if (strpos($message, '{date}') !== false) {
                        $date = $transaction->date;
                        $message = str_replace('{date}', $date, $message);
                  }

                  if (strpos($message, '{due}') !== false) {
                       $employee=$transaction->employee;
                       $due=$employee->salary- $employee->thismonth->sum('amount') - $employee->prvmonth->sum('amount');
                       $message = str_replace('{due}', $due, $message);
                  }

                  if (strpos($message, '{advance}') !== false) {
                       $employee=$transaction->employee;
                       $advance=$employee->prvmonth->sum('amount');
                       $message = str_replace('{advance}', $advance, $message);
                  }


                }else if($type=='payment'){
                    if($transaction->worker){
                        $contact_name=$transaction->worker->name;
                    }else if($transaction->employee){
                        $contact_name=$transaction->employee->name;
                    }else if($transaction->contact){
                        $contact_name=$transaction->contact->name;
                    }
                    

                    if (strpos($message, '{total_payment}') !== false) {
                       $total_payment = $transaction->amount;
                       $message = str_replace('{total_payment}', $total_payment, $message);
                  }

                  if (strpos($message, '{date}') !== false) {
                        $date = $transaction->date;
                        $message = str_replace('{date}', $date, $message);
                  }

                }else if(in_array($type,$type_array)) {

                    $contact_name = $transaction->contact->name;
                    if (strpos($message, '{invoice_no}') !== false) {
                        $invoice_no = $transaction->invoice_no;
                        $message = str_replace('{invoice_no}', $invoice_no, $message);
                    }

                    if (strpos($message, '{total_amount}') !== false) {
                        $total_amount = $transaction->amount;
                        $message = str_replace('{total_amount}', $total_amount, $message);
                    }

                    if (strpos($message, '{total_payment}') !== false) {
                        $total_payment = $transaction->payments->sum('amount');
                        $message = str_replace('{total_payment}', $total_payment, $message);
                    }

                    if (strpos($message, '{due}') !== false) {
                        $due = $transaction->amount - $transaction->payments->sum('amount');
                        $message = str_replace('{due}', $due, $message);
                    }

                    
                    
                    if (strpos($message, '{date}') !== false) {
                        $date = $transaction->date;
                        $message = str_replace('{date}', $date, $message);
                    }
                    
                    if (strpos($message, '{delivery_date}') !== false) {
                        $delivery_date = $transaction->delivery_date;
                        $message = str_replace('{delivery_date}', $delivery_date, $message);
                    }
                    
                }else if($type=='worker_assign'){

                    if($transaction->worker){
                        $contact_name=$transaction->worker->name;
                    }

                    if (strpos($message, '{category_name}') !== false) {
                        $category_name=($transaction->order_category && $transaction->order_category->category) ? $transaction->order_category->category->name:'';
                        $message = str_replace('{category_name}', $category_name, $message);
                    }

                    if (strpos($message, '{total_amount}') !== false) {
                        $total_amount = $transaction->amount;
                        $message = str_replace('{total_amount}', $total_amount, $message);
                    }

                    if (strpos($message, '{date}') !== false) {
                        $date = date('d.m.Y');
                        $message = str_replace('{date}', $date, $message);
                    }

                    if (strpos($message, '{quantity}') !== false) {
                        $quantity=$transaction->quantity;
                        $message = str_replace('{quantity}', $quantity, $message);
                    }

                    if (strpos($message, '{invoice_no}') !== false) {
                        $invoice_no=$transaction->order_category->order->invoice_no;
                        $message = str_replace('{invoice_no}', $invoice_no, $message);
                    }
                }else if($type=='master_assign'){

                    if($transaction->employee){
                        $contact_name=$transaction->employee->name;
                    }

                    if (strpos($message, '{category_name}') !== false) {
                        $category_name=($transaction->order_category && $transaction->order_category->category) ? $transaction->order_category->category->name:'';
                        $message = str_replace('{category_name}', $category_name, $message);
                    }

                    if (strpos($message, '{total_amount}') !== false) {
                        $total_amount = $transaction->amount;
                        $message = str_replace('{total_amount}', $total_amount, $message);
                    }

                    if (strpos($message, '{date}') !== false) {
                        $date = date('d.m.Y');
                        $message = str_replace('{date}', $date, $message);
                    }

                    if (strpos($message, '{quantity}') !== false) {
                        $quantity=$transaction->quantity;
                        $message = str_replace('{quantity}', $quantity, $message);
                    }

                    if (strpos($message, '{invoice_no}') !== false) {
                        $invoice_no=$transaction->order_category->order->invoice_no;
                        $message = str_replace('{invoice_no}', $invoice_no, $message);
                    }
                }

                $message = str_replace('{contact_name}', $contact_name, $message);
                
            }
        

        return $message;
    }




}