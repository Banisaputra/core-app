<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Saving;
use App\Models\SavingType;
use Illuminate\Http\Request;

class SavingController extends Controller
{
    public function index() 
    {
        $savings = Saving::with('member')->latest()->paginate(10);
        return view('savings.index', compact('savings'));
    }

    public function create() 
    {
        $members = Member::all();
        $sv_types = SavingType::all();
        return view('savings.create', compact('members', 'sv_types'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'type' => 'required|exists:saving_types,id',
            'amount' => 'required|numeric|min:1000',
            'date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        Saving::create($request->all());

        return redirect()->route('savings.index')->with('success', 'Data simpanan berhasil ditambahkan.');
    }
}
