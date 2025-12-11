<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardSettings extends Model
{
    use HasFactory;

    protected $table = 'stoma_dashboard_settings';
    protected $primaryKey = 'dashboardsettingsid';
    public $timestamps = false;

    protected $fillable = [
        'storeid',
        'sale_per_labour_hour',
    ];
}

