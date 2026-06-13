<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationConfigController extends Controller
{
    //
	
	public function index(Request $request)
	{
		$testvar = '';
		$dealerid = isset($request->dealerid) ? $request->dealerid : 0;
		$disc_res = DB::table('config_discount')->where('is_delete',0)->where('dealerid',$dealerid)->orderby('disc_type','asc')->get();
		$fill_res = DB::table('config_filling')->where('is_delete',0)->get();
		$fields_res = DB::table('config_fields')->where('is_delete',0)->where('dealerid',$dealerid)->get();
		$shiptype_res = DB::table('shipment_type')->where('is_delete',0)->orderby('shipment_type','asc')->get();
		$cargotype_res = DB::table('cargo_type')->where('is_delete',0)->orderby('cargo_type','asc')->get();
		$cfg_working_res = DB::table('config_working')->where('is_delete',0)->where('dealerid',$dealerid)->orderby('ordering','asc')->get();
		//DEALERS
		$dealers = DB::table('dealer_master')->select('*')->where('is_delete',0)->get();
		//DEALERS
		return view('quotationconfigs.index', compact('testvar','disc_res','cfg_working_res','shiptype_res','cargotype_res','fill_res','fields_res','dealerid','dealers'));
	}
	
	public function store(Request $request)
	{		
		$dealerid = isset($request->dealerid2) ? $request->dealerid2 : 0;
		
		if(count($request->Weight) > 0)
		{				
			//foreach($request->mat_code as $k1=>$mat_code)
			foreach($request->Weight as $k1=>$Weight)
			{
				if($Weight != '')
				{
					if($k1 == 0)
					{
						$del_arr = array('is_delete' => 1, 'updated_at' => date('Y-m-d H:i:s'));
						DB::table("config_working")->where('dealerid',$dealerid)->update($del_arr);
					}
					
					$shipment_type = isset($request->shipment_type[$k1]) ? $request->shipment_type[$k1] : '';
					$cargo_type = isset($request->cargo_type[$k1]) ? $request->cargo_type[$k1] : '';
					
					//compqty					
					$Palletized = isset($request->Palletized[$k1]) ? $request->Palletized[$k1] : 0;
					$Transport = isset($request->Transport[$k1]) ? $request->Transport[$k1] : 0;
					$Forwarding = isset($request->Forwarding[$k1]) ? $request->Forwarding[$k1] : 0;
					$Freight = isset($request->Freight[$k1]) ? $request->Freight[$k1] : 0;
					$IndHAZ = isset($request->IndHAZ[$k1]) ? $request->IndHAZ[$k1] : 0;
					$Lashing = isset($request->Lashing[$k1]) ? $request->Lashing[$k1] : 0;
					$DelO = isset($request->DelO[$k1]) ? $request->DelO[$k1] : 0;
					$Customs = isset($request->Customs[$k1]) ? $request->Customs[$k1] : 0;
					$thc = isset($request->THC[$k1]) ? $request->THC[$k1] : 0;
					$GatePass = isset($request->GatePass[$k1]) ? $request->GatePass[$k1] : 0;
					$Insp = isset($request->Insp[$k1]) ? $request->Insp[$k1] : 0;
					$UAEHaz = isset($request->UAEHaz[$k1]) ? $request->UAEHaz[$k1] : 0;
					$Transport2 = isset($request->Transport2[$k1]) ? $request->Transport2[$k1] : 0;
					
					$Weight = ($Weight == '') ? 0 : floatval($Weight);
					$Palletized = ($Palletized == '') ? 0 : intval($Palletized);
					$Transport = ($Transport == '') ? 0 : floatval($Transport);
					$Forwarding = ($Forwarding == '') ? 0 : floatval($Forwarding);
					$Freight = ($Freight == '') ? 0 : floatval($Freight);
					$IndHAZ = ($IndHAZ == '') ? 0 : floatval($IndHAZ);
					$Lashing = ($Lashing == '') ? 0 : floatval($Lashing);
					$DelO = ($DelO == '') ? 0 : floatval($DelO);
					$Customs = ($Customs == '') ? 0 : floatval($Customs);
					$thc = ($thc == '') ? 0 : floatval($thc);
					$GatePass = ($GatePass == '') ? 0 : floatval($GatePass);
					$Insp = ($Insp == '') ? 0 : floatval($Insp);
					$UAEHaz = ($UAEHaz == '') ? 0 : floatval($UAEHaz);
					$Transport2 = ($Transport2 == '') ? 0 : floatval($Transport2);
					
					//$ReadableDesc = isset($request->ReadableDesc[$k1]) ? $request->ReadableDesc[$k1] : '';	
					
					$ReadableDesc = round(($Weight/1000), 1) ." tons " .$cargo_type ." ".$shipment_type ." (" .(($Palletized) ? "Palletized" : "Unpalletized") .")";
					
					$config_data = array(
						'ShipmentType' => $shipment_type,
						'CargoType' => $cargo_type,
						'Weight' => $Weight,
						'Palletized' => $Palletized,
						'Transport' => $Transport,
						'Forwarding' => $Forwarding,
						'Freight' => $Freight,
						'IndHAZ' => $IndHAZ,
						'Lashing' => $Lashing,
						'DelO' => $DelO,
						'Customs' => $Customs,
						'THC' => $thc,
						'GatePass' => $GatePass,
						'Insp' => $Insp,
						'UAEHaz' => $UAEHaz,
						'Transport2' => $Transport2,
						'ReadableDesc' => $ReadableDesc
					);
					
					DB::table("config_working")->where('dealerid',$dealerid)->insert($config_data);
				}
				
			}
			
		}
		
		if(count($request->disc_type) > 0)
		{				
			//foreach($request->mat_code as $k1=>$mat_code)
			foreach($request->disc_type as $k4=>$disc_type)
			{
				if($disc_type != '')
				{
					$disc_val = isset($request->disc_val[$k4]) ? $request->disc_val[$k4] : 0;
					$disc_margin = isset($request->disc_margin[$k4]) ? $request->disc_margin[$k4] : 0;
					
					$disc_val = ($disc_val == '') ? 0 : floatval($disc_val);
					$disc_margin = ($disc_margin == '') ? 0 : floatval($disc_margin);
					
					$config_data = array(
						'disc_val' => $disc_val,
						'disc_margin' => $disc_margin,
						'updated_at' => date('Y-m-d H:i:s')
					);
					
					$ex_id = DB::table('config_discount')->where('disc_type',$disc_type)->where('is_delete',0)->where('dealerid',$dealerid)->first();
					if($ex_id)
					{
						DB::table('config_discount')->where('id',$ex_id->id)->where('dealerid',$dealerid)->update($config_data);
					}
					else
					{
						$config_data['disc_type'] = $disc_type;
						DB::table('config_discount')->where('dealerid',$dealerid)->insert($config_data);
					}
					
				}
			}
		}
		
		if(count($request->para_name) > 0)
		{				
			//foreach($request->mat_code as $k1=>$mat_code)
			foreach($request->para_name as $k4=>$para_name)
			{
				if($para_name != '')
				{
					$para_type = isset($request->para_type[$k4]) ? $request->para_type[$k4] : '';
					$para_value = isset($request->para_value[$k4]) ? $request->para_value[$k4] : '';
					
					$config_data = array(
						'para_name' => $para_name,
						'para_type' => $para_type,
						'para_value' => $para_value
					);
					
					$ex_id = DB::table('config_filling')->where('para_name',$para_name)->where('para_type',$para_type)->where('is_delete',0)->first();
					if($ex_id)
					{
						DB::table('config_filling')->where('id',$ex_id->id)->update($config_data);
					}
					else
					{
						DB::table('config_filling')->insert($config_data);
					}
					
				}
			}
		}
		
		if(count($request->fpara_lbl) > 0)
		{				
			//foreach($request->mat_code as $k1=>$mat_code)
			foreach($request->fpara_lbl as $k4=>$fpara_lbl)
			{
				if($fpara_lbl != '')
				{
					$fpara_name = isset($request->fpara_name[$k4]) ? $request->fpara_name[$k4] : '';
					$fpara_value = isset($request->fpara_value[$k4]) ? $request->fpara_value[$k4] : 0;
					$fpara_value = ($fpara_value == '') ? 0 : floatval($fpara_value);
					
					$config_data = array(
						'para_lbl' => $fpara_lbl,
						'para_name' => $fpara_name,
						'para_value' => $fpara_value
					);
					
					$ex_id = DB::table('config_fields')->where('para_name',$fpara_name)->where('is_delete',0)->where('dealerid',$dealerid)->first();
					if($ex_id)
					{
						DB::table('config_fields')->where('id',$ex_id->id)->where('dealerid',$dealerid)->update($config_data);
					}
					else
					{
						DB::table('config_fields')->where('dealerid',$dealerid)->insert($config_data);
					}
					
				}
			}
		}
		
		return redirect()->route('quotationconfig.index')->with('success', 'Settings updated successfully.');
	}
}
