<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductShipment extends Model
{
    protected $table = 'stoma_productshipment';
    
    protected $primaryKey = 'shipmentid';
    
    public $incrementing = true;
    
    public $timestamps = false;
    
    protected $fillable = [
        'shipment',
        'storeid',
        'insertdate',
        'insertip',
        'insertby',
        'editdate',
        'editip',
        'editby',
    ];
    
    protected $casts = [
        'insertdate' => 'datetime',
        'editdate' => 'datetime',
    ];
    
    public function store()
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }
}

