<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Site extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'config'    => 'array',
        'lat'       => 'decimal:7',
        'lng'       => 'decimal:7',
    ];

    // RELATIONS
    public function powerBiReports()
    {
        return $this->belongsToMany(PowerBiReport::class, 'powerbi_report_site', 'site_id', 'report_id')
            ->withTimestamps();
    }
}
