<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogProductGroup extends Model
{
    protected $table = 'stoma_catalog_product_group';
    
    protected $primaryKey = 'catalog_product_groupid';
    
    public $incrementing = true;
    
    public $timestamps = false;
    
    protected $fillable = [
        'catalog_product_group_name',
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

