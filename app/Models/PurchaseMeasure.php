<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseMeasure extends Model
{
    protected $table = 'stoma_purchasemeasures';
    
    protected $primaryKey = 'purchasemeasuresid';
    
    public $incrementing = true;
    
    public $timestamps = false;
    
    protected $fillable = [
        'purchasemeasure',
        'storeid',
        'insertdate',
        'insertip',
        'insertby',
        'editdate_pm',
        'editip',
        'editby',
    ];
    
    protected $casts = [
        'insertdate' => 'datetime',
        'editdate_pm' => 'datetime',
    ];
    
    public function store()
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }
}

