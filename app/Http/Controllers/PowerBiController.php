<?php // app/Http/Controllers/PowerBiController.php
namespace App\Http\Controllers;

use App\Models\PowerBiReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PowerBiController extends Controller
{
    public function index(Request $r) {
        $reports = PowerBiReport::visibleTo($r->user())->latest()->paginate(12);
        return view('powerbi.index', compact('reports'));
    }

    public function show(PowerBiReport $report) {
        abort_unless(Gate::allows('view-powerbi',$report), 403);
        $embedUrl = $report->embedUrlWithUI();
        return view('powerbi.show', compact('report','embedUrl'));
    }
}
