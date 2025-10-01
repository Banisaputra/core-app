<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use App\Models\Loan;
use App\Models\Sale;
use App\Models\User;
use App\Models\Member;
use App\Models\Policy;
use App\Models\Saving;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\DatabaseBackupService;
use App\Repositories\PinjamanRepository;
use App\Repositories\SimpananRepository;
use App\Repositories\PembelianRepository;
use App\Repositories\PenjualanRepository;

class UserController extends Controller
{ 
    protected $penjualanRepository;
    protected $pembelianRepository;
    protected $simpananRepository;
    protected $pinjamanRepository;

    public function __construct(
        PenjualanRepository $penjualanRepository, PembelianRepository $pembelianRepository,
        SimpananRepository $simpananRepository, PinjamanRepository $pinjamanRepository
    ) {
        $this->penjualanRepository = $penjualanRepository;
        $this->pembelianRepository = $pembelianRepository;
        $this->simpananRepository = $simpananRepository;
        $this->pinjamanRepository = $pinjamanRepository;
    }

    public function dashboard() 
    {   
        $role = strtoupper(auth()->user()->roles[0]['name']);
        if ($role === "MEMBER") return $this->dashboardMember();
        $cut_off_day = Policy::where('pl_name', 'cut_off_bulanan')->value('pl_value');
        $today = new DateTime();
        $current_day = (int)$today->format('d');
        $current_month = (int)$today->format('m');
        $current_year = (int)$today->format('Y');

        $periode_start = new DateTime("$current_year-$current_month-".($cut_off_day + 1)."");
        $periode_end = new DateTime("$current_year-$current_month-".($cut_off_day ?? 1)."");
        if ($current_day <= $cut_off_day) {
            $periode_start->modify("-1 month");
        } else {
            $periode_end->modify("+1 month");

        }

        // data member
        $userID = auth()->id();
        
        $saleOfYear = $this->penjualanRepository->getPenjualanPerPeriode($current_year, $cut_off_day);
        $purchaseOfYear = $this->pembelianRepository->getPembelianPerPeriode($current_year, $cut_off_day);
        $savingOfYear = $this->simpananRepository->getSimpananPerPeriode($current_year, $cut_off_day);
        $loanOfYear = $this->pinjamanRepository->getPinjamanPerPeriode($current_year, $cut_off_day);

        // data monthly
        $sales = Sale::whereBetween('sa_date', [$periode_start->format('Ymd'), $periode_end->format('Ymd')])
                ->sum('sub_total');
        $purchase = Purchase::where('pr_state', '<>', '99')
                ->whereBetween('pr_date', [$periode_start->format('Ymd'), $periode_end->format('Ymd')])
                ->sum('total');
        $saving = Saving::where('sv_state', '<>', '99')
                ->whereBetween('sv_date', [$periode_start->format('Ymd'), $periode_end->format('Ymd')])
                ->sum('sv_value');
        $loan = Loan::where('loan_state', '<>', '99')
                ->whereBetween('loan_date', [$periode_start->format('Ymd'), $periode_end->format('Ymd')])
                ->sum('loan_value');

        $data = [
            "sales" => $sales,
            "purchase" => $purchase,
            "saving" => $saving,
            "loan" => $loan,
            "saleOfYear" => $saleOfYear,
            "purchaseOfYear" => $purchaseOfYear,
            "savingOfYear" => $savingOfYear,
            "loanOfYear" => $loanOfYear,
        ];
       
        return view('dashboard', $data);
    }

    public function dashboardMember() 
    {
        $userID = auth()->id();
        $member = Member::where('user_id', $userID)->first();
        $savings = Saving::with('svType')->where('member_id', $member->id)->get();

        $total_saving = 0;
        $total_sht = 0;
        foreach ($savings as $key => $saving) {
            if ($saving->svType->name == 'SHT') {
                $total_sht += ($saving->sv_value*1);
            }
            $total_saving += ($saving->sv_value*1);           
            
        }

        $loans = Loan::with('payments')->where('member_id', $member->id)->get();

        $total_loan = 0;
        foreach ($loans as $key => $loan) {
            $total_loan += ($loan->loan_value*1);
        }

        $data = [
            'member' => $member,
            'total_saving' => $total_saving,
            'total_sht' => $total_sht,
            'total_loan' => $total_loan,
        ];
        return view('dashboard-member', $data);
    }

    public function downloadDB(DatabaseBackupService $backupService)
    {
        $filePath = $backupService->backup();
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function profile() 
    {
        $user = User::with(['roles', 'member'])
        ->where('id', auth()->id())
        ->first();
        return view('profiles.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function search(Request $request)
    {
        $search = $request->q;

        $users = User::where('name', 'like', "%$search%")
            ->orwhere('email', 'like', "%$search%")
            ->select('id', 'name')
            ->limit(10)
            ->get();

        return response()->json($users);
    }
}
