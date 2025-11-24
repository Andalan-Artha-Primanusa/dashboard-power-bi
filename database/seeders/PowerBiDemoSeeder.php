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

            // =====================================================
            // Companies (opsional, auto-skip kalau table/model ga ada)
            // =====================================================
            $companyByCode = [];
            $hasCompanyModel = false;
            $CompanyClass = null;

            try {
                $hasCompanyModel = class_exists(\App\Models\Company::class);
                if ($hasCompanyModel) {
                    $CompanyClass = \App\Models\Company::class;
                }
            } catch (\Throwable $e) {
                $hasCompanyModel = false;
            }

            if (Schema::hasTable('companies')) {

                $companyColumns = Schema::getColumnListing('companies');
                $filterCompany = function(array $data) use ($companyColumns) {
                    return array_filter(
                        $data,
                        fn($v, $k) => in_array($k, $companyColumns, true),
                        ARRAY_FILTER_USE_BOTH
                    );
                };

                $rawCompanies = [
                    ['code' => 'AAP', 'name' => 'PT Andalan Artha Primanusa',  'is_active' => true],
                    ['code' => 'ABN', 'name' => 'PT Andalan Bhumi Nusantara', 'is_active' => true],
                    ['code' => 'ABC', 'name' => 'PT Andalan Bhumi Cakrawala', 'is_active' => true],
                ];

                foreach ($rawCompanies as $c) {
                    if ($hasCompanyModel) {
                        $company = $CompanyClass::firstOrCreate(
                            $filterCompany(['code' => $c['code']]),
                            $filterCompany([
                                'id'        => (string) Str::uuid(),
                                'name'      => $c['name'],
                                'is_active' => (bool) ($c['is_active'] ?? true),
                            ])
                        );

                        $company->fill($filterCompany([
                            'name'      => $c['name'],
                            'is_active' => (bool) ($c['is_active'] ?? true),
                        ]))->save();

                        $companyByCode[$c['code']] = $company;
                    } else {
                        $existing = DB::table('companies')->where('code', $c['code'])->first();

                        if (!$existing) {
                            DB::table('companies')->insert(
                                $filterCompany([
                                    'id'         => (string) Str::uuid(),
                                    'code'       => $c['code'],
                                    'name'       => $c['name'],
                                    'is_active'  => (bool) ($c['is_active'] ?? true),
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ])
                            );
                            $existing = DB::table('companies')->where('code', $c['code'])->first();
                        } else {
                            DB::table('companies')->where('id', $existing->id)->update(
                                $filterCompany([
                                    'name'       => $c['name'],
                                    'is_active'  => (bool) ($c['is_active'] ?? true),
                                    'updated_at' => now(),
                                ])
                            );
                        }

                        $companyByCode[$c['code']] = $existing;
                    }
                }
            }

            $allCompanyIds = collect($companyByCode)->pluck('id')->values()->all();
            $abnCompanyId  = $companyByCode['ABN']->id ?? null;

            // =====================================================
            // Divisions (sesuai list gambar)
            // =====================================================
            $rawDivisions = [
                ['code' => 'ACI',   'name' => 'Asset, Commercial, and Insurance'],
                ['code' => 'ENG',   'name' => 'Engineering'],
                ['code' => 'FIN',   'name' => 'Finance'],
                ['code' => 'HRGA',  'name' => 'HRGA'],
                ['code' => 'IT',    'name' => 'IT'],
                ['code' => 'OPS',   'name' => 'Operation'],
                ['code' => 'PLANT', 'name' => 'Plant'],
                ['code' => 'SCM',   'name' => 'SCM'],
                ['code' => 'SHE',   'name' => 'SHE'],
            ];

            $divisionByCode = [];
            foreach ($rawDivisions as $d) {
                $div = Division::firstOrCreate(
                    ['code' => $d['code']],
                    [
                        'id'   => (string) Str::uuid(),
                        'name' => $d['name'],
                    ]
                );

                if ($div->name !== $d['name']) {
                    $div->forceFill(['name' => $d['name']])->save();
                }

                $divisionByCode[$d['code']] = $div;
            }

            $ops  = $divisionByCode['OPS'];
            $hrga = $divisionByCode['HRGA'];

            // =====================================================
            // Sites
            // =====================================================
            $rawSites = [
                ['code' => 'HO',  'name' => 'Head Office Jakarta', 'region' => 'Jakarta',           'is_active' => true],
                ['code' => 'DBK', 'name' => 'DBK – Kalteng',       'region' => 'Kalimantan Tengah', 'is_active' => true],
                ['code' => 'POS', 'name' => 'POS – Halmahera',     'region' => 'Maluku Utara',      'is_active' => true],
                ['code' => 'SBS', 'name' => 'SBS – Sumsel',        'region' => 'Sumatera Selatan',  'is_active' => true],
            ];

            $siteByCode = [];
            foreach ($rawSites as $s) {

                $payload = [
                    'id'         => (string) Str::uuid(),
                    'name'       => $s['name'],
                    'region'     => $s['region'] ?? null,
                    'address'    => null,
                    'lat'        => null,
                    'lng'        => null,
                    'is_active'  => (bool) ($s['is_active'] ?? true),
                    'config'     => null,
                    'created_by' => null,
                ];

                if ($abnCompanyId && Schema::hasColumn('sites', 'company_id')) {
                    $payload['company_id'] = $abnCompanyId;
                }

                $site = Site::firstOrCreate(
                    ['code' => $s['code']],
                    $payload
                );

                $sync = [
                    'name'      => $s['name'],
                    'region'    => $s['region'] ?? null,
                    'is_active' => (bool) ($s['is_active'] ?? true),
                ];
                if ($abnCompanyId && Schema::hasColumn('sites', 'company_id')) {
                    $sync['company_id'] = $abnCompanyId;
                }

                $site->fill($sync)->save();
                $siteByCode[$s['code']] = $site;
            }

            $allSiteIds = collect($siteByCode)->pluck('id')->values()->all();
            $dbkId      = $siteByCode['DBK']->id;
            $hoId       = $siteByCode['HO']->id;

            Storage::disk('public')->makeDirectory('avatars');

            // =====================================================
            // Users (+ default_site_id & allowed_site_ids)
            // =====================================================
            $fillCompanyForUser = function(array $base) use ($abnCompanyId, $allCompanyIds) {
                if ($abnCompanyId && Schema::hasColumn('users', 'default_company_id')) {
                    $base['default_company_id'] = $abnCompanyId;
                }
                if (!empty($allCompanyIds) && Schema::hasColumn('users', 'allowed_company_ids')) {
                    $base['allowed_company_ids'] = $allCompanyIds;
                }
                return $base;
            };

            // Super Admin
            $super = User::firstOrCreate(
                ['email' => 'admin@local.test'],
                $fillCompanyForUser([
                    'id'              => (string) Str::uuid(),
                    'name'            => 'Super Admin',
                    'password'        => Hash::make('password123'),
                    'division_id'     => $hrga->id,
                    'role'            => 'super_admin',
                    'default_site_id' => $hoId,
                    'photo_path'      => null,
                ])
            );
            $super->forceFill($fillCompanyForUser([
                'division_id'      => $hrga->id,
                'role'             => 'super_admin',
                'default_site_id'  => $hoId,
                'allowed_site_ids' => $allSiteIds,
            ]))->save();
            $this->ensureAvatar($super, bg: '#7f1d1d', fg: '#fef3c7');

            // GM
            $gm = User::firstOrCreate(
                ['email' => 'gm@andalan.local'],
                $fillCompanyForUser([
                    'id'              => (string) Str::uuid(),
                    'name'            => 'General Manager',
                    'password'        => Hash::make('password'),
                    'division_id'     => $ops->id,
                    'role'            => 'gm',
                    'default_site_id' => $dbkId,
                    'photo_path'      => null,
                ])
            );
            $gm->forceFill($fillCompanyForUser([
                'division_id'      => $ops->id,
                'role'             => 'gm',
                'default_site_id'  => $dbkId,
                'allowed_site_ids' => $allSiteIds,
            ]))->save();
            $this->ensureAvatar($gm, bg: '#0f766e', fg: '#ecfeff');

            // Operator
            $op = User::firstOrCreate(
                ['email' => 'user@andalan.local'],
                $fillCompanyForUser([
                    'id'              => (string) Str::uuid(),
                    'name'            => 'Operator Site',
                    'password'        => Hash::make('password'),
                    'division_id'     => $ops->id,
                    'role'            => 'user',
                    'default_site_id' => $dbkId,
                    'photo_path'      => null,
                ])
            );
            $op->forceFill($fillCompanyForUser([
                'division_id'      => $ops->id,
                'role'             => 'user',
                'default_site_id'  => $dbkId,
                'allowed_site_ids' => [$dbkId],
            ]))->save();
            $this->ensureAvatar($op, bg: '#0b1b3f', fg: '#e0f2fe');

            // =====================================================
            // (Opsional) Relasi user <-> sites via pivot "site_user"
            // =====================================================
            if (Schema::hasTable('site_user')) {
                $ids = collect($siteByCode)->pluck('id')->all();
                $super->sites()->syncWithoutDetaching($ids);
                $gm->sites()->syncWithoutDetaching($ids);
                $op->sites()->syncWithoutDetaching([$dbkId]);
            }

            // =====================================================
            // Power BI Reports (demo)
            // =====================================================
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

            if (Schema::hasTable('powerbi_report_user')) {
                $r1->users()->syncWithoutDetaching([$op->id, $gm->id, $super->id]);
                $r2->users()->syncWithoutDetaching([$super->id]);
            }

            if (Schema::hasTable('powerbi_report_division')) {
                $r1->divisions()->syncWithoutDetaching([$ops->id]);
                $r2->divisions()->syncWithoutDetaching([$hrga->id]);
            }

            if (Schema::hasTable('powerbi_report_site')) {
                $r1->sites()->syncWithoutDetaching([$dbkId, $hoId]);
                $r2->sites()->syncWithoutDetaching([$hoId]);
            }
        });
    }

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

    protected function makeAvatarSvg(string $initial, string $bg, string $fg): string
    {
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
