<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailFormat extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_mailformat';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'emailid';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vartitle',
        'varsubject',
        'varmailformat',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The name of the "created at" column.
     *
     * @var string|null
     */
    const CREATED_AT = 'timestamp';

    /**
     * The name of the "updated at" column.
     *
     * @var string|null
     */
    const UPDATED_AT = 'timestamp';
}

