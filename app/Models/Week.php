<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Week extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_week';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'weekid';

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
        'weeknumber',
        'yearid',
    ];

    /**
     * Get the year that owns the week.
     */
    public function year(): BelongsTo
    {
        return $this->belongsTo(Year::class, 'yearid', 'yearid');
    }

    /**
     * Get the week rosters for this week.
     */
    public function weekRosters(): HasMany
    {
        return $this->hasMany(WeekRoster::class, 'weekid', 'weekid');
    }
}
