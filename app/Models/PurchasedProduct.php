<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\StoreProduct;

class PurchasedProduct extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_purchasedproducts';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'purchasedproductsid';

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
        'purchase_orders_id',
        'productid',
        'storeid',
        'departmentid',
        'supplierid',
        'shipmentid',
        'deliverydocketstatus',
        'invoicestatus',
        'invoicenumber',
        'quantity',
        'product_price',
        'taxid',
        'totalamount',
        'purchasemeasuresid',
        'purchase_orders_type',
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
     * Get the purchase order that owns this purchased product.
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_orders_id', 'purchase_orders_id');
    }

    /**
     * Get the store that owns the purchased product.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }

    /**
     * Get the product for the purchased product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(StoreProduct::class, 'productid', 'productid');
    }

    /**
     * Get the department that owns the purchased product.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'departmentid', 'departmentid');
    }

    /**
     * Get the supplier for the purchased product.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(StoreSupplier::class, 'supplierid', 'supplierid');
    }

    /**
     * Get the shipment method for the purchased product.
     */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(ProductShipment::class, 'shipmentid', 'shipmentid');
    }

    /**
     * Get the tax setting for the purchased product.
     */
    public function taxSetting(): BelongsTo
    {
        return $this->belongsTo(TaxSetting::class, 'taxid', 'taxid');
    }

    /**
     * Get the purchase measure for the purchased product.
     */
    public function purchaseMeasure(): BelongsTo
    {
        return $this->belongsTo(PurchaseMeasure::class, 'purchasemeasuresid', 'purchasemeasuresid');
    }
}

