<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Company extends Model
{
    use SoftDeletes;

    protected $table = 'companies';

    // UUID primary key
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'code',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Auto-generate UUID saat create
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }

            // default active kalau belum di-set
            if ($model->is_active === null) {
                $model->is_active = true;
            }
        });
    }

    /* =========================
     * Scopes
     * ========================= */

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /* =========================
     * Relations (sesuaikan kalau beda)
     * ========================= */

    /**
     * Company punya banyak site
     */
    public function sites()
    {
        return $this->hasMany(Site::class, 'company_id', 'id');
    }

    /**
     * Company punya banyak division
     */
    public function divisions()
    {
        return $this->hasMany(Division::class, 'company_id', 'id');
    }

    /**
     * User milik company (kalau user ada kolom company_id)
     * Kalau kamu pakai pivot user_company, ganti ke belongsToMany.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'company_id', 'id');
    }

    /**
     * Kalau PowerBiReport ada kolom company_id
     */
    public function powerbiReports()
    {
        return $this->hasMany(PowerBiReport::class, 'company_id', 'id');
    }
}
