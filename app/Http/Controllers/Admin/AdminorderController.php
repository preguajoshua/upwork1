<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Session;

class AdminorderController extends Controller
{
    
     public function admin_com_orders(Request $request)
    {
         $title = "Completed Order section";
         $admin_email=Session::get('bamaAdmin');
    	 $admin= DB::table('admin')
    	 		   ->where('admin_email',$admin_email)
    	 		   ->first();
    	  $logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();
                
        $ord =DB::table('orders')
             ->join('store','orders.store_id', '=', 'store.store_id')
             ->join('delivery_boy','orders.dboy_id', '=', 'delivery_boy.dboy_id')
             ->join('users', 'orders.user_id', '=','users.user_id')
             ->orderBy('orders.delivery_date','DESC')
             ->where('order_status', 'completed')
             ->paginate(10);
             
         $details  =   DB::table('orders')
    	                ->join('store_orders', 'orders.cart_id', '=', 'store_orders.order_cart_id') 
    	                ->join('product_varient', 'store_orders.varient_id', '=', 'product_varient.varient_id')
    	                ->join('product','product_varient.product_id', '=', 'product.product_id')
    	                ->select('product.product_name','product_varient.price','product_varient.mrp','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','orders.cart_id','store_orders.varient_id','store_orders.store_order_id','store_orders.qty','orders.total_price')
    	               ->where('store_orders.store_approval',1)
    	               ->get();         
                
       return view('admin.all_orders.com_orders', compact('title','logo','ord','details','admin'));         
    }
    
      public function admin_pen_orders(Request $request)
    {
         $title = "Pending Order section";
         $admin_email=Session::get('bamaAdmin');
    	 $admin= DB::table('admin')
    	 		   ->where('admin_email',$admin_email)
    	 		   ->first();
    	  $logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();
                
        $ord =DB::table('orders')
             ->join('users', 'orders.user_id', '=','users.user_id')
             ->orderBy('orders.delivery_date','DESC')
             ->where('orders.order_status', 'Pending')
             ->paginate(10);
             
         $details  =   DB::table('orders')
    	                ->join('store_orders', 'orders.cart_id', '=', 'store_orders.order_cart_id') 
    	                ->join('product_varient', 'store_orders.varient_id', '=', 'product_varient.varient_id')
    	                ->join('product','product_varient.product_id', '=', 'product.product_id')
    	                ->select('product.product_name','product_varient.price','product_varient.mrp','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','orders.cart_id','store_orders.varient_id','store_orders.store_order_id','store_orders.qty','orders.total_price')
    	               ->where('store_orders.store_approval',1)
    	               ->get();         
                
       return view('admin.all_orders.pending', compact('title','logo','ord','details','admin'));         
    }
    
    
    public function admin_store_orders(Request $request)
    {
         $title = "Store Order section";
         $id = $request->id;
         $store = DB::table('store')
                ->where('store_id',$id)
                ->first();
         $admin_email=Session::get('bamaAdmin');
    	 $admin= DB::table('admin')
    	 		   ->where('admin_email',$admin_email)
    	 		   ->first();
    	  $logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();
                
        $ord =DB::table('orders')
             ->join('users', 'orders.user_id', '=','users.user_id')
             ->where('orders.store_id',$store->store_id)
             ->orderBy('orders.delivery_date','ASC')
             ->where('order_status','!=', 'completed')
             ->paginate(10);
             
         $details  =   DB::table('orders')
    	                ->join('store_orders', 'orders.cart_id', '=', 'store_orders.order_cart_id') 
    	                ->join('product_varient', 'store_orders.varient_id', '=', 'product_varient.varient_id')
    	                ->join('product','product_varient.product_id', '=', 'product.product_id')
    	                ->select('product.product_name','product_varient.price','product_varient.mrp','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','orders.cart_id','store_orders.varient_id','store_orders.store_order_id','store_orders.qty','orders.total_price')
    	               ->where('orders.store_id',$id)
    	               ->where('store_orders.store_approval',1)
    	               ->get();         
                
       return view('admin.store.orders', compact('title','logo','ord','store','details','admin'));         
    }
    
    
    
     public function admin_dboy_orders(Request $request)
    {
         $title = "Delivery Boy Order section";
         $id = $request->id;
         $dboy = DB::table('delivery_boy')
                ->where('dboy_id',$id)
                ->first();
         $admin_email=Session::get('bamaAdmin');
    	 $admin= DB::table('admin')
    	 		   ->where('admin_email',$admin_email)
    	 		   ->first();
    	  $logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();
    
          $date = date('Y-m-d');
     $nearbydboy = DB::table('delivery_boy')
                ->leftJoin('orders', 'delivery_boy.dboy_id', '=', 'orders.dboy_id') 
                ->select("delivery_boy.boy_name","delivery_boy.dboy_id","delivery_boy.lat","delivery_boy.lng","delivery_boy.boy_city",DB::raw("Count(orders.order_id)as count"),DB::raw("6371 * acos(cos(radians(".$dboy->lat . ")) 
                * cos(radians(delivery_boy.lat)) 
                * cos(radians(delivery_boy.lng) - radians(" . $dboy->lng . ")) 
                + sin(radians(" .$dboy->lat. ")) 
                * sin(radians(delivery_boy.lat))) AS distance"))
               ->groupBy("delivery_boy.boy_name","delivery_boy.dboy_id","delivery_boy.lat","delivery_boy.lng","delivery_boy.boy_city")
               ->where('delivery_boy.boy_city', $dboy->boy_city)
               ->where('delivery_boy.dboy_id','!=',$dboy->dboy_id)
               ->orderBy('count')
               ->orderBy('distance')
               ->get();  
    
                
        $ord =DB::table('orders')
             ->join('users', 'orders.user_id', '=','users.user_id')
             ->where('orders.dboy_id',$dboy->dboy_id)
             ->orderBy('orders.delivery_date','ASC')
             ->where('order_status','!=', 'completed')
             ->paginate(10);
             
         $details  =   DB::table('orders')
    	                ->join('store_orders', 'orders.cart_id', '=', 'store_orders.order_cart_id') 
    	                ->join('product_varient', 'store_orders.varient_id', '=', 'product_varient.varient_id')
    	                ->join('product','product_varient.product_id', '=', 'product.product_id')
    	                ->select('product.product_name','product_varient.price','product_varient.mrp','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','orders.cart_id','store_orders.varient_id','store_orders.store_order_id','store_orders.qty','orders.total_price')
    	               ->where('orders.dboy_id',$id)
    	               ->where('store_orders.store_approval',1)
    	               ->get();         
                
       return view('admin.d_boy.orders', compact('title','logo','ord','dboy','details','admin','nearbydboy'));         
    }
    
    
    
    public function store_cancelled(Request $request)
    {
         $title = "Store Cancelled Orders";
         $admin_email=Session::get('bamaAdmin');
    	 $admin= DB::table('admin')
    	 		   ->where('admin_email',$admin_email)
    	 		   ->first();
    	  $logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();
                
        $ord =DB::table('orders')
             ->join('users', 'orders.user_id', '=','users.user_id')
             ->join('address', 'orders.address_id', '=','address.address_id')
             ->orderBy('orders.delivery_date','ASC')
             ->where('order_status','!=', 'completed')
             ->where('store_id', 0)
             ->paginate(10);
             
            
             $nearbystores = DB::table('store')
                          ->get();
         
             
         $details  =   DB::table('orders')
    	                ->join('store_orders', 'orders.cart_id', '=', 'store_orders.order_cart_id') 
    	                ->join('product_varient', 'store_orders.varient_id', '=', 'product_varient.varient_id')
    	                ->join('product','product_varient.product_id', '=', 'product.product_id')
    	                ->select('product.product_name','product_varient.price','product_varient.mrp','product_varient.unit','product_varient.quantity','product_varient.varient_image','product_varient.description','orders.cart_id','store_orders.varient_id','store_orders.store_order_id','store_orders.qty','orders.total_price')
    	               ->where('store_orders.store_approval',1)
    	               ->get();         
                
       return view('admin.store.cancel_orders', compact('title','logo','ord','details','admin','nearbystores'));  
    }
    
    
    public function assignstore(Request $request)
    {
         $title = "Store Cancelled Orders";
         $cart_id=$request->id;
         $store = $request->store;
         $admin_email=Session::get('bamaAdmin');
    	 $admin= DB::table('admin')
    	 		   ->where('admin_email',$admin_email)
    	 		   ->first();
    	  $logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();
      
          $ord =DB::table('orders')
             ->where('cart_id', $cart_id)
             ->update(['store_id'=>$store, 'cancel_by_store'=>0]);
             
      
      return redirect()->back()->withSuccess('Assigned to store successfully');
    }
    
    
    
    
       public function assigndboy(Request $request)
    {
         $cart_id=$request->id;
         $d_boy = $request->d_boy;
         $admin_email=Session::get('bamaAdmin');
    	 $admin= DB::table('admin')
    	 		   ->where('admin_email',$admin_email)
    	 		   ->first();
    	  $logo = DB::table('tbl_web_setting')
                ->where('set_id', '1')
                ->first();
      
          $ord =DB::table('orders')
             ->where('cart_id', $cart_id)
             ->update(['dboy_id'=>$d_boy]);
             
      
      return redirect()->back()->withSuccess('Assigned to Another Delivery Boy Successfully');
    }
    
}