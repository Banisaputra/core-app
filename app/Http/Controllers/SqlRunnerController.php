<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SqlRunnerController extends Controller
{
    public function index()
    {
        return view('sql.index');
    }

    public function run(Request $request)
    {
        $request->validate([
            'query' => 'required|string',
        ]);

        $sql = trim($request->get('query'));

        try {
            if (stripos($sql, 'SELECT') === 0 || stripos($sql, 'SHOW') === 0) {
                $results = DB::select($sql);
                return back()->with('result_return', true)->with('results', $results)->with('query', $sql);
            } else {
                $affected = DB::affectingStatement($sql);
                return back()->with('message', "Query berhasil dieksekusi. $affected baris terpengaruh.")->with('query', $sql);
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->with('query', $sql);
        }
    }
}
