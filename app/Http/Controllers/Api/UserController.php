<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;

class UserController extends Controller
{
    public function signUp(Request $request)
    {
        
        $this->validate(
            $request, 
            [
                'user_name' => 'required',
                'user_email' => 'required|email',
                'user_phone' => 'required',
                'user_password' => 'required'
            ],
            [
                'user_name.required' => 'Enter Name...',
                'user_email.required' => 'Enter email...',
                'user_phone.required' => 'Enter Mobile...',
                'user_password.required' => 'Enter password...',
            ]
        );
    	$user_name = $request->user_name;
    	$user_email = $request->user_email;
    	$user_phone = $request->user_phone;
    	$user_image = $request->user_image;
    	$user_password = $request->user_password;
    	$device_id = $request->device_id;
        $created_at = Carbon::now();
        $updated_at = Carbon::now();
        // $date=date('d-m-Y');
    	$checkUser = DB::table('users')
    					->where('user_phone', $user_phone)
    					->first();

    	if($checkUser && $checkUser->is_verified==1){
    		$message = array('status'=>'0', 'message'=>'user already registered', 'data'=>[]);
            return $message;
    	}
    	
    ///////if phone not verified/////	
    	
	elseif($checkUser && $checkUser->is_verified==0){
	                 $delnot= DB::table('notificationby')
    						->where('user_id', $checkUser->user_id)
    				     	->delete();
    						
    	    	$delUser = DB::table('users')
    					->where('user_phone', $user_phone)
    					->delete();
    					
    	    
    	   if($request->user_image){
            $user_image = $request->user_image;
            $user_image = str_replace('data:image/png;base64,', '', $user_image);
            $fileName = str_replace(" ", "-", $user_image);
            $fileName = date('dmyHis').'user_image'.'.'.'png';
            $fileName = str_replace(" ", "-", $fileName);
            \File::put(public_path(). '/images/user/' . $fileName, base64_decode($user_image));
            $user_image = 'images/user/'.$fileName;
        }
            else{
                $user_image = 'N/A';
            }
        
    		$insertUser = DB::table('users')
    						->insertGetId([
    							'user_name'=>$user_name,
    							'user_email'=>$user_email,
    							'user_phone'=>$user_phone,
    							'user_image'=>$user_image,
    							'user_password'=>$user_password,
    							'device_id'=>$device_id,
    							'reg_date'=>$created_at
    						]);
    						
            	$Userdetails = DB::table('users')
    					->where('user_phone', $user_phone)
    					->first();
    		if($insertUser){
    		     DB::table('notificationby')
    						->insert(['user_id'=> $insertUser,
    						'sms'=> '1',
    						'app'=> '1',
    						'email'=> '1']);
    						
    						
    			$chars = "0123456789";
                $otpval = "";
                for ($i = 0; $i < 4; $i++){
                    $otpval .= $chars[mt_rand(0, strlen($chars)-1)];
                }
                
                $sms_api_key=  DB::table('msg91')
                	              ->select('api_key', 'sender_id')
                                  ->first();
                $api_key = $sms_api_key->api_key;
                $sender_id = $sms_api_key->sender_id;
    
                $getAuthKey = $api_key;
                $getSenderId = $sender_id;
                $getInvitationMsg = "Your OTP is: ".$otpval.".\nNote: Please DO NOT SHARE this OTP with anyone."; 
    
                $authKey = $getAuthKey;
                $mobileNumber = $user_phone;
                $senderId = $getSenderId;
                $message = $getInvitationMsg;
                $route = "4";
                $postData = array(
                    'authkey' => $authKey,
                    'mobiles' => $mobileNumber,
                    'message' => $message,
                    'sender' => $senderId,
                    'route' => $route
                );
    
                $url="https://control.msg91.com/api/sendhttp.php";
    
                $ch = curl_init();
                curl_setopt_array($ch, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $postData
                    //,CURLOPT_FOLLOWLOCATION => true
                ));
    
                //Ignore SSL certificate verification
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    
                //get response
                $output = curl_exec($ch);
    
                curl_close($ch);
    
                $updateOtp = DB::table('users')
                                ->where('user_phone', $user_phone)
                                ->update(['otp_value'=>$otpval]);
    						
	    		$message = array('status'=>'1', 'message'=>'OTP Sent', 'data'=>$Userdetails);
	        	return $message;
	    	}
	    	else{
	    		$message = array('status'=>'0', 'message'=>'Password is wrong');
	        return $message;
	    	}  
    	}
    	 ///////new user/////	
    	else{
       if($request->user_image){
            $user_image = $request->user_image;
            $user_image = str_replace('data:image/png;base64,', '', $user_image);
            $fileName = str_replace(" ", "-", $user_image);
            $fileName = date('dmyHis').'user_image'.'.'.'png';
            $fileName = str_replace(" ", "-", $fileName);
            \File::put(public_path(). '/images/user/' . $fileName, base64_decode($user_image));
            $user_image = 'images/user/'.$fileName;
        }
            else{
                $user_image = 'N/A';
            }
        
    		$insertUser = DB::table('users')
    						->insertGetId([
    							'user_name'=>$user_name,
    							'user_email'=>$user_email,
    							'user_phone'=>$user_phone,
    							'user_image'=>$user_image,
    							'user_password'=>$user_password,
    							'device_id'=>$device_id,
    							'reg_date'=>$created_at
    						]);
    						
            	$Userdetails = DB::table('users')
    					->where('user_phone', $user_phone)
    					->first();
    		if($insertUser){
    		     DB::table('notificationby')
    						->insert(['user_id'=> $insertUser,
    						'sms'=> '1',
    						'app'=> '1',
    						'email'=> '1']);
    						
    						
    			$chars = "0123456789";
                $otpval = "";
                for ($i = 0; $i < 4; $i++){
                    $otpval .= $chars[mt_rand(0, strlen($chars)-1)];
                }
                
                $sms_api_key=  DB::table('msg91')
                	              ->select('api_key', 'sender_id')
                                  ->first();
                $api_key = $sms_api_key->api_key;
                $sender_id = $sms_api_key->sender_id;
    
                $getAuthKey = $api_key;
                $getSenderId = $sender_id;
                $getInvitationMsg = "Your OTP is: ".$otpval.".\nNote: Please DO NOT SHARE this OTP with anyone."; 
    
                $authKey = $getAuthKey;
                $mobileNumber = $user_phone;
                $senderId = $getSenderId;
                $message = $getInvitationMsg;
                $route = "4";
                $postData = array(
                    'authkey' => $authKey,
                    'mobiles' => $mobileNumber,
                    'message' => $message,
                    'sender' => $senderId,
                    'route' => $route
                );
    
                $url="https://control.msg91.com/api/sendhttp.php";
    
                $ch = curl_init();
                curl_setopt_array($ch, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $postData
                    //,CURLOPT_FOLLOWLOCATION => true
                ));
    
                //Ignore SSL certificate verification
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    
                //get response
                $output = curl_exec($ch);
    
                curl_close($ch);
    
                $updateOtp = DB::table('users')
                                ->where('user_phone', $user_phone)
                                ->update(['otp_value'=>$otpval]);
    						
	    		$message = array('status'=>'1', 'message'=>'OTP Sent', 'data'=>$Userdetails);
	        	return $message;
	    	}
	    	else{
	    		$message = array('status'=>'0', 'message'=>'Password is wrong');
	        return $message;
	    	}
    	}
    }
    
    public function verifyPhone(Request $request)
    {
        $phone = $request->user_phone;
        $otp = $request->otp;
        
        // check for otp verify
        $getUser = DB::table('users')
                    ->where('user_phone', $phone)
                    ->first();
                    
        if($getUser){
            $getotp = $getUser->otp_value;
            
            if($otp == $getotp){
                // verify phone
                $getUser = DB::table('users')
                            ->where('user_phone', $phone)
                            ->update(['is_verified'=>1,
                            'otp_value'=>NULL]);
                    
                $message = array('status'=>1, 'message'=>"Phone Verified! login successfully");
                return $message;
            }
            else{
                $message = array('status'=>0, 'message'=>"Wrong OTP");
                return $message;
            }
        }
        else{
            $message = array('status'=>0, 'message'=>"User not registered");
            return $message;
        }
    }


    public function login(Request $request)
    
     {
    	$user_phone = $request->user_phone;
    	$user_password = $request->user_password;
    	$device_id = $request->device_id;
    	
    	$checkUserReg = DB::table('users')
    					->where('user_phone', $user_phone)
    					->first();
    					
    	if(!($checkUserReg) || $checkUserReg->is_verified== 0){
    	    $message = array('status'=>'0', 'message'=>'Phone not registered', 'data'=>[]);
	        return $message;
    	}
                
    	$checkUser = DB::table('users')
    					->where('user_phone', $user_phone)
    					->where('user_password', $user_password)
    					->first();

    	if($checkUser){
    	    
    	    if($checkUser->is_verified == 0){
    	        $chars = "0123456789";
                $otpval = "";
                for ($i = 0; $i < 4; $i++){
                    $otpval .= $chars[mt_rand(0, strlen($chars)-1)];
                }
                
                $sms_api_key=  DB::table('msg91')
                	       ->select('api_key', 'sender_id')
                           ->first();
                $api_key = $sms_api_key->api_key;
                $sender_id = $sms_api_key->sender_id;
    
                $getAuthKey = $api_key;
                $getSenderId = $sender_id;
                $getInvitationMsg = "Your OTP is: ".$otpval.".\nNote: Please DO NOT SHARE this OTP with anyone."; 
    
                $authKey = $getAuthKey;
                $mobileNumber = $user_phone;
                $senderId = $getSenderId;
                $message = $getInvitationMsg;
                $route = "4";
                $postData = array(
                    'authkey' => $authKey,
                    'mobiles' => $mobileNumber,
                    'message' => $message,
                    'sender' => $senderId,
                    'route' => $route
                );
    
                $url="https://control.msg91.com/api/sendhttp.php";
    
                $ch = curl_init();
                curl_setopt_array($ch, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $postData
                    //,CURLOPT_FOLLOWLOCATION => true
                ));
    
                //Ignore SSL certificate verification
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    
                //get response
                $output = curl_exec($ch);
    
                curl_close($ch);
    
                $updateOtp = DB::table('users')
                                ->where('user_phone', $user_phone)
                                ->update(['otp_value'=>$otpval]);
                                
                $checkUser1 = DB::table('users')
            					->where('user_phone', $user_phone)
            					->first();                
                                
    	        $message = array('status'=>'2', 'message'=>'Verify Phone', 'data'=>[$checkUser1]);
	        	return $message;
    	    }
    	   else{
    		   $updateDeviceId = DB::table('users')
    		                        ->where('user_phone', $user_phone)
    		                        ->update(['device_id'=>$device_id]);
    		                       
    		   $checkUser1 = DB::table('users')
            					->where('user_phone', $user_phone)
            					->where('user_password', $user_password)
            					->first();
    		                        
    			$message = array('status'=>'1', 'message'=>'login successfully', 'data'=>[$checkUser1]);
	        	return $message;
    	   }	   
    	
    	}
    	else{
    		$message = array('status'=>'0', 'message'=>'Wrong Password', 'data'=>[]);
	        return $message;
    	}
    }
    
    
    
    
    public function myprofile(Request $request)
    {   
        $user_id = $request->user_id;
         $user =  DB::table('users')
                ->where('user_id', $user_id )
                ->first();
                        
    if($user){
        	$message = array('status'=>'1', 'message'=>'User Profile', 'data'=>$user);
	        return $message;
              }
    	else{
    		$message = array('status'=>'0', 'message'=>'User not found', 'data'=>[]);
	        return $message;
    	}
        
    }   
    
    public function forgotPassword(Request $request)
    {
        $user_phone = $request->user_phone;
        
        $checkUser = DB::table('users')
                        ->where('user_phone', $user_phone)
                        ->where('is_verified',1)
                        ->first();
                        
        if($checkUser){
                $chars = "0123456789";
                $otpval = "";
                for ($i = 0; $i < 4; $i++){
                    $otpval .= $chars[mt_rand(0, strlen($chars)-1)];
                }
                
                $sms_api_key=  DB::table('msg91')
                	              ->select('api_key', 'sender_id')
                                  ->first();
                $api_key = $sms_api_key->api_key;
                $sender_id = $sms_api_key->sender_id;
    
                $getAuthKey = $api_key;
                $getSenderId = $sender_id;
                $getInvitationMsg = "Your OTP is: ".$otpval.".\nNote: Please DO NOT SHARE this OTP with anyone."; 
    
                $authKey = $getAuthKey;
                $mobileNumber = $user_phone;
                $senderId = $getSenderId;
                $message = $getInvitationMsg;
                $route = "4";
                $postData = array(
                    'authkey' => $authKey,
                    'mobiles' => $mobileNumber,
                    'message' => $message,
                    'sender' => $senderId,
                    'route' => $route
                );
    
                $url="https://control.msg91.com/api/sendhttp.php";
    
                $ch = curl_init();
                curl_setopt_array($ch, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $postData
                    //,CURLOPT_FOLLOWLOCATION => true
                ));
    
                //Ignore SSL certificate verification
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    
                //get response
                $output = curl_exec($ch);
    
                curl_close($ch);
    
                $updateOtp = DB::table('users')
                                ->where('user_phone', $user_phone)
                                ->update(['otp_value'=>$otpval]);
                                
            if($updateOtp){
              $checkUser1 = DB::table('users')
            					->where('user_phone', $user_phone)
            					->first();
    		                        
    			$message = array('status'=>'1', 'message'=>'Verify OTP', 'data'=>[$checkUser1]);
	        	return $message; 
            }
            else{
                $message = array('status'=>'0', 'message'=>'Something wrong', 'data'=>[]);
	        	return $message; 
            }
        }                
        else{
            $message = array('status'=>'0', 'message'=>'User not registered', 'data'=>[]);
	        return $message;
        }
        
    }
    
    public function verifyOtp(Request $request)
    {
        $phone = $request->user_phone;
        $otp = $request->otp;
        
        // check for otp verify
        $getUser = DB::table('users')
                    ->where('user_phone', $phone)
                    ->first();
                    
        if($getUser){
            $getotp = $getUser->otp_value;
            
            if($otp == $getotp){
                $message = array('status'=>1, 'message'=>"Otp Matched Successfully");
                return $message;
            }
            else{
                $message = array('status'=>0, 'message'=>"Wrong OTP");
                return $message;
            }
        }
        else{
            $message = array('status'=>0, 'message'=>"User not registered");
            return $message;
        }
    }
    
    public function changePassword(Request $request)
    {
        $user_phone = $request->user_phone;
        $password = $request->user_password;
        
        $getUser = DB::table('users')
                    ->where('user_phone', $user_phone)
                    ->first();
                    
        if($getUser){
            $updateOtp = DB::table('users')
                            ->where('user_phone', $user_phone)
                            ->update(['user_password'=>$password]);
                                
            if($updateOtp){
              $checkUser1 = DB::table('users')
            					->where('user_phone', $user_phone)
            					->first();
    		                        
    			$message = array('status'=>'1', 'message'=>'Password changed', 'data'=>[$checkUser1]);
	        	return $message; 
            }
            else{
                $message = array('status'=>'0', 'message'=>'Something wrong', 'data'=>[]);
	        	return $message; 
            }
        }
        else{
            $message = array('status'=>0, 'message'=>"User not registered");
            return $message;
        }
    }
    
    
     public function profile_edit(Request $request)
    {
        $user_id = $request->user_id;
    	$user_name = $request->user_name;
    	$user_email = $request->user_email;
    	$user_phone = $request->user_phone;
    	$user_image = $request->user_image;
    	$user_password = $request->user_password;
        // $date=date('d-m-Y');
    	    
    	   if($request->user_image){
            $user_image = $request->user_image;
            $user_image = str_replace('data:image/png;base64,', '', $user_image);
            $fileName = str_replace(" ", "-", $user_image);
            $fileName = date('dmyHis').'user_image'.'.'.'png';
            $fileName = str_replace(" ", "-", $fileName);
            \File::put(public_path(). '/images/user/' . $fileName, base64_decode($user_image));
            $user_image = 'images/user/'.$fileName;
        }
            else{
                $user_image = 'N/A';
            }
        
    		$insertUser = DB::table('users')
    		            ->where('user_id', $user_id)
    						->update([
    							'user_name'=>$user_name,
    							'user_email'=>$user_email,
    							'user_phone'=>$user_phone,
    							'user_image'=>$user_image,
    							'user_password'=>$user_password,
    						]);
    						
            	$Userdetails = DB::table('users')
    					->where('user_id', $user_id)
    					->first();
    					
    					
    		if($insertUser){
    						
	    		$message = array('status'=>'1', 'message'=>'Profile Updated', 'data'=>$Userdetails);
	        	return $message;
	    	}
	    	else{
	    		$message = array('status'=>'0', 'message'=>'Something Went wrong');
	        return $message;
	    	}  
    	}
    
    
    
    
}
