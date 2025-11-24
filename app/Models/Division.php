<?php // app/Models/Division.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Division extends Model
{
    use HasUuids, SoftDeletes;

    protected $guarded = [];

    public function users() {
        return $this->hasMany(User::class);
    }

    public function powerBiReports() {
        return $this->belongsToMany(PowerBiReport::class, 'powerbi_report_division', 'division_id', 'report_id')
                    ->withTimestamps();
    }

    public function company()
{
    return $this->belongsTo(\App\Models\Company::class, 'company_id');
}

}
