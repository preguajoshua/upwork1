<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class SearchController extends Controller
{
 
    public function search(Request $request)
    {
        $keyword = $request->keyword;

        $search = DB::table('product')
                ->where('product_name', 'like', '%'.$keyword.'%')
                ->get();
                      
        if(count($search)>0)   { 
            $message = array('status'=>'1', 'message'=>'data found', 'data'=>$search);
            return $message;
        }
        else{
            $message = array('status'=>'0', 'message'=>'data not found', 'data'=>[]);
            return $message;
        }

        return $message;
    }
}
