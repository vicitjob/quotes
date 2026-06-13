<div class="row">
    @php
        $gateentry ??= null;
		$today_dt = $dtobj->toDateString();
		$today_time = $dtobj->toTimeString();
		
    @endphp
   
    <div class="col-md-2 dispflex">
        <label class="col-md-5 lblcommon">Gate In Date</label>
		<div class="col-md-7">
			<input style="width:80%;" type="date" name="gate_in_date" id="gate_in_date" class="form-control" value="{{ old('gate_in_date', $gateentry->gate_in_date ?? $today_dt) }}" required>
			
			<input type="hidden" name="gate_in_no" value="{{ old('gate_in_no', $gateentry->gate_in_no ?? '') }}" />
			<input type="hidden" name="isstoreuser" id="isstoreuser" value="<?php echo ($is_storeuser) ? 1 : 0; ?>" />
			<input type="hidden" name="gtid" id="gtid" value="<?php echo !(empty($gateentry)) ? $gateentry->id : ''; ?>" />
			<input type="hidden" name="csrf-token" content="{{ csrf_token() }}">
		</div>
    </div>
    <div class="col-md-2 dispflex">
        <label class="col-md-5 lblcommon">Gate In Time </label>
		<div class="col-md-7">
			<input style="width:80%;" type="time" name="gate_in_time" id="gate_in_time" class="form-control" value="{{ old('gate_in_time', $gateentry->gate_in_time ?? $today_time) }}" required>
		</div>
    </div>
	
	<div class="col-md-2 dispflex">
        <label class="col-md-4 lblcommon">Plant</label>
		<div class="col-md-8">
			<select style="width:100%;" class="" name="plant_code" id="plant_code" onchange="setplantname();" required>
			<option value="">None</option>
			<?php			
			$def_plant = 'PLPN';
			
			if($plantsres->count() > 0)
			{
				//foreach($plant_arr as $plantcode => $plantname)
				foreach($plantsres as $plantobj)
				{
					$plantcode = $plantobj->plant_code;
					$plantname = $plantobj->plant_name;
					
					$sel = '';
					if(!(empty($gateentry)))
					{
						if($gateentry->plant_code == $plantcode)
						{
							$sel = 'selected';
						}
					}
					else
					{
						if($def_plant == $plantcode)
						{
							$sel = 'selected';
						}
					}
					echo '<option value="'.$plantcode.'" '.$sel.'>'.$plantname.'</option>';
				}
			}
			?>
				
			</select>
			<input type="hidden" name="plant_name" id="plant_name" class="form-control" value="{{ old('plant_name', $gateentry->plant_name ?? '') }}" >
			
			
		</div>
    </div>
	
	<div class="col-md-2 dispflex">
        <label class="col-md-4 lblcommon">Location</label>
		<div class="col-md-8">
			<select style="width:100%;" class="" name="loc_code" id="loc_code" onchange="setlocname();">
				
			</select>
			
			
			<input type="hidden" name="sel_loc" id="sel_loc"  value="<?php echo $sel_loc;?>" />
			<input type="hidden" name="loc_name" id="loc_name"  value="{{ old('loc_name', $gateentry->loc_name ?? '') }}" />
			
		</div>
    </div>
	
	<div class="col-md-2 dispflex">
        <label class="col-md-4 lblcommon">Gate No.</label>
		<div class="col-md-8">
						
			<select style="width:100%;" class="" name="sec_id_gt_in" id="sec_id_gt_in" onchange="setgatename();" required>
			<option value="">None</option>
			<?php
			$gateentry_arr = array('1' => 'Main Gate', '2' => 'Parking Gate');
			
			$def_gateentry = '1';
			
			foreach($gateentry_arr as $gateentrycode => $gateentryname)
			{
				$sel = '';
				if(!(empty($gateentry)))
				{
					if($gateentry->sec_id_gt_in == $gateentrycode)
					{
						$sel = 'selected';
					}
				}
				else
				{
					if($def_gateentry == $gateentrycode)
					{
						$sel = 'selected';
					}
				}
				echo '<option value="'.$gateentrycode.'" '.$sel.'>'.$gateentryname.'</option>';
			}
			?>
			
			</select>
			<input type="hidden" name="sec_id_gt_in_name" id="sec_id_gt_in_name" value="{{ old('sec_id_gt_in_name', $gateentry->sec_id_gt_in_name ?? '') }}" />
			
		</div>
    </div>
	
</div>

<div class="row">
    
	
	<div class="col-md-3 dispflex">
        <label class="col-md-4 lblcommon">Vehicle Type</label>
		<div class="col-md-6">
						
			<select style="width:100%;" class="" name="vehicle_type_code" id="vehicle_type_code" onchange="setvehiclename();" required>
			<option value="">None</option>
			<?php
			$veh_arr = array('05' => 'Truck', '04' => 'Tempo');
			
			$def_veh = '05';
			
			foreach($veh_arr as $vehcode => $vehname)
			{
				$sel = '';
				if(!(empty($gateentry)))
				{
					if($gateentry->vehicle_type_code == $vehcode)
					{
						$sel = 'selected';
					}
				}
				else
				{
					if($def_veh == $vehcode)
					{
						$sel = 'selected';
					}
				}
				echo '<option value="'.$vehcode.'" '.$sel.'>'.$vehname.'</option>';
			}
			?>
			
			</select>
			<input type="hidden" name="vehicle_type_desc" id="vehicle_type_desc"  value="{{ old('vehicle_type_desc', $gateentry->vehicle_type_desc ?? '') }}" />
		</div>
    </div>
    
	 <div class="col-md-3 dispflex">
        <label class="col-md-4 lblcommon">Vehicle No.</label>
		<div class="col-md-6">
			<input type="text" name="vehicle_no" id="vehicle_no" placeholder="MH-47-AB-1234" class="form-control" value="{{ old('vehicle_no', $gateentry->vehicle_no ?? '') }}" required>
			
			<input type="hidden" name="wb_number" value="{{ old('wb_number', $gateentry->wb_number ?? '') }}" >
			<input type="hidden" name="lr_number" value="{{ old('lr_number', $gateentry->lr_number ?? '') }}" >
		</div>
    </div>
	
	<div class="col-md-3 dispflex">
        <label class="col-md-4 lblcommon">Reg. Ref No.</label>
		<div class="col-md-6">
			<input type="text" name="sec_reg_ref_no" class="form-control" value="{{ old('sec_reg_ref_no', $gateentry->sec_reg_ref_no ?? '') }}" >
		</div>
    </div>
	
	 <div class="col-md-3 dispflex">
        <label class="col-md-3 lblcommon">Remark&nbsp;</label>
		<div class="col-md-9">
			<textarea name="remarks" class="form-control" rows="2">{{ old('remarks', $gateentry->remarks ?? '') }}</textarea>
			
			<input type="hidden" name="createdby_name" value="{{ old('createdby_name', $op_name) }}" />
			
		</div>
    </div>
   
</div>

<div class="row lastrowsep">
	<div class="col-md-3 dispflex">
        <label class="col-md-4 lblcommon">Department</label>
		<div class="col-md-8">
			<select style="width:60%;" class="" name="dept_id" id="dept_id" onchange="setdeptname();">
			<option value="">None</option>
			
				
			</select>
			<input type="hidden" name="dept_name" id="dept_name" class="form-control" value="{{ old('dept_name', $gateentry->dept_name ?? '') }}" >
			
			<input type="hidden" name="sel_dept" id="sel_dept"  value="<?php echo $sel_dept;?>" />
		</div>
    </div>
	<div class="col-md-3 dispflex">
        <label class="col-md-4 lblcommon">Security Name In</label>
		<div class="col-md-6">
			<input type="text" name="security_name_in" class="form-control" value="{{ old('security_name_in', $gateentry->security_name_in ?? '') }}" >
		</div>
    </div>
	<div class="col-md-3 dispflex">
        <label class="col-md-6 lblcommon">Security Name Unloading</label>
		<div class="col-md-6">
			<input type="text" name="security_name_out" id="security_name_out" class="form-control" value="{{ old('security_name_out', $gateentry->security_name_out ?? '') }}" >
		</div>
    </div>
</div>

<div class="row headerrow">
	<div class="col-md-8">
		<h5>Item Details</h5>
	</div>
		
</div>
<?php 
$po_no_db = $rgp_int_no_db = '';
if(!empty($gateentry))
{
	if($materialDetails->count() > 0)
	{
		$po_no_db = $materialDetails[0]->po_no;
		$rgp_int_no_db = $materialDetails[0]->rgp_int_no;
	}
}
?>
<div id="materials">
	<div class="row">
		
		<div class="col-md-3 dispflex">
			<label class="col-md-4 lblcommon">Doc Type</label>
			<div class="col-md-6">
							
				<select style="width:100%;" class="" name="doc_type_code" id="doc_type_code" onchange="setdoctypename();" required>
				<option value="">None</option>
				<?php
				$doc_arr = array('PO' => 'Purchase Order', 'DC' => 'Delivery Challan', 'OTH' => 'Other');
				
				$def_doc = 'PO';
				
				foreach($doc_arr as $doccode => $docname)
				{
					$sel = '';
					if(!(empty($gateentry)))
					{
						if($gateentry->doc_type_code == $doccode)
						{
							$sel = 'selected';
						}
					}
					else
					{
						if($def_doc == $doccode)
						{
							$sel = 'selected';
						}
					}
					echo '<option value="'.$doccode.'" '.$sel.'>'.$docname.'</option>';
				}
				?>
				
				</select>
				<input type="hidden" name="doc_type_name" id="doc_type_name" value="{{ old('doc_type_name', $gateentry->doc_type_name ?? '') }}" >
				
			</div>
		</div>
	
		<div class="col-md-4 dispflex">
			<label class="col-md-3 lblcommon"><span id="doctyptext">PO No.</span></label>
			<div class="col-md-9" style="display:inline-block;">
				<input style="width:40%;display:inline;" class="form-control" type="text" name="po_no" id="po_no" value="{{ old('po_no', $po_no_db ?? '') }}" />
				<!--button class="btn btn-primary" style="padding:1px;font-size:12px;" id="getpodetails" onclick="getpodetailsfunc();">Get PO Details</button-->
				<a href="javascript:void(0);" class="btn btn-primary" style="padding:1px;font-size:12px;" id="getpodetails" onclick="getpodetailsfunc();">Get PO Details</a>
				<div class="matloader" id="matloader" style="display:none;">Please Wait.....</div>
				<input type="hidden" name="rgp_int_no" id="rgp_int_no" value="{{ old('rgp_int_no', $rgp_int_no_db ?? '') }}" />
			</div>
		</div>
		
		<div class="col-md-4 dispflex">
			<label class="col-md-4 lblcommon">Vendor Name</label>
			<div class="col-md-8">
				<input type="text" name="vendor_name" id="vendor_name" class="form-control" value="{{ old('vendor_name', $gateentry->vendor_name ?? '') }}" >
			</div>
		</div>
	
		
	</div>
   
    @if(!empty($gateentry))
       
            @include('gateentries._material_form', ['index' => 0, 'materialDetails' => $materialDetails])
       
    @else
        @include('gateentries._material_form', ['index' => 0, 'materialDetails' => null])
    @endif
</div>


