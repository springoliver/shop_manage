<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class StoreProduct extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stoma_store_products';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'productid';

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
        'catalog_product_groupid',
        'supplierid',
        'product_name',
        'product_status',
        'taxid',
        'product_price',
        'product_notes',
        'shipmentid',
        'purchasepaymentmethodid',
        'purchasemeasuresid',
        'insertby',
        'insertdate',
        'insertip',
        'editdate',
        'editip',
        'username',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'insertdate' => 'datetime',
        'editdate' => 'date',
    ];

    /**
     * Get the store that owns the product.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }

    /**
     * Get the department that owns the product.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'departmentid', 'departmentid');
    }

    /**
     * Get the catalog product group that owns the product.
     */
    public function catalogProductGroup(): BelongsTo
    {
        return $this->belongsTo(CatalogProductGroup::class, 'catalog_product_groupid', 'catalog_product_groupid');
    }

    /**
     * Get the supplier for the product.
     * Note: supplierid is stored as varchar in products table but int in suppliers table
     * Using a custom query method since Eloquent doesn't support CAST in relationships
     */
    public function getSupplierAttribute()
    {
        if (!$this->supplierid) {
            return null;
        }
        return StoreSupplier::where('supplierid', (int)$this->supplierid)->first();
    }

    /**
     * Get the tax setting for the product.
     */
    public function taxSetting(): BelongsTo
    {
        return $this->belongsTo(TaxSetting::class, 'taxid', 'taxid');
    }

    /**
     * Get the shipment method for the product.
     */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(ProductShipment::class, 'shipmentid', 'shipmentid');
    }

    /**
     * Get the payment method for the product.
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PurchasePaymentMethod::class, 'purchasepaymentmethodid', 'purchasepaymentmethodid');
    }

    /**
     * Get the purchase measure for the product.
     */
    public function purchaseMeasure(): BelongsTo
    {
        return $this->belongsTo(PurchaseMeasure::class, 'purchasemeasuresid', 'purchasemeasuresid');
    }

    /**
     * Get the route key name for Laravel route model binding.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'productid';
    }
}

