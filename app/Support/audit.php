<?php

use App\Models\ActivityLog;

if (!function_exists('audit')) {
    function audit(string $action, array $payload = [], ?string $subjectType = null, ?string $subjectId = null): void {
        $u = auth()->user();
        ActivityLog::create([
            'causer_id'    => $u?->id,
            'action'       => $action,
            'subject_type' => $subjectType,
            'subject_id'   => $subjectId,
            'ip'           => request()->ip(),
            'user_agent'   => request()->userAgent(),
            'payload'      => $payload,
        ]);
    }
}
