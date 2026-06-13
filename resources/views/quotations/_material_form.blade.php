
<div class="row">
	<div id="tbloverlay">
	  <div class="cv-spinner">
		<span class="spinner"></span>
	  </div>
	</div>
	<div class="col-md-1 dispflex">
		<input type="checkbox" id="materialselectall" value="1" /> <span style="margin-left:5px;">Select All</span>
	</div>
	<div class="col-md-2 dispflex">
		<a href="javascript:void(0);" class="btn btn-danger" style="padding:1px;font-size:12px;" id="removeselectedmat" onclick="removeselectedmat();">Remove Selected</a>
	</div>
	<div class="col-md-3 dispflex">
        <label class="col-md-4 lblcommon"><i class="fa-solid fa-book iconthemecolor"></i> Invoice No.</label>
		<div class="col-md-6">
			<input type="text" name="invoice_no" id="invoice_no" class="form-control" value="{{ old('invoice_no', $gateentry->invoice_no ?? '') }}" >
		</div>
    </div>
	<div class="col-md-12" style="display: table;">
	<?php
		$rowcnt = !empty($gateentry) ? (($gateentry->status >= 1) ? $materialDetails->count() : (($materialDetails->count() > 10) ? $materialDetails->count() : 10)) : 10;
		$roweditcnt = !empty($gateentry) ? $materialDetails->count() : 0;
	?>
		<input type="hidden" id="roweditcnt" value="<?php echo $roweditcnt;?>" />
		<table class="table table-bordered" id="tblbatchprod" style="width:100%;">
			<thead>
				<tr>	
					<th style="width:3%;">&nbsp;</th>
					<th style="width:5%;">Sr No.</th>
					<th style="width:10%;">Material Code</th>
					<th>Material Desc.</th>
					<th style="width:7%;">PO/CHLN Qty</th>
					<th style="width:5%;">Unit</th>
					<th style="width:7%;">Gate Qty</th>
					
					<!--th style="width:5%;">Unit 2</th>
					<th style="width:10%;">Net Weight</th-->
					<th style="width:15%;">Remark</th>
					<?php if($is_storeuser) { ?>
					<th style="width:7%;">Store Qty</th>
					<th style="width:15%;">Store Remark</th>
					<?php } ?>
					
				</tr>
			</thead>
			<tbody>
			<?php 
			
			for($m=1;$m<=100;$m++) 
			{
				$j = $i = $m;
				$cls = ($j>10) ? 'next10' : '';
				$styl = ($j>10) ? 'display:none;' : '';
				
				if(!empty($gateentry)) 
				{ 
					$cls = ($j>$rowcnt) ? 'next10' : '';
					$styl = ($j>$rowcnt) ? 'display:none;' : ''; 
					$j = $m - 1;
					$i = isset($materialDetails[$j]->sr_no) ? $materialDetails[$j]->sr_no : $j+1;
				} 
				
			?>
			<tr id="rowno<?php echo $m;?>" class="<?php echo $cls;?>" style="<?php echo $styl;?>">
				<td>
					<input class="matselect" id="matsel<?php echo $m;?>" type="checkbox" name="materialselect" value="<?php echo $i;?>" />
					<input type="hidden" name="mat_id[]" id="mat_id<?php echo $m;?>" value="<?php echo isset($materialDetails[$j]->id) ? $materialDetails[$j]->id : 0; ?>" />
				</td>
				<td><input class="form-control onlynumbers onright" type="text" name="mat_srno[]" id="mat_srno<?php echo $m;?>" value="<?php echo $i;?>" /></td>
				<td>
					<!--select class="form-control" name="product[]" id="product" />>
					<option value="">None</option>
					</select-->
					<input class="form-control" type="text" name="mat_code[]" id="mat_code<?php echo $m;?>" value="<?php echo isset($materialDetails[$j]->material_code) ? $materialDetails[$j]->material_code : ''; ?>" />
				</td>
				<td>
					<input class="form-control" type="text" name="mat_desc[]" id="mat_desc<?php echo $m;?>" value="<?php echo isset($materialDetails[$j]->material_desc) ? $materialDetails[$j]->material_desc : ''; ?>" />
				</td>
				<td>
					<input class="form-control onlydecimalval onright" type="text" name="mat_po_chln_qty[]" id="mat_po_chln_qtyy<?php echo $m;?>" value="<?php echo isset($materialDetails[$j]->po_chln_qty) ? $materialDetails[$j]->po_chln_qty : ''; ?>" />
					<input type="hidden" name="mat_totalqty[]" id="mat_totalqty<?php echo $m;?>" value="<?php echo isset($materialDetails[$j]->total_qty) ? $materialDetails[$j]->total_qty : 0; ?>" />
				</td>
				<td>
					<input class="form-control" type="text" name="mat_unit[]" id="mat_unit<?php echo $m;?>" value="<?php echo isset($materialDetails[$j]->mat_unit) ? $materialDetails[$j]->mat_unit : ''; ?>" />
				</td>
				<td>
					
					<input class="form-control onlydecimalval onright" type="text" name="mat_gateqty[]" id="mat_gateqty<?php echo $m;?>" value="<?php echo isset($materialDetails[$j]->gateentry_qty) ? $materialDetails[$j]->gateentry_qty : ''; ?>" />
					<input type="hidden" name="mat_unit2[]" id="mat_unit2<?php echo $m;?>" value="<?php echo isset($materialDetails[$j]->unit2) ? $materialDetails[$j]->unit2 : ''; ?>" />
					<input type="hidden" name="mat_netweight[]" id="mat_netweight<?php echo $m;?>" value="<?php echo isset($materialDetails[$j]->net_weight) ? $materialDetails[$j]->net_weight : 0; ?>" />
				</td>
				
				<td>
					<input class="form-control" type="text" name="mat_remark[]" id="mat_remark<?php echo $m;?>" value="<?php echo isset($materialDetails[$j]->remark) ? $materialDetails[$j]->remark : ''; ?>" />
				</td>
				<?php if($is_storeuser) { ?>
				<td>
					<input class="form-control onlydecimalval onright" type="text" name="mat_storeqty[]" id="mat_storeqty<?php echo $m;?>" value="<?php echo isset($materialDetails[$j]->storeqty) ? $materialDetails[$j]->storeqty : ''; ?>" />
				</td>
				<td>
					<input class="form-control" type="text" name="mat_storeremark[]" id="mat_storeremark<?php echo $m;?>" value="<?php echo isset($materialDetails[$j]->storeremark) ? $materialDetails[$j]->storeremark : ''; ?>" />
				</td>
				<?php } ?>
				<!--td>
					<input class="form-control" type="text" name="mat_unit2[]" id="mat_unit2<?php //echo $i;?>" value="<?php //echo isset($materialDetails[$j]->unit2) ? $materialDetails[$j]->unit2 : ''; ?>" />
				</td>
				<td>
					<input class="form-control onlydecimalval" type="text" name="mat_netweight[]" id="mat_netweight<?php //echo $i;?>" value="<?php //echo isset($materialDetails[$j]->net_weight) ? $materialDetails[$j]->net_weight : ''; ?>" />
				</td-->
				
				
			</tr>
			<?php } ?>
			</tbody>
			<tfoot>
			
			<tr><td colspan="12"><a href="javascript:void(0);" id="shownextbatch" onclick="shownextbatch();">Show/Hide More Rows </a></td></tr>
			
			</tfoot>
		</table>
	</div>
</div>
