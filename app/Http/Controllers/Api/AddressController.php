<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;

class AddressController extends Controller
{
     public function address(Request $request)
    {
            $user_id = $request->user_id;
            $unselect= DB::table('address')
                     ->where('user_id' ,$user_id)
                     ->get();
                     
            if(count($unselect)>0){
            $unselect= DB::table('address')
                     ->where('user_id' ,$user_id)
                     ->update(['select_status' => 0]);
            }
            $receiver_name = $request->receiver_name;
            $receiver_phone = $request->receiver_phone;
            $city = $request->city_name;
            $society = $request->society_name;
            $house_no = $request->house_no;
            $landmark = $request->landmark;
            $state = $request->state;
            $pin = $request->pin;
            $status= 1;
            $address = $house_no .",".  $society .",".  $landmark .",".  $city .",".  $state .",". $pin; 
            $addres = str_replace(" ", "+", $address);
            $address1 = str_replace("-", "+", $addres);
            $added_at= Carbon::Now();
         $mapapi = DB::table('map_API')
                 ->first();
                 
        $key = $mapapi->map_api_key;         
        $response = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=".$address1."&key=".$key));
        
        
         $lat = $response->results[0]->geometry->location->lat;
         $lng = $response->results[0]->geometry->location->lng;
    	    
    	    $insertaddress = DB::table('address')
    						->insert([
    							'user_id'=>$user_id,
    							'receiver_name'=>$receiver_name,
    							'receiver_phone'=>$receiver_phone,
    							'city'=>$city,
    							'society'=>$society,
    							'house_no'=>$house_no,
    							'landmark'=> $landmark,
    							'state'=>$state,
    							'pincode'=>$pin,
    							'select_status'=>1,
    							'lat' => $lat,
    							'lng' => $lng,
    							'added_at'=>$added_at
                            ]);
                            
          if($insertaddress){
                $message = array('status'=>'1', 'message'=>'Address Saved');
                return $message;
                            }		
          else{
                 $message = array('status'=>'0', 'message'=>'something went wrong');
	            return $message;
    	}
      }
      
    public function city(Request $request)
    {
    $city= DB::table('city')
         ->get();
         
       if(count($city)>0){
                $message = array('status'=>'1', 'message'=>'city list','data'=>$city);
                return $message;
                            }		
          else{
                 $message = array('status'=>'0', 'message'=>'city not found', 'data'=>[]);
	            return $message;
    	}    
    }
    
    public function society(Request $request)
    {
    $city_id = $request->city_id;
    $society= DB::table('society')
         ->where('city_id',$city_id)
         ->get();
         
       if(count($society)>0){
                $message = array('status'=>'1', 'message'=>'Society list','data'=>$society);
                return $message;
                            }		
          else{
                 $message = array('status'=>'0', 'message'=>'Society not found', 'data'=>[]);
	            return $message;
    	}    
     }
     
     
   public function show_address(Request $request)
    {
    $user_id = $request->user_id;
    $address = DB::table('address')
         ->where('user_id',$user_id)
         ->get();
         
       if(count($address)>0){
                $message = array('status'=>'1', 'message'=>'Address list','data'=>$address);
                return $message;
                            }		
          else{
                 $message = array('status'=>'0', 'message'=>'Address not found! Add Address', 'data'=>[]);
	            return $message;
    	}    
     }
     
     
public function select_address(Request $request)
    {
    $address_id = $request->address_id;
    $select = DB::table('address')
         ->where('address_id',$address_id)
         ->update(['select_status'=> 1]);
         
       if($select){
                $message = array('status'=>'1', 'message'=>'Address Selected');
                return $message;
                            }		
          else{
                 $message = array('status'=>'0', 'message'=>'cannot select please try again later');
	            return $message;
    	}    
     }     
     
     
      
public function edit_add(Request $request)
    {
           $address_id = $request->address_id;
           $user_id = $request->user_id;
            $unselect= DB::table('address')
                     ->where('user_id' ,$user_id)
                     ->get();
                     
            if(count($unselect)>0){
            $unselect= DB::table('address')
                     ->where('user_id' ,$user_id)
                     ->update(['select_status'=> 0]);
            }
            
            $receiver_name = $request->receiver_name;
            $receiver_phone = $request->receiver_phone;
            $city = $request->city_name;
            $society = $request->society_name;
            $house_no = $request->house_no;
            $landmark = $request->landmark;
            $state = $request->state;
            $pin = $request->pin;
            $status= 1;
            $address = $house_no .",".  $society .",".  $landmark .",".  $city .",".  $state .",". $pin; 
            $addres = str_replace(" ", "+", $address);
            $address1 = str_replace("-", "+", $addres);
            $added_at= Carbon::Now();
         $mapapi = DB::table('map_API')
                 ->first();
                 
        $key = $mapapi->map_api_key;         
        $response = json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=".$address1."&key=".$key));
        
        
         $lat = $response->results[0]->geometry->location->lat;
         $lng = $response->results[0]->geometry->location->lng;
    	    
    	    $insertaddress = DB::table('address')
    	                  ->where('address_id', $address_id)
    						->update([
    							'receiver_name'=>$receiver_name,
    							'receiver_phone'=>$receiver_phone,
    							'city'=>$city,
    							'society'=>$society,
    							'house_no'=>$house_no,
    							'landmark'=> $landmark,
    							'state'=>$state,
    							'pincode'=>$pin,
    							'select_status'=>1,
    							'lat' => $lat,
    							'lng' => $lng,
    							'updated_at'=>$added_at
                            ]);
                            
          if($insertaddress){
                $message = array('status'=>'1', 'message'=>'Address Saved');
                return $message;
                            }		
          else{
                 $message = array('status'=>'0', 'message'=>'something went wrong');
	            return $message;
    	}  
     }  
      
}