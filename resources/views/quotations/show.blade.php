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
			<input type="hidden" id="sheet_type" value="<?php echo $sheet_type;?>" />
		</div>
		<div class="col-md-2 dispflex">
			<label class="col-md-5 lblcommon">Shipment Type: </label>
			<span><?php echo $shipment_type;?></span>
			<input type="hidden" id="shipment_type" value="<?php echo $shipment_type;?>" />
		</div>
		<div class="col-md-2 dispflex">
			<label class="col-md-5 lblcommon">Cargo Type: </label>
			<span><?php echo isset($cargoarr[$cargo_type]) ? $cargoarr[$cargo_type] : '';?></span>
			<input type="hidden" id="cargo_type" value="<?php echo isset($cargoarr[$cargo_type]) ? $cargoarr[$cargo_type] : '';?>" />
		</div>
		<div class="col-md-2 dispflex">
			<label class="col-md-5 lblcommon">Currency: </label>
			<span><?php echo $currency_type;?></span>
			<input type="hidden" id="currency_type" value="<?php echo $currency_type;?>" />
		</div>
		
	</div>
	<div class="row">
		<div class="col-md-12 dispflex">

			<?php if($quote_type == 'Customer') { ?>
			<span class="col-md-1 lblcommon">Cust Location: </span>
			<span class="col-md-2">
				<?php echo $quoteentry->cust_loc;?>
				<select id="cust_loc" style="display:none;">
					<option value="<?php echo $quoteentry->cust_loc;?>" selected><?php echo $quoteentry->cust_loc;?></option>
				</select>
			</span>
			<?php } else { ?>
				<span class="col-md-1 lblcommon">Selected Dealer: </span>
				<span class="col-md-2">
					<?php echo $quoteentry->dealer_name_text;?>
					<select id="dealer_name" style="display:none;">
						<option value="<?php echo $quoteentry->dealer_name_text;?>" selected><?php echo $quoteentry->dealer_name_text;?></option>
					</select>
				</span>
			<?php } ?>

			<span class="col-md-2 lblcommon">Expected Credit Cost over 3 months: </span>
			<span class="col-md-1">
				<?php echo round($exp_cred_cost_thr_months,4);?>%
				<input type="hidden" id="exp_cred_cost_thr_months" value="<?php echo round($exp_cred_cost_thr_months,4);?>%" />
			</span>
			<span class="col-md-1 lblcommon" style="margin-left:10px;">Estd MiniMan Cst: </span>
			<span class="col-md-1">
				<?php echo round($estd_miniman_cst,4);?>%
				<input type="hidden" id="estd_miniman_cst" value="<?php echo round($estd_miniman_cst,4);?>%" />
			</span>
			<span class="col-md-2 lblcommon" style="margin-left:10px;">Consider Actual Weight: </span>
			<span class="col-md-1">
				
				<input type="checkbox" name="cons_act_weight" id="cons_act_weight" value="1" <?php echo ($quoteentry->cons_act_weight == 1) ? 'checked' : '';?> disabled />
				<input type="hidden" name="total_ship_vol_qty_db" id="total_ship_vol_qty_db" value="<?php echo ($quoteentry->total_ship_vol_qty_h) ? $quoteentry->total_ship_vol_qty_h : '-';?>" />
			</span>
		</div>
	</div>
	
	@include('quotations._form')
	<div class="row" style="margin-top:10px;">
		
		<div class="col-md-12" style="text-align:center;display: none;">
			
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
	  $(".colforcust").hide();
		  $(".coltotalprice").show();
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
		$(".colforcust").show();
		$(".coltotalprice").hide();
		$(".recomdistsptobuyer").hide();
		$('.collanded').hide();
		$('.colfob').hide();
		$('.colpacked').hide();
		$('.colcif').hide();
		$('.colexworks').hide();
  }
 
  
};

</script>
@endsection
