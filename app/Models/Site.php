<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $guarded = [];

    protected $casts = [
        'is_active'  => 'boolean',
        'config'     => 'array',
        'lat'        => 'decimal:7',
        'lng'        => 'decimal:7',
        'deleted_at' => 'datetime', // opsional, hanya untuk casting
    ];

    // ===== RELATIONS =====

    public function powerBiReports()
    {
        return $this->belongsToMany(PowerBiReport::class, 'powerbi_report_site', 'site_id', 'report_id')
            ->withTimestamps();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'site_user', 'site_id', 'user_id')
            ->withTimestamps();
    }

    public function defaultForUsers()
    {
        return $this->hasMany(User::class, 'default_site_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ===== SCOPES =====

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeAccessibleBy($q, User $user)
    {
        if ($user->isSuperAdmin() || $user->isGM()) {
            return $q;
        }

        $pivotIds = $user->sites()->pluck('sites.id')->all();
        $jsonIds  = (array) ($user->allowed_site_ids ?? []);
        $default  = $user->default_site_id ? [$user->default_site_id] : [];

        $ids = array_values(array_unique(array_merge($pivotIds, $jsonIds, $default)));

        return empty($ids) ? $q->whereRaw('1=0') : $q->whereIn('id', $ids);
    }

    // ===== ACCESSORS =====

    public function getLabelAttribute(): string
    {
        return trim(($this->code ?? 'SITE').' — '.($this->name ?? ''));
    }
}
