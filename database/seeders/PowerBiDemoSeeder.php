<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

use App\Models\User;
use App\Models\Division;
use App\Models\Site;
use App\Models\PowerBiReport;

class PowerBiDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // =========================
            // Divisions
            // =========================
            $ops = Division::firstOrCreate(
                ['code' => 'OPS'],
                ['id' => (string) Str::uuid(), 'name' => 'Operations']
            );
            if ($ops->name !== 'Operations') {
                $ops->forceFill(['name' => 'Operations'])->save();
            }

            $hr = Division::firstOrCreate(
                ['code' => 'HR'],
                ['id' => (string) Str::uuid(), 'name' => 'Human Resource']
            );
            if ($hr->name !== 'Human Resource') {
                $hr->forceFill(['name' => 'Human Resource'])->save();
            }

            // =========================
            // Sites
            // =========================
            $rawSites = [
                ['code' => 'HO',  'name' => 'Head Office Jakarta', 'region' => 'Jakarta',           'is_active' => true],
                ['code' => 'DBK', 'name' => 'DBK – Kalteng',       'region' => 'Kalimantan Tengah', 'is_active' => true],
                ['code' => 'POS', 'name' => 'POS – Halmahera',     'region' => 'Maluku Utara',      'is_active' => true],
                ['code' => 'SBS', 'name' => 'SBS – Sumsel',        'region' => 'Sumatera Selatan',  'is_active' => true],
            ];

            $siteByCode = [];
            foreach ($rawSites as $s) {
                $site = Site::firstOrCreate(
                    ['code' => $s['code']],
                    [
                        'id'         => (string) Str::uuid(),
                        'name'       => $s['name'],
                        'region'     => $s['region'] ?? null,
                        'address'    => null,
                        'lat'        => null,
                        'lng'        => null,
                        'is_active'  => (bool) ($s['is_active'] ?? true),
                        'config'     => null,
                        'created_by' => null,
                    ]
                );

                // Keep name/region/active in sync on subsequent runs
                $site->fill([
                    'name'      => $s['name'],
                    'region'    => $s['region'] ?? null,
                    'is_active' => (bool) ($s['is_active'] ?? true),
                ])->save();

                $siteByCode[$s['code']] = $site;
            }

            $allSiteIds = collect($siteByCode)->pluck('id')->values()->all();
            $dbkId      = $siteByCode['DBK']->id;
            $hoId       = $siteByCode['HO']->id;

            // Pastikan folder avatars tersedia
            Storage::disk('public')->makeDirectory('avatars');

            // =========================
            // Users (+ default_site_id & allowed_site_ids)
            // =========================
            // Super Admin
            $super = User::firstOrCreate(
                ['email' => 'admin@local.test'],
                [
                    'id'              => (string) Str::uuid(),
                    'name'            => 'Super Admin',
                    'password'        => Hash::make('password123'),
                    'division_id'     => $hr->id,
                    'role'            => 'super_admin',
                    'default_site_id' => $hoId,
                    'photo_path'      => null, // diisi setelah avatar dibuat
                ]
            );
            $super->forceFill([
                'division_id'      => $hr->id,
                'role'             => 'super_admin',
                'default_site_id'  => $hoId,
                'allowed_site_ids' => $allSiteIds, // semua site
            ])->save();
            $this->ensureAvatar($super, bg: '#7f1d1d', fg: '#fef3c7'); // maroon / gold

            // GM
            $gm = User::firstOrCreate(
                ['email' => 'gm@andalan.local'],
                [
                    'id'              => (string) Str::uuid(),
                    'name'            => 'General Manager',
                    'password'        => Hash::make('password'),
                    'division_id'     => $ops->id,
                    'role'            => 'gm',
                    'default_site_id' => $dbkId,
                    'photo_path'      => null,
                ]
            );
            $gm->forceFill([
                'division_id'      => $ops->id,
                'role'             => 'gm',
                'default_site_id'  => $dbkId,
                'allowed_site_ids' => $allSiteIds, // semua site
            ])->save();
            $this->ensureAvatar($gm, bg: '#0f766e', fg: '#ecfeff'); // teal / soft

            // Operator
            $op = User::firstOrCreate(
                ['email' => 'user@andalan.local'],
                [
                    'id'              => (string) Str::uuid(),
                    'name'            => 'Operator Site',
                    'password'        => Hash::make('password'),
                    'division_id'     => $ops->id,
                    'role'            => 'user',
                    'default_site_id' => $dbkId,
                    'photo_path'      => null,
                ]
            );
            $op->forceFill([
                'division_id'      => $ops->id,
                'role'             => 'user',
                'default_site_id'  => $dbkId,
                'allowed_site_ids' => [$dbkId], // hanya DBK
            ])->save();
            $this->ensureAvatar($op, bg: '#0b1b3f', fg: '#e0f2fe'); // navy / sky

            // =========================
            // (Opsional) Relasi user <-> sites via pivot "site_user"
            // =========================
            if (Schema::hasTable('site_user')) {
                $ids = collect($siteByCode)->pluck('id')->all();

                // Super Admin & GM: semua site
                $super->sites()->syncWithoutDetaching($ids);
                $gm->sites()->syncWithoutDetaching($ids);

                // Operator: hanya DBK
                $op->sites()->syncWithoutDetaching([$dbkId]);
            }

            // =========================
            // Power BI Reports (demo)
            // =========================
            $r1 = PowerBiReport::firstOrCreate(
                ['name' => 'KPI Produksi Bulanan'],
                [
                    'id'                    => (string) Str::uuid(),
                    'embed_url'             => 'https://app.powerbi.com/reportEmbed?reportId=XXXX&groupId=YYYY&ctid=ZZZZ',
                    'show_filter_pane'      => false,
                    'show_nav_pane'         => true,
                    'show_toolbar'          => true,
                    'allow_client_download' => true,
                    'created_by'            => $gm->id,
                ]
            );
            $r1->fill([
                'embed_url'             => 'https://app.powerbi.com/reportEmbed?reportId=XXXX&groupId=YYYY&ctid=ZZZZ',
                'show_filter_pane'      => false,
                'show_nav_pane'         => true,
                'show_toolbar'          => true,
                'allow_client_download' => true,
            ])->save();

            $r2 = PowerBiReport::firstOrCreate(
                ['name' => 'KPI HR Tahunan'],
                [
                    'id'                    => (string) Str::uuid(),
                    'embed_url'             => 'https://app.powerbi.com/reportEmbed?reportId=AAAA&groupId=BBBB&ctid=CCCC',
                    'show_filter_pane'      => true,
                    'show_nav_pane'         => true,
                    'show_toolbar'          => true,
                    'allow_client_download' => false,
                    'created_by'            => $super->id,
                ]
            );
            $r2->fill([
                'embed_url'             => 'https://app.powerbi.com/reportEmbed?reportId=AAAA&groupId=BBBB&ctid=CCCC',
                'show_filter_pane'      => true,
                'show_nav_pane'         => true,
                'show_toolbar'          => true,
                'allow_client_download' => false,
            ])->save();

            // Grant akses per-user
            if (Schema::hasTable('powerbi_report_user')) {
                $r1->users()->syncWithoutDetaching([$op->id, $gm->id, $super->id]);
                $r2->users()->syncWithoutDetaching([$super->id]);
            }

            // Grant akses per-division
            if (Schema::hasTable('powerbi_report_division')) {
                $r1->divisions()->syncWithoutDetaching([$ops->id]);
                $r2->divisions()->syncWithoutDetaching([$hr->id]);
            }

            // Grant akses per-site
            if (Schema::hasTable('powerbi_report_site')) {
                // r1 untuk DBK & HO, r2 untuk HO
                $r1->sites()->syncWithoutDetaching([$dbkId, $hoId]);
                $r2->sites()->syncWithoutDetaching([$hoId]);
            }
        });
    }

    /**
     * Pastikan user punya avatar SVG di storage/public/avatars dan simpan path ke photo_path.
     */
    protected function ensureAvatar(User $user, string $bg = '#0f766e', string $fg = '#ffffff'): void
    {
        $initial = mb_strtoupper(mb_substr($user->name ?? 'U', 0, 1));
        $slug    = Str::slug($user->email ?: ($user->name ?: (string) $user->id), '_');
        $path    = "avatars/{$slug}.svg";

        if (!Storage::disk('public')->exists($path)) {
            $svg = $this->makeAvatarSvg($initial, $bg, $fg);
            Storage::disk('public')->put($path, $svg);
        }

        if ($user->photo_path !== $path) {
            $user->forceFill(['photo_path' => $path])->save();
        }
    }

    /**
     * Generate SVG avatar sederhana dengan inisial.
     */
    protected function makeAvatarSvg(string $initial, string $bg, string $fg): string
    {
        // 256x256, rounded
        return <<<SVG
<?xml version="1.0" encoding="UTF-8"?>
<svg width="256" height="256" viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="{$initial}">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="{$bg}" stop-opacity="1"/>
      <stop offset="100%" stop-color="{$bg}" stop-opacity="0.85"/>
    </linearGradient>
  </defs>
  <rect x="0" y="0" width="256" height="256" rx="48" ry="48" fill="url(#g)"/>
  <circle cx="40" cy="36" r="10" fill="{$fg}" opacity="0.15"/>
  <circle cx="220" cy="210" r="12" fill="{$fg}" opacity="0.12"/>
  <text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle"
        font-family="Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto"
        font-size="140" font-weight="800" fill="{$fg}">{$initial}</text>
</svg>
SVG;
    }
}
