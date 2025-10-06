<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Division;
use App\Models\PowerBiReport;

class PowerBiDemoSeeder extends Seeder
{
    public function run(): void
    {
        // ===== Divisions =====
        $division = Division::firstOrCreate(
            ['name' => 'Operations'],
            ['id' => (string) Str::uuid(), 'code' => 'OPS']
        );

        // (opsional) division lain contoh
        $hrDivision = Division::firstOrCreate(
            ['name' => 'Human Resource'],
            ['id' => (string) Str::uuid(), 'code' => 'HR']
        );

        // ===== Users =====
        // Super Admin
        $super = User::firstOrCreate(
            ['email' => 'admin@andalan.local'],
            [
                'id'          => (string) Str::uuid(),
                'name'        => 'Super Admin',
                'password'    => Hash::make('password'),
                'division_id' => $hrDivision->id,
                'role'        => 'super_admin', // <<< penting (tanpa hardcode di Gate)
            ]
        );

        // GM
        $gm = User::firstOrCreate(
            ['email' => 'gm@andalan.local'],
            [
                'id'          => (string) Str::uuid(),
                'name'        => 'General Manager',
                'password'    => Hash::make('password'),
                'division_id' => $division->id,
                'role'        => 'gm',
            ]
        );

        // Operator/User biasa
        $user = User::firstOrCreate(
            ['email' => 'user@andalan.local'],
            [
                'id'          => (string) Str::uuid(),
                'name'        => 'Operator Site',
                'password'    => Hash::make('password'),
                'division_id' => $division->id,
                'role'        => 'user',
            ]
        );

        // ===== Power BI Report =====
        $report = PowerBiReport::firstOrCreate(
            ['name' => 'KPI Produksi Bulanan'],
            [
                'id'                      => (string) Str::uuid(),
                'embed_url'               => 'https://app.powerbi.com/reportEmbed?reportId=XXXX&groupId=YYYY&ctid=ZZZZ',
                'show_filter_pane'        => false,
                'show_nav_pane'           => true,
                'show_toolbar'            => true,
                'allow_client_download'   => true,
                'created_by'              => $gm->id,
            ]
        );

        // ===== Grant Access =====
        // Per user
        $report->users()->syncWithoutDetaching([$user->id, $gm->id, $super->id]);

        // Per division
        $report->divisions()->syncWithoutDetaching([$division->id]);

        // Contoh tambahan: seed 1 report lain hanya untuk HR & Super Admin
        $reportHr = PowerBiReport::firstOrCreate(
            ['name' => 'KPI HR Tahunan'],
            [
                'id'                      => (string) Str::uuid(),
                'embed_url'               => 'https://app.powerbi.com/reportEmbed?reportId=AAAA&groupId=BBBB&ctid=CCCC',
                'show_filter_pane'        => true,
                'show_nav_pane'           => true,
                'show_toolbar'            => true,
                'allow_client_download'   => false,
                'created_by'              => $super->id,
            ]
        );
        $reportHr->users()->syncWithoutDetaching([$super->id]);
        $reportHr->divisions()->syncWithoutDetaching([$hrDivision->id]);
    }
}
