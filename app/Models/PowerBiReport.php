<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// ===== Tambahkan import yang diperlukan =====
use App\Models\User;
use App\Models\Division;
use App\Models\Site;

class PowerBiReport extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'powerbi_reports';

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
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'powerbi_report_user', 'report_id', 'user_id')
            ->withTimestamps();
    }

    public function divisions(): BelongsToMany
    {
        return $this->belongsToMany(Division::class, 'powerbi_report_division', 'report_id', 'division_id')
            ->withTimestamps();
    }

    public function sites(): BelongsToMany
    {
        return $this->belongsToMany(Site::class, 'powerbi_report_site', 'report_id', 'site_id')
            ->withTimestamps();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* =========================
     |  SCOPES
     |=========================*/

    /**
     * Visible jika:
     *  - dibagikan langsung ke user, ATAU
     *  - dibagikan ke division user, ATAU
     *  - dibagikan ke site aktif user, ATAU
     *  - (opsional) tidak punya grant sama sekali => global.
     */
    public function scopeVisibleTo($q, User|string|null $user, array $opts = [])
    {
        // $opts['global_if_no_grant'] default true
        $globalIfNoGrant = $opts['global_if_no_grant'] ?? true;

        if (!$user) {
            return $q->whereRaw('1=0');
        }

        $u = $user instanceof User ? $user : User::find($user);
        if (!$u) {
            return $q->whereRaw('1=0');
        }

        // Ambil site aktif: SiteContext::currentId() -> session('active_site_id') -> $u->site_id
        $activeSiteId = null;
        if (class_exists(\App\Support\SiteContext::class) && method_exists(\App\Support\SiteContext::class, 'currentId')) {
            $activeSiteId = \App\Support\SiteContext::currentId();
        }
        $activeSiteId = $activeSiteId ?? session('active_site_id') ?? ($u->site_id ?? null);

        return $q->where(function ($w) use ($u, $activeSiteId, $globalIfNoGrant) {
            // direct user grant
            $w->whereHas('users', fn($wu) => $wu->where('users.id', $u->id));

            // division grant
            if ($u->division_id) {
                $w->orWhereHas('divisions', fn($wd) => $wd->where('divisions.id', $u->division_id));
            }

            // site grant
            if ($activeSiteId) {
                $w->orWhereHas('sites', fn($ws) => $ws->where('sites.id', $activeSiteId));
            }

            // global (tanpa grant sama sekali)
            if ($globalIfNoGrant) {
                $w->orWhere(function ($x) {
                    $x->whereDoesntHave('users')
                      ->whereDoesntHave('divisions')
                      ->whereDoesntHave('sites');
                });
            }
        });
    }

    /* =========================
     |  HELPERS
     |=========================*/
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
     |  EVENTS
     |=========================*/
    protected static function booted(): void
    {
        static::creating(function (self $m) {
            if (blank($m->created_by) && auth()->check()) {
                $m->created_by = auth()->id();
            }
        });
    }
}
