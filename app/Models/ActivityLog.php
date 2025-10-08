<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    public $incrementing = false;
    protected $keyType = 'string';

    // semua kolom bisa diisi mass assignment
    protected $guarded = [];

    protected $casts = [
        'payload'    => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        // isi otomatis UUID untuk primary key
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    /** Relasi user pelaku */
    public function causer()
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    /** Scope pencarian bebas */
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        if (!filled($term)) return $q;
        $t = trim($term);

        return $q->where(function ($w) use ($t) {
            $w->where('action', 'like', "%{$t}%")
              ->orWhere('subject_type', 'like', "%{$t}%")
              ->orWhere('subject_id', 'like', "%{$t}%")
              ->orWhere('ip', 'like', "%{$t}%")
              ->orWhere('user_agent', 'like', "%{$t}%")
              ->orWhereJsonContains('payload->before', $t)
              ->orWhereJsonContains('payload->after', $t);
        })->orWhereHas('causer', function ($u) use ($t) {
            $u->where('name','like',"%{$t}%")
              ->orWhere('email','like',"%{$t}%");
        });
    }

    /** Scope filter berdasarkan action */
    public function scopeAction(Builder $q, ?string $action): Builder
    {
        return filled($action) ? $q->where('action', $action) : $q;
    }

    /** Scope filter untuk log milik 1 user */
    public function scopeForUser(Builder $q, $userId): Builder
    {
        return $q->where('causer_id', $userId);
    }
}
