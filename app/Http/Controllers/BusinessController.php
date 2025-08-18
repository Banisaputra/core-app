<?php

namespace App\Http\Controllers;

use App\Models\Business;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function index() {
        $data = [];
 
        // get sales
        $salesPolicy = Business::where('doc_type', 'SALES')
        ->get()
        ->mapWithKeys(function ($policy) {
            return [
                $policy->bs_name => [
                    'id' => $policy->id,
                    'name' => $policy->bs_name,
                    'value' => $policy->bs_value
                ]
            ];
        })
        ->all();
        $data['salesPolicy'] = [];
        if ($salesPolicy) $data['salesPolicy'] = $salesPolicy;
        
        return view("business.index", $data);
    }
 
}
