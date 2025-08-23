<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceProduct extends Model
{
    protected $fillable = [
        'qty',
        'sale_price',
        'product_id',
        'user_id',
        'invoice_id'
    ];

    public function product(){
        return $this->belongsTo(Product::class);
    }
}
