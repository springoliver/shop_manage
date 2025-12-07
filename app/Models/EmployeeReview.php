<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeReview extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_employee_reviews_new';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'emp_reviewid';

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
        'employeeid',
        'review_subjectid',
        'comments',
        'next_review_date',
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
            'next_review_date' => 'date',
            'insertdatetime' => 'datetime',
            'editdatetime' => 'datetime',
        ];
    }

    /**
     * Get the store that owns the review.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }

    /**
     * Get the employee that owns the review.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(StoreEmployee::class, 'employeeid', 'employeeid');
    }

    /**
     * Get the review subject.
     */
    public function reviewSubject(): BelongsTo
    {
        return $this->belongsTo(EmployeeReviewSubject::class, 'review_subjectid', 'review_subjectid');
    }
}

