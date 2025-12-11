<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierDocType extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_store_supplier_docs_type';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'docs_type_id';

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
        'docs_type_name',
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
     * @var array<string, string>
     */
    protected $casts = [
        'insertdate' => 'datetime',
        'editdate' => 'datetime',
    ];

    /**
     * Get the store that owns the document type.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }
}

