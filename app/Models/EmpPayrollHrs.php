<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpPayrollHrs extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_emp_payroll_hrs';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'payroll_id';

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
        'weekno',
        'week_start',
        'week_end',
        'year',
        'hours_worked',
        'numberofdaysworked',
        'break_deducted',
        'sunday_hrs',
        'owertime1_hrs',
        'owertime2_hrs',
        'holiday_hrs',
        'holiday_days',
        'sickpay_hrs',
        'extras1_hrs',
        'extras2_hrs',
        'total_hours',
        'notes',
        'insertdate',
        'insertip',
        'editdate',
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
            'week_start' => 'date',
            'week_end' => 'date',
            'break_deducted' => 'datetime',
            'insertdate' => 'datetime',
            'editdate' => 'datetime',
        ];
    }

    /**
     * Get the store that owns the payroll hours record.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }

    /**
     * Get the employee that owns the payroll hours record.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(StoreEmployee::class, 'employeeid', 'employeeid');
    }
}

