<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\ShippingAddress;
use App\Http\Controllers\Controller;

class ShippingAddController extends Controller
{
    public function CreateShipping(Request $request){

        $v_data=$request->validate([
            'name' => 'required|string|max:255',
            'price'=> 'required|numeric',
        ]);

        $shipping = ShippingAddress::create($v_data);
        return response()->json($shipping);


    }
}
