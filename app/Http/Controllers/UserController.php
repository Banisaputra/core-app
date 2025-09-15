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

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function dashboard() 
    {   
        $cut_off_day = Policy::where('pl_name', 'cut_off_bulanan')->value('pl_value');
        $today = new DateTime();
        $current_day = (int)$today->format('d');
        $current_month = (int)$today->format('m');
        $current_year = (int)$today->format('Y');

        $periode_start = new DateTime("$current_year-$current_month-".($cut_off_day + 1)."");
        $periode_start->modify("-1 month");
        $periode_end = new DateTime("$current_year-$current_month-".($cut_off_day ?? 0)."");

        $sales = Sale::whereBetween('sa_date', [$periode_start->format('Ymd'), $periode_end->format('Ymd')])
                    ->sum('sub_total');
        $purchase = Purchase::whereBetween('pr_date', [$periode_start->format('Ymd'), $periode_end->format('Ymd')])
                    ->sum('total');


        return view('dashboard', compact('sales', 'purchase'));
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
