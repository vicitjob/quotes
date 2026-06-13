<!DOCTYPE html>
<html>
<head>
<style>
@page {
                margin: 15px 20px;
            }
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  text-align: left;
  padding: 4px;
  font-size:10px;
}

.headertbl td, headertbl th{
	padding: 3px;
}

#orderdetail, #orderdetail th, #orderdetail td {
  border: 1px solid;
}
#orderdetail {
  border-collapse: collapse;
}
.txtlbl{
	font-weight:600;
}
.entrycol1{width:10%;}
.entrycol2{width:22%;}
.onright{text-align:right !important;}
</style>
</head>
<body>
  
<main>
<table style="width:100%;" class="headertbl">
	<tr>
		<td style="width:10%;">
			<div style="width: 30%;text-align:center;">
				<img style="float:left;" src="<?php echo $logo_img;?>" alt="logo" title="Growels" width="auto" height="60px" />
			</div>
		</td>
		<td style="width:30%;">
			<h3 style="text-align:left;">GRAUER & WEIL (INDIA) LIMITED</h3>
			<p style="text-align:left;margin:0px;font-size: 9px;"><?php echo $addr1;?></p>
			<p style="text-align:left;margin:0px;font-size: 9px;"><?php echo $addr2;?></p>
			<p><span class="txtlbl">Plant: </span><?php echo ($gateentry) ? $gateentry->plant_name : ''; ?> <span class="txtlbl">Location: </span><?php echo $location; ?></p>
		</td>
	
		<td style="width:30%;">
			<h2 style="text-align:center;text-decoration: underline;">GATE ENTRY REPORT</h2>
		</td>
		<td style="width:30%;">
			<table style="width:100%;">
				<tr>
					
					<td width="100px;" class="txtlbl">GATE REF. NO.: </td>
					<td><?php echo ($gateentry) ? $gateentry->gate_in_no : ''; ?></td>
				</tr>
				<tr>
					<td width="100px;" class="txtlbl">GATE-IN TIME: </td>
					<td><?php echo ($gateentry) ? (($gateentry->gate_in_date) ? date('d-m-Y H:i', strtotime($gateentry->gate_in_date.' '.$gateentry->gate_in_time)) : '') : ''; ?></td>
				</tr>
				<tr>
					<td width="100px;" class="txtlbl">GATE-OUT TIME: </td>
					<td><?php echo ($gateentry) ? (($gateentry->gate_out_date) ? date('d-m-Y H:i', strtotime($gateentry->gate_out_date.' '.$gateentry->gate_out_time)) : '') : ''; ?></td>
				</tr>
				<tr>
					<td width="100px;" class="txtlbl">GATE: </td>
					<td><?php echo ($gateentry) ? $gateentry->sec_id_gt_in_name : ''; ?></td>
				</tr>
				<tr>
					<td width="100px;" class="txtlbl">CREATED BY: </td>
					<td><?php echo ($gateentry) ? $gateentry->createdby_name : ''; ?></td>
				</tr>
				
			</table>
		</td>
	</tr>
	
	
</table>
<hr />

<?php
$created_at_dt = '';
/*$created_at_date = $orderdata['created_at'];
 if($created_at_date!='')
{
    $orderdttmp = explode(" ",$created_at_date);
    $created_at = $orderdttmp[0];
    if($created_at != '')
    {
        $created_atstr = strtotime($created_at);
        $created_at_dt = date('d-m-Y', $created_atstr);
    }
}*/

$order_date = '';
/*$order_date_db = $orderdata['order_date'];
if($order_date_db != '')
{
    $order_date_db_str = strtotime($order_date_db);
    $order_date = date('d-m-Y', $order_date_db_str);
}*/
?>
<table style="width:100%;">
    
    <tr>
        <td class="txtlbl entrycol1">Vehicle Type:</td>
        <td class="entrycol2"><?php echo ($gateentry) ? $gateentry->vehicle_type_desc : ''; ?></td>
        
        <td class="txtlbl entrycol1">Vehicle No.: </td>
        <td class="entrycol2"><?php echo ($gateentry) ? $gateentry->vehicle_no : ''; ?></td>
		
		<td class="txtlbl entrycol1">Department:</td>
        <td class="entrycol2">
			<?php 
				echo $dept_name;
				if($gateentry)
				{
					if($gateentry->status == 1) { echo ' (Pending)'; }
					else if($gateentry->status > 1) { echo ' (Confirmed)'; }
				}
			?>
		</td>
    </tr>
	 <tr>
        <td class="txtlbl entrycol1">Doc Type:</td>
        <td class="entrycol2"><?php echo ($gateentry) ? $gateentry->doc_type_name : ''; ?></td>
        
        <td class="txtlbl entrycol1"><?php echo $seldoclbl.' No.';?>: </td>
        <td class="entrycol2"><?php echo $doc_no; ?></td>
		
		<td class="txtlbl entrycol1">Vendor: </td>
        <td class="entrycol2"><?php echo ($gateentry) ? $gateentry->vendor_name : ''; ?></td>
    </tr>
    <tr>
		<td class="txtlbl entrycol1">Reg Ref. No.: </td>
        <td class="entrycol2"><?php echo ($gateentry) ? $gateentry->sec_reg_ref_no : ''; ?></td>
		
        <td class="txtlbl entrycol1">Check-In By: </td>
        <td class="entrycol2"><?php echo ($gateentry) ? $gateentry->security_name_in : ''; ?></td>
		
		<td class="txtlbl entrycol1">Check-Out By: </td>
        <td class="entrycol2"><?php echo ($gateentry) ? $gateentry->security_checkout_name : ''; ?></td>
		
		
    </tr>
  
</table>

<table id="orderdetail" style="width:100%;border: 1px solid black;">
    
    <tr>
        <th width="5%">Sr. No.</th>
        <th width="10%">Item Code</th>
        <th width="30%">Item Desc.</th>
        <th width="5%"><?php echo $seldoclbl.' Qty.';?></th>
        <th width="5%">Unit</th>
        <th width="5%">Gate Qty</th>
        <th width="20%">Remark</th>
        <th width="5%">Store Qty</th>
		<th width="15%">Store Remark</th>
    </tr>
	
	<tbody>
		<?php
		if($materialDetails->count() > 0)
		{
			foreach($materialDetails as $matobj)
			{
				echo '<tr>';
				echo '<td class="onright">'.$matobj->sr_no.'</td>';
				echo '<td>'.$matobj->material_code.'</td>';
				echo '<td>'.$matobj->material_desc.'</td>';
				echo '<td class="onright">'.$matobj->po_chln_qty.'</td>';
				echo '<td>'.$matobj->mat_unit.'</td>';
				echo '<td class="onright">'.$matobj->gateentry_qty.'</td>';
				echo '<td>'.$matobj->remark.'</td>';
				echo '<td class="onright">'.$matobj->storeqty.'</td>';
				echo '<td>'.$matobj->storeremark.'</td>';
				echo '</tr>';
			}
		}
		?>
	</tbody>
    
</table>
<div style="margin-top:25px;"></div>

<table style="width:100%;">
     <tr>
		<td class="txtlbl entrycol1">Dept. InCharge:</td>
        <td class="entrycol2"><?php echo $dept_incharge; ?></td>
		
        <td class="txtlbl entrycol1">Dept. Conf. Date:</td>
        <td class="entrycol2"><?php echo ($gateentry) ? (($gateentry->checkout_dt) ? date('d-m-Y H:i', strtotime($gateentry->checkout_dt)) : '') : ''; ?></td>
       
		<td class="txtlbl entrycol1">&nbsp; </td>
        <td class="entrycol2">&nbsp;</td>
    </tr>
	<tr>
		<td class="txtlbl entrycol1">Sec. Remark:</td>
        <td class="entrycol2"><?php echo ($gateentry) ? $gateentry->remarks : ''; ?></td>
		
        <td class="txtlbl entrycol1">Unloaded By:</td>
        <td class="entrycol2"><?php echo ($gateentry) ? $gateentry->security_name_out : ''; ?></td>
       
		<td class="txtlbl entrycol1">Checkout Remark: </td>
        <td class="entrycol2"><?php echo ($gateentry) ? $gateentry->checkout_remark : ''; ?></td>
    </tr>
   
</table>
</main>
</body>
</html>