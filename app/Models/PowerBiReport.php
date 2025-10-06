<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PowerBiReport extends Model
{
    use HasUuids, SoftDeletes;

    /** Pastikan ini match dengan migration kamu */
    protected $table = 'powerbi_reports';

    /** Kolom yang boleh diisi mass-assignment */
    protected $fillable = [
        'name',
        'embed_url',
        'show_filter_pane',
        'show_nav_pane',
        'show_toolbar',
        'allow_client_download',
        'created_by',
    ];

    protected $casts = [
        'show_filter_pane'      => 'bool',
        'show_nav_pane'         => 'bool',
        'show_toolbar'          => 'bool',
        'allow_client_download' => 'bool',
        'deleted_at'            => 'datetime',
    ];

    /* =========================
     |  RELATIONS
     |=========================*/

    /** User yang punya akses langsung (pivot powerbi_report_user) */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'powerbi_report_user', 'report_id', 'user_id')
            ->withTimestamps();
    }

    /** Divisi yang punya akses (pivot powerbi_report_division) */
    public function divisions(): BelongsToMany
    {
        return $this->belongsToMany(Division::class, 'powerbi_report_division', 'report_id', 'division_id')
            ->withTimestamps();
    }

    /** Siapa yang membuat entri report ini */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* =========================
     |  SCOPES
     |=========================*/

    /**
     * Report yang dapat dilihat oleh user:
     * - punya akses langsung via pivot users(), atau
     * - user-nya berada di division yang punya akses via divisions().
     */
    public function scopeVisibleTo($q, User|string|null $user)
    {
        if (!$user) {
            return $q->whereRaw('1=0');
        }

        $u = $user instanceof User ? $user : User::find($user);
        if (!$u) {
            return $q->whereRaw('1=0');
        }

        return $q->where(function ($w) use ($u) {
            $w->whereHas('users', fn($wu) => $wu->where('users.id', $u->id));
            if ($u->division_id) {
                $w->orWhereHas('divisions', fn($wd) => $wd->where('divisions.id', $u->division_id));
            }
        });
    }

    /* =========================
     |  HELPERS
     |=========================*/

    /** Build embed URL lengkap dengan opsi UI (tanpa controller) */
    public function embedUrlWithUI(array $ui = []): string
    {
        $defaults = [
            'filterPaneEnabled'     => $this->show_filter_pane ? 'true' : 'false',
            'navContentPaneEnabled' => $this->show_nav_pane ? 'true' : 'false',
            'toolbarEnabled'        => $this->show_toolbar ? 'true' : 'false',
            'autoAuth'              => 'true',
        ];

        $params = array_merge($defaults, $this->normalizeBoolParams($ui));
        $sep = str_contains($this->embed_url, '?') ? '&' : '?';

        return $this->embed_url . $sep . http_build_query($params);
    }

    /** Normalisasi boolean ke 'true'/'false' untuk query string */
    protected function normalizeBoolParams(array $params): array
    {
        return collect($params)->map(function ($v) {
            if (is_bool($v)) return $v ? 'true' : 'false';
            if (in_array($v, [1, '1', 'true', true], true)) return 'true';
            if (in_array($v, [0, '0', 'false', false], true)) return 'false';
            return $v;
        })->all();
    }

    /* =========================
     |  EVENTS (quality-of-life)
     |=========================*/

    /** Auto-set created_by jika ada user login */
    protected static function booted(): void
    {
        static::creating(function (self $m) {
            if (blank($m->created_by) && auth()->check()) {
                $m->created_by = auth()->id();
            }
        });
    }

    public function sites()
    {
        return $this->belongsToMany(Site::class, 'powerbi_report_site', 'report_id', 'site_id')
            ->withTimestamps();
    }
}
