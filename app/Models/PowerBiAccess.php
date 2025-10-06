<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PowerBiAccess extends Pivot
{
    protected $table = 'powerbi_report_user';
    public $incrementing = false; // pivot biasanya tanpa PK auto-increment
    protected $keyType = 'string';

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
