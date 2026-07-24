<?php
namespace App\Utils;
use App\Utils\Util;
use Intervention\Image\Laravel\Facades\Image;
use App\Models\ProductStock;

class ProductUtil extends Util
{
    
    public function FileUpload($request , $file_name, $folder){

    	$fileName='';

        if($request->hasFile($file_name)) {
            $image = $request->file($file_name);
            $fileName = time().'.'.$image->extension();
           
            // $destinationPathThumbnail = public_path('/thumbnail');
            // $img = Image::read($image->path());
            // $img->resize(100, 100, function ($constraint) {
            //     $constraint->aspectRatio();
            // })->save($destinationPathThumbnail.'/'.$fileName);
         
            $destinationPath = public_path('/'.$folder);
            $image->move($destinationPath, $fileName);
        }

    	return $fileName;

    }



    public function increaseProductStock($product_id,$variation_id,$location_id, $stock){

        $item=ProductStock::where(['product_id'=>$product_id,'variation_id'=>$variation_id,'location_id'=>$location_id])->first();

        if ($item) {
            
        }else{
            $item=new ProductStock();
            $item->product_id=$product_id;
            $item->variation_id=$variation_id;
            $item->location_id=$location_id;
            $item->qty_available=0;
        }

        $item->qty_available+=$stock;
        $item->save();


        return true;

    }


    public function updateProductStock($product_id, $variation_id,$location_id, $old_stock, $new_stock){

        $item=ProductStock::where(['product_id'=>$product_id, 'variation_id'=>$variation_id])->first();
        $stock=$new_stock -$old_stock;
        if ($stock !=0) {
            if ($item) {
                
            }else{
                $item=new ProductStock();
                $item->product_id=$product_id;
                $item->variation_id=$variation_id;
                $item->location_id=$location_id;
                $item->qty_available=0;
            }

            $item->qty_available +=$stock;
            $item->save();

        }
        return true;

        

    }


    public function decreaseProductStock($product_id, $variation_id, $location_id,$stock){

        $item=ProductStock::where(['product_id'=>$product_id, 'variation_id'=>$variation_id, 'location_id'=>$location_id])->first();

        if($item){
            $item->qty_available-=$stock;
            $item->save();
        }
        
        return true;


    }


    public function checkProductStock($product_id, $variation_id, $location_id){
        $item=ProductStock::where([
                                    'product_id'=>$product_id, 
                                    'variation_id'=>$variation_id,
                                    'location_id'=>$location_id,
                                ])->first();
        return $item?$item->qty_available:0;
    }
}

