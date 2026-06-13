@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row headerrow">
		<div class="col-md-8">
			<h5>Gate Entry List</h5>
		</div>
		<?php
		if(!$is_storeuser) {
		?>
		<div class="col-md-4">
			<a href="{{ route('gateentries.create') }}" class="btn btn-primary mb-3 floatonright btnlist">Add New Gate Entry</a>
		</div>
		<?php } ?>
	<input type="hidden" name="isstoreuser" id="isstoreuser" value="<?php echo ($is_storeuser) ? 1 : 0; ?>" />
	</div>
    
    
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered" style="width:100%;">
        <thead>
            <tr>
                <th>Gate Ref. No</th>
                <th>In Time</th>
				<th>Out Time</th>
                <th>Vehicle No</th>
				<th>Doc Type</th>
				<th>Doc No.</th>
				<th>Vendor</th>
				<?php if($show_plant_loc) { ?>
                <th>Plant</th>
				<th>Location</th>
				<?php } ?>
				<th>Dept. Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($gateEntries as $k1 => $entry)
			<?php 
				$rowno = $k1 + 1; 
				$clsname = '';
				if($entry->status == 3)
				{
					$clsname = 'green-row';
				}
			?>
            <tr class="<?php echo $clsname;?>">
			
                <td id="gtnorowid<?php echo $rowno;?>">
					{{ $entry->gate_in_no }}
					
				</td>
                <td>
				<?php
				$gtdt = '';
				if($entry->gate_in_date)
				{
					$gtdttm = $entry->gate_in_date.' '.$entry->gate_in_time;
					$gtdt = date('d-m-Y H:i:s',strtotime($gtdttm));
				}
				echo $gtdt;
				
				?>
				<input type="hidden" id="gtnoid<?php echo $rowno;?>" value="<?php echo $entry->id;?>" />
				</td>
				<td>
				<?php
				$gtdtout = '';
				if($entry->gate_out_date)
				{
					$gtdttm1 = $entry->gate_out_date.' '.$entry->gate_out_time;
					$gtdtout = date('d-m-Y H:i:s',strtotime($gtdttm1));
				}
				echo $gtdtout;
				?>
				</td>
                <td>{{ $entry->vehicle_no }}</td>
				<td>{{ $entry->doc_type_name }}</td>
				<td>{{ $entry->doc_no }}</td>
				<td>{{ $entry->vendor_name }}</td>
				<?php if($show_plant_loc) { ?>
                <td>{{ $entry->plant_name }}</td>
				<td>{{ $entry->loc_name }}</td>
				<?php } ?>
				<td style="text-align:center;">
					<?php if($entry->status == 1) { ?>
					<span class="tag tag-pro tag-lg">Pending</span>
					<?php } else if($entry->status > 1) { ?>
					<span class="tag tag-angular tag-lg">Confirmed</span>
					<?php } ?>
				</td>
                <td>
                    <a href="{{ route('gateentries.show', $entry->id) }}" class="btn btn-info btn-sm btnlist">View</a>
					<?php if(($entry->status <= 0) || ($entry->status == 1 && $is_storeuser)) { ?>
                    <a href="{{ route('gateentries.edit', $entry->id) }}" class="btn btn-warning btn-sm btnlist">Edit</a>
					<?php } ?>
					<?php if($entry->status >= 1) { ?>
					<a href="{{ route('gateentries.edit', $entry->id) }}" class="btn btn-success btn-sm btnlist">Print</a>
					<?php } ?>
					<?php if((!$is_storeuser) && ($entry->status <= 0)) { ?>
                    <form action="{{ route('gateentries.destroy', $entry->id) }}" method="POST" style="display:inline-block;">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm btnlist"
                                onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
					<?php } ?>
					<?php if((!$is_storeuser) && ($entry->status == 2)) { ?>
					 <!--a href="javascript:void(0);" class="btn btn-primary btn-sm btnlist" id="closegtentry">Close</a-->
					<button type="button" class="btn btn-primary btn-sm btnlist" data-bs-toggle="modal" data-bs-target="#gatecloseModal" data-bs-gaterowno="<?php echo $rowno;?>">
					  Close
					</button>
					 
					<?php } ?>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $gateEntries->links() }}
	
	<!-- The Modal -->
	<div class="modal fade" id="gatecloseModal">
	  <div class="modal-dialog">
		<div class="modal-content">

		  <!-- Modal Header -->
		  <div class="modal-header">
			<h4 class="modal-title">Closing of Gate Ref. No: <span id="closegatehead"></span></h4>
			<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
		  </div>

		  <!-- Modal body -->
		  <div class="modal-body">
			  <div class="row">
				<div class="col-md-12 dispflex">
					<label class="col-md-4 lblcommon">Checkout Security Name</label>
					<div class="col-md-6">
						<input type="hidden" name="csrf-token1" content="{{ csrf_token() }}">
						<input type="text" name="security_name_checkout" id="security_name_checkout" class="form-control" value="" maxlength="60">
					</div>
				</div>
			  </div>
			<div class="row">
				 <div class="col-md-12 dispflex">
					<label class="col-md-3 lblcommon">Checkout Remark&nbsp;</label>
					<div class="col-md-8">
						<textarea name="checkoutremarks" id="checkoutremarks" class="form-control" rows="5"></textarea>
						<input type="hidden" id="gtidcheckout" value="" />
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">&nbsp;</div>
				<div class="col-md-8 dispflex">
					<a href="javascript:void(0);" onclick="save_checkout_data();" id="savecheckoutdata" class="btn btn-primary btnlist">Submit</a>
					<span id="checkoutloader" style="display:none;">Please wait......</span>
					<span id="checkoutmsg" style="display:none;"></span>
				</div>
			</div>
		  </div>

		  <!-- Modal footer -->
		  <div class="modal-footer">
			<!--button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button-->
		  </div>

		</div>
	  </div>
	</div>	
	<!-- The Modal -->
	
</div>
@endsection
