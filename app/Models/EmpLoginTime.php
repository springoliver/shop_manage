<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpLoginTime extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_emp_login_time';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'eltid';

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
        'clockin',
        'clockout',
        'weekid',
        'day',
        'inRoster',
        'status',
        'insertdate',
        'insertby',
        'editby',
        'insertip',
        'editip',
        'editdate',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'clockin' => 'datetime',
            'clockout' => 'datetime',
            'insertdate' => 'datetime',
            'editdate' => 'datetime',
        ];
    }

    /**
     * Get the store that owns the login time record.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }

    /**
     * Get the employee that owns the login time record.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(StoreEmployee::class, 'employeeid', 'employeeid');
    }

    /**
     * Get the week that this login time record belongs to.
     */
    public function week(): BelongsTo
    {
        return $this->belongsTo(Week::class, 'weekid', 'weekid');
    }
}

