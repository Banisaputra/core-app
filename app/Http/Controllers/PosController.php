<?php

namespace App\Http\Controllers;

use App\Models\POS;
use App\Models\MasterItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index() 
    {
        $items = MasterItem::all();
        return view('pos.index', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|integer|exists:members,id',
            'items' => 'required|array|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|integer|min:1',
            'total' => 'required|numeric|min:0',
            'received' => 'required|numeric|min:'.max($request->total, 0),
            'change' => 'required|numeric|min:0',
            'payment_type' => 'required|in:CASH,DEBIT,CREDIT,TRANSFER,EWALLET',
        ]);
        $items = $request->items;
        $total = $request->total;
        $received = $request->received;
        $change = $request->change;

        $sa_code = POS::generateSalesCode();
        // Store the sale
        DB::beginTransaction();

        try {
            // Store the sale and get the ID
            $saleId = DB::table('sales')->insertGetId([
                'sa_code' => $sa_code,
                'sa_date' => now()->format('Ymd'),
                'member_id' => $request->member_id,
                'sub_total' => $total,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
            // Insert sales details
            $salesDetails = [];
            foreach ($items as $item) {
                $salesDetails[] = [
                    'sa_id' => $saleId,
                    'item_id' => $item['id'],
                    'amount' => $item['qty'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['qty'],
                ];
            }

            // Batch insert for better performance
            DB::table('sales_detail')->insert($salesDetails);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale recorded successfully',
                'sa_code' => $sa_code,
            ]);
        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to record sale',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
