<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HolidayRequest extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_holiday_request';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'requestid';

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
        'from_date',
        'to_date',
        'subject',
        'description',
        'status',
        'reason',
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
            'from_date' => 'datetime',
            'to_date' => 'datetime',
            'insertdatetime' => 'datetime',
            'editdatetime' => 'datetime',
        ];
    }

    /**
     * Get the store that owns the holiday request.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }

    /**
     * Get the employee that owns the holiday request.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(StoreEmployee::class, 'employeeid', 'employeeid');
    }
}

