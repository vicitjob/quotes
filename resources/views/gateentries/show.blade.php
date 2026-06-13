@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row headerrow">
		<div class="col-md-4">
			<h6 class="frmhead">Details - Gate Ref No. : {{ old('gate_in_no', $gateentry->gate_in_no ?? '') }}</h6>
		</div>
		<div class="col-md-4">
			<h6 class="frmhead">Created By : {{ $gateentry->createdby_name }}</h6>
		</div>
		<div class="col-md-4">
			<a href="{{ route('gateentries.index') }}" class="btn btn-secondary mb-3 floatonright">Back to List</a>
		</div>
	
	</div>
	
	<div class="row">
	
		
		<div class="col-md-2 dispflex">
			<div class="col-md-6 lblcommon">Gt. In Date:</div> 
			<div class="col-md-6"><?php echo ($gateentry->gate_in_date) ? date('d-m-Y', strtotime($gateentry->gate_in_date)) : ''; ?></div>
		</div>
		<div class="col-md-2 dispflex">
			<div class="col-md-6 lblcommon">Gt. In Time:</div> 
			<div class="col-md-6">{{ $gateentry->gate_in_time }}</div>
			
		</div>
		<div class="col-md-2 dispflex">
			<div class="col-md-4 lblcommon">Plant:</div> 
			<div class="col-md-6">{{ $gateentry->plant_name }}</div>
		</div>
		<div class="col-md-2 dispflex">
			<div class="col-md-4 lblcommon">Location:</div> 
			<div class="col-md-6">{{ $gateentry->loc_name }}</div>
		</div>
		<div class="col-md-2 dispflex">
			<div class="col-md-6 lblcommon">Gate No.:</div> 
			<div class="col-md-6">{{ $gateentry->sec_id_gt_in_name }}</div>
		</div>
		<div class="col-md-2 dispflex">
			<div class="col-md-7 lblcommon">Gate Reg. Ref No.:</div> 
			<div class="col-md-5">{{ $gateentry->sec_reg_ref_no }}</div>
		</div>
		
	</div>
	<div class="row">
		
		<div class="col-md-2 dispflex">
			<div class="col-md-6 lblcommon">Vehicle Type:</div> 
			<div class="col-md-6">{{ $gateentry->vehicle_type_desc }}</div>
		</div>
		
		<div class="col-md-2 dispflex">
			<div class="col-md-6 lblcommon"><?php echo $selvehidlbl;?> No:</div> 
			<div class="col-md-6">{{ $gateentry->vehicle_no }}</div>
		</div>
		
		<div class="col-md-2 dispflex">
			<div class="col-md-6 lblcommon"><?php echo $seldellbl;?> Name:</div> 
			<div class="col-md-6">{{ $gateentry->del_person_name }}</div>
		</div>
		
		<div class="col-md-2 dispflex">
			<div class="col-md-6 lblcommon"><?php echo $seldellbl;?> No.:</div> 
			<div class="col-md-6">{{ $gateentry->del_person_mob }}</div>
		</div>
		
	</div>
	
	<div class="row">
		<div class="col-md-2 dispflex">
			<div class="col-md-6 lblcommon">LR No.(Rec.):</div> 
			<div class="col-md-6">{{ $gateentry->lr_number }}</div>
		</div>
		<div class="col-md-2 dispflex">
			<div class="col-md-6 lblcommon">Transporter:</div> 
			<div class="col-md-6">{{ $gateentry->transporter }}</div>
		</div>
		<div class="col-md-2 dispflex">
			<div class="col-md-6 lblcommon">LR No.(Prev):</div> 
			<div class="col-md-6">{{ $gateentry->lr_number_prev1 }}</div>
		</div>
		<div class="col-md-2 dispflex">
			<div class="col-md-6 lblcommon">Transporter:</div> 
			<div class="col-md-6">{{ $gateentry->transporter_prev1 }}</div>
		</div>
		<div class="col-md-2 dispflex">
			<div class="col-md-6 lblcommon">LR No.(Prev2):</div> 
			<div class="col-md-6">{{ $gateentry->lr_number_prev2 }}</div>
		</div>
		<div class="col-md-2 dispflex">
			<div class="col-md-6 lblcommon">Transporter:</div> 
			<div class="col-md-6">{{ $gateentry->transporter_prev2 }}</div>
		</div>
	</div>
	
	<div class="row">
		<div class="col-md-2 dispflex">
			<div class="col-md-6 lblcommon">Department:</div> 
			<div class="col-md-6">{{ $dept_name }}</div>
		</div>
		<div class="col-md-3 dispflex">
			<div class="col-md-5 lblcommon">Check-In Gt. Person:</div> 
			<div class="col-md-6">{{ $gateentry->security_name_in }}</div>
		</div>
		<div class="col-md-2 dispflex">
			<div class="col-md-4 lblcommon">File 1:</div> 
			<div class="col-md-6">
				<?php 
				if(!empty($gateentry)) {
				if($gateentry->file1) {
				?>
					
					<a href="{{ route('attachments.download', [$gateentry->id, 1]) }}" target="_blank" class="">Download File
					</a>
				<?php
				}
				}
				?>
			</div>
		</div>
		<div class="col-md-2 dispflex">
			<div class="col-md-4 lblcommon">File 2:</div> 
			<div class="col-md-6">
				<?php 
				if(!empty($gateentry)) {
				if($gateentry->file2) {
				?>
					<a href="{{ route('attachments.download', [$gateentry->id, 2]) }}" target="_blank" class="">Download File
					</a>
				<?php
				}
				}
				?>
			</div>
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
		
	<div class="row lastrowsep">
		
		<div class="col-md-4 dispflex">
			<div class="col-md-6 lblcommon">Dept. Person Unloading:</div> 
			<div class="col-md-6">{{ $gateentry->security_name_out }}</div>
		</div>
		<div class="col-md-5 dispflex">
			<div class="col-md-2 lblcommon">Remarks:</div> 
			<div class="col-md-9">{{ $gateentry->remarks }}</div>
		</div>
	</div>
	
	<div class="row headerrow">
		<div class="col-md-8">
			<h6 class="frmhead">Box/Container Details</h6>
		</div>
			
	</div>
	
	<table class="table table-bordered" style="width:100%;">
        <thead>
            <tr>
                <th>Sr No.</th>
                <th>Container Type</th>
                <th>No. of Container</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
		@if($containerDetails->count() > 0)
			@foreach($containerDetails as $cobj)
			   <tr>
				 <td>{{ $cobj->sr_no }}</td>
				 <td>{{ $cobj->cont_type }}</td>
				 <td>{{ $cobj->no_of_cont }}</td>
				 <td>{{ $cobj->cont_remark }}</td>
			   </tr>
			@endforeach
		@else
			<tr><td colspan="4">No Data Found.</td></tr>
		@endif
        </tbody>
    </table>
	
	<div class="row headerrow">
		<div class="col-md-8">
			<h6 class="frmhead">Item Details</h6>
		</div>
			
	</div>
	
	<div class="row lastrowsep">
		
		<div class="col-md-2 dispflex">
			<div class="col-md-5 lblcommon">Doc Type:</div> 
			<div class="col-md-7">{{ $gateentry->doc_type_name }}</div>
		</div>
		
		<div class="col-md-2 dispflex">
			<div class="col-md-4 lblcommon">PO No.:</div> 
			<div class="col-md-6"><?php echo $po_no_db;?></div>
		</div>
		<div class="col-md-2 dispflex">
			<div class="col-md-4 lblcommon">Invoice No.:</div> 
			<div class="col-md-6">{{ $gateentry->invoice_no }}</div>
		</div>
		
		<div class="col-md-4 dispflex">
			<div class="col-md-4 lblcommon">Vendor Name:</div> 
			<div class="col-md-8">{{ $gateentry->vendor_name }}</div>
		</div>
		
	</div>
    
    <table class="table table-bordered" style="width:100%;">
        <thead>
            <tr>
                <th>SR No</th>
                <th>Material Code</th>
                <th>Material Desc</th>
                <th>PO/CHLN Qty</th>
                <th>Unit</th>
                <th>Gate Qty</th>
                <th>Remark</th>
            </tr>
        </thead>
        <tbody>
		@if($materialDetails->count() > 0)
			@foreach($materialDetails as $material)
			   <tr>
				 <td>{{ $material->sr_no }}</td>
				 <td>{{ $material->material_code }}</td>
				 <td>{{ $material->material_desc }}</td>
				 <td>{{ $material->po_chln_qty }}</td>
				 <td>{{ $material->mat_unit }}</td>
				 <td>{{ $material->gateentry_qty }}</td>
				 <td>{{ $material->remark }}</td>
			   </tr>
			@endforeach
		@else
			<tr><td colspan="7">No Item Found.</td></tr>
		@endif
        </tbody>
    </table>
</div>
@endsection
