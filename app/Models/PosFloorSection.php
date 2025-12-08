<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PosFloorSection extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_pos_floor_sections';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'pos_floor_section_id';

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
        'pos_floor_section_name',
        'pos_floor_section_colour',
        'pos_section_list_number',
        'storeid',
        'insertdate',
        'insertip',
        'insertby',
        'editdate',
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
            'insertdate' => 'datetime',
            'editdate' => 'datetime',
        ];
    }

    /**
     * Get the store that owns the floor section.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }

    /**
     * Get the floor tables for this section.
     */
    public function floorTables(): HasMany
    {
        return $this->hasMany(PosFloorTable::class, 'pos_floor_section_id', 'pos_floor_section_id');
    }
}

