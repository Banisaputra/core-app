<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sale;
use App\Models\User;
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

        $startOfMonth = Carbon::now()->startOfMonth()->format('Ymd');
        $endOfMonth = Carbon::now()->endOfMonth()->format('Ymd');

        $sales = Sale::whereBetween('sa_date', [$startOfMonth, $endOfMonth])
                    ->sum('sub_total');
        $purchase = Purchase::whereBetween('pr_date', [$startOfMonth, $endOfMonth])
                    ->sum('total');

        return view('dashboard', compact('sales', 'purchase'));
    }

    public function downloadDB(DatabaseBackupService $backupService)
    {
        $filePath = $backupService->backup();
        return response()->download($filePath)->deleteFileAfterSend(true);
        // return $backupService->backup();
    }

    public function profile() 
    {
        $user = User::with(['roles', 'member'])
        ->where('id', auth()->id())
        ->first();
        // dd($user);
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
