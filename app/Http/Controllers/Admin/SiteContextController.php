<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SiteContextController extends Controller
{
    public function select(Request $request)
    {
        $sites = Site::query()
            ->when(!$request->boolean('include_inactive'), fn($q)=>$q->where('is_active',true))
            ->orderBy('name')
            ->get(['id','code','name','region','is_active']);

        $currentSiteId = session('site_id');

        return view('admin.sites.select', compact('sites','currentSiteId'));
    }

    public function switch(Request $request)
    {
        $data = $request->validate([
            'site_id' => ['required','uuid','exists:sites,id'],
        ]);

        $site = Site::where('id',$data['site_id'])->where('is_active',true)->firstOrFail();

        session(['site_id'=>$site->id]);

        return back()->with('success',"Site switched to {$site->code} — {$site->name}.");
    }

    public function setDefault(Request $request)
    {
        $data = $request->validate([
            'site_id' => ['required','uuid','exists:sites,id'],
        ]);

        $site = Site::where('id',$data['site_id'])->where('is_active',true)->firstOrFail();

        $user = $request->user();
        if (Schema::hasColumn('users','default_site_id')) {
            $user->forceFill(['default_site_id'=>$site->id])->save();
        }
        session(['site_id'=>$site->id]);

        return back()->with('success',"Default site set to {$site->code} — {$site->name}.");
    }
}
