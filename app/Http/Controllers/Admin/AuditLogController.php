<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    /** List semua log (semua user) + search + filter action */
    public function index(Request $request)
    {
        $q       = trim((string) $request->query('q'));
        $action  = trim((string) $request->query('action'));

        $logs = ActivityLog::with(['causer:id,name,email'])
            ->search($q)         // scope di Model: cari di action/subject/ip/user_agent/payload + causer name/email
            ->action($action)    // scope di Model: where('action', $action)
            ->latest('created_at')
            ->paginate(50)
            ->withQueryString();

        // daftar action unik untuk dropdown
        $actions = ActivityLog::query()
            ->select('action')
            ->whereNotNull('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('admin.audit.index', compact('logs','q','action','actions'));
    }

    /** List log untuk 1 user */
    public function showUser(Request $request, User $user)
    {
        $q       = trim((string) $request->query('q'));
        $action  = trim((string) $request->query('action'));

        $logs = ActivityLog::with(['causer:id,name,email'])
            ->forUser($user->id) // scope: where('causer_id', $user->id)
            ->search($q)
            ->action($action)
            ->latest('created_at')
            ->paginate(50)
            ->withQueryString();

        $actions = ActivityLog::query()
            ->forUser($user->id)
            ->select('action')
            ->whereNotNull('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('admin.audit.user', compact('logs','user','q','action','actions'));
    }

    /** Export CSV: semua log */
    public function exportCsv(Request $request): StreamedResponse
    {
        $q       = trim((string) $request->query('q'));
        $action  = trim((string) $request->query('action'));

        $filename = 'audit_logs_all_'.now()->format('Ymd_His').'.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($q, $action) {
            $out = fopen('php://output', 'w');

            // header CSV sesuai skema tabel kamu
            fputcsv($out, [
                'id','created_at','user_name','user_email','causer_id',
                'action','subject_type','subject_id','ip','user_agent','payload_json',
            ]);

            ActivityLog::with(['causer:id,name,email'])
                ->search($q)
                ->action($action)
                ->orderBy('id')
                ->chunk(1000, function ($rows) use ($out) {
                    foreach ($rows as $r) {
                        fputcsv($out, [
                            $r->id,
                            optional($r->created_at)->toDateTimeString(),
                            $r->causer->name  ?? null,
                            $r->causer->email ?? null,
                            $r->causer_id,
                            $r->action,
                            $r->subject_type,
                            $r->subject_id,
                            $r->ip,
                            $r->user_agent,
                            is_string($r->payload) ? $r->payload : json_encode($r->payload),
                        ]);
                    }
                });

            fclose($out);
        }, 200, $headers);
    }

    /** Export CSV: log per user */
    public function exportUserCsv(Request $request, User $user): StreamedResponse
    {
        $q       = trim((string) $request->query('q'));
        $action  = trim((string) $request->query('action'));

        $safeName = str($user->name ?? 'user')->slug('_');
        $filename = "audit_logs_user_{$safeName}_".now()->format('Ymd_His').'.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($user, $q, $action) {
            $out = fopen('php://output', 'w');

            fputcsv($out, [
                'id','created_at','user_name','user_email','causer_id',
                'action','subject_type','subject_id','ip','user_agent','payload_json',
            ]);

            ActivityLog::with(['causer:id,name,email'])
                ->forUser($user->id)
                ->search($q)
                ->action($action)
                ->orderBy('id')
                ->chunk(1000, function ($rows) use ($out) {
                    foreach ($rows as $r) {
                        fputcsv($out, [
                            $r->id,
                            optional($r->created_at)->toDateTimeString(),
                            $r->causer->name  ?? null,
                            $r->causer->email ?? null,
                            $r->causer_id,
                            $r->action,
                            $r->subject_type,
                            $r->subject_id,
                            $r->ip,
                            $r->user_agent,
                            is_string($r->payload) ? $r->payload : json_encode($r->payload),
                        ]);
                    }
                });

            fclose($out);
        }, 200, $headers);
    }
}
