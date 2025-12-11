<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\PurchaseOrderCategory;

class PurchaseOrder extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_purchase_orders';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'purchase_orders_id';

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
        'departmentid',
        'supplierid',
        'categoryid',
        'shipmentid',
        'deliverydocketstatus',
        'deliverynotes',
        'invoicestatus',
        'invoicenumber',
        'total_amount',
        'total_tax',
        'amount_inc_tax',
        'products_bought',
        'delivery_date',
        'po_note',
        'purchase_orders_type',
        'insertdate',
        'status',
        'creditnote',
        'creditnotedesc',
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
        'delivery_date' => 'date',
        'total_amount' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'amount_inc_tax' => 'decimal:2',
    ];

    /**
     * Get the store that owns the purchase order.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }

    /**
     * Get the department that owns the purchase order.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'departmentid', 'departmentid');
    }

    /**
     * Get the supplier for the purchase order.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(StoreSupplier::class, 'supplierid', 'supplierid');
    }

    /**
     * Get the shipment method for the purchase order.
     */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(ProductShipment::class, 'shipmentid', 'shipmentid');
    }

    /**
     * Get the purchased products for this purchase order.
     */
    public function purchasedProducts(): HasMany
    {
        return $this->hasMany(PurchasedProduct::class, 'purchase_orders_id', 'purchase_orders_id');
    }

    /**
     * Get the category for the purchase order.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderCategory::class, 'categoryid', 'categoryid');
    }
}

