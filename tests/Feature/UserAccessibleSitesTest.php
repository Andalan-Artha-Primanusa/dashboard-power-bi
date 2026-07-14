<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAccessibleSitesTest extends TestCase
{
    use RefreshDatabase;

    public function test_accessible_sites_merges_default_pivot_and_allowed_sites(): void
    {
        $company = Company::create([
            'code' => 'CMP',
            'name' => 'Company',
            'is_active' => true,
        ]);

        $defaultSite = Site::create([
            'company_id' => $company->id,
            'code' => 'DEF',
            'name' => 'Default Site',
            'is_active' => true,
        ]);

        $allowedSite = Site::create([
            'company_id' => $company->id,
            'code' => 'OTH',
            'name' => 'Other Site',
            'is_active' => true,
        ]);

        $user = User::factory()->create([
            'default_site_id' => $defaultSite->id,
            'allowed_site_ids' => [$allowedSite->id],
        ]);

        $user->sites()->sync([$defaultSite->id]);

        $this->assertSame(
            ['DEF', 'OTH'],
            $user->accessibleSites()->pluck('code')->all()
        );
    }
}
