<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpPayroll extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_emp_payroll';

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
        'departmentid',
        'weekid',
        'weekday',
        'shift',
        'total_hours',
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
            'total_hours' => 'decimal:2',
            'insertdate' => 'datetime',
            'editdate' => 'datetime',
        ];
    }

    /**
     * Get the store that owns the payroll record.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }

    /**
     * Get the employee that owns the payroll record.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employeeid', 'employeeid');
    }

    /**
     * Get the week for this payroll record.
     */
    public function week(): BelongsTo
    {
        return $this->belongsTo(Week::class, 'weekid', 'weekid');
    }
}

