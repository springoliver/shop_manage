<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BreakEvent extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_break_events';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'breakid';

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
        'eltid', // Reference to emp_login_time record
        'storeid',
        'employeeid',
        'break_start',
        'break_end',
        'break_duration', // in minutes
        'status', // 'active' or 'completed'
        'insertdate',
        'insertip',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'break_start' => 'datetime',
            'break_end' => 'datetime',
            'insertdate' => 'datetime',
        ];
    }

    /**
     * Get the login time record that owns this break event.
     */
    public function loginTime(): BelongsTo
    {
        return $this->belongsTo(EmpLoginTime::class, 'eltid', 'eltid');
    }

    /**
     * Get the store that owns the break event.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }

    /**
     * Get the employee that owns the break event.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employeeid', 'employeeid');
    }
}

