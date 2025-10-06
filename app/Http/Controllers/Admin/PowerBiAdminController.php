<?php // app/Http/Controllers/Admin/PowerBiAdminController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PowerBiReport;
use App\Models\User;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PowerBiAdminController extends Controller
{
    public function __construct() { $this->middleware(['auth']); }

    public function index(Request $r) {
        abort_unless(Gate::allows('manage-powerbi'), 403);
        $reports = PowerBiReport::with(['creator'])->withTrashed()->orderBy('created_at','desc')->paginate(15);
        return view('admin.powerbi.index', compact('reports'));
    }

    public function create() {
        abort_unless(Gate::allows('manage-powerbi'), 403);
        $users = User::orderBy('name')->get(['id','name','email']);
        $divisions = Division::orderBy('name')->get(['id','name']);
        return view('admin.powerbi.create', compact('users','divisions'));
    }

    public function store(Request $r) {
        abort_unless(Gate::allows('manage-powerbi'), 403);
        $data = $r->validate([
            'name'       => 'required|string|max:255',
            'embed_url'  => 'required|url',
            'show_filter_pane'      => 'sometimes|boolean',
            'show_nav_pane'         => 'sometimes|boolean',
            'show_toolbar'          => 'sometimes|boolean',
            'allow_client_download' => 'sometimes|boolean',
            'user_ids'     => 'array',
            'user_ids.*'   => 'uuid|exists:users,id',
            'division_ids' => 'array',
            'division_ids.*' => 'uuid|exists:divisions,id',
        ]);

        $report = PowerBiReport::create([
            'id'         => (string) Str::uuid(),
            'name'       => $data['name'],
            'embed_url'  => $data['embed_url'],
            'show_filter_pane'      => (bool)($data['show_filter_pane'] ?? false),
            'show_nav_pane'         => (bool)($data['show_nav_pane'] ?? true),
            'show_toolbar'          => (bool)($data['show_toolbar'] ?? true),
            'allow_client_download' => (bool)($data['allow_client_download'] ?? true),
            'created_by' => $r->user()->id ?? null,
        ]);

        $report->users()->sync($data['user_ids'] ?? []);
        $report->divisions()->sync($data['division_ids'] ?? []);

        return redirect()->route('admin.powerbi.index')->with('ok','Report dibuat.');
    }

    public function edit(PowerBiReport $report) {
        abort_unless(Gate::allows('manage-powerbi'), 403);
        $users = User::orderBy('name')->get(['id','name','email']);
        $divisions = Division::orderBy('name')->get(['id','name']);
        $selectedUsers = $report->users()->pluck('users.id')->all();
        $selectedDivs  = $report->divisions()->pluck('divisions.id')->all();
        return view('admin.powerbi.edit', compact('report','users','divisions','selectedUsers','selectedDivs'));
    }

    public function update(Request $r, PowerBiReport $report) {
        abort_unless(Gate::allows('manage-powerbi'), 403);
        $data = $r->validate([
            'name'       => 'required|string|max:255',
            'embed_url'  => 'required|url',
            'show_filter_pane'      => 'sometimes|boolean',
            'show_nav_pane'         => 'sometimes|boolean',
            'show_toolbar'          => 'sometimes|boolean',
            'allow_client_download' => 'sometimes|boolean',
            'user_ids'     => 'array',
            'user_ids.*'   => 'uuid|exists:users,id',
            'division_ids' => 'array',
            'division_ids.*' => 'uuid|exists:divisions,id',
        ]);

        $report->update([
            'name'       => $data['name'],
            'embed_url'  => $data['embed_url'],
            'show_filter_pane'      => (bool)($data['show_filter_pane'] ?? false),
            'show_nav_pane'         => (bool)($data['show_nav_pane'] ?? true),
            'show_toolbar'          => (bool)($data['show_toolbar'] ?? true),
            'allow_client_download' => (bool)($data['allow_client_download'] ?? true),
        ]);

        $report->users()->sync($data['user_ids'] ?? []);
        $report->divisions()->sync($data['division_ids'] ?? []);

        return redirect()->route('admin.powerbi.index')->with('ok','Report diperbarui.');
    }

    // Soft delete
    public function destroy(PowerBiReport $report) {
        abort_unless(Gate::allows('manage-powerbi'), 403);
        $report->delete();
        return back()->with('ok','Report dihapus (soft delete).');
    }

    // Restore
    public function restore(string $id) {
        abort_unless(Gate::allows('manage-powerbi'), 403);
        $report = PowerBiReport::withTrashed()->findOrFail($id);
        $report->restore();
        return back()->with('ok','Report dipulihkan.');
    }
}
