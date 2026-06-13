<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GateEntryDetail extends Model
{
    protected $fillable = [
        'gate_in_no', 'gate_in_date', 'gate_in_time', 'plant_code', 'plant_name',
        'vehicle_no', 'vehicle_type_code', 'vehicle_type_desc', 'lr_number',
        'doc_type_code', 'doc_type_name', 'wb_number', 'sec_id_gt_in',
        'sec_id_gt_in_name', 'remarks','sec_reg_ref_no','vendor_name','createdby_name','loc_name','loc_code','status','dept_id','dept_name','security_name_in','security_name_out','del_person_name','del_person_mob','invoice_no','transporter','lr_number_prev1','transporter_prev1','lr_number_prev2','transporter_prev2','file1','file2'
    ];

    public function materialDetails()
    {
        return $this->hasMany(GateEntryItemDetail::class);
    }
}

