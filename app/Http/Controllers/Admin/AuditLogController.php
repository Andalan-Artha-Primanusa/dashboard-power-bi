<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index()
    {
        // contoh ambil dari tabel activity_logs
        $logs = \DB::table('activity_logs')->latest()->paginate(50);

        return view('admin.audit.index', compact('logs'));
    }

    public function showUser(User $user)
    {
        $logs = \DB::table('activity_logs')
            ->where('causer_id', $user->id)
            ->latest()
            ->paginate(50);

        return view('admin.audit.user', compact('logs','user'));
    }

    public function exportCsv()
    {
        // TODO: implement export global
    }

    public function exportUserCsv(User $user)
    {
        // TODO: implement export per user
    }
}
