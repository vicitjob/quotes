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
<div class="row" style="margin-top:10px;">
		<div class="col-md-10" style="display: table;">
			<table class="table table-bordered" id="tblcontaineritem" style="width:90%;">
				<thead>
					<tr>
						<th class="oncent" style="width:2%;">Sr No.</th>
						<th class="oncent" style="width:20%;">Product Name</th>
						<th class="oncent" style="width:5%;">Unit Qty</th>
						<th class="oncent" style="width:4%;">Shipment Vol.(kg)/Qty</th>
						<th class="oncent" colspan="2" style="width:8%;">List Price Per ltr/kg</th>
						<th class="oncent" colspan="2" style="width:8%;">List Price (Pack)</th>
						<th class="oncent" colspan="2" style="width:12%;">Discount</th>
						<th class="oncent colexworks" colspan="2" style="width:8%;">Unpacked Ex-Works Price</th>
						<th class="oncent colpacked" style="width:7%;">Packed Price (<span class="currtype"><?php echo $currency_type;?></span>)</th>
						<th class="oncent colfob" style="width:7%;">FOB Price (<span class="currtype"><?php echo $currency_type;?></span>)</th>
						<th class="oncent colcif" style="width:7%;">CIF Price (<span class="currtype"><?php echo $currency_type;?></span>)</th>
						<th class="oncent collanded" style="width:14%;">Landed Cost (<span class="currtype"><?php echo $currency_type;?></span>)</th>
						<th class="oncent recomdistsptobuyer" style="width:6%;">Recom.Distr. S.P. to Buyer</th>
						<th class="oncent" style="width:6%;">Recom SP after Credit & MiniMan</th>
					</tr>
					<tr>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th>&nbsp;</th>
						<th class="oncent"><span id="maxshipvol"><?php echo $maxshipvol;?></span></th>
						<th class="oncent">INR</th>
						<th class="oncent"><span class="currtype"><?php echo $currency_type;?></span></th>
						<th class="oncent">INR</th>
						<th class="oncent"><span class="currtype"><?php echo $currency_type;?></span></th>
						<th class="oncent">Type</th>
						<th class="oncent" style="width:5%;">Disc. Value (%)</th>
						<th class="oncent colexworks">INR</th>
						<th class="oncent colexworks"><span class="currtype"><?php echo $currency_type;?></span></th>
						<th class="oncent colpackedcolFCLNH">FCL NH</th>
						<th class="oncent colpackedcolLCLNH">Palletized</th>
						
						<th class="oncent colfobcolFCLNH">FCL NH</th>
						<th class="oncent colfobcolLCLNH">LCL NH</th>
						<th class="oncent colfobcolFCLH">FCL H</th>
						<th class="oncent colfobcolLCLH">LCL H</th>
						
						<th class="oncent colcifcolFCLNH">FCL NH</th>
						<th class="oncent colcifcolLCLNH">LCL NH</th>
						<th class="oncent colcifcolFCLH">FCL H</th>
						<th class="oncent colcifcolLCLH">LCL H</th>
						
						<th class="oncent collandedcolFCLNH">FCL NH</th>
						<th class="oncent collandedcolLCLNH">LCL NH</th>
						<th class="oncent collandedcolFCLH">FCL H</th>
						<th class="oncent collandedcolLCLH">LCL H</th>
						
						<th class="oncent recomdistsptobuyer"><span class="currtype"><?php echo $currency_type;?></span></th>
						<!--th class="oncent">AED</th-->
						<th class="oncent"><span class="currtype"><?php echo $currency_type;?></span></th>
						<!--th class="oncent">AED</th-->
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
							<span class="floatonright tblinput backyellow" id="ship_vol_qty_span<?php echo $rowk2;?>"><?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->ship_vol_qty : '')) : '';?></span>
							<input class="shipvol" type="hidden" name="ship_vol_qty[]" id="ship_vol_qty<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->ship_vol_qty : '')) : '';?>" READONLY />
						</td>
						<td class="backyellow">
							<span class="floatonright tblinput backyellow" id="listprice_inr_unit_span<?php echo $rowk2;?>"><?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->listprice_inr_unit : '')) : '';?></span>
							<input type="hidden" name="listprice_inr_unit[]" id="listprice_inr_unit<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->listprice_inr_unit : '')) : '';?>" READONLY />
						</td>
						<td>
							<span class="floatonright tblinput backyellow currencyspan" id="listprice_usd_unit_span<?php echo $rowk2;?>"><?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->listprice_usd_unit : '')) : '';?></span>
							<input class="currencyinp" type="hidden" name="listprice_usd_unit[]" id="listprice_usd_unit<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->listprice_usd_unit : '')) : '';?>" READONLY />
						</td>
						<td class="backyellow">
							<span class="floatonright tblinput backyellow" id="listprice_inr_span<?php echo $rowk2;?>"><?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->listprice_inr : '')) : '';?></span>
							<input type="hidden" name="listprice_inr[]" id="listprice_inr<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->listprice_inr : '')) : '';?>" READONLY />
						</td>
						<td>
							<span class="floatonright tblinput backyellow currencyspan" id="listprice_usd_span<?php echo $rowk2;?>"><?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->listprice_usd : '')) : '';?></span>
							<input class="currencyinp" type="hidden" name="listprice_usd[]" id="listprice_usd<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->listprice_usd : '')) : '';?>" READONLY />
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
							<input class="form-control onlydecimalval onright tblinput" type="text" name="disc_val[]" id="disc_val<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->disc_val : '')) : '';?>" onchange="setshiptoqty(<?php echo $rowk2;?>);" />
						</td>
						<td class="backdarkgreen colexworks">
							<span class="floatonright tblinput backdarkgreen" id="unp_exwork_inr_span<?php echo $rowk2;?>"><?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->unp_exwork_inr : '')) : '';?></span>
							
							<input type="hidden" name="unp_exwork_inr[]" id="unp_exwork_inr<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->unp_exwork_inr : '')) : '';?>" READONLY />
						</td>
						<td class="backdarkgreen colexworks">
							
							<span class="floatonright tblinput backdarkgreen currencyspan" id="unp_exwork_usd_span<?php echo $rowk2;?>"><?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->unp_exwork_usd : '')) : '';?></span>
							
							<input class="currencyinp" type="hidden" name="unp_exwork_usd[]" id="unp_exwork_usd<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->unp_exwork_usd : '')) : '';?>" READONLY />
						</td>
						<td class="backdarkgreen colpackedcolFCLNH">
							<span class="floatonright tblinput backdarkgreen currencyspan" id="pack_fcl_nh_span<?php echo $rowk2;?>"><?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->pack_fcl_nh : '')) : '';?></span>
							
							<input class="currencyinp" type="hidden" name="pack_fcl_nh[]" id="pack_fcl_nh<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->pack_fcl_nh : '')) : '';?>" READONLY />
						</td>
						<td class="backlightgreen colpackedcolLCLNH">
							<span class="floatonright tblinput backlightgreen currencyspan" id="pack_lcl_pallet_span<?php echo $rowk2;?>"><?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? (($materialDetails[$k2]->pack_lcl_pallet == -1) ? 'Too Large' :$materialDetails[$k2]->pack_lcl_pallet ) : '')) : '';?></span>
							
							<input class="currencyinp" type="hidden" name="pack_lcl_pallet[]" id="pack_lcl_pallet<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? (($materialDetails[$k2]->pack_lcl_pallet == -1) ? 'Too Large' :$materialDetails[$k2]->pack_lcl_pallet ) : '')) : '';?>" READONLY/>
						</td>
						
						<td class="backdarkgreen colfobcolFCLNH">
							<span class="floatonright tblinput backdarkgreen currencyspan" id="fob_fcl_nh_span<?php echo $rowk2;?>"><?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->fob_fcl_nh : '')) : '';?></span>
							
							<input class="currencyinp" type="hidden" name="fob_fcl_nh[]" id="fob_fcl_nh<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->fob_fcl_nh : '')) : '';?>" READONLY/>
						</td>
						<td class="backlightgreen colfobcolLCLNH">
							<span class="floatonright tblinput backdarkgreen currencyspan" id="fob_lcl_nh_pl_span<?php echo $rowk2;?>"><?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? (($materialDetails[$k2]->fob_lcl_nh_pl == -1) ? 'Too Large' :$materialDetails[$k2]->fob_lcl_nh_pl ) : '')) : '';?></span>
						
							<input class="currencyinp" type="hidden" name="fob_lcl_nh_pl[]" id="fob_lcl_nh_pl<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? (($materialDetails[$k2]->fob_lcl_nh_pl == -1) ? 'Too Large' :$materialDetails[$k2]->fob_lcl_nh_pl ) : '')) : '';?>" READONLY/>
						</td>
						<td class="backdarkyellow colfobcolFCLH">
							<span class="floatonright tblinput backdarkyellow currencyspan" id="fob_fcl_h_pl_span<?php echo $rowk2;?>"><?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->fob_fcl_h_pl : '')) : '';?></span>
							
							<input class="currencyinp" type="hidden" name="fob_fcl_h_pl[]" id="fob_fcl_h_pl<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->fob_fcl_h_pl : '')) : '';?>" />
						</td>
						<td class="backdarkred colfobcolLCLH">
							<span class="floatonright tblinput backdarkred currencyspan" id="fob_lcl_h_pl_span<?php echo $rowk2;?>"><?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? (($materialDetails[$k2]->fob_lcl_h_pl == -1) ? 'Too Large' :$materialDetails[$k2]->fob_lcl_h_pl ) : '')) : '';?></span>
							
							<input class="currencyinp" type="hidden" name="fob_lcl_h_pl[]" id="fob_lcl_h_pl<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? (($materialDetails[$k2]->fob_lcl_h_pl == -1) ? 'Too Large' :$materialDetails[$k2]->fob_lcl_h_pl ) : '')) : '';?>" READONLY/>
						</td>
						
						<td class="backdarkgreen colcifcolFCLNH">
							<span class="floatonright tblinput backdarkgreen currencyspan" id="cif_fcl_nh_span<?php echo $rowk2;?>"><?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->cif_fcl_nh : '')) : '';?></span>
						
							<input class="currencyinp" type="hidden" name="cif_fcl_nh[]" id="cif_fcl_nh<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->cif_fcl_nh : '')) : '';?>" READONLY/>
						</td>
						<td class="backlightgreen colcifcolLCLNH">
							<span class="floatonright tblinput backlightgreen currencyspan" id="cif_lcl_nh_pl_span<?php echo $rowk2;?>"><?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? (($materialDetails[$k2]->cif_lcl_nh_pl == -1) ? 'Too Large' :$materialDetails[$k2]->cif_lcl_nh_pl ) : '')) : '';?></span>
							
							<input class="currencyinp" type="hidden" name="cif_lcl_nh_pl[]" id="cif_lcl_nh_pl<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? (($materialDetails[$k2]->cif_lcl_nh_pl == -1) ? 'Too Large' :$materialDetails[$k2]->cif_lcl_nh_pl ) : '')) : '';?>" READONLY />
						</td>
						<td class="backdarkyellow colcifcolFCLH">
							<span class="floatonright tblinput backdarkyellow currencyspan" id="cif_fcl_h_pl_span<?php echo $rowk2;?>"><?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->cif_fcl_h_pl : '')) : '';?></span>
							
							<input class="currencyinp" type="hidden" name="cif_fcl_h_pl[]" id="cif_fcl_h_pl<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->cif_fcl_h_pl : '')) : '';?>" READONLY/>
						</td>
						<td class="backdarkred colcifcolLCLH">
							<span class="floatonright tblinput backdarkred currencyspan" id="cif_lcl_h_pl_span<?php echo $rowk2;?>"><?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? (($materialDetails[$k2]->cif_lcl_h_pl == -1) ? 'Too Large' :$materialDetails[$k2]->cif_lcl_h_pl ) : '')) : '';?></span>
							
							<input class="currencyinp" type="hidden" name="cif_lcl_h_pl[]" id="cif_lcl_h_pl<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? (($materialDetails[$k2]->cif_lcl_h_pl == -1) ? 'Too Large' :$materialDetails[$k2]->cif_lcl_h_pl ) : '')) : '';?>" READONLY />
						</td>
						
						<td class="backdarkgreen collandedcolFCLNH">
							<span class="floatonright tblinput backdarkgreen currencyspan" id="landed_fcl_nh_span<?php echo $rowk2;?>"><?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->landed_fcl_nh : '')) : '';?></span>
							
							<input class="currencyinp" type="hidden" name="landed_fcl_nh[]" id="landed_fcl_nh<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->landed_fcl_nh : '')) : '';?>" READONLY />
						</td>
						<td class="backlightgreen collandedcolLCLNH">
							<span class="floatonright tblinput backlightgreen currencyspan" id="landed_lcl_nh_pl_span<?php echo $rowk2;?>"><?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? (($materialDetails[$k2]->landed_lcl_nh_pl == -1) ? 'Too Large' :$materialDetails[$k2]->landed_lcl_nh_pl ) : '')) : '';?></span>
							
							<input class="currencyinp" type="hidden" name="landed_lcl_nh_pl[]" id="landed_lcl_nh_pl<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? (($materialDetails[$k2]->landed_lcl_nh_pl == -1) ? 'Too Large' :$materialDetails[$k2]->landed_lcl_nh_pl ) : '')) : '';?>" READONLY />
						</td>
						<td class="backdarkyellow collandedcolFCLH">
							<span class="floatonright tblinput backdarkyellow currencyspan" id="landed_fcl_h_pl_span<?php echo $rowk2;?>"><?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->landed_fcl_h_pl : '')) : '';?></span>
							
							<input class="currencyinp" type="hidden" name="landed_fcl_h_pl[]" id="landed_fcl_h_pl<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->landed_fcl_h_pl : '')) : '';?>" READONLY />
						</td>
						<td class="backdarkred collandedcolLCLH">
							<span class="floatonright tblinput backdarkred currencyspan" id="landed_lcl_h_pl_span<?php echo $rowk2;?>"><?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? (($materialDetails[$k2]->landed_lcl_h_pl == -1) ? 'Too Large' :$materialDetails[$k2]->landed_lcl_h_pl ) : '')) : '';?></span>
							
							<input class="currencyinp" type="hidden" name="landed_lcl_h_pl[]" id="landed_lcl_h_pl<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? (($materialDetails[$k2]->landed_lcl_h_pl == -1) ? 'Too Large' :$materialDetails[$k2]->landed_lcl_h_pl ) : '')) : '';?>" READONLY/>
						</td>
						<td class="recomdistsptobuyer">
							
							<span class="floatonright tblinput backdarkyellow currencyspan" id="recom_dis_sp_to_buyer_span<?php echo $rowk2;?>"><?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->recom_dis_sp_to_buyer : '')) : '';?></span>
							
							<input class="currencyinp" type="hidden" name="recom_dis_sp_to_buyer[]" id="recom_dis_sp_to_buyer<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->recom_dis_sp_to_buyer : '')) : '';?>" READONLY />
							
						</td>
						<!--td>&nbsp;</td-->
						<td>
														
							<span class="floatonright tblinput backdarkyellow currencyspan" id="recom_sp_aft_credit_miniman_span<?php echo $rowk2;?>"><?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->recom_sp_aft_credit_miniman : '')) : '';?></span>
							
							<input class="currencyinp" type="hidden" name="recom_sp_aft_credit_miniman[]" id="recom_sp_aft_credit_miniman<?php echo $rowk2;?>" value="<?php echo ($quoteentry) ? ((isset($materialDetails[$k2]) ? $materialDetails[$k2]->recom_sp_aft_credit_miniman : '')) : '';?>" READONLY />
							
						</td>
						<!--td>&nbsp;</td-->
						
					</tr>
				<?php
						}
					?>
					
					
					
				
				</tbody>
			</table>
			
			
			
		</div>
		<div class="col-md-2">
			<h6 style="text-decoration:underline;">Current Exchange Rate</h6>
			<label class="col-md-12 lblcommon" style="font-weight:600;">INR/USD: <span id="exchange_inr_usd"><?php echo $inr_usd;?></span></label><br/>
			<label class="col-md-12 lblcommon" style="font-weight:600;">AED/USD: <span id="exchange_inr_aed"><?php echo $aed_usd;?></span></label>
		</div>
		
		<?php if((!$quoteentry)) { ?>
		<div class="col-md-3" style="display:none;">
			<input type="hidden" id="containercnt" value="<?php echo $rowcnt;?>" />
			<a class="btn btn-primary" href="javascript:void(0);" id="addcontainerbtn" onclick="addmorecontainer();">Add More Row</a>
		</div>
		<?php } ?>
	</div>




