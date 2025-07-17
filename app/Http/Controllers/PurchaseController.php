<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index() {
        $purchases = Purchase::all();
        return view('purchases.index', compact('purchases'));
    }

    public function create() {

        return view('purchase.create');
    }

}
