<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $fillable = [
        'date',
        'user_id',
        'customer_id',
        'warehouse_id',
        'company_name',
        'contact_person',
        'designation',
        'contact_number',
        'email',
        'head_office',
        'factory',
        'requirement',
        'reffer',
        'remark',
        'department_id',
    ];




    public function company()
    {
        return $this->belongsTo(Company::class,'company_name');

    }

    public function product(){

        return $this->belongsTo(Product::class,'requirement');
    }


}
