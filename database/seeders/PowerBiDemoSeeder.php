<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

use App\Models\User;
use App\Models\Division;
use App\Models\Site;
use App\Models\PowerBiReport;

class PowerBiDemoSeeder extends Seeder
{
    public function run(): void
    {
        // =========================
        // Divisions
        // =========================
        $ops = Division::firstOrCreate(
            ['code' => 'OPS'],
            ['id' => (string) Str::uuid(), 'name' => 'Operations']
        );

        $hr  = Division::firstOrCreate(
            ['code' => 'HR'],
            ['id' => (string) Str::uuid(), 'name' => 'Human Resource']
        );

        // =========================
        // Sites
        // =========================
        $sites = [
            ['code' => 'HO',  'name' => 'Head Office Jakarta', 'region' => 'Jakarta',         'is_active' => true],
            ['code' => 'DBK', 'name' => 'DBK – Kalteng',       'region' => 'Kalimantan Tengah','is_active' => true],
            ['code' => 'POS', 'name' => 'POS – Halmahera',     'region' => 'Maluku Utara',     'is_active' => true],
            ['code' => 'SBS', 'name' => 'SBS – Sumsel',        'region' => 'Sumatera Selatan', 'is_active' => true],
        ];

        $siteByCode = [];
        foreach ($sites as $s) {
            $siteByCode[$s['code']] = Site::firstOrCreate(
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
        }

        // =========================
        // Users
        // =========================
        // Super Admin
        $super = User::firstOrCreate(
            ['email' => 'admin@local.test'],
            [
                'id'             => (string) Str::uuid(),
                'name'           => 'Super Admin',
                'password'       => Hash::make('password123'),
                'division_id'    => $hr->id,
                'role'           => 'super_admin',
                'default_site_id'=> $siteByCode['HO']->id,  // default HO
            ]
        );

        // GM
        $gm = User::firstOrCreate(
            ['email' => 'gm@andalan.local'],
            [
                'id'             => (string) Str::uuid(),
                'name'           => 'General Manager',
                'password'       => Hash::make('password'),
                'division_id'    => $ops->id,
                'role'           => 'gm',
                'default_site_id'=> $siteByCode['DBK']->id,
            ]
        );

        // Operator
        $op = User::firstOrCreate(
            ['email' => 'user@andalan.local'],
            [
                'id'             => (string) Str::uuid(),
                'name'           => 'Operator Site',
                'password'       => Hash::make('password'),
                'division_id'    => $ops->id,
                'role'           => 'user',
                'default_site_id'=> $siteByCode['DBK']->id,
            ]
        );

        // =========================
        // (Opsional) Relasi user <-> sites via pivot "site_user"
        // =========================
        if (Schema::hasTable('site_user')) {
            // Super Admin: semua site
            $super->sites()->syncWithoutDetaching(collect($siteByCode)->pluck('id')->all());

            // GM: semua site
            $gm->sites()->syncWithoutDetaching(collect($siteByCode)->pluck('id')->all());

            // Operator: hanya DBK
            $op->sites()->syncWithoutDetaching([$siteByCode['DBK']->id]);
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

        // Grant akses per-user
        $r1->users()->syncWithoutDetaching([$op->id, $gm->id, $super->id]);
        $r2->users()->syncWithoutDetaching([$super->id]);

        // Grant akses per-division
        $r1->divisions()->syncWithoutDetaching([$ops->id]);
        $r2->divisions()->syncWithoutDetaching([$hr->id]);
    }
}
