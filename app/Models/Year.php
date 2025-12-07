<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Year extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_year';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'yearid';

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
        'year',
    ];

    /**
     * Get the weeks for this year.
     */
    public function weeks(): HasMany
    {
        return $this->hasMany(Week::class, 'yearid', 'yearid');
    }
}
