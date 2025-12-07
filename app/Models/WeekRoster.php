<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeekRoster extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_week_roster';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'wrid';

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
        'start_time',
        'end_time',
        'day',
        'shift',
        'day_date',
        'work_status',
        'weekid',
        'status',
        'insertdatetime',
        'insertip',
        'editdatetime',
        'break_every_hrs',
        'break_min',
        'paid_break',
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
            'day_date' => 'date',
        ];
    }

    /**
     * Get the store that owns the week roster.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }

    /**
     * Get the employee that owns the week roster.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(StoreEmployee::class, 'employeeid', 'employeeid');
    }

    /**
     * Get the department that owns the week roster.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'departmentid', 'departmentid');
    }

    /**
     * Get the week that owns the week roster.
     */
    public function week(): BelongsTo
    {
        return $this->belongsTo(Week::class, 'weekid', 'weekid');
    }
}
