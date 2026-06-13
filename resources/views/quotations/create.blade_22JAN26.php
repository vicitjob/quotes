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
				<select style="width:100%;" name="quote_type" id="quote_type">
					<option value="Dealer" <?php echo ($quote_type == 'Dealer') ? 'selected' : '';?>>Dealer</option>
					<option value="Customer" <?php echo ($quote_type == 'Customer') ? 'selected' : '';?>>Customer</option>
				</select>
			</div>
			<div class="col-md-2 dispflex">
				<label class="col-md-5 lblcommon">Sheet Type: </label>
				<select style="width:100%;" name="sheet_type" id="sheet_type">
					<option value="Packed" <?php echo ($sheet_type == 'Packed') ? 'selected' : '';?>>Packed</option>
					<option value="Ex Works" <?php echo ($sheet_type == 'Ex Works') ? 'selected' : '';?>>Ex Works</option>
					<option value="FOB" <?php echo ($sheet_type == 'FOB') ? 'selected' : '';?>>FOB</option>
					<option value="CIF" <?php echo ($sheet_type == 'CIF') ? 'selected' : '';?>>CIF</option>
					<option value="Delivered" <?php echo ($sheet_type == 'Delivered') ? 'selected' : '';?>>Delivered</option>
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
	var containercnt = $("#containercnt").val();
	var containercntnew = parseInt(containercnt) + 1;
	$("#containercnt").val(containercntnew);
		
	var tbl_html = '<tr id="controw'+containercntnew+'"><td><input class="form-control onlynumbers onright tblinput" type="text" name="cmat_srno[]" id="cmat_srno'+containercntnew+'" value="'+containercntnew+'" /><input type="hidden" name="container_id[]" id="container_id'+containercntnew+'" value="" /></td><td><input class="form-control tblinput" type="text" name="prodtag[]" id="prodtag'+containercntnew+'" value="" /></td><td><input class="form-control onlydecimalval onright tblinput" type="text" name="ship_vol_qty[]" id="ship_vol_qty'+containercntnew+'" value="" /></td><td><input class="form-control onlydecimalval onright tblinput" type="text" name="unit_qty[]" id="unit_qty'+containercntnew+'" value="" /></td><td><input class="form-control onlydecimalval onright tblinput" type="text" name="listprice_inr[]" id="listprice_inr'+containercntnew+'" value="" /></td><td><span id="listprice_usd_span"></span><input type="hidden" name="listprice_usd[]" id="listprice_usd'+containercntnew+'" value="" /></td><td><select style="width:100%;" class="" name="disc_type" id="disc_type'+containercntnew+'"><option value="">None</option>';
		<?php
		
		if($disc_res->count() > 0)
		{
			foreach($disc_res as $discobj)
			{
				//$sel1 = ($srch_disctype == $discobj->id) ? 'selected' : '';
				$tmpoption = '<option value="'.$discobj->id.'">'.$discobj->disc_type.'</option>';
			?>
			
			
				tbl_html += '<?php echo $tmpoption;?>';
			<?php
			}
		}
		?>
	tbl_html += '</select></td><td><span id="disc_val_span"></span><input type="hidden" name="disc_val[]" id="disc_val'+containercntnew+'" value="" /></td><td><span id="unp_exwork_inr_span"></span><input type="hidden" name="unp_exwork_inr[]" id="unp_exwork_inr'+containercntnew+'" value="" /></td><td><span id="unp_exwork_usd_span"></span><input type="hidden" name="unp_exwork_usd[]" id="unp_exwork_usd'+containercntnew+'" value="" /></td><td><span id="pack_fcl_nh_span"></span><input type="hidden" name="pack_fcl_nh[]" id="pack_fcl_nh'+containercntnew+'" value="" /></td><td><span id="pack_fcl_pallet_span"></span><input type="hidden" name="pack_fcl_pallet[]" id="pack_fcl_pallet'+containercntnew+'" value="" /></td><td><span id="fob_fcl_nh_span"></span><input type="hidden" name="fob_fcl_nh[]" id="fob_fcl_nh'+containercntnew+'" value="" /></td><td><span id="fob_lcl_nh_span"></span><input type="hidden" name="fob_lcl_nh[]" id="fob_lcl_nh'+containercntnew+'" value="" /></td><td><span id="fob_fcl_h_span"></span><input type="hidden" name="fob_fcl_h[]" id="fob_fcl_h'+containercntnew+'" value="" /></td><td><span id="fob_lcl_h_span"></span><input type="hidden" name="fob_lcl_h[]" id="fob_lcl_h'+containercntnew+'" value="" /></td><td><span id="cif_fcl_nh_span"></span><input type="hidden" name="cif_fcl_nh[]" id="cif_fcl_nh'+containercntnew+'" value="" /></td><td><span id="cif_lcl_nh_span"></span><input type="hidden" name="cif_lcl_nh[]" id="cif_lcl_nh'+containercntnew+'" value="" /></td><td><span id="cif_fcl_h_span"></span><input type="hidden" name="cif_fcl_h[]" id="cif_fcl_h'+containercntnew+'" value="" /></td><td><span id="cif_lcl_h_span"></span><input type="hidden" name="cif_lcl_h[]" id="cif_lcl_h'+containercntnew+'" value="" /></td><td><span id="landed_fcl_nh_span"></span><input type="hidden" name="landed_fcl_nh[]" id="landed_fcl_nh'+containercntnew+'" value="" /></td><td><span id="landed_lcl_nh_span"></span><input type="hidden" name="landed_lcl_nh[]" id="landed_lcl_nh'+containercntnew+'" value="" /></td><td><span id="landed_fcl_h_span"></span><input type="hidden" name="landed_fcl_h[]" id="landed_fcl_h'+containercntnew+'" value="" /></td><td><span id="landed_lcl_h_span"></span><input type="hidden" name="landed_lcl_h[]" id="landed_lcl_h'+containercntnew+'" value="" /></td></tr>';
	
	$("#tblcontaineritem tbody").append(tbl_html);
}

</script>

@endsection
