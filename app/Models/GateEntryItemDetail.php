<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GateEntryItemDetail extends Model
{
    protected $fillable = [
        'gate_entry_id', 'po_no', 'rgp_int_no', 'sr_no', 'material_code',
        'material_desc', 'gateentry_qty', 'mat_unit', 'total_qty', 'unit2', 'net_weight','po_chln_qty','remark','storeqty','storeuserid','storedt','storeremark'
    ];

    public function gateEntry()
    {
        return $this->belongsTo(GateEntryDetail::class);
    }
}
