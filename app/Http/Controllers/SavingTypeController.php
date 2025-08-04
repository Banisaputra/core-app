<?php

namespace App\Http\Controllers;

use App\Models\SavingType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SavingTypeController extends Controller
{
    public function search(Request $request)
    {
        $search = $request->q;

        $svTypes = SavingType::when($search, function($query) use ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            });
        })
        ->where('is_transactional', 1)
        ->select('id', 'name')
        ->limit(5)
        ->get();

        return response()->json($svTypes);
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:saving_types,name',
                'description' => 'required',
                'value' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            SavingType::create([
                'name' => $request->name,
                'description' => $request->description,
                'value' => $request->value,
                'is_auto' => 0,
                'auto_date' => 0,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            session()->flash('success', 'Jenis simpanan berhasil ditambahkan!');
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $type = SavingType::findOrFail($id);
        return response()->json($type);
    }

    public function update(Request $request, $id)
    {
        $type = SavingType::findOrFail($id);
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:saving_types,name,'.$id,
                'description' => 'required',
                'value' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $type->update([
                'name' => $request->name,
                'description' => $request->description,
                'value' => $request->value,
                'updated_by' => auth()->id(),
            ]);

            session()->flash('success', 'Jenis simpanan berhasil diubah!');
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        $svType = SavingType::findOrFail($id);
        $message = "";
        if($svType) { 
            if ($svType->is_transactional == 0) {
                $svType->is_transactional = 1;
                $message .= "diaktifkan";
            } else {
                $svType->is_transactional = 0;
                $message .= "dinonaktifkan";
            }
        
            $svType->update();
        }

        return redirect()->back()->with('success', "Jenis Simpanan berhasil ".$message);
        
    }

    public function schedule(Request $request) {
        $request->validate([
            'auto_day' => 'required|integer|min:1|max:28',
            'sv_type_id' => 'nullable|array',
            'sv_type_id.*' => 'exists:saving_types,id',
        ]);

        $svTypes = SavingType::pluck('id');
        foreach ($svTypes as $key => $id) {
            $isAuto = 0;
            $autoDay = 0;
            if (in_array($id, $request->sv_type_id ?? [])) {
                $isAuto = 1;
                $autoDay = $request->auto_day;
            } 
            SavingType::where('id', $id)->update([
                'is_auto' => $isAuto,
                'auto_date' => $autoDay,
                'updated_by' => auth()->id()
            ]);
        }

        return redirect()->back()->with('success', "Penjadwalan berhasil disimpan.");
    }
}
