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
        'default_company_id',   // ✅ TAMBAH INI
        'division_id',
        'role',
        'default_site_id',
        'allowed_site_ids',
        // optional kalau kolom ada:
        'avatar_path',
        'photo_path',
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

    public function defaultSite()
    {
        return $this->belongsTo(Site::class, 'default_site_id');
    }

    public function sites()
    {
        return $this->belongsToMany(Site::class, 'site_user', 'user_id', 'site_id')
                    ->withTimestamps();
    }

    // ✅ RELASI COMPANY
    public function defaultCompany()
    {
        return $this->belongsTo(Company::class, 'default_company_id');
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
    public function accessibleSites()
    {
        $viaPivot = $this->sites()->pluck('sites.id')->all();
        $viaJson  = array_filter(Arr::wrap($this->allowed_site_ids));
        $default  = $this->default_site_id ? [$this->default_site_id] : [];

        $ids = collect(array_merge($viaPivot, $viaJson, $default))
            ->filter()
            ->map(fn ($id) => (string) $id)
            ->unique()
            ->values();

        return $ids->isEmpty()
            ? collect()
            : Site::whereIn('id', $ids)->orderBy('code')->get();
    }

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

    public function activeSite(): ?Site
    {
        $sid = session('site_id');
        if ($sid && $this->canAccessSite($sid)) {
            return Site::find($sid);
        }
        return $this->defaultSite;
    }
}
