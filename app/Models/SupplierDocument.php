<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierDocument extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_store_supplier_docs';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'docid';

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
        'supplierid',
        'doctypeid',
        'docname',
        'docpath',
        'doc_date',
        'insertdatetime',
        'insertip',
        'editdatetime',
        'editip',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'doc_date' => 'date',
        'insertdatetime' => 'datetime',
        'editdatetime' => 'datetime',
    ];

    /**
     * Get the supplier that owns the document.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(StoreSupplier::class, 'supplierid', 'supplierid');
    }

    /**
     * Get the document type.
     */
    public function docType(): BelongsTo
    {
        return $this->belongsTo(SupplierDocType::class, 'doctypeid', 'docs_type_id');
    }

    /**
     * Get the store that owns the document.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }
}

