@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row headerrow">
		<div class="col-md-8">
			<h5>Quotation List</h5>
		</div>
		
		<div class="col-md-4">
			<a href="{{ route('quotations.create') }}" class="btn btn-primary mb-3 floatonright btnlist">Generate Quotation</a>
		</div>
	</div>
    
    
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
	
	
	<form id="srchfrm_quoteentry" action="{{ route('quotations.index') }}" method="GET" onsubmit="return validatesearchform();">
	
	<div class="row nomarginleft">
		<div class="col-md-2 dispflex">
			<label class="col-md-5 lblcommon">Quote Ref No.</label>
			<div class="col-md-7">
				<input type="text" name="srch_quoterefno" id="srch_quoterefno" class="form-control" value="<?php echo $srch_quoterefno;?>" />
				
			</div>
		</div>
		<div class="col-md-2 dispflex">
			<label class="col-md-5 lblcommon">From Date:</label>
			<div class="col-md-7">
				<input style="width:100%;display:inline;" type="date" name="from_quote_date" id="from_quote_date" class="form-control" value="<?php echo $from_quote_date;?>" title="">
				
			</div>
		</div>
		<div class="col-md-2 dispflex">
			<label class="col-md-5 lblcommon">To Date:</label>
			<div class="col-md-7">
				<input style="width:100%;display:inline;" type="date" name="to_quote_date" id="to_quote_date" class="form-control" value="<?php echo $to_quote_date;?>" title="">
				
			</div>
		</div>
		
		<div class="col-md-3">
			<button type="submit" class="btn btn-primary btn-sm btnlist" id="srchbtn">Search</button>
			<a href="javascript:void(0);" onclick="resetsrchform();" class="btn btn-secondary btn-sm btnlist">Reset</a>
		</div>
		
		
	</div>
	
	
	</form>
		

    <table class="table table-bordered" style="width:100%;margin-top:10px;">
        <thead>
            <tr>
                <th>Quote Ref. No</th>
                <th>Total Shipment Vol.</th>
				<th>Created By</th>
				<th>Created Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
		@if($quoteEntries->count() > 0)
        @foreach($quoteEntries as $k1 => $entry)
			<?php 
				$rowno = $k1 + 1; 
				$clsname = '';
				
			?>
            <tr class="<?php echo $clsname;?>">
			
                <td id="gtnorowid<?php echo $rowno;?>">
					{{ $entry->quotation_no }}
					
				</td>
				<td>{{ $entry->total_shipment_vol }}</td>
				<td>{{ $entry->name }}</td>
                <td>
				<?php
				$gtdt = '';
				if($entry->created_at)
				{
					$gtdttm = $entry->created_at;
					$gtdt = date('d-m-Y H:i',strtotime($gtdttm));
				}
				echo $gtdt;
				
				?>
				
				</td>
				
                <td>
                    <a href="{{ route('quotations.show', $entry->id) }}" class="btn btn-info btn-sm btnlist" style="padding:0.1rem;font-size:0.7rem;">View</a>
					
					<!--a href="<?php //echo url('show?isdwn=1&id='.$entry->id); ?>" target="_blank" class="btn btn-success btn-sm btnlist">Download Excel</a-->
					
                </td>
            </tr>
        @endforeach
		@else
			<tr>
				<td colspan="5">No Data Found</td>
			</tr>
		@endif
        </tbody>
    </table>
    
	<div class="d-felx justify-content-center">
			 {{ $quoteEntries->withQueryString()->links('pagination::bootstrap-4') }}
			
	</div>
	
</div>
@endsection
