<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $fillable =[
        "name", "company_name",
        "email", "phone_number", "type", "commission_type", "commission_value", "address", "is_active"
    ];

    public function sale()
    {
    	return $this->hasMany('App\Models\Sale');
    }
    public function purchase()
    {
    	return $this->hasMany('App\Models\Purchase');
    }
}
