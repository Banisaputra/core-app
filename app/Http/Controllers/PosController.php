<?php

namespace App\Http\Controllers;

use DateTime;
use DateInterval;
use App\Models\POS;
use App\Models\Loan;
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
            'payment_type' => 'required|in:CASH,DEBIT,CREDIT,TRANSFER,EWALLET',
        ]);
        $payment_type = $request->payment_type;

        if ($payment_type == "CASH") {
            $request->validate([
                'member_id' => 'required|integer|exists:members,id',
                'items' => 'required|array|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.qty' => 'required|integer|min:1',
                'total' => 'required|numeric|min:0',
                'received' => 'required|numeric|min:'.max($request->total, 0),
                'change' => 'required|numeric|min:0',
            ]);

            $received = $request->received;
            $change = $request->change;

        } else if ($payment_type == "CREDIT") {
            $request->validate([
                'member_id' => 'required|integer|exists:members,id',
                'items' => 'required|array|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'items.*.qty' => 'required|integer|min:1',
                'total' => 'required|numeric|min:0',
                'tenor' => 'required|numeric|min:1',
                'crInterest' => 'required|numeric|min:0',
                'crType' => 'required|string',
            ]);

            $crInterest = $request->crInterest;
            $tenor = $request->tenor;
            $crType = $request->crType;
        } 

        $items = $request->items;
        $total = $request->total;

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

            // insert loan
            if($payment_type == "CREDIT") {
                $loan_code = Loan::generateCode();
                $date = new DateTime(now());
                $date->add(new DateInterval('P' . $request->tenor . 'M'));
                $dueDate = $date->format('Ymd');

                Loan::create([
                    'member_id' => $request->member_id,
                    'loan_code' => $loan_code,
                    'loan_date' => now()->format('Ymd'),
                    'loan_tenor' => $request->tenor,
                    'loan_value' => $total,
                    'interest_percent' => 0,
                    'due_date' => $dueDate,
                    'loan_state' => 1,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
            }

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
