<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosPaymentType extends Model
{
    use HasFactory;

    protected $table = 'stoma_pos_payment_types';
    protected $primaryKey = 'pos_payment_types_id';
    public $timestamps = false;

    protected $fillable = [
        'storeid',
        'pos_payment_type_name',
        'insertdate',
        'insertip',
        'editdate',
        'editip',
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

