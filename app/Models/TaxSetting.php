<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxSetting extends Model
{
    protected $table = 'stoma_tax_settings';
    
    protected $primaryKey = 'taxid';
    
    public $incrementing = true;
    
    public $timestamps = false;
    
    protected $fillable = [
        'storeid',
        'tax_name',
        'tax_status',
        'tax_amount',
        'insertby',
        'insertdate',
        'insertip',
        'editdate_tax',
        'editip',
    ];
    
    protected $casts = [
        'insertdate' => 'datetime',
        'editdate_tax' => 'datetime',
    ];
    
    public function store()
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }
}

