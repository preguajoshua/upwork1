<?php

namespace App\Traits;

use App\User;
use App\Partner;
use App\WebSetting;
use DB;

trait SendSms {

	public function msgKey()
    {
        $msgKey = WebSetting::find(1);

        return $msgKey->value;
    }

    public function senderId()
    {
        $senderId = WebSetting::find(2);

        return $senderId->value;
    }
    
    public function welcomeSms($user_id) {
        $partners = User::find($user_id);
        
        $user_name = $partners->name;
        $user_phone = $partners->phone;
        $user_email = $partners->email;

        $message = "Hello ".$user_name. " Welcome to GrowX. Avail the first recharge offer today & get double benefits. Start growing your business";

        $postData = array(
            'authkey' => $this->msgKey(),
            'mobiles' => $user_phone,
            'message' => $message,
            'sender' => $this->senderId(),
            'route' => 4
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
        // if(curl_errno($ch)){
            // return redirect()->back()->withErrors('Something Wents Wrong');
        // }

        curl_close($ch);
            
        return "send";
    }
    
    public function rechargeSms($user_id, $coins, $price) {
        $partners = User::find($user_id);
        
        $user_name = $partners->name;
        $user_phone = $partners->phone;
        $user_email = $partners->email;
        $coin_amount = $price;
        $credited_coin = $coins;
        
        $message = "Dear ".$user_name." your recharge of $price is successful.Your account is credited with $coins";;

        $postData = array(
            'authkey' => $this->msgKey(),
            'mobiles' => $user_phone,
            'message' => $message,
            'sender' => $this->senderId(),
            'route' => 4
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
        // if(curl_errno($ch)){
            // return redirect()->back()->withErrors('Something Wents Wrong');
        // }

        curl_close($ch);
        
        return "send";
    }
    
    // public function newLeadSms($lead_city, $lead_category, $lead_req_name, $lead_coin, $lead_budget, $lead_description) {
    //     $partner = User::where('city', $lead_city)
    //                             ->where('category', $lead_category)
    //                             ->get();
                                
    //     $lead_name = $lead_req_name;
    //     $lead_coin = $lead_coin;
    //     $lead_budget = $lead_budget;
        
    //     foreach ($partner as $partners){
    //         $user_name = $partners->name;
    //         $user_phone = $partners->phone;
    //         $user_email = $partners->email;

    //         $message = "Welcome".$user_name;

	   //     $postData = array(
	   //         'authkey' => $this->msgKey(),
	   //         'mobiles' => $user_phone,
	   //         'message' => $message,
	   //         'sender' => $this->senderId(),
	   //         'route' => 4
	   //     );

	   //     $url="https://control.msg91.com/api/sendhttp.php";

	   //     $ch = curl_init();
	   //         curl_setopt_array($ch, array(
	   //         CURLOPT_URL => $url,
	   //         CURLOPT_RETURNTRANSFER => true,
	   //         CURLOPT_POST => true,
	   //         CURLOPT_POSTFIELDS => $postData
	   //         //,CURLOPT_FOLLOWLOCATION => true
	   //     ));

	   //     //Ignore SSL certificate verification
	   //     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	   //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

	   //     //get response
	   //     $output = curl_exec($ch);
	   //     // if(curl_errno($ch)){
	   //         // return redirect()->back()->withErrors('Something Wents Wrong');
	   //     // }

	   //     curl_close($ch);
    //     }
        
    //     return "send";
    // }
    
    public function profileViewSms() {
        $partner = User::get();
        
        foreach ($partner as $partners){
            $user_name = $partners->name;
            $user_phone = $partners->phone;
            $user_email = $partners->email;
            $profile_visit = $partners->hit;
            $company_name = $partners->type;

            $message = "Hello ".$user_name.", your profile has $profile_visit visits. Don't wait, before its late. Download the GrowX App & earn 100X.";

	        $postData = array(
	            'authkey' => $this->msgKey(),
	            'mobiles' => $user_phone,
	            'message' => $message,
	            'sender' => $this->senderId(),
	            'route' => 4
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
	        // if(curl_errno($ch)){
	            // return redirect()->back()->withErrors('Something Wents Wrong');
	        // }

	        curl_close($ch);
        }
        
        return request()->json(200, ["message"=>"SMS Sent"]);
    }
}