<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosReceiptPrinter extends Model
{
    use HasFactory;

    protected $table = 'stoma_pos_receiptprinters';
    protected $primaryKey = 'pos_receiptprinters_id';
    public $timestamps = false;

    protected $fillable = [
        'storeid',
        'pos_receiptprinters_name',
        'pos_receiptprinters_ipadress',
        'pos_receiptprinters_port',
        'pos_receiptprinters_type',
        'pos_receiptprinters_profile',
        'pos_receiptprinters_path',
        'pos_receiptprinters_char_per_line',
        'table_number',
        'customer_number',
        'server_name',
        'receipt_number',
        'store_name',
        'date_time',
        'tax_summary',
        'tender_details',
        'customer_address',
        'customer_email',
        'customer_tel',
        'service_charge',
        'sc_message',
        'cut_paper',
        'barcode',
        'insertdate',
        'insertip',
        'insertby',
        'editdate',
        'editip',
        'editby',
    ];

    protected function casts(): array
    {
        return [
            'insertdate' => 'datetime',
            'editdate' => 'datetime',
            'isertdate' => 'datetime',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'storeid', 'storeid');
    }
}

