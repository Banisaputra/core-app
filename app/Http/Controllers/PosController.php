<?php

namespace App\Http\Controllers;

use DateTime;
use DateInterval;
use App\Models\POS;
use App\Models\Loan;
use App\Models\Sale;
use App\Models\Member;
use App\Models\MasterItem;
use App\Models\SavingType;
use App\Models\LoanPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class PosController extends Controller
{
    public function index() 
    {
        $items = MasterItem::latest()->get();
        return view('pos.index', compact('items'));
    }

    public function index2() 
    {
        $items = MasterItem::latest()->get();
        return view('pos.index2', compact('items'));
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
            ]);

            $crInterest = $request->crInterest;
            $tenor = $request->tenor;
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
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            // Insert sales details
            $salesDetails = [];
            foreach ($items as $item) {
                $salesDetails[] = [
                    'sa_id' => $saleId,
                    'item_id' => $item['id'],
                    'amount' => $item['qty'],
                    'price' => $item['price'],
                    'disc_price' => $item['disc_price'],
                    'total' => ($item['price'] - $item['disc_price']) * $item['qty'],
                ];

                MasterItem::where('id', $item['id'])->decrement('stock', $item['qty']);
            }

            // Batch insert for better performance
            DB::table('sale_detail')->insert($salesDetails);

            // insert loan
            if($payment_type == "CREDIT") {
                // cek limit
                $member = Member::findOrFail($request->member_id);
                $monthlySaving = SavingType::getMonthlySaving();
                $currentLoan = $member->getTotalLoan();
                $totalBayar = ($currentLoan['total_bayar']*1) + ($total / $request->tenor*1) + ($monthlySaving*1);

                if ($totalBayar > $currentLoan['maxBayar']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Pembayaran Angsuran melebihi batas maksimal '.number_format((int) $currentLoan['maxBayar'],0).'',
                        'receipt' => []
                    ]);
                }

                if ($currentLoan['total_pokok'] > $currentLoan['maxPokok']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Angsuran Pokok melebihi batas maksimal '.number_format((int) $currentLoan['maxPokok'],0).' per anggota',
                        'receipt' => []
                    ]);
                }

                $loan_code = Loan::generateCode();
                $date = new DateTime(now());
                $date->add(new DateInterval('P' . $request->tenor . 'M'));
                $dueDate = $date->format('Ymd');

                $loan = Loan::create([
                    'member_id' => $request->member_id,
                    'loan_type' => 'BARANG',
                    'loan_code' => $loan_code,
                    'loan_date' => now()->format('Ymd'),
                    'loan_tenor' => $request->tenor,
                    'loan_value' => $total,
                    'interest_percent' => 0,
                    'due_date' => $dueDate,
                    'loan_state' => 2,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);

                // insert loan payment
                $loan_total = $loan->loan_value;
                $ln_date = $loan->loan_date;
                for ($i=1; $i <= $request->tenor ; $i++) { 
                    $lp_val = round($loan->loan_value / $loan->loan_tenor, 0);
                    $lp_intr = round(($lp_val*$loan->interest_percent)/100, 0);
                    $ln_remain = round($loan_total - $lp_val);
                    $pay_date = new DateTime($ln_date);
                    $lp_date = $pay_date->add(new DateInterval('P1M'))->format('Ymd');
                    LoanPayment::create([
                        'lp_code' => LoanPayment::generateCode($lp_date),
                        'loan_id' => $loan->id,
                        'lp_date' => $lp_date,
                        'lp_value' => $lp_val,
                        'loan_interest' => $lp_intr,
                        'loan_remaining' => $ln_remain,
                        'lp_total' => ($lp_val+$lp_intr),
                        'tenor_month' => $i,
                        'lp_state' => 1,
                        'remark' => '',
                        'proof_of_payment' => '',
                        'lp_forfeit' => 0,
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
            
                    ]);
                    $loan_total -= $lp_val;
                    $ln_date = $lp_date;
                    
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale recorded successfully',
                'receipt' => [
                    'id' => $saleId,
                    'code' => $sa_code,
                ]
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

    public function printReceipt($id)
    {
        $sale = Sale::with('saDetail.item')->findOrFail($id);

        $pdf = Pdf::loadView('reports.receipt58', compact('sale'))
                    ->setPaper([0,0,164.36,600], 'portrait'); 
                    // ukuran thermal 80mm (226.77 point)
                    // ukuran thermal 58mm (164.36 point)

        return $pdf->stream('receipt-'.$sale->invoice_no.'.pdf');
    }

}
