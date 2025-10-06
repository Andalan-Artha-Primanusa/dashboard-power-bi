<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable
{
    use Notifiable, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    // tambahkan 'role' & 'division_id' ke fillable
    protected $fillable = [
        'name',
        'email',
        'password',
        'division_id',
        'role',          // <= penting
    ];

    protected $hidden = ['password','remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // --- Relations ---
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function powerBiReports()
    {
        return $this->belongsToMany(PowerBiReport::class, 'powerbi_report_user', 'user_id', 'report_id')
                    ->withTimestamps();
    }

    // --- Helpers (opsional, enak dipakai di Blade/Gate) ---
    public function hasRole(string $role): bool
    {
        return ($this->role ?? 'user') === $role;
    }

    public function inRoles(array $roles): bool
    {
        return in_array($this->role ?? 'user', $roles, true);
    }
}
