<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalePrice extends Model
{
    protected $table = 'sale_prices';
    protected $fillable = [
        'product_id',
        'product_batch_id',
        'variant_id',
        'imei_number',
        'warehouse_id',
        'qty',
        'price',
        'cost',
        't_shipping',
        't_disc',
        'actual_cost',
        'profit_margin',
        'Sale_price',
        "reference_no",
        "brand_id",
        "category_id",
        "user_id",
        "type",
        "initial_file",
        "final_file",
        "note",
        "is_adjusted"
    ];
}
