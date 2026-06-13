@php
	$quoteentry ??= null;
	$today_dt = $dtobj->toDateString();
	$today_time = $dtobj->toTimeString();
	
@endphp

<?php
$rowcnt = ($quoteentry) ? (($materialDetails->count() > 0) ? $materialDetails->count() : 0) : 10;
$fieldattr = ($quoteentry) ? 'DISABLED' : '';
$fielddisabl = ($quoteentry) ? 'DISABLED' : '';
?>
<div class="row">
		<div class="col-md-12" style="display: table;">
			<table class="table table-bordered" id="tblcontaineritem" style="width:100%;">
				<thead>
					<tr>
						<th class="oncent" style="width:2%;">Sr No.</th>
						<th class="oncent" style="width:20%;">Product Name</th>
						<th class="oncent" style="width:3%;">Unit Qty</th>
						<th class="oncent" style="width:4%;">Shipment Vol.(kg)/Qty</th>
						<th class="oncent" colspan="2" style="width:6%;">List Price</th>
						<th class="oncent" colspan="2" style="width:9%;">Discount</th>
						<th class="oncent" colspan="2" style="width:7%;">Unpacked Ex-Works Price</th>
						<th class="oncent" colspan="2" style="width:7%;">Packed Price</th>
						<th class="oncent" colspan="4" style="width:14%;">FOB Price</th>
						<th class="oncent" colspan="4" style="width:14%;">CIF Price</th>
						<th class="oncent" colspan="4" style="width:14%;">Landed Cost</th>
					</tr>
					<tr>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th class="oncent">-</th>
						<th class="oncent">INR</th>
						<th class="oncent">USD</th>
						<th class="oncent">Type</th>
						<th class="oncent" style="width:4%;">Disc. Value</th>
						<th class="oncent">INR</th>
						<th class="oncent">USD</th>
						<th class="oncent">FCL NH</th>
						<th class="oncent">Palletized</th>
						<th class="oncent">FCL NH</th>
						<th class="oncent">LCL NH</th>
						<th class="oncent">FCL H</th>
						<th class="oncent">LCL H</th>
						<th class="oncent">FCL NH</th>
						<th class="oncent">LCL NH</th>
						<th class="oncent">FCL H</th>
						<th class="oncent">LCL H</th>
						<th class="oncent">FCL NH</th>
						<th class="oncent">LCL NH</th>
						<th class="oncent">FCL H</th>
						<th class="oncent">LCL H</th>
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
							<input class="form-control onlynumbers onright tblinput" type="text" name="cmat_srno[]" id="cmat_srno<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->cmat_srno : '')) : $rowk2;?>" <?php echo $fieldattr;?> />
							<input type="hidden" name="container_id[]" id="container_id<?php echo $rowk2;?>" value="" />
						</td>
						<td class="backyellow">
							<input class="form-control tblinput prodcls backyellow" type="text" name="prodtag[]" id="prodtag<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->prodtag : '')) : '';?>" <?php echo $fieldattr;?> />
							
							<input type="hidden" name="prdcd[]" id="prdcd<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->prdcd : '')) : '';?>" />
							
							<input type="hidden" name="pack_size[]" id="pack_size<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->pack_size : '')) : '';?>" />
						</td>
						<td>
							<input class="form-control onlydecimalval onright tblinput" type="text" name="unit_qty[]" id="unit_qty<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->unit_qty : '')) : '';?>" <?php echo $fieldattr;?> onblur="setshiptoqty(<?php echo $rowk2;?>);" />
						</td>
						<td class="backyellow">
							<input class="form-control onlydecimalval onright tblinput shipvol backyellow" type="text" name="ship_vol_qty[]" id="ship_vol_qty<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->ship_vol_qty : '')) : '';?>" READONLY />
						</td>
						<td class="backyellow">
							<input class="form-control onlydecimalval onright tblinput backyellow" type="text" name="listprice_inr[]" id="listprice_inr<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->listprice_inr : '')) : '';?>" READONLY />
						</td>
						<td>
							
							<input class="form-control onlydecimalval onright tblinput" type="text" name="listprice_usd[]" id="listprice_usd<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->listprice_usd : '')) : '';?>" READONLY />
						</td>
						<td class="backyellow">
							
							<select style="width:100%;" class="backyellow" name="disc_type[]" id="disc_type<?php echo $rowk2;?>" onchange="setdisvalue(this.value,<?php echo $rowk2;?>);" <?php echo $fielddisabl;?>>
								<option value="">None</option>
								<?php
								
								$seldisc = ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->disc_type : '')) : '';
								if($disc_res->count() > 0)
								{
									foreach($disc_res as $discobj)
									{
										$sel1 = ($seldisc == $discobj->id) ? 'selected' : '';
										echo '<option value="'.$discobj->id.'" '.$sel1.'>'.$discobj->disc_type.'</option>';
									}
								}
								?>
							</select>
						</td>
						<td>
							<input class="form-control onlydecimalval onright tblinput" type="text" name="disc_val[]" id="disc_val<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->disc_val : '')) : '';?>"  />
						</td>
						<td class="backdarkgreen">
							
							<input class="form-control onlydecimalval onright tblinput backdarkgreen" type="text" name="unp_exwork_inr[]" id="unp_exwork_inr<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->unp_exwork_inr : '')) : '';?>" READONLY />
						</td>
						<td class="backdarkgreen">
							
							<input class="form-control onlydecimalval onright tblinput backdarkgreen" type="text" name="unp_exwork_usd[]" id="unp_exwork_usd<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->unp_exwork_usd : '')) : '';?>" READONLY />
						</td>
						<td class="backdarkgreen">
							
							<input class="form-control onlydecimalval onright tblinput backdarkgreen" type="text" name="pack_fcl_nh[]" id="pack_fcl_nh<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->pack_fcl_nh : '')) : '';?>" READONLY />
						</td>
						<td class="backlightgreen">
							
							<input class="form-control onlydecimalval onright tblinput backlightgreen" type="text" name="pack_lcl_pallet[]" id="pack_lcl_pallet<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? (($materialDetails[$k2]->pack_lcl_pallet == -1) ? 'Too Large' :$materialDetails[$k2]->pack_lcl_pallet ) : '')) : '';?>" READONLY/>
						</td>
						
						<td class="backdarkgreen">
							
							<input type="text" class="form-control onlydecimalval onright tblinput backdarkgreen" name="fob_fcl_nh[]" id="fob_fcl_nh<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->fob_fcl_nh : '')) : '';?>" READONLY/>
						</td>
						<td class="backlightgreen">
							<input type="text" class="form-control onlydecimalval onright tblinput backlightgreen" name="fob_lcl_nh_pl[]" id="fob_lcl_nh_pl<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? (($materialDetails[$k2]->fob_lcl_nh_pl == -1) ? 'Too Large' :$materialDetails[$k2]->fob_lcl_nh_pl ) : '')) : '';?>" READONLY/>
						</td>
						<td class="backdarkyellow">
							
							<input type="text" class="form-control onlydecimalval onright tblinput backdarkyellow" name="fob_fcl_h_pl[]" id="fob_fcl_h_pl<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->fob_fcl_h_pl : '')) : '';?>" />
						</td>
						<td class="backdarkred">
							
							<input type="text" class="form-control onlydecimalval onright tblinput backdarkred" name="fob_lcl_h_pl[]" id="fob_lcl_h_pl<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? (($materialDetails[$k2]->fob_lcl_h_pl == -1) ? 'Too Large' :$materialDetails[$k2]->fob_lcl_h_pl ) : '')) : '';?>" READONLY/>
						</td>
						
						<td class="backdarkgreen">
							<input type="text" class="form-control onlydecimalval onright tblinput backdarkgreen" name="cif_fcl_nh[]" id="cif_fcl_nh<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->cif_fcl_nh : '')) : '';?>" READONLY/>
						</td>
						<td class="backlightgreen">
							<input type="text" class="form-control onlydecimalval onright tblinput backlightgreen" name="cif_lcl_nh_pl[]" id="cif_lcl_nh_pl<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? (($materialDetails[$k2]->cif_lcl_nh_pl == -1) ? 'Too Large' :$materialDetails[$k2]->cif_lcl_nh_pl ) : '')) : '';?>" READONLY />
						</td>
						<td class="backdarkyellow">
							
							<input type="text" class="form-control onlydecimalval onright tblinput backdarkyellow" name="cif_fcl_h_pl[]" id="cif_fcl_h_pl<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->cif_fcl_h_pl : '')) : '';?>" READONLY/>
						</td>
						<td class="backdarkred">
							
							<input type="text" class="form-control onlydecimalval onright tblinput backdarkred" name="cif_lcl_h_pl[]" id="cif_lcl_h_pl<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? (($materialDetails[$k2]->cif_lcl_h_pl == -1) ? 'Too Large' :$materialDetails[$k2]->cif_lcl_h_pl ) : '')) : '';?>" READONLY />
						</td>
						
						<td class="backdarkgreen">
							
							<input type="text" class="form-control onlydecimalval onright tblinput backdarkgreen" name="landed_fcl_nh[]" id="landed_fcl_nh<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->landed_fcl_nh : '')) : '';?>" READONLY />
						</td>
						<td class="backlightgreen">
							
							<input type="text" class="form-control onlydecimalval onright tblinput backlightgreen" name="landed_lcl_nh_pl[]" id="landed_lcl_nh_pl<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? (($materialDetails[$k2]->landed_lcl_nh_pl == -1) ? 'Too Large' :$materialDetails[$k2]->landed_lcl_nh_pl ) : '')) : '';?>" READONLY />
						</td>
						<td class="backdarkyellow">
							
							<input type="text" class="form-control onlydecimalval onright tblinput backdarkyellow" name="landed_fcl_h_pl[]" id="landed_fcl_h_pl<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->landed_fcl_h_pl : '')) : '';?>" READONLY />
						</td>
						<td class="backdarkred">
							
							<input type="text" class="form-control onlydecimalval onright tblinput backdarkred" name="landed_lcl_h_pl[]" id="landed_lcl_h_pl<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? (($materialDetails[$k2]->landed_lcl_h_pl == -1) ? 'Too Large' :$materialDetails[$k2]->landed_lcl_h_pl ) : '')) : '';?>" READONLY/>
						</td>
						
					</tr>
				<?php
						}
					?>
					
					
					
				
				</tbody>
			</table>
			
			
			
		</div>
		<?php if((!$quoteentry)) { ?>
		<div class="col-md-3">
			<input type="hidden" id="containercnt" value="<?php echo $rowcnt;?>" />
			<a class="btn btn-primary" href="javascript:void(0);" id="addcontainerbtn" onclick="addmorecontainer();">Add More Row</a>
		</div>
		<?php } ?>
	</div>




