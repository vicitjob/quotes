<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductMaster extends Model
{
    //
    protected $table = 'product_master';
    protected $fillable = ['GROUP_CODE', 'GROUP_DESC','PROD_CODE','PROD_DESC','UOM','PACK_SIZE','PROD_RATE','KONDM'];
}
