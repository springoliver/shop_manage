<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_module';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'moduleid';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'insertdate';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'editdate';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'module',
        'module_description',
        'module_detailed_info',
        'price_1months',
        'price_3months',
        'price_6months',
        'price_12months',
        'free_days',
        'status',
        'insertip',
        'insertby',
        'editip',
        'editby',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price_1months' => 'decimal:2',
            'price_3months' => 'decimal:2',
            'price_6months' => 'decimal:2',
            'price_12months' => 'decimal:2',
            'free_days' => 'integer',
            'insertdate' => 'datetime',
            'editdate' => 'datetime',
        ];
    }

    /**
     * Get the route key name for Laravel route model binding.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'moduleid';
    }
}

