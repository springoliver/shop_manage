<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payslip extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_payslip';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'payslipid';

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
        'employeeid',
        'payslipname',
        'weekid',
        'year',
        'insertdatetime',
        'insertip',
        'editdatetime',
        'editip',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'insertdatetime' => 'datetime',
            'editdatetime' => 'datetime',
        ];
    }

    /**
     * Get the store that owns the payslip.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }

    /**
     * Get the employee that owns the payslip.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(StoreEmployee::class, 'employeeid', 'employeeid');
    }

    /**
     * Get the week for this payslip.
     */
    public function week(): BelongsTo
    {
        return $this->belongsTo(Week::class, 'weekid', 'weekid');
    }
}

