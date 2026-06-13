@extends('layouts.app')

@section('content')
<div class="container-fluid">
   
	<div class="row headerrow">
		<div class="col-md-12">
			<h6 class="frmhead">Generate Quotation <span class="operatorname">Initiating By: {{ old('createdby_name', $op_name) }}</span></h6>
		</div>
		
	</div>
	
	@if ($errors->any())
		<div class="alert alert-danger">
			<ul class="mb-0">
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif
	
	<form action="{{ route('quotations.create') }}" method="GET">
		<div class="row" style="margin-top:10px;">
			<div class="col-md-2 dispflex">
				<label class="col-md-5 lblcommon">Quote For: </label>
				<select style="width:100%;" name="quote_type" id="quote_type" onchange="setsheetype(this.value);">
					<option value="Dealer" <?php echo ($quote_type == 'Dealer') ? 'selected' : '';?>>Dealer</option>
					<option value="Customer" <?php echo ($quote_type == 'Customer') ? 'selected' : '';?>>Customer</option>
				</select>
			</div>
			<div class="col-md-2 dispflex">
				<label class="col-md-5 lblcommon">Sheet Type: </label>
				<select style="width:100%;" name="sheet_type" id="sheet_type">
					<option value="Ex Works" <?php echo ($sheet_type == 'Ex Works') ? 'selected' : '';?>>Ex Works</option>
					<option class="dealopt" value="Packed" <?php echo ($sheet_type == 'Packed') ? 'selected' : '';?>>Packed</option>
					
					<option class="dealopt" value="FOB" <?php echo ($sheet_type == 'FOB') ? 'selected' : '';?>>FOB</option>
					<option class="dealopt" value="CIF" <?php echo ($sheet_type == 'CIF') ? 'selected' : '';?>>CIF</option>
					<option class="custopt" value="Delivered" <?php echo ($sheet_type == 'Delivered') ? 'selected' : '';?>>Delivered</option>
				</select>
			</div>
			<div class="col-md-2 dispflex">
				<label class="col-md-5 lblcommon">Shipment Type: </label>
				<select style="width:100%;" name="shipment_type" id="shipment_type">
					<option value="FCL" <?php echo ($shipment_type == 'FCL') ? 'selected' : '';?>>FCL</option>
					<option value="LCL" <?php echo ($shipment_type == 'LCL') ? 'selected' : '';?>>LCL</option>
					
				</select>
			</div>
			<div class="col-md-2 dispflex">
				<label class="col-md-5 lblcommon">Cargo Type: </label>
				<select style="width:100%;" name="cargo_type" id="cargo_type">
					
					<option value="NH" <?php echo ($cargo_type == 'NH') ? 'selected' : '';?>>Non-Haz</option>
					<option value="H" <?php echo ($cargo_type == 'H') ? 'selected' : '';?>>Haz</option>
					
				</select>
			</div>
			<div class="col-md-1 dispflex">
				<label class="col-md-5 lblcommon">Curr.: </label>
				<select style="width:100%;" name="currency_type" id="currency_type" onchange="changecurrencyvalues(this.value);">
					<option value="USD" <?php echo ($currency_type == 'USD') ? 'selected' : '';?>>USD</option>
					<option value="AED" <?php echo ($currency_type == 'AED') ? 'selected' : '';?>>AED</option>
					
				</select>
			</div>
			<div class="col-md-2 dispflex" style="text-align:center;">
				<button type="submit" class="btn btn-success" id="srchbtn">Submit</button>
			</div>
		</div>
		<div class="row" >
			<div class="col-md-12 dispflex">
				<span class="col-md-2 lblcommon">Expected Credit Cost(%): </span>
				<span class="col-md-1">
					<input class="form-control onlydecimalval onright tblinput" style="width:50%;" type="text" name="exp_cred_cost_thr_months" id="exp_cred_cost_thr_months" value="0" onchange="setshiptoqty(1);" />
				</span>
				<span class="col-md-3 lblcommon dispflex">Estd MiniMan Cst(%): 
				<input class="form-control onlydecimalval onright tblinput" style="width:20%;margin-left:10px;" type="text" name="estd_miniman_cst" id="estd_miniman_cst" value="0" onchange="setshiptoqty(1);" />
				</span>
				
					
				
			</div>
			
		</div>
	</form>
	
	<?php if($showtable) { ?>
    <form action="{{ route('quotations.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validatentryform();">
        @csrf
        @include('quotations._form')
		<div class="row" style="margin-top:10px;">
			
			<div class="col-md-12" style="text-align:center;">
				<button type="submit" class="btn btn-success" id="savebtn">Save Quote</button>
				<a href="javascript:void(0);" onclick="exportexcel('tblcontaineritem', 'quotations_report')"  class="btn btn-danger">Download Excel</a>
				<a href="{{ route('quotations.index') }}" class="btn btn-warning" style="display:none;">Print PDF</a>
				<input type="hidden" name="isautocomplete_open" id="isautocomplete_open" value="0" />
				<input type="hidden" name="prodname_hidden" id="prodname_hidden" value="" />
				<input type="hidden" name="total_ship_vol_qty" id="total_ship_vol_qty" value="0" />
				<input type="hidden" name="quote_type2" id="quote_type2" value="" />
				<input type="hidden" name="sheet_type2" id="sheet_type2" value="" />
				<input type="hidden" name="shipment_type2" id="shipment_type2" value="" />
				<input type="hidden" name="cargo_type2" id="cargo_type2" value="" />
				<input type="hidden" name="currency_type2" id="currency_type2" value="" />
				<input type="hidden" name="exp_cred_cost_thr_months2" id="exp_cred_cost_thr_months2" value="" />
				<input type="hidden" name="estd_miniman_cst2" id="estd_miniman_cst2" value="" />
				<input type="hidden" id="createpagehidden" value="1" />
			</div>
		</div>
		<div class="row">
			<div class="col-md-12" id="clonetbl">
			
			</div>
		</div>
		
    </form>
	<?php } ?>
	
</div>

<script>

var glbquotettlcost = 0;

window.onload = function() {
  var sheet_type = '<?php echo $sheet_type;?>';
  var shipment_type = '<?php echo $shipment_type;?>';
  var cargo_type = '<?php echo $cargo_type;?>';
  var currency_type = '<?php echo $currency_type;?>';
  var quote_type = '<?php echo $quote_type;?>';
  var tmpsheet = '';
  var exrate_updated = '<?php echo $exrate_updated;?>';
  
  if(quote_type == '') { quote_type = 'Dealer'; }
  setsheetype(quote_type,sheet_type);
  setexchangerate(exrate_updated);
  
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
  
  /*if(shipment_type == 'FCL')
  {
	   $('.'+tmpsheet+'colfcl').show();
  }
  else if(shipment_type == 'LCL')
  {
	  $('.'+tmpsheet+'collcl').show();
  }
  
  if(cargo_type == 'H')
  {
	  $('.'+tmpsheet+'col').show();
  }
  else if(cargo_type == 'NH')
  {
	  $('.'+tmpsheet+'colnh').show();
  }*/
  
};

let materialIndex = 0;
function addMaterial() {
    const template = document.querySelector('.material-template').cloneNode(true);
    template.style.display = 'block';
    template.classList.remove('material-template');

    template.querySelectorAll('[data-name]').forEach(input => {
        const realName = input.getAttribute('data-name').replace('INDEX', materialIndex);
        input.setAttribute('name', realName);
        input.removeAttribute('data-name');

        // Add required attribute only on visible inputs
        input.setAttribute('required', 'required');

        input.value = '';
    });

    document.getElementById('materials').appendChild(template);
    materialIndex++;
}

function removeMaterial(button) {
    button.closest('.material-item').remove();
}

function validateEntries()
{
	console.log('Saving');
}

function addmorecontainer()
{
	
}

</script>

@endsection