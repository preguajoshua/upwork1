<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;

class CouponController extends Controller
{
    public function apply_coupon(Request $request)
    {
        $cart_id = $request->cart_id;
        $coupon_code = $request->coupon_code;
        $check = DB::table('orders')
               ->where('cart_id',$cart_id)
               ->first();
        $coupon = DB::table('coupon')
                ->where('coupon_code', $coupon_code)
                ->first();
        $p=$check->total_price;
        $mincart = $coupon->cart_value;
        $am = $coupon->amount;
        $type = $coupon->type;
        if($type=='%'||$type=='Percentage'||$type=='percentage'){
          $per = ($p*$am)/100;  
          $rem_price = $p-$per;
        }
        else{
            $per = $am;
            $rem_price = $p-$am; 
        }
        $update=DB::table('orders')
              ->where('cart_id',$cart_id)
              ->update(['rem_price'=>$rem_price,
              'coupon_discount'=>$per,
              'coupon_id'=>$coupon->coupon_id]);
              
        $order =DB::table('orders')
              ->where('cart_id',$cart_id)
              ->first();
     if($order){   
        if($update){
            $message = array('status'=>'1', 'message'=>'Coupon Applied Successfully', 'data'=>$order);
            return $message;
            }
        else{
            $message = array('status'=>'1', 'message'=>'Cannot Applied', 'data'=>$order);
            return $message;
        }
     }else{
         $message = array('status'=>'0', 'message'=>'order not found');
         return $message;
     }
    }
    
    public function coupon_list(Request $request)
    {
        $cart_id = $request->cart_id; 
        $check = DB::table('orders')
               ->where('cart_id',$cart_id)
               ->first();
        $p=$check->total_price;
        
        $coupon = DB::table('coupon')
                ->where('cart_value','<=', $p)
                ->get();
         if(count($coupon)>0){
            $message = array('status'=>'1', 'message'=>'Coupon List', 'data'=>$coupon);
            return $message;
            }
        else{
            $message = array('status'=>'0', 'message'=>'Coupon not Found');
            return $message;
        }
    
    }
    
}