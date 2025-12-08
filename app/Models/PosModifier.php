<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosModifier extends Model
{
    use HasFactory;

    protected $table = 'stoma_pos_modifiers';
    protected $primaryKey = 'pos_modifiers_id';
    public $timestamps = false;

    protected $fillable = [
        'storeid',
        'pos_modifier_name',
        'catalog_product_groupid',
        'insertip',
        'editip',
        'isertdate',
        'editdate',
    ];

    protected function casts(): array
    {
        return [
            'isertdate' => 'datetime',
            'editdate' => 'datetime',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }

    // Note: catalog_product_group relationship - table may not exist yet
    // public function catalogProductGroup(): BelongsTo
    // {
    //     return $this->belongsTo(CatalogProductGroup::class, 'catalog_product_groupid', 'catalog_product_groupid');
    // }
}

