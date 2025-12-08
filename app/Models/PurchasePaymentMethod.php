<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasePaymentMethod extends Model
{
    protected $table = 'stoma_purchasepaymentmethod';
    
    protected $primaryKey = 'purchasepaymentmethodid';
    
    public $incrementing = true;
    
    public $timestamps = false;
    
    protected $fillable = [
        'paymentmethod',
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

