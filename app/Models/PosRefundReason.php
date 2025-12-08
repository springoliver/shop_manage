<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosRefundReason extends Model
{
    use HasFactory;

    protected $table = 'stoma_pos_refund_reasons';
    protected $primaryKey = 'pos_refund_reason_id';
    public $timestamps = false;

    protected $fillable = [
        'storeid',
        'pos_refund_reason_name',
        'min_security_level_id',
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

    public function userGroup(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class, 'min_security_level_id', 'usergroupid');
    }
}

