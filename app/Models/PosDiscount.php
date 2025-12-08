<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosDiscount extends Model
{
    use HasFactory;

    protected $table = 'stoma_pos_discounts';
    protected $primaryKey = 'pos_discount_id';
    public $timestamps = false;

    protected $fillable = [
        'pos_discount_percentage',
        'pos_discount_name',
        'storeid',
        'insertdate',
        'editdate',
        'editip',
        'insertip',
        'editby',
    ];

    protected function casts(): array
    {
        return [
            'insertdate' => 'datetime',
            'editdate' => 'datetime',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }
}

