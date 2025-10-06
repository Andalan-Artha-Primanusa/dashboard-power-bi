<?php // app/Http/Controllers/Admin/PowerBiAdminController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PowerBiReport;
use App\Models\User;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PowerBiAdminController extends Controller
{
      public function __construct()
    {
        // Batasi ke GM & Super Admin sesuai alias middleware yang sudah kamu buat
        $this->middleware(['auth', 'role:gm,super_admin']);
    }

    public function index(Request $r)
    {
        $reports = PowerBiReport::with(['creator'])
            ->withTrashed()
            ->orderBy('created_at','desc')
            ->paginate(15);

        return view('admin.powerbi.index', compact('reports'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get(['id','name','email']);
        $divisions = Division::orderBy('name')->get(['id','name']);
        return view('admin.powerbi.create', compact('users','divisions'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name'                  => 'required|string|max:255',
            'embed_url'             => 'required|url',
            'show_filter_pane'      => 'sometimes|boolean',
            'show_nav_pane'         => 'sometimes|boolean',
            'show_toolbar'          => 'sometimes|boolean',
            'allow_client_download' => 'sometimes|boolean',
            'user_ids'              => 'array',
            'user_ids.*'            => 'uuid|exists:users,id',
            'division_ids'          => 'array',
            'division_ids.*'        => 'uuid|exists:divisions,id',
        ]);

        $report = PowerBiReport::create([
            'id'                    => (string) Str::uuid(),
            'name'                  => $data['name'],
            'embed_url'             => $data['embed_url'],
            'show_filter_pane'      => (bool)($data['show_filter_pane'] ?? false),
            'show_nav_pane'         => (bool)($data['show_nav_pane'] ?? true),
            'show_toolbar'          => (bool)($data['show_toolbar'] ?? true),
            'allow_client_download' => (bool)($data['allow_client_download'] ?? true),
            'created_by'            => $r->user()->id ?? null,
        ]);

        $report->users()->sync($data['user_ids'] ?? []);
        $report->divisions()->sync($data['division_ids'] ?? []);

        // AUDIT: create
        if (function_exists('audit')) {
            audit('powerbi.create', [
                'report_id' => $report->id,
                'name'      => $report->name,
                'embed_url' => $report->embed_url,
                'flags'     => [
                    'filter_pane' => $report->show_filter_pane,
                    'nav_pane'    => $report->show_nav_pane,
                    'toolbar'     => $report->show_toolbar,
                    'client_dl'   => $report->allow_client_download,
                ],
                'grants' => [
                    'users'     => $data['user_ids'] ?? [],
                    'divisions' => $data['division_ids'] ?? [],
                ],
            ], PowerBiReport::class, $report->id);
        }

        return redirect()->route('admin.powerbi.index')->with('ok','Report dibuat.');
    }

    public function edit(PowerBiReport $report)
    {
        $users = User::orderBy('name')->get(['id','name','email']);
        $divisions = Division::orderBy('name')->get(['id','name']);
        $selectedUsers = $report->users()->pluck('users.id')->all();
        $selectedDivs  = $report->divisions()->pluck('divisions.id')->all();

        return view('admin.powerbi.edit', compact('report','users','divisions','selectedUsers','selectedDivs'));
    }

    public function update(Request $r, PowerBiReport $report)
    {
        $data = $r->validate([
            'name'                  => 'required|string|max:255',
            'embed_url'             => 'required|url',
            'show_filter_pane'      => 'sometimes|boolean',
            'show_nav_pane'         => 'sometimes|boolean',
            'show_toolbar'          => 'sometimes|boolean',
            'allow_client_download' => 'sometimes|boolean',
            'user_ids'              => 'array',
            'user_ids.*'            => 'uuid|exists:users,id',
            'division_ids'          => 'array',
            'division_ids.*'        => 'uuid|exists:divisions,id',
        ]);

        // snapshot before
        $before = [
            'name'      => $report->name,
            'embed_url' => $report->embed_url,
            'flags'     => [
                'filter_pane' => $report->show_filter_pane,
                'nav_pane'    => $report->show_nav_pane,
                'toolbar'     => $report->show_toolbar,
                'client_dl'   => $report->allow_client_download,
            ],
            'grants'    => [
                'users'     => $report->users()->pluck('users.id')->all(),
                'divisions' => $report->divisions()->pluck('divisions.id')->all(),
            ],
        ];

        $report->update([
            'name'                  => $data['name'],
            'embed_url'             => $data['embed_url'],
            'show_filter_pane'      => (bool)($data['show_filter_pane'] ?? false),
            'show_nav_pane'         => (bool)($data['show_nav_pane'] ?? true),
            'show_toolbar'          => (bool)($data['show_toolbar'] ?? true),
            'allow_client_download' => (bool)($data['allow_client_download'] ?? true),
        ]);

        $report->users()->sync($data['user_ids'] ?? []);
        $report->divisions()->sync($data['division_ids'] ?? []);

        // snapshot after
        $after = [
            'name'      => $report->name,
            'embed_url' => $report->embed_url,
            'flags'     => [
                'filter_pane' => $report->show_filter_pane,
                'nav_pane'    => $report->show_nav_pane,
                'toolbar'     => $report->show_toolbar,
                'client_dl'   => $report->allow_client_download,
            ],
            'grants'    => [
                'users'     => $report->users()->pluck('users.id')->all(),
                'divisions' => $report->divisions()->pluck('divisions.id')->all(),
            ],
        ];

        // AUDIT: update
        if (function_exists('audit')) {
            audit('powerbi.update', [
                'report_id' => $report->id,
                'before'    => $before,
                'after'     => $after,
            ], PowerBiReport::class, $report->id);
        }

        return redirect()->route('admin.powerbi.index')->with('ok','Report diperbarui.');
    }

    public function destroy(PowerBiReport $report)
    {
        // AUDIT sebelum delete (biar datanya masih ada)
        if (function_exists('audit')) {
            audit('powerbi.delete', [
                'report_id' => $report->id,
                'name'      => $report->name,
            ], PowerBiReport::class, $report->id);
        }

        $report->delete();
        return back()->with('ok','Report dihapus (soft delete).');
    }

    public function restore(string $id)
    {
        $report = PowerBiReport::withTrashed()->findOrFail($id);
        $report->restore();

        // AUDIT: restore
        if (function_exists('audit')) {
            audit('powerbi.restore', [
                'report_id' => $report->id,
                'name'      => $report->name,
            ], PowerBiReport::class, $report->id);
        }

        return back()->with('ok','Report dipulihkan.');
    }
}
