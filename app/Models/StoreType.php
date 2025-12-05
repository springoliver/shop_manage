<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreType extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_storetype';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'typeid';

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
        'store_type',
        'status',
    ];

    /**
     * Get the route key name for Laravel route model binding.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'typeid';
    }

    /**
     * Get the stores for the store type.
     */
    public function stores(): HasMany
    {
        return $this->hasMany(Store::class, 'typeid', 'typeid');
    }
}

