<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosGratuity extends Model
{
    use HasFactory;

    protected $table = 'stoma_pos_graduity';
    protected $primaryKey = 'pos_graduity_id';
    public $timestamps = false;

    protected $fillable = [
        'pos_graduity_percentage',
        'pos_graduity_customers_over',
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

