<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_store';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'storeid';

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
        'storeownerid',
        'store_name',
        'typeid',
        'logofile',
        'full_google_address',
        'latitude',
        'longitude',
        'website_url',
        'store_email',
        'store_email_pass',
        'manager_email',
        'monday_hour_from',
        'monday_hour_to',
        'monday_dayoff',
        'tuesday_hour_from',
        'tuesday_hour_to',
        'tuesday_dayoff',
        'wednesday_hour_from',
        'wednesday_hour_to',
        'wednesday_dayoff',
        'thursday_hour_from',
        'thursday_hour_to',
        'thursday_dayoff',
        'friday_hour_from',
        'friday_hour_to',
        'friday_dayoff',
        'saturday_hour_from',
        'saturday_hour_to',
        'saturday_dayoff',
        'sunday_hour_from',
        'sunday_hour_to',
        'sunday_dayoff',
        'insertdate',
        'insertip',
        'insertby',
        'editdate',
        'editip',
        'editby',
        'status',
        'enable_break_events',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
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
        return 'storeid';
    }

    /**
     * Get the store owner that owns the store.
     */
    public function storeOwner(): BelongsTo
    {
        return $this->belongsTo(StoreOwner::class, 'storeownerid', 'ownerid');
    }

    /**
     * Get the store type/category that the store belongs to.
     */
    public function storeType(): BelongsTo
    {
        return $this->belongsTo(StoreType::class, 'typeid', 'typeid');
    }

    /**
     * Get the groups for the store.
     */
    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    /**
     * Get the store type name.
     */
    public function getStoreTypeName(): string
    {
        return $this->storeType?->store_type ?? 'N/A';
    }
}
