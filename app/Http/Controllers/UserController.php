<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\User;
use App\Models\Policy;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\DatabaseBackupService;
use App\Repositories\PenjualanRepository;
use App\Repositories\PembelianRepository;

class UserController extends Controller
{ 
    protected $penjualanRepository;
    protected $pembelianRepository;

    public function __construct(PenjualanRepository $penjualanRepository, PembelianRepository $pembelianRepository)
    {
        $this->penjualanRepository = $penjualanRepository;
        $this->pembelianRepository = $pembelianRepository;
    }

    public function dashboard() 
    {   
        $role = strtoupper(auth()->user()->roles[0]['name']);
        $cut_off_day = Policy::where('pl_name', 'cut_off_bulanan')->value('pl_value');
        $today = new DateTime();
        $current_day = (int)$today->format('d');
        $current_month = (int)$today->format('m');
        $current_year = (int)$today->format('Y');

        $periode_start = new DateTime("$current_year-$current_month-".($cut_off_day + 1)."");
        $periode_start->modify("-1 month");
        $periode_end = new DateTime("$current_year-$current_month-".($cut_off_day ?? 1)."");

        // data member
        $userID = auth()->id();
        
        $saleOfYear = $this->penjualanRepository->getPenjualanPerPeriode($current_year, $cut_off_day);
        $purchaseOfYear = $this->pembelianRepository->getPembelianPerPeriode($current_year, $cut_off_day);

        // data all
        $sales = Sale::whereBetween('sa_date', [$periode_start->format('Ymd'), $periode_end->format('Ymd')])
                    ->sum('sub_total');
        $purchase = Purchase::whereBetween('pr_date', [$periode_start->format('Ymd'), $periode_end->format('Ymd')])
                    ->sum('total');

        $data = [
            "sales" => $sales,
            "purchase" => $purchase,
            "saleOfYear" => $saleOfYear,
            "purchaseOfYear" => $purchaseOfYear,
        ];
        if ($role === "MEMBER") return view('dashboard-member');
        return view('dashboard', $data);
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
