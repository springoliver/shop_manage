<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosFloorTable extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_pos_floor_tables';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'pos_floor_table_id';

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
        'pos_floor_section_id',
        'pos_floor_table_number',
        'pos_floor_table_seat',
        'pos_floor_table_colour',
        'pos_floor_table_width',
        'pos_floor_table_height',
        'pos_floor_table_top',
        'pos_floor_table_left',
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
     * Get the store that owns the floor table.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }

    /**
     * Get the floor section for this table.
     * Note: pos_floor_section_id is stored as varchar, so we need to cast it for the relationship.
     */
    public function floorSection(): BelongsTo
    {
        return $this->belongsTo(PosFloorSection::class, 'pos_floor_section_id', 'pos_floor_section_id');
    }
}

