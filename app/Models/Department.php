<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Department extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_store_department';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'departmentid';

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
        'department',
        'storetypeid',
        'storeid',
        'roster_max_time',
        'day_max_time',
        'target_hours',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday',
        'status',
    ];

    /**
     * Get the route key name for Laravel route model binding.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'departmentid';
    }

    /**
     * Get the store type that owns the department.
     */
    public function storeType(): BelongsTo
    {
        return $this->belongsTo(StoreType::class, 'storetypeid', 'typeid');
    }

    /**
     * Get the store that owns the department.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }
}
