<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Arr;

class User extends Authenticatable
{
    use Notifiable, HasUuids, HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'email',
        'password',
        'division_id',
        'role',
        'default_site_id',
        'allowed_site_ids',
    ];

    protected $hidden = ['password','remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'allowed_site_ids'  => 'array',
    ];

    // ===== Relations =====
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function powerBiReports()
    {
        return $this->belongsToMany(PowerBiReport::class, 'powerbi_report_user', 'user_id', 'report_id')
                    ->withTimestamps();
    }

    public function defaultSite()
    {
        return $this->belongsTo(Site::class, 'default_site_id');
    }

    public function sites()
    {
        return $this->belongsToMany(Site::class, 'site_user', 'user_id', 'site_id')
                    ->withTimestamps();
    }

    // ===== Role helpers =====
    public function hasRole(string $role): bool
    {
        return ($this->role ?? 'user') === $role;
    }

    public function inRoles(array $roles): bool
    {
        return in_array($this->role ?? 'user', $roles, true);
    }

    public function isSuperAdmin(): bool
    {
        return ($this->role ?? '') === 'super_admin';
    }

    public function isGM(): bool
    {
        $r = strtolower((string) ($this->role ?? ''));
        return in_array($r, ['gm','general manager','generalmanager'], true);
    }

    // ===== Site access helpers =====
    /**
     * Kembalikan koleksi Site yang dapat diakses user:
     * 1) via pivot site_user; 2) via allowed_site_ids (JSON); 3) fallback defaultSite.
     */
    public function accessibleSites()
    {
        $viaPivot = $this->sites()->pluck('sites.id')->all();
        if (!empty($viaPivot)) {
            return Site::whereIn('id', $viaPivot)->get();
        }

        $ids = array_filter(Arr::wrap($this->allowed_site_ids));
        if (!empty($ids)) {
            return Site::whereIn('id', $ids)->get();
        }

        return $this->defaultSite ? Site::whereKey($this->default_site_id)->get() : collect();
    }

    /**
     * Apakah user boleh akses site tertentu?
     * SA/GM selalu true; lainnya cek pivot, JSON allowed_site_ids, atau default_site_id.
     */
    public function canAccessSite($siteOrId): bool
    {
        if ($this->isSuperAdmin() || $this->isGM()) return true;

        $targetId = $siteOrId instanceof Site ? $siteOrId->getKey() : (string) $siteOrId;
        if (!$targetId) return false;

        if ($this->sites()->where('sites.id', $targetId)->exists()) return true;

        $ids = array_filter(Arr::wrap($this->allowed_site_ids));
        if (in_array($targetId, $ids, true)) return true;

        return $this->default_site_id === $targetId;
    }

    /**
     * Ambil Site aktif dari session jika valid; fallback ke defaultSite.
     */
    public function activeSite(): ?Site
    {
        $sid = session('site_id');
        if ($sid && $this->canAccessSite($sid)) {
            return Site::find($sid);
        }
        return $this->defaultSite;
    }
}
