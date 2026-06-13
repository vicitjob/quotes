<div class="row">
    @php
        $gateentry ??= null;
		$today_dt = $dtobj->toDateString();
		$today_time = $dtobj->toTimeString();
		
    @endphp
   
    <div class="col-md-2 dispflex">
        <label class="col-md-5 lblcommon"><i class="fa-regular fa-calendar-check iconthemecolor"></i> Gt. In Date<span class="redasterisk">*</span></label>
		<div class="col-md-7">
			<input style="width:80%;" type="date" name="gate_in_date" id="gate_in_date" class="form-control" value="{{ old('gate_in_date', $gateentry->gate_in_date ?? $today_dt) }}" title="{{ old('gate_in_date', $gateentry->gate_in_date ?? $today_dt) }}" required>
			
			<input type="hidden" name="gate_in_no" value="{{ old('gate_in_no', $gateentry->gate_in_no ?? '') }}" />
			<input type="hidden" name="isstoreuser" id="isstoreuser" value="<?php echo ($is_storeuser) ? 1 : 0; ?>" />
			<input type="hidden" name="gtid" id="gtid" value="<?php echo !(empty($gateentry)) ? $gateentry->id : ''; ?>" />
			<input type="hidden" name="csrf-token" content="{{ csrf_token() }}">
		</div>
    </div>
    <div class="col-md-2 dispflex">
        <label class="col-md-6 lblcommon"><i class="fa-regular fa-calendar-check iconthemecolor"></i> Gt. In Time<span class="redasterisk">*</span></label>
		<div class="col-md-6">
			<input style="width:90%;" type="time" name="gate_in_time" id="gate_in_time" class="form-control" value="{{ old('gate_in_time', $gateentry->gate_in_time ?? $today_time) }}" title="{{ old('gate_in_time', $gateentry->gate_in_time ?? $today_time) }}" required>
		</div>
    </div>
	
	<div class="col-md-2 dispflex">
        <label class="col-md-4 lblcommon"><i class="fa-solid fa-industry iconthemecolor"></i> Plant<span class="redasterisk">*</span></label>
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
        <label class="col-md-5 lblcommon"><i class="fa-solid fa-location-dot iconthemecolor"></i> Location<span class="redasterisk">*</span></label>
		<div class="col-md-7">
			<select style="width:100%;" class="" name="loc_code" id="loc_code" onchange="setlocname();">
				
			</select>
			
			
			<input type="hidden" name="sel_loc" id="sel_loc"  value="<?php echo $sel_loc;?>" />
			<input type="hidden" name="loc_name" id="loc_name"  value="{{ old('loc_name', $gateentry->loc_name ?? '') }}" />
			
		</div>
    </div>
	
	<div class="col-md-2 dispflex">
        <label class="col-md-5 lblcommon"><i class="fa-solid fa-door-open iconthemecolor"></i> Gate No.<span class="redasterisk">*</span></label>
		<div class="col-md-7">
						
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
	
	<div class="col-md-2 dispflex">
        <label class="col-md-7 lblcommon"><i class="fa-solid fa-book iconthemecolor"></i> Gate Reg. Ref No.</label>
		<div class="col-md-5">
			<input type="text" name="sec_reg_ref_no" class="form-control" value="{{ old('sec_reg_ref_no', $gateentry->sec_reg_ref_no ?? '') }}" title="{{ old('sec_reg_ref_no', $gateentry->sec_reg_ref_no ?? '') }}" >
		</div>
    </div>
	
</div>

<div class="row">
    
	
	<div class="col-md-2 dispflex">
        <label class="col-md-6 lblcommon"><i class="fa-solid fa-truck-moving iconthemecolor"></i> Vehicle Type<span class="redasterisk">*</span></label>
		<div class="col-md-6">
						
			<select style="width:100%;" class="" name="vehicle_type_code" id="vehicle_type_code" onchange="setvehiclename();" required>
			<option value="">None</option>
			<?php
					
			$def_veh = '05';
			
			if($vehiclelist->count() > 0)	
			{
				foreach($vehiclelist as $vobj)
				{
					$vehcode = $vobj->vtypecode;
					$vehname = $vobj->vtypedesc;
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
			}
			?>
			
			</select>
			<input type="hidden" name="vehicle_type_desc" id="vehicle_type_desc"  value="{{ old('vehicle_type_desc', $gateentry->vehicle_type_desc ?? '') }}" />
		</div>
    </div>
    
	 <div class="col-md-3 dispflex">
        <label class="col-md-4 lblcommon"><i class="fa-solid fa-arrow-down-1-9 iconthemecolor"></i> <span id="vehiclelbl">Vehicle No.</span><span class="redasterisk">*</span></label>
		<div class="col-md-6">
			<input type="text" name="vehicle_no" id="vehicle_no" placeholder="MH-47-AB-1234" class="form-control" value="{{ old('vehicle_no', $gateentry->vehicle_no ?? '') }}" title="{{ old('vehicle_no', $gateentry->vehicle_no ?? '') }}" required>
			
			<input type="hidden" name="wb_number" value="{{ old('wb_number', $gateentry->wb_number ?? '') }}" >
			
		</div>
    </div>
	
	<div class="col-md-3 dispflex">
        <label class="col-md-4 lblcommon"><i class="fa-solid fa-person iconthemecolor"></i> <span id="delpernamelbl">Driver</span> Name<span class="redasterisk">*</span></label>
		<div class="col-md-8">
			<input type="text" name="del_person_name" id="del_person_name" class="form-control" value="{{ old('del_person_name', $gateentry->del_person_name ?? '') }}" title="{{ old('del_person_name', $gateentry->del_person_name ?? '') }}" >
		</div>
    </div>
	
	<div class="col-md-2 dispflex">
        <label class="col-md-6 lblcommon"><i class="fa-solid fa-phone iconthemecolor"></i> <span id="delpermobilelbl">Driver</span> Mob.<span class="redasterisk">*</span></label>
		<div class="col-md-6">
			<input type="text" name="del_person_mob" id="del_person_mob" class="form-control" value="{{ old('del_person_mob', $gateentry->del_person_mob ?? '') }}" title="{{ old('del_person_mob', $gateentry->del_person_mob ?? '') }}" >
		</div>
    </div>
	
   
</div>

<div class="row ">
	<div class="col-md-2 dispflex">
        <label class="col-md-6 lblcommon"><i class="fa-regular fa-file-lines iconthemecolor"></i> LR No.(Rec.)</label>
		<div class="col-md-6">
			<input type="text" name="lr_number" id="lr_number" class="form-control" value="{{ old('lr_number', $gateentry->lr_number ?? '') }}" title="{{ old('lr_number', $gateentry->lr_number ?? '') }}" >
		</div>
    </div>
	<div class="col-md-2 dispflex">
        <label class="col-md-6 lblcommon"><i class="fa-solid fa-truck-arrow-right iconthemecolor"></i> Transporter</label>
		<div class="col-md-6">
			<input type="text" name="transporter" id="transporter" class="form-control" value="{{ old('transporter', $gateentry->transporter ?? '') }}" title="{{ old('transporter', $gateentry->transporter ?? '') }}" >
		</div>
    </div>
	<div class="col-md-2 dispflex">
        <label class="col-md-6 lblcommon"><i class="fa-regular fa-file-lines iconthemecolor"></i> LR No.(Prev)</label>
		<div class="col-md-6">
			<input type="text" name="lr_number_prev1" id="lr_number_prev1" class="form-control" value="{{ old('lr_number_prev1', $gateentry->lr_number_prev1 ?? '') }}" title="{{ old('lr_number_prev1', $gateentry->lr_number_prev1 ?? '') }}" >
		</div>
    </div>
	<div class="col-md-2 dispflex">
        <label class="col-md-6 lblcommon"><i class="fa-solid fa-truck-arrow-right iconthemecolor"></i> Transporter</label>
		<div class="col-md-6">
			<input type="text" name="transporter_prev1" id="transporter_prev1" class="form-control" value="{{ old('transporter_prev1', $gateentry->transporter_prev1 ?? '') }}" title="{{ old('transporter_prev1', $gateentry->transporter_prev1 ?? '') }}" >
		</div>
    </div>
	<div class="col-md-2 dispflex">
        <label class="col-md-6 lblcommon"><i class="fa-regular fa-file-lines iconthemecolor"></i> LR No.(Prev2)</label>
		<div class="col-md-6">
			<input type="text" name="lr_number_prev2" id="lr_number_prev2" class="form-control" value="{{ old('lr_number_prev2', $gateentry->lr_number_prev2 ?? '') }}" title="{{ old('lr_number_prev2', $gateentry->lr_number_prev2 ?? '') }}" >
		</div>
    </div>
	<div class="col-md-2 dispflex">
        <label class="col-md-6 lblcommon"><i class="fa-solid fa-truck-arrow-right iconthemecolor"></i> Transporter</label>
		<div class="col-md-6">
			<input type="text" name="transporter_prev2" id="transporter_prev2" class="form-control" value="{{ old('transporter_prev2', $gateentry->transporter_prev2 ?? '') }}" title="{{ old('transporter_prev2', $gateentry->transporter_prev2 ?? '') }}" >
		</div>
    </div>
</div>

<div class="row ">
	<div class="col-md-2 dispflex">
        <label class="col-md-6 lblcommon"><i class="fa-solid fa-network-wired iconthemecolor"></i> Department<span class="redasterisk">*</span></label>
		<div class="col-md-6">
			<select style="width:100%;" class="" name="dept_id" id="dept_id" onchange="setdeptname();">
			<option value="">None</option>
			
				
			</select>
			<input type="hidden" name="dept_name" id="dept_name" class="form-control" value="{{ old('dept_name', $gateentry->dept_name ?? '') }}" >
			
			<input type="hidden" name="sel_dept" id="sel_dept"  value="<?php echo $sel_dept;?>" />
		</div>
    </div>
	<div class="col-md-3 dispflex">
        <label class="col-md-6 lblcommon"><i class="fa-solid fa-person-circle-check iconthemecolor"></i> Check-In Gt. Person</label>
		<div class="col-md-6">
			<input type="text" name="security_name_in" class="form-control" value="{{ old('security_name_in', $gateentry->security_name_in ?? '') }}" title="{{ old('security_name_in', $gateentry->security_name_in ?? '') }}" >
		</div>
    </div>
	
	<div class="col-md-3 dispflex">
        <label class="col-md-4 lblcommon"><i class="fa-solid fa-solid fa-file-arrow-up iconthemecolor"></i> Upload File1</label>
		<div class="col-md-8">
			<input type="file" name="file1" id="file1" class="" accept=".pdf,.doc,.docx,.jpg,.png,.txt,.xls,.xlsx">
			<?php 
			if(!empty($gateentry)) {
			if($gateentry->file1) {
			?>
				
				<a href="{{ route('attachments.download', [$gateentry->id, 1]) }}" target="_blank" class="btn btn-sm btn-outline-primary">Download File
				</a>
			<?php
			}
			}
			?>
		</div>
    </div>
	<div class="col-md-3 dispflex">
        <label class="col-md-4 lblcommon"><i class="fa-solid fa-solid fa-file-arrow-up iconthemecolor"></i> Upload File2</label>
		<div class="col-md-8">
			<input type="file" name="file2" id="file2" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png,.txt,.xls,.xlsx" >
			<?php 
			if(!empty($gateentry)) {
			if($gateentry->file2) {
			?>
				<a href="{{ route('attachments.download', [$gateentry->id, 2]) }}" target="_blank" class="btn btn-sm btn-outline-primary">Download File
				</a>
			<?php
			}
			}
			?>
		</div>
    </div>
</div>

<div class="row lastrowsep">
	
	
	<div class="col-md-4 dispflex">
        <label class="col-md-3 lblcommon"><i class="fa-regular fa-comment-dots iconthemecolor"></i> Remark&nbsp;</label>
		<div class="col-md-9">
			<textarea name="remarks" class="form-control" rows="2">{{ old('remarks', $gateentry->remarks ?? '') }}</textarea>
			
			<input type="hidden" name="createdby_name" value="{{ old('createdby_name', $op_name) }}" />
			
		</div>
    </div>
	
	<?php if($is_storeuser) { ?>
	<div class="col-md-3 dispflex">
        <label class="col-md-6 lblcommon"><i class="fa-solid fa-people-carry-box iconthemecolor"></i> Dept. Person Unloading</label>
		<div class="col-md-6">
			<input type="text" name="security_name_out" id="security_name_out" class="form-control" value="{{ old('security_name_out', $gateentry->security_name_out ?? '') }}" title="{{ old('security_name_out', $gateentry->security_name_out ?? '') }}" >
		</div>
    </div>
	<?php } ?>
	
</div>

<div class="row headerrow">
	<div class="col-md-8">
		<h6 class="frmhead">Box/Container Details</h6>
	</div>
</div>
<?php
$rowcnt = !empty($gateentry) ? (($gateentry->status >= 1) ? $containerDetails->count() : (($containerDetails->count() > 1) ? $containerDetails->count() : 1)) : 1;
?>
<div class="row">
		<div class="col-md-12" style="display: table;">
			<table class="table table-bordered" id="tblcontaineritem" style="width:100%;">
				<thead>
					<tr>
						<th style="width:3%;">Sr No.</th>
						<th style="width:10%;">Container Type</th>
						<th style="width:5%;">No. of Container</th>
						<th style="width:15%;">Remark</th>
						<?php if($is_storeuser) { ?>
							<th style="width:7%;">Store Qty</th>
							<th style="width:15%;">Store Remark</th>
						<?php } else { ?>
						<th style="width:3%;">&nbsp;</th>
						<?php } ?>
					</tr>
				</thead>
				<tbody>
				<?php 
					if(!empty($gateentry)) 
					{
						//foreach($containerDetails as $k2 => $cobj)
						for($k2=0;$k2<$rowcnt;$k2++)
						{
							$rowk2 = $k2 + 1;
							$cobj = isset($containerDetails[$k2]) ? $containerDetails[$k2] : 0;
				?>
					<tr id="controw<?php echo $rowk2;?>">
						<td>
							<input class="form-control onlynumbers onright" type="text" name="cmat_srno[]" id="cmat_srno<?php echo $rowk2;?>" value="<?php echo ($cobj) ? $cobj->sr_no : $rowk2;?>" />
							<input type="hidden" name="container_id[]" id="container_id<?php echo $rowk2;?>" value="<?php echo ($cobj) ? $cobj->id : 0;?>" />
						</td>
						<td>
							<input class="form-control" type="text" name="cont_type[]" id="cont_type<?php echo $rowk2;?>" value="<?php echo ($cobj) ? $cobj->cont_type : '';?>" />
						</td>
						<td>
							<input class="form-control onlydecimalval onright" type="text" name="no_of_cont[]" id="no_of_cont<?php echo $rowk2;?>" value="<?php echo ($cobj) ? $cobj->no_of_cont : '';?>" />
						</td>
						<td>
							<input class="form-control" type="text" name="cont_remark[]" id="cont_remark<?php echo $rowk2;?>" value="<?php echo ($cobj) ? $cobj->cont_remark : '';?>" />
						</td>
						<?php if($is_storeuser) { ?>
							<td>
								<input class="form-control onlydecimalval onright" type="text" name="cont_storeqty[]" id="cont_storeqty<?php echo $rowk2;?>" value="<?php echo ($cobj) ? $cobj->cont_storeqty : '';?>" />
							</td>
							<td>
								<input class="form-control" type="text" name="cont_storeremark[]" id="cont_storeremark<?php echo $rowk2;?>" value="<?php echo ($cobj) ? $cobj->cont_storeremark : '';?>" />
							</td>
						<?php } else { ?>
						<td class="oncent ">
							<a href="javascript:void(0);" onclick="removecontainer(<?php echo $rowk2;?>);"><i class="fa-solid fa-xmark remrow"></i></a>
						</td>
						<?php } ?>
					</tr>
				<?php
						}
					}
					else
					{
					?>
					
					<tr id="controw1">
						<td>
							<input class="form-control onlynumbers onright" type="text" name="cmat_srno[]" id="cmat_srno1" value="1" />
						</td>
						<td>
							<input class="form-control" type="text" name="cont_type[]" id="cont_type1" value="" />
						</td>
						<td>
							<input class="form-control onlydecimalval onright" type="text" name="no_of_cont[]" id="no_of_cont1" value="" />
						</td>
						<td>
							<input class="form-control" type="text" name="cont_remark[]" id="cont_remark1" value="" />
						</td>
						<td class="oncent ">
							<a href="javascript:void(0);" onclick="removecontainer(1);"><i class="fa-solid fa-xmark remrow"></i></a>
						</td>
					</tr>
					
				<?php
					}
				?>
				</tbody>
			</table>
		</div>
		<div class="col-md-3">
			<input type="hidden" id="containercnt" value="<?php echo $rowcnt;?>" />
			<a class="btn btn-primary" href="javascript:void(0);" id="addcontainerbtn" onclick="addmorecontainer();">Add Container</a>
		</div>
	</div>

<div class="row headerrow" style="margin-top:10px;">
	<div class="col-md-8">
		<h6 class="frmhead">Item Details</h6>
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
			<label class="col-md-4 lblcommon"><i class="fa-regular fa-file iconthemecolor"></i> Doc Type</label>
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
			<label class="col-md-3 lblcommon"><i class="fa-solid fa-receipt iconthemecolor"></i> <span id="doctyptext">PO No.</span></label>
			<div class="col-md-9" style="display:inline-block;">
				<input style="width:40%;display:inline;" class="form-control" type="text" name="po_no" id="po_no" value="{{ old('po_no', $po_no_db ?? '') }}" />
				<!--button class="btn btn-primary" style="padding:1px;font-size:12px;" id="getpodetails" onclick="getpodetailsfunc();">Get PO Details</button-->
				<a href="javascript:void(0);" class="btn btn-primary" style="padding:1px;font-size:12px;" id="getpodetails" onclick="getpodetailsfunc();">Get PO Details</a>
				<div class="matloader" id="matloader" style="display:none;">Please Wait.....</div>
				<input type="hidden" name="rgp_int_no" id="rgp_int_no" value="{{ old('rgp_int_no', $rgp_int_no_db ?? '') }}" />
			</div>
		</div>
		
		<div class="col-md-4 dispflex">
			<label class="col-md-4 lblcommon"><i class="fa-solid fa-user-group iconthemecolor"></i> Vendor Name</label>
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

