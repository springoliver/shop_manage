<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaidModule extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_paid_module';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'pmid';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'storeid',
        'moduleid',
        'purchase_date',
        'expire_date',
        'paid_amount',
        'status',
        'insertdatetime',
        'insertip',
        'paypal_profile_id',
        'transactionid',
        'isTrial',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'purchase_date' => 'datetime',
            'expire_date' => 'datetime',
            'paid_amount' => 'decimal:2',
            'insertdatetime' => 'datetime',
            'isTrial' => 'boolean',
        ];
    }

    /**
     * Get the store that owns this paid module.
     */
    public function store()
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }

    /**
     * Get the module for this paid module.
     */
    public function module()
    {
        return $this->belongsTo(Module::class, 'moduleid', 'moduleid');
    }
}
