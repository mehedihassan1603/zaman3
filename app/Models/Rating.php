<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable =[
        "product_id", "customer_id", "sale_id", "value",
    ];

    public function category()
    {
    	return $this->belongsTo('App\Models\Category');
    }

    public function brand()
    {
    	return $this->belongsTo('App\Models\Brand');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit');
    }

    public function variant()
    {
        return $this->belongsToMany('App\Models\Variant', 'product_variants')->withPivot('id', 'item_code', 'additional_cost', 'additional_price');
    }

}
