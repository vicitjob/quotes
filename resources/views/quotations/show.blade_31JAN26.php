@extends('layouts.app')

@section('content')
<div class="container-fluid">
	<div class="row headerrow">
		<div class="col-md-4">
			<h6 class="frmhead">Details - Gate Ref No. : {{ ($quoteentry) ? $quoteentry->quotation_no : '' }}</h6>
		</div>
		<div class="col-md-4">
			<h6 class="frmhead">Created By : {{ ($quoteentry) ? $quoteentry->name : ''}}</h6>
		</div>
		<div class="col-md-4">
			<a href="{{ route('quotations.index') }}" class="btn btn-secondary mb-3 floatonright">Back to List</a>
		</div>
	
	</div>
	
	<div class="row" style="margin-top:10px;">
		<div class="col-md-2 dispflex">
			<label class="col-md-5 lblcommon">Quote For: </label>
			<span><?php echo $quote_type;?></span>
			<input type="hidden" id="quote_type" value="<?php echo $quote_type;?>" />
		</div>
		<div class="col-md-2 dispflex">
			<label class="col-md-5 lblcommon">Sheet Type: </label>
			<span><?php echo $sheet_type;?></span>
			
		</div>
		<div class="col-md-2 dispflex">
			<label class="col-md-5 lblcommon">Shipment Type: </label>
			<span><?php echo $shipment_type;?></span>
			
		</div>
		<div class="col-md-2 dispflex">
			<label class="col-md-5 lblcommon">Cargo Type: </label>
			<span><?php echo isset($cargoarr[$cargo_type]) ? $cargoarr[$cargo_type] : '';?></span>
			
		</div>
		<div class="col-md-2 dispflex">
			<label class="col-md-5 lblcommon">Currency: </label>
			<span><?php echo $currency_type;?></span>
			
		</div>
		
	</div>
	<div class="row">
		<div class="col-md-12 dispflex">
			<span class="col-md-2 lblcommon">Expected Credit Cost over 3 months: </span>
			<span class="col-md-1">
				<?php echo round($exp_cred_cost_thr_months,4);?>%
			</span>
			<span class="col-md-1 lblcommon" style="margin-left:10px;">Estd MiniMan Cst: </span>
			<span class="col-md-1">
				<?php echo round($estd_miniman_cst,4);?>%
			</span>
		</div>
	</div>
	
	@include('quotations._form')
	<div class="row" style="margin-top:10px;">
		
		<div class="col-md-12" style="text-align:center;">
			
			<a href="javascript:void(0);" onclick="exportexcel('tblcontaineritem', 'quotations_report')"  class="btn btn-danger">Download Excel</a>
			
		</div>
	</div>
	<div class="row">
		<div class="col-md-12" id="clonetbl">
		
		</div>
	</div>
	

</div>

<script>

window.onload = function() {
  var sheet_type = '<?php echo $sheet_type;?>';
  var shipment_type = '<?php echo $shipment_type;?>';
  var cargo_type = '<?php echo $cargo_type;?>';
  var currency_type = '<?php echo $currency_type;?>';
  var quote_type = '<?php echo $quote_type;?>';
  var tmpsheet = '';
  
  if(quote_type == 'Dealer')
  {
	  if(sheet_type == 'Ex Works')
	  {
		  $('.colexworks').show();
		  tmpsheet = 'colexworks';
	  }
	  else if(sheet_type == 'CIF')
	  {
		  $('.colcif').show();
		  tmpsheet = 'colcif';
	  }
	  else if(sheet_type == 'Packed')
	  {
		  if(cargo_type == 'NH')
		  {
			  $('.colpacked').show();
			  tmpsheet = 'colpacked';
		  }
	  }
	  else if(sheet_type == 'FOB')
	  {
		  $('.colfob').show();
		   tmpsheet = 'colfob';
	  }
	  else if(sheet_type == 'Delivered')
	  {
		  $('.collanded').show();
		  tmpsheet = 'collanded';
	  }
	  //console.log('.'+tmpsheet+'col'+shipment_type+cargo_type);
	  $('.'+tmpsheet+'col'+shipment_type+cargo_type).show();
  }
  
  if(quote_type == 'Customer')
  {
	  $(".recomdistsptobuyer").hide();
  }
 
  
};

</script>
@endsection
