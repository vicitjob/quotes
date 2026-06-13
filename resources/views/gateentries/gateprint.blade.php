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

.orderdetail, .orderdetail th, .orderdetail td {
  border: 1px solid;
}
.orderdetail {
  border-collapse: collapse;
}
.txtlbl{
	font-weight:600;
}

.txtlblfoot{
	font-weight:600;
	font-size:13px;
}
.entrycol1{width:11%;}
.entrycol2{width:14%;}
.entryfootcol1{width:18%;}
.entryfootcol2{width:32%;font-size:13px;}
.onright{text-align:right !important;}
.topspace{margin-top:50px;}
.tbltopspace{margin-top:1px;}
.tbltopspace2{padding-top:2px;}
caption {text-align:left;}
#watermark {
            position: fixed;
            top: 35%;
            left: 15%;
            width: 70%;
            text-align: center;
            opacity: 0.04;
            transform: rotate(-30deg);
            transform-origin: 50% 50%;
            z-index: -1000;
            font-size: 36px;
            font-weight: bold;
            color: #000;
        }
</style>
</head>
<body>
<div id="watermark">
    GROWEL'S GATE ENTRY<br><?php echo ($gateentry) ? strtoupper($gateentry->plant_name) : ''; ?>
</div>
<main>
<table style="width:100%;" class="headertbl">
	<tr>
		<!--td style="width:10%;">
			<div style="width: 30%;text-align:center;">
				<img style="float:left;" src="<?php //echo $logo_img;?>" alt="logo" title="Growels" width="auto" height="60px" />
			</div>
		</td-->
		<td style="width:35%;">
			<div style="width: 50%;text-align:left;">
				<img style="float:left;" src="{{ $logo_img }}" alt="logo" title="Growels" width="auto" height="60px" />
			</div>
			<p style="text-align:left;">GRAUER & WEIL (INDIA) LIMITED</p>
			<p style="text-align:left;margin:0px;font-size: 9px;">&nbsp;<?php echo $addr1;?></p>
			<p style="text-align:left;margin:0px;font-size: 9px;"><?php echo $addr2;?></p>
			<p><span class="txtlbl">Plant: </span><?php echo ($gateentry) ? $gateentry->plant_name : ''; ?> <span class="txtlbl">Location: </span><?php echo $location; ?></p>
		</td>
	
		<td style="width:35%;">
			<h2 style="text-align:center;text-decoration: underline;">GATE ENTRY</h2>
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
        
        <td class="txtlbl entrycol1"><?php echo $selvehidlbl;?> No.: </td>
        <td class="entrycol2"><?php echo ($gateentry) ? $gateentry->vehicle_no : ''; ?></td>
		
		<td class="txtlbl entrycol1"><?php echo $seldellbl;?> Name.: </td>
        <td class="entrycol2"><?php echo ($gateentry) ? $gateentry->del_person_name : ''; ?></td>
		
		<td class="txtlbl entrycol1"><?php echo $seldellbl;?> Mob.: </td>
        <td class="entrycol2"><?php echo ($gateentry) ? $gateentry->del_person_mob : ''; ?></td>
		
		
    </tr>
	
	<tr>
				
		<td class="txtlbl entrycol1">Recv. Department:</td>
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
		<td class="txtlbl entrycol1">Gate Reg Ref. No.: </td>
        <td class="entrycol2"><?php echo ($gateentry) ? $gateentry->sec_reg_ref_no : ''; ?></td>
		
		<td class="txtlbl entrycol1">Doc Type:</td>
        <td class="entrycol2"><?php echo ($gateentry) ? $gateentry->doc_type_name : ''; ?></td>
		
		<td colspan="2" rowspan="3">
			<table class="orderdetail " style="width:100%;border: 1px solid black;">
				<thead>
					<tr>
						<th>LR No.</th>
						<th>Transporter</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><?php echo ($gateentry) ? $gateentry->lr_number : ''; ?></td>
						<td><?php echo ($gateentry) ? $gateentry->transporter : ''; ?></td>
					</tr>
					<tr>
						<td><?php echo ($gateentry) ? $gateentry->lr_number_prev1 : ''; ?></td>
						<td><?php echo ($gateentry) ? $gateentry->transporter_prev1 : ''; ?></td>
					</tr>
					<tr>
						<td><?php echo ($gateentry) ? $gateentry->lr_number_prev2 : ''; ?></td>
						<td><?php echo ($gateentry) ? $gateentry->transporter_prev2 : ''; ?></td>
					</tr>
				</tbody>
		</td>
        
		
	</tr>
	
	<tr>
        
        <td class="txtlbl entrycol1"><?php echo $seldoclbl.' No.';?>: </td>
        <td class="entrycol2"><?php echo $doc_no; ?></td>
		
		<td class="txtlbl entrycol1">Invoice No.: </td>
        <td class="entrycol2"><?php echo ($gateentry) ? $gateentry->invoice_no : ''; ?></td>
		
		
		
		
	</tr>
	
	<tr>
        
        <td class="txtlbl entrycol1">Vendor: </td>
        <td colspan="2" class="entrycol2"><?php echo ($gateentry) ? $gateentry->vendor_name : ''; ?></td>
		
	</tr>
		
</table>
<div class="tbltopspace">&nbsp;</div>
<table class="orderdetail " style="width:100%;border: 1px solid black;">
    <caption>Box/Container Details:</caption>
	<thead>
		<tr>
			<th style="width:3%;">Sr No.</th>
			<th style="width:10%;">Container Type</th>
			<th style="width:4%;">No. of Container</th>
			<th style="width:15%;">Remark</th>
		</tr>
	</thead>
	<tbody>
		<?php
		if($containerDetails->count() > 0)
		{
			foreach($containerDetails as $contobj)
			{
				echo '<tr>';
				echo '<td class="onright">'.$contobj->sr_no.'</td>';
				echo '<td>'.$contobj->cont_type.'</td>';
				echo '<td class="onright">'.$contobj->no_of_cont.'</td>';
				echo '<td>'.$contobj->cont_remark.'</td>';
				echo '</tr>';
			}
		}
		else
		{
			echo '<tr><td colspan="4">No Data Found.</td></tr>';
		}
		?>
	</tbody>
    
</table>
<div class="tbltopspace">&nbsp;</div>
<table class="orderdetail" style="width:100%;border: 1px solid black;">
    <caption>Material/Item Details:</caption>
	<thead>
		<tr>
			<th width="6%">Sr. No.</th>
			<th width="10%">Item Code</th>
			<th width="39%">Item Desc.</th>
			<th width="7%"><?php echo $seldoclbl.' Qty.';?></th>
			<th width="5%">Unit</th>
			<th width="7%">Gate Qty</th>
			<th width="25%">Remark</th>
			
		</tr>
	</thead>
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
				
				echo '</tr>';
			}
		}
		else
		{
			echo '<tr><td colspan="7">No Items Found.</td></tr>';
		}
		?>
	</tbody>
    
</table>
<div class="topspace"></div>

<table style="width:100%;">
	<tr>
		<td colspan="2" class="txtlbl entryfootcol1">______________________________________ </td>
		<td colspan="2" class="txtlbl entryfootcol2">___________________________________</td>
	</tr>
    <tr>
		<td class="txtlblfoot entryfootcol1">Check-In By: </td>
        <td class="entryfootcol2"><?php echo ($gateentry) ? $gateentry->security_name_in : ''; ?></td>
		
		<td class="txtlblfoot entryfootcol1">Gate-In Supervisor: </td>
		<td class="entryfootcol2"><?php echo ($gateentry) ? $gateentry->createdby_name : ''; ?></td>
	</tr>
	<tr>
		<td class="txtlblfoot entryfootcol1">Check-Out By: </td>
        <td class="entryfootcol2"><?php echo ($gateentry) ? $gateentry->security_checkout_name : '-'; ?></td>
		
		<td class="txtlblfoot entryfootcol1">Gate-Out Supervisor: </td>
		<td class="entryfootcol2"><?php echo ($closed_by) ? $closed_by : '-'; ?></td>
	</tr>
	<tr class="tbltopspace2">
		<td colspan="4" ><span class="txtlblfoot">Gate Check-In Security Remark: </span><span class="entryfootcol2"><?php echo ($gateentry) ? $gateentry->remarks : ''; ?></span></td>
        		
    </tr>
   
</table>
</main>
</body>
</html>