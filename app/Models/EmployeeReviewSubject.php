<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeReviewSubject extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_employee_review_subjects';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'review_subjectid';

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
        'usergroupid',
        'subject_name',
        'status',
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
            'insertdatetime' => 'datetime',
            'editdatetime' => 'datetime',
        ];
    }

    /**
     * Get the store that owns the review subject.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }

    /**
     * Get the user group for this review subject.
     */
    public function userGroup(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class, 'usergroupid', 'usergroupid');
    }
}

