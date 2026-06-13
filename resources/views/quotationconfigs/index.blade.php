@extends('layouts.app')

@section('content')
<div class="container-fluid">
   
	<div class="row headerrow">
		<div class="col-md-12">
			<h6 class="frmhead">SetUp Configuration 
				<span style="float:right;">
					<form action="{{ route('quotationconfig.index') }}" method="GET">
						<select name="dealerid" id="dealerid" onchange="this.form.submit()">
							<option value="0" <?php ($dealerid == 0) ? 'selected' : '';?>>Customer</option>
							<?php
								if($dealers->count() > 0)
								{
									foreach($dealers as $dealobj)
									{
										$sel = ($dealerid == $dealobj->id) ? 'selected' : '';
										echo '<option value="'.$dealobj->id.'" '.$sel.'>'.$dealobj->dealer_name.'</option>';
									}
								}
							?>
						</select> 
					</form>
				</span>
			</h6>
		</div>
		
	</div>
	 @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
	@if ($errors->any())
		<div class="alert alert-danger">
			<ul class="mb-0">
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif
	
    <form action="{{ route('quotationconfig.store') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateconfigform();">
        @csrf
       <input type="hidden" id="dealerid2" name="dealerid2" value="0" />
		<?php
			$rowcnt = ($cfg_working_res->count() > 0) ? $cfg_working_res->count() : 1;
			$cfg_arr = $cfg_disc_arr = $cfg_fill = $cfg_fieldarr = array();
			if($cfg_working_res->count() > 0)
			{
				foreach($cfg_working_res as $x2 => $cfgobj)
				{
					$cfg_arr[] = $cfgobj;
				}
			}
			
			if($disc_res->count() > 0)
			{
				foreach($disc_res as $dobj)
				{
					$cfg_disc_arr[] = $dobj;
				}
			}
			
			if($fill_res->count() > 0)
			{
				foreach($fill_res as $fillobj)
				{
					$cfg_fill[] = $fillobj;
				}
			}
			
			if($fields_res->count() > 0)
			{
				foreach($fields_res as $fobj)
				{
					$cfg_fieldarr[] = $fobj;
				}
			}
			
		?>
<div class="row">
		<div class="col-md-12" style="display: table;">
			<table class="table table-bordered" id="tblcontaineritem" style="width:100%;">
				<thead>
					<tr>
						<th class="oncent" style="width:4%;">Shipment Type</th>
						<th class="oncent" style="width:6%;">CargoType</th>
						<th class="oncent" style="width:4%;">Palletized</th>
						<th class="oncent" style="width:4%;">Weight</th>
						<th class="oncent" style="width:5%;">Transport</th>
						<th class="oncent" style="width:5%;">Forwarding</th>
						<th class="oncent" style="width:5%;">Freight</th>
						<th class="oncent" style="width:5%;">IndHAZ</th>
						<th class="oncent" style="width:5%;">Lashing</th>
						<th class="oncent" style="width:5%;">DelO</th>
						<th class="oncent" style="width:5%;">Customs</th>
						<th class="oncent" style="width:5%;">THC</th>
						<th class="oncent" style="width:5%;">GatePass</th>
						<th class="oncent" style="width:5%;">Insp</th>
						<th class="oncent" style="width:5%;">UAEHaz</th>
						<th class="oncent" style="width:5%;">Transport2</th>
						<th class="oncent" style="width:15%;">Readable Desc</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					
						//foreach($containerDetails as $k2 => $cobj)
						for($k2=0;$k2<$rowcnt;$k2++)
						{
							$rowk2 = $k2 + 1;
					?>
					<tr id="controw<?php echo $rowk2;?>">
						<td>
							
							<select style="width:100%;" class="" name="shipment_type[]" id="shipment_type<?php echo $rowk2;?>">
								
								<?php
								
								if($shiptype_res->count() > 0)
								{
									foreach($shiptype_res as $spobj)
									{
										$shipment_typeval = isset($cfg_arr[$k2]) ? $cfg_arr[$k2]->ShipmentType : '';
										$sel1 = ($shipment_typeval == $spobj->shipment_type) ? 'selected' : '';
										
										echo '<option value="'.$spobj->shipment_type.'" '.$sel1.'>'.$spobj->shipment_type.'</option>';
									}
								}
								?>
							</select>
						</td>
						<td>
							<select style="width:100%;" class="" name="cargo_type[]" id="cargo_type<?php echo $rowk2;?>">
								
								<?php
								
								if($cargotype_res->count() > 0)
								{
									foreach($cargotype_res as $cgobj)
									{
										$cargo_typeval = isset($cfg_arr[$k2]) ? $cfg_arr[$k2]->CargoType : '';
										$sel1 = ($cargo_typeval == $cgobj->cargo_type) ? 'selected' : '';
										echo '<option value="'.$cgobj->cargo_type.'" '.$sel1.'>'.$cgobj->cargo_type.'</option>';
									}
								}
								?>
							</select>
						</td>
						<td>
							<select style="width:100%;" class="" name="Palletized[]" id="Palletized<?php echo $rowk2;?>">
								
								<?php
								$palletized_arr = array('False','True');
								
								foreach($palletized_arr as $kk => $vv)
								{
									$Palletizedval = isset($cfg_arr[$k2]) ? $cfg_arr[$k2]->Palletized : '';
									$sel1 = ($Palletizedval == $kk) ? 'selected' : '';
									echo '<option value="'.$kk.'" '.$sel1.'>'.$vv.'</option>';
								}
								
								?>
							</select>
						</td>
						<td>
							<input class="form-control onlydecimalval onright tblinput" type="text" name="Weight[]" id="Weight<?php echo $rowk2;?>" value="<?php echo isset($cfg_arr[$k2]) ? $cfg_arr[$k2]->Weight : ''; ?>" onblur="" />
						</td>
						<td class="flex-container">
							<span class="flex-item">&#8377;</span>
							<input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="Transport[]" id="Transport<?php echo $rowk2;?>" value="<?php echo isset($cfg_arr[$k2]) ? $cfg_arr[$k2]->Transport : ''; ?>" onblur="" />
						</td>
						<td class="flex-container">
							<span class="flex-item">&#8377;</span>
							<input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="Forwarding[]" id="Forwarding<?php echo $rowk2;?>" value="<?php echo isset($cfg_arr[$k2]) ? $cfg_arr[$k2]->Forwarding : ''; ?>" onblur="" />
						</td>
						<td class="flex-container">
							<span class="flex-item">&#36;</span>
							<input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="Freight[]" id="Freight<?php echo $rowk2;?>" value="<?php echo isset($cfg_arr[$k2]) ? $cfg_arr[$k2]->Freight : ''; ?>" onblur="" />
						</td>
						<td class="flex-container">
							<span class="flex-item">&#36;</span>
							<input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="IndHAZ[]" id="IndHAZ<?php echo $rowk2;?>" value="<?php echo isset($cfg_arr[$k2]) ? $cfg_arr[$k2]->IndHAZ : ''; ?>" onblur="" />
						</td>
						<td class="flex-container">
							<span class="flex-item">&#8377;</span>
							<input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="Lashing[]" id="Lashing<?php echo $rowk2;?>" value="<?php echo isset($cfg_arr[$k2]) ? $cfg_arr[$k2]->Lashing : ''; ?>" onblur="" />
						</td>
						<td class="flex-container">
							<span class="flex-item">&#36;</span>
							<input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="DelO[]" id="DelO<?php echo $rowk2;?>" value="<?php echo isset($cfg_arr[$k2]) ? $cfg_arr[$k2]->DelO : ''; ?>" onblur="" />
						</td>
						<td class="flex-container">
							<span class="flex-item">&#36;</span>
							<input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="Customs[]" id="Customs<?php echo $rowk2;?>" value="<?php echo isset($cfg_arr[$k2]) ? $cfg_arr[$k2]->Customs : ''; ?>" onblur="" />
						</td>
						<td class="flex-container">
							<span class="flex-item">&#36;</span>
							<input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="THC[]" id="THC<?php echo $rowk2;?>" value="<?php echo isset($cfg_arr[$k2]) ? $cfg_arr[$k2]->THC : ''; ?>" onblur="" />
						</td>
						<td class="flex-container">
							<span class="flex-item">&#36;</span>
							<input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="GatePass[]" id="GatePass<?php echo $rowk2;?>" value="<?php echo isset($cfg_arr[$k2]) ? $cfg_arr[$k2]->GatePass : ''; ?>" onblur="" />
						</td>
						<td class="flex-container">
							<span class="flex-item">&#36;</span>
							<input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="Insp[]" id="Insp<?php echo $rowk2;?>" value="<?php echo isset($cfg_arr[$k2]) ? $cfg_arr[$k2]->Insp : ''; ?>" onblur="" />
						</td>
						<td class="flex-container">
							<span class="flex-item">&#36;</span>
							<input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="UAEHaz[]" id="UAEHaz<?php echo $rowk2;?>" value="<?php echo isset($cfg_arr[$k2]) ? $cfg_arr[$k2]->UAEHaz : ''; ?>" onblur="" />
						</td>
						<td class="flex-container">
							<span class="flex-item">&#36;</span>
							<input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="Transport2[]" id="Transport2<?php echo $rowk2;?>" value="<?php echo isset($cfg_arr[$k2]) ? $cfg_arr[$k2]->Transport2 : ''; ?>" onblur="" />
						</td>
						<td id="readabledesc<?php echo $rowk2;?>">
							<input class="form-control onlydecimalval onright tblinput" type="text" name="ReadableDesc[]" id="ReadableDesc<?php echo $rowk2;?>" value="<?php echo isset($cfg_arr[$k2]) ? $cfg_arr[$k2]->ReadableDesc : ''; ?>" onblur="" />
						</td>
					</tr>
						<?php } ?>
				</tbody>
			</table>
		</div>
		<div class="col-md-3">
			<input type="hidden" id="containercnt" value="<?php echo $rowcnt;?>" />
			<a class="btn btn-primary" href="javascript:void(0);" id="addcontainerbtn" onclick="addmorecontainer();">Add More Row</a>
		</div>
		
		<div class="row headerrow">
			<div class="col-md-6">
				<h6 class="frmhead">Discounts:</h6>
			</div>
				
		</div>
		
		<div class="row">
		
			<div class="col-md-3" style="display: table;">
				<table class="table table-bordered" id="tblcontaineritem2" style="width:100%;">
					<thead>
						<tr>
							<th>Type</th>
							<th>Discount</th>
							<th>Margin</th>
						</tr>
					</thead>
					<tbody>
						<?php
							if($disc_res->count() > 0)
							{
								foreach($disc_res as $k3=>$discobj)
								{
									$rowk3 = $k3 + 1;
									?>
									<tr id="controw<?php echo $rowk3;?>">
										<td>
											<input class="form-control tblinput" type="text" name="disc_type[]" id="disc_type<?php echo $rowk3;?>" value="<?php echo isset($cfg_disc_arr[$k3]) ? $cfg_disc_arr[$k3]->disc_type : ''; ?>" onblur="" />
										</td>
										<td>
											<input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="disc_val[]" id="disc_val<?php echo $rowk3;?>" value="<?php echo isset($cfg_disc_arr[$k3]) ? $cfg_disc_arr[$k3]->disc_val : ''; ?>" onblur="" /> %
										</td>
										<td>
											<input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="disc_margin[]" id="disc_margin<?php echo $rowk3;?>" value="<?php echo isset($cfg_disc_arr[$k3]) ? $cfg_disc_arr[$k3]->disc_margin : ''; ?>" onblur="" /> %
										</td>
									</tr>
									<?php
								}
							}
						?>
					</tbody>
				</table>
			</div>
			
			<div class="col-md-3" style="display: none;">
				<table class="table table-bordered" id="tblcontaineritem3" style="width:100%;">
					<thead>
						<tr>
							<th>Filling</th>
							<th>Type</th>
							<th>Column</th>
						</tr>
					</thead>
					<tbody>
						<?php
							if($fill_res->count() > 0)
							{
								foreach($fill_res as $k4=>$fillobj)
								{
									$rowk4 = $k4 + 1;
									?>
									<tr id="controw<?php echo $rowk4;?>">
										<td>
											<input class="form-control tblinput" type="text" name="para_name[]" id="para_name<?php echo $rowk4;?>" value="<?php echo isset($cfg_fill[$k4]) ? $cfg_fill[$k4]->para_name : ''; ?>" onblur="" />
										</td>
										<td>
											<input class="form-control tblinput" type="text" name="para_type[]" id="para_type<?php echo $rowk4;?>" value="<?php echo isset($cfg_fill[$k4]) ? $cfg_fill[$k4]->para_type : ''; ?>" onblur="" />
										</td>
										<td>
											<input class="form-control tblinput" type="text" name="para_value[]" id="para_value<?php echo $rowk4;?>" value="<?php echo isset($cfg_fill[$k4]) ? $cfg_fill[$k4]->para_value : ''; ?>" onblur="" />
										</td>
									</tr>
									<?php
								}
							}
						?>
					</tbody>
				</table>
			</div>
			
			<div class="col-md-3" style="display: table;">
				<table class="table table-bordered" id="tblcontaineritem4" style="width:100%;">
					<thead>
						<tr>
							<th>Field</th>
							<th>Value</th>
						</tr>
					</thead>
					<tbody>
						<?php
							if($fields_res->count() > 0)
							{
								foreach($fields_res as $k4=>$feldobj)
								{
									if($k4 <= 4) 
									{
									$rowk4 = $k4 + 1;
									?>
									<tr id="controw<?php echo $rowk4;?>">
										<td>
											<input class="form-control tblinput" type="text" name="fpara_lbl[]" id="fpara_lbl<?php echo $rowk4;?>" value="<?php echo isset($cfg_fieldarr[$k4]) ? $cfg_fieldarr[$k4]->para_lbl : ''; ?>" onblur="" />
											<input type="hidden" name="fpara_name[]" id="fpara_name<?php echo $rowk4;?>" value="<?php echo isset($cfg_fieldarr[$k4]) ? $cfg_fieldarr[$k4]->para_name : ''; ?>" onblur="" />
										</td>
										
										<td>
											<input class="form-control onlydecimalval onright tblinput" type="text" name="fpara_value[]" id="para_value<?php echo $rowk4;?>" value="<?php echo isset($cfg_fieldarr[$k4]) ? $cfg_fieldarr[$k4]->para_value : ''; ?>" onblur="" />
										</td>
									</tr>
									<?php
									}
								}
							}
						?>
					</tbody>
				</table>
			</div>
			
			<div class="col-md-3" style="display: table;">
				<table class="table table-bordered" id="tblcontaineritem5" style="width:100%;">
					<thead>
						<tr>
							<th style="width:70%;">Field</th>
							<th>Value</th>
						</tr>
					</thead>
					<tbody>
						<?php
							if($fields_res->count() > 0)
							{
								foreach($fields_res as $k4=>$feldobj)
								{
									if($k4 > 4) 
									{
									$rowk4 = $k4 + 1;
									?>
									<tr id="controw<?php echo $rowk4;?>">
										<td>
											<input class="form-control tblinput" type="text" name="fpara_lbl[]" id="fpara_lbl<?php echo $rowk4;?>" value="<?php echo isset($cfg_fieldarr[$k4]) ? $cfg_fieldarr[$k4]->para_lbl : ''; ?>" onblur="" />
											<input type="hidden" name="fpara_name[]" id="fpara_name<?php echo $rowk4;?>" value="<?php echo isset($cfg_fieldarr[$k4]) ? $cfg_fieldarr[$k4]->para_name : ''; ?>" onblur="" />
										</td>
										
										<td>
											<input class="form-control onlydecimalval onright tblinput" type="text" name="fpara_value[]" id="fpara_value<?php echo $rowk4;?>" value="<?php echo isset($cfg_fieldarr[$k4]) ? $cfg_fieldarr[$k4]->para_value : ''; ?>" onblur="" />
										</td>
									</tr>
									<?php
									}
								}
							}
						?>
					</tbody>
				</table>
			</div>
			
		</div>
	
		<div class="row" style="margin-top:10px;">
			<div class="col-md-4"></div>
			<div class="col-md-4">
				<button type="submit" class="btn btn-success" id="savebtn1">Save Config</button>
				<a href="{{ route('quotationconfig.index') }}" class="btn btn-danger">Cancel</a>
				
			</div>
		</div>
    </form>
</div>

<script>

window.onload = function() {
  
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

function validateconfigform()
{
	var dealerid = $("#dealerid").val();
	$("#dealerid2").val(dealerid);
	return true;
}

function addmorecontainer()
{
	var containercnt = $("#containercnt").val();
	var containercntnew = parseInt(containercnt) + 1;
	$("#containercnt").val(containercntnew);
		
	var tbl_html = '<tr id="controw'+containercntnew+'"><td><select style="width:100%;" class="" name="shipment_type[]" id="shipment_type'+containercntnew+'">';
	
	<?php
		
		if($shiptype_res->count() > 0)
		{
			foreach($shiptype_res as $shipobj)
			{
				//$sel1 = ($srch_disctype == $discobj->id) ? 'selected' : '';
				$tmpoption = '<option value="'.$shipobj->shipment_type.'">'.$shipobj->shipment_type.'</option>';
			?>
			
			
				tbl_html += '<?php echo $tmpoption;?>';
			<?php
			}
		}
		?>
	
	tbl_html += '</select></td><td><select style="width:100%;" class="" name="cargo_type[]" id="cargo_type'+containercntnew+'">';
	
	<?php
		
		if($cargotype_res->count() > 0)
		{
			foreach($cargotype_res as $cgobj)
			{
				//$sel1 = ($srch_disctype == $discobj->id) ? 'selected' : '';
				$tmpoption = '<option value="'.$cgobj->cargo_type.'">'.$cgobj->cargo_type.'</option>';
			?>
			
			
				tbl_html += '<?php echo $tmpoption;?>';
			<?php
			}
		}
		?>
	
	tbl_html += '</select></td><td><select style="width:100%;" class="" name="Palletized[]" id="Palletized'+containercntnew+'">';
	
	<?php
				
		foreach($palletized_arr as $kk => $vv)
		{
			//$sel1 = ($srch_disctype == $discobj->id) ? 'selected' : '';
			$tmpoption = '<option value="'.$kk.'">'.$vv.'</option>';
			?>
			
				tbl_html += '<?php echo $tmpoption;?>';
			<?php
		}
	
	?>
	
	tbl_html += '</select></td><td class="flex-container"><input class="form-control onlydecimalval onright tblinput" type="text" name="Weight[]" id="Weight'+containercntnew+'" value="" /></td><td class="flex-container"><span class="flex-item">&#8377;</span><input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="Transport[]" id="Transport'+containercntnew+'" value="" /></td><td class="flex-container"><span class="flex-item">&#8377;</span><input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="Forwarding[]" id="Forwarding'+containercntnew+'" value="" /></td><td class="flex-container"><span class="flex-item">&#36;</span><input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="Freight[]" id="Freight'+containercntnew+'" value="" /></td><td class="flex-container"><span class="flex-item">&#36;</span><input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="IndHAZ[]" id="IndHAZ'+containercntnew+'" value="" /></td><td class="flex-container"><span class="flex-item">&#8377;</span><input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="Lashing[]" id="Lashing'+containercntnew+'" value="" /></td><td class="flex-container"><span class="flex-item">&#36;</span><input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="DelO[]" id="DelO'+containercntnew+'" value="" /></td><td class="flex-container"><span class="flex-item">&#36;</span><input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="Customs[]" id="Customs'+containercntnew+'" value="" /></td><td class="flex-container"><span class="flex-item">&#36;</span><input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="THC[]" id="THC'+containercntnew+'" value="" /></td><td class="flex-container"><span class="flex-item">&#36;</span><input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="GatePass[]" id="GatePass'+containercntnew+'" value="" /></td><td class="flex-container"><span class="flex-item">&#36;</span><input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="Insp[]" id="Insp'+containercntnew+'" value="" /></td><td class="flex-container"><span class="flex-item">&#36;</span><input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="UAEHaz[]" id="UAEHaz'+containercntnew+'" value="" /></td><td class="flex-container"><span class="flex-item">&#36;</span><input class="form-control onlydecimalval onright tblinput withcurrency flex-item" type="text" name="Transport2[]" id="Transport2'+containercntnew+'" value="" /></td><td><input class="form-control onlydecimalval onright tblinput" type="text" name="ReadableDesc[]" id="ReadableDesc'+containercntnew+'" value="" /></td></tr>';
	
	$("#tblcontaineritem tbody").append(tbl_html);
}

</script>

@endsection
