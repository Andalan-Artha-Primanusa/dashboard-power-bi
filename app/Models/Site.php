<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Site extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType   = 'string';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'config'    => 'array',
        'lat'       => 'decimal:7',
        'lng'       => 'decimal:7',
    ];

    // ===== RELATIONS =====

    // Power BI reports <-> sites (pivot: powerbi_report_site)
    public function powerBiReports()
    {
        return $this->belongsToMany(PowerBiReport::class, 'powerbi_report_site', 'site_id', 'report_id')
            ->withTimestamps();
    }

    // Users yang memiliki akses via pivot site_user
    public function users()
    {
        return $this->belongsToMany(User::class, 'site_user', 'site_id', 'user_id')
            ->withTimestamps();
    }

    // Users yang menjadikan site ini sebagai default
    public function defaultForUsers()
    {
        return $this->hasMany(User::class, 'default_site_id');
    }

    // (opsional) Pembuat site (kolom created_by di tabel sites)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ===== SCOPES =====

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    /**
     * Batasi query ke site yang bisa diakses user tertentu:
     * - Super Admin & GM: semua site
     * - Lainnya: gabungan dari pivot site_user, allowed_site_ids (JSON), dan default_site_id
     */
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
