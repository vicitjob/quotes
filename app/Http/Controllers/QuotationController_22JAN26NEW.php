<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\QuotationController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Pagination\Paginator;
use App\Models\Quotation;
use App\Models\QuotationItemDetail;
use Carbon\Carbon;
use PDF;
use App\Models\User;
use App\Mail\GateEntryMail;


use Illuminate\Support\Facades\Mail;

class QuotationController extends Controller
{
	
	public function boot()
	{
		Paginator::useBootstrap();
	}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
		$is_storeuser = false;
		$show_plant_loc = false;
		$show_edit_del = true;
		
		//REMOVE
		/*$id = 129;
		$gateentrynew = Quotation::where('id',$id)->first();		
		$this->generatepdf($gateentrynew,1);*/
		//REMOVE
		
        //$gateEntries = Quotation::with('materialDetails')->paginate(10);
		$loc_code = $plant_code = array();
		$cargoarr = array('NH' => 'Non-Haz', 'H' => 'Haz');
		//Filter by Plant and location
		if(Auth::User())
		{
			$loc_code = explode(',',Auth::User()->loc_code);
			$plant_code = explode(',',Auth::User()->plant_code);
		}
		
		//Filter by Plant and location
		
		$sql1 = DB::table('quotations')
				->select(array('quotations.*','users.name'))
				->leftjoin('users','users.id','=','quotations.user_id');
				
		//Search
		$srch_quoterefno = '';
		//echo 'TEST : '.$request->srch_gaterefno;die;
		if(isset($request->srch_quoterefno))
		{
			if($request->srch_quoterefno != '')
			{
				$srch_quoterefno = $request->srch_quoterefno;
				$sql1->whereRaw("quotations.quotation_no = '".$srch_quoterefno."'");
			}
		}
		
		$from_quote_date = '';
		//echo 'TEST : '.$request->srch_gaterefno;die;
		if(isset($request->from_quote_date))
		{
			if($request->from_quote_date != '')
			{
				$from_quote_date = $request->from_quote_date;
			}
		}
		
		//if($from_quote_date || $from_gate_in_time)
		if($from_quote_date)
		{
			
			if($from_quote_date == '')
			{
				$from_quote_date = date('Y-m-d');
			}
			else
			{
				$from_quote_date = date('Y-m-d', strtotime($from_quote_date));
			}
						
			$sql1->whereRaw("quotations.created_at >= '".$from_quote_date."'");
			//$sql1->whereRaw("gate_in_time >= '".$from_gate_in_time."'");
		}
		
		$to_quote_date = '';
		//echo 'TEST : '.$request->srch_gaterefno;die;
		if(isset($request->to_quote_date))
		{
			if($request->to_quote_date != '')
			{
				$to_quote_date = $request->to_quote_date;
			}
		}
		
		$to_gate_in_time = '';
		//echo 'TEST : '.$request->srch_gaterefno;die;
		if(isset($request->to_gate_in_time))
		{
			if($request->to_gate_in_time != '')
			{
				$to_gate_in_time = $request->to_gate_in_time;
			}
		}
		
		//if($to_quote_date || $to_gate_in_time)
		if($to_quote_date)
		{
			
			if($to_quote_date == '')
			{
				$to_quote_date = date('Y-m-d');
			}
			/*if($to_gate_in_time == '')
			{
				$to_gate_in_time = '00:00';
			}*/
			
			$sql1->whereRaw("quotations.created_at <= '".$to_quote_date."'");
			//$sql1->whereRaw("gate_in_time <= '".$to_gate_in_time."'");
		}
		
		//Search
		
		$userRoles = array();
		if(Auth::user())
		{
			$userRoles = Auth::user()->roles->pluck('name')->toArray();
		}
		
		if((in_array('Super Admin', $userRoles)))
		{
			$show_plant_loc = true;
		}
		
		if(!(in_array('Super Admin', $userRoles)))
		{
			//$sql1->whereIn('plant_code', $plant_code);
		}
		
		$quoteEntries = $sql1->orderby('id','DESC')->paginate(20);	
		
		//$gateEntries = $sql1->orderby('id','DESC')->get();
		//print_r($plant_code);print_r($loc_code);print_r($deptarr);
		//$gateEntries = $sql1->orderby('id','DESC')->toSql(); echo $gateEntries;die;
		
		$disc_res = DB::table('config_discount')->where('is_delete',0)->orderby('disc_type','asc')->get();
		$srch_prodname = '';
		$srch_qty = '';
		$srch_disctype = '';
		
		
        return view('quotations.index', compact('quoteEntries','srch_quoterefno','from_quote_date','to_quote_date','disc_res','srch_prodname','srch_qty','srch_disctype','cargoarr'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
		$dtobj = Carbon::now();
		$op_name = '';
		if(Auth::user())
		{
			$op_name = Auth::user()->name;
		}
		
		$disc_res = DB::table('config_discount')->where('is_delete',0)->orderby('disc_type','asc')->get();
		$cfg_res = DB::table('config_fields')->where('is_delete',0)->get();
		
		$cargo_type = isset($request->cargo_type) ? $request->cargo_type : '';
		
		$maxshipvol = '-';
		if($cargo_type != '')
		{
			$cargoarr = array('NH' => 'Non-Haz', 'H' => 'Haz');
			$haztype = isset($cargoarr[$cargo_type]) ? $cargoarr[$cargo_type] : '';
			if($haztype != '')
			{
				$cfg_price_res = DB::table('config_prices')
									->where('is_delete',0)
									->where('haztype',$haztype)
									->first();
				$maxshipvol = ($cfg_price_res) ? (int) $cfg_price_res->shipment_vol : '-';
			}
		}
		
		$quote_type = isset($request->quote_type) ? $request->quote_type : '';
		$sheet_type = isset($request->sheet_type) ? $request->sheet_type : '';
		$shipment_type = isset($request->shipment_type) ? $request->shipment_type : '';
		$cargo_type = isset($request->cargo_type) ? $request->cargo_type : '';
		$currency_type = isset($request->currency_type) ? $request->currency_type : 'USD';
		
		$showtable = false;
		if($quote_type != '')
		{
			$showtable = true;
		}
		
		$inr_usd = $aed_usd = 0;
		if($cfg_res->count() > 0)
		{
			foreach($cfg_res as $res)
			{
				if($res->para_name == 'inr_usd')
				{
					$inr_usd = $res->para_value;
				}
				if($res->para_name == 'aed_usd')
				{
					$aed_usd = $res->para_value;
				}
			}
		}
		
		$sheet_type_arr = array('Ex Works','Packed','FOB','CIF','Delivered');
		
        return view('quotations.create', compact('dtobj','op_name','disc_res','cfg_res','request','maxshipvol','quote_type','sheet_type','shipment_type','cargo_type','currency_type','showtable','inr_usd','aed_usd','sheet_type_arr'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

		$update_by = $insert_id = 0;
		if(Auth::user())
		{
			$update_by = Auth::user()->id;
		}
		//$request->merge(['created_by' => $prepared_by]);
		
		$total_ship_vol_qty = isset($request->total_ship_vol_qty) ? $request->total_ship_vol_qty : 0;
		$total_ship_vol_qty = ($total_ship_vol_qty == '') ? 0 : floatval($total_ship_vol_qty);
		
		$quote_type = isset($request->quote_type2) ? $request->quote_type2 : '';
		$sheet_type = isset($request->sheet_type2) ? $request->sheet_type2 : '';
		$shipment_type = isset($request->shipment_type2) ? $request->shipment_type2 : '';
		$cargo_type = isset($request->cargo_type2) ? $request->cargo_type2 : '';
		$currency_type = isset($request->currency_type2) ? $request->currency_type2 : '';
		$exp_cred_cost_thr_months = isset($request->exp_cred_cost_thr_months2) ? $request->exp_cred_cost_thr_months2 : 0;
		$exp_cred_cost_thr_months = ($exp_cred_cost_thr_months == '') ? 0 : floatval($exp_cred_cost_thr_months);
		$estd_miniman_cst = isset($request->estd_miniman_cst2) ? $request->estd_miniman_cst2 : 0;
		$estd_miniman_cst = ($estd_miniman_cst == '') ? 0 : floatval($estd_miniman_cst);
		
		if($total_ship_vol_qty > 0)
		{
			$plantcode = 'PLVO';
			$dtobj = Carbon::now();
			$year2 = $dtobj->format('y');
			
			$quotation_no  = $this->generate_quoterefno($plantcode,$year2);	
			
			$prod_data_main = array(
				'quotation_no' => $quotation_no,
				'quote_type' => $quote_type,
				'sheet_type' => $sheet_type,
				'shipment_type' => $shipment_type,
				'cargo_type' => $cargo_type,
				'currency_type' => $currency_type,
				'total_shipment_vol' => $total_ship_vol_qty,
				'exp_cred_cost_thr_months' => $exp_cred_cost_thr_months,
				'estd_miniman_cst' => $estd_miniman_cst,
				'user_id' => $update_by
			);
			
			$insert_id = DB::table("quotations")->insertGetId($prod_data_main);
		}
		
		if($insert_id)
		{
			if(count($request->prodtag) > 0)
			{				
				//foreach($request->mat_code as $k1=>$mat_code)
				foreach($request->prodtag as $k1=>$prodtag)
				{
					if($prodtag != '')
					{
						$prdcd = isset($request->prdcd[$k1]) ? $request->prdcd[$k1] : '';
						$disc_type = isset($request->disc_type[$k1]) ? $request->disc_type[$k1] : 0;
						
						$cmat_srno = isset($request->cmat_srno[$k1]) ? $request->cmat_srno[$k1] : 0;
						$unit_qty = isset($request->unit_qty[$k1]) ? $request->unit_qty[$k1] : 0;
						$pack_size = isset($request->pack_size[$k1]) ? $request->pack_size[$k1] : 0;
						$ship_vol_qty = isset($request->ship_vol_qty[$k1]) ? $request->ship_vol_qty[$k1] : 0;
						
						$listprice_inr_unit = isset($request->listprice_inr_unit[$k1]) ? $request->listprice_inr_unit[$k1] : 0;
						$listprice_usd_unit = isset($request->listprice_usd_unit[$k1]) ? $request->listprice_usd_unit[$k1] : 0;
						
						$listprice_inr = isset($request->listprice_inr[$k1]) ? $request->listprice_inr[$k1] : 0;
						$listprice_usd = isset($request->listprice_usd[$k1]) ? $request->listprice_usd[$k1] : 0;
						
						$disc_val = isset($request->disc_val[$k1]) ? $request->disc_val[$k1] : 0;
						$unp_exwork_inr = isset($request->unp_exwork_inr[$k1]) ? $request->unp_exwork_inr[$k1] : 0;
						$unp_exwork_usd = isset($request->unp_exwork_usd[$k1]) ? $request->unp_exwork_usd[$k1] : 0;
						$pack_fcl_nh = isset($request->pack_fcl_nh[$k1]) ? $request->pack_fcl_nh[$k1] : 0;
						$pack_lcl_pallet = isset($request->pack_lcl_pallet[$k1]) ? $request->pack_lcl_pallet[$k1] : 0;
						$fob_fcl_nh = isset($request->fob_fcl_nh[$k1]) ? $request->fob_fcl_nh[$k1] : 0;
						$fob_lcl_nh_pl = isset($request->fob_lcl_nh_pl[$k1]) ? $request->fob_lcl_nh_pl[$k1] : 0;
						$fob_fcl_h_pl = isset($request->fob_fcl_h_pl[$k1]) ? $request->fob_fcl_h_pl[$k1] : 0;
						$fob_lcl_h_pl = isset($request->fob_lcl_h_pl[$k1]) ? $request->fob_lcl_h_pl[$k1] : 0;
						$cif_fcl_nh = isset($request->cif_fcl_nh[$k1]) ? $request->cif_fcl_nh[$k1] : 0;
						$cif_lcl_nh_pl = isset($request->cif_lcl_nh_pl[$k1]) ? $request->cif_lcl_nh_pl[$k1] : 0;
						$cif_fcl_h_pl = isset($request->cif_fcl_h_pl[$k1]) ? $request->cif_fcl_h_pl[$k1] : 0;
						$cif_lcl_h_pl = isset($request->cif_lcl_h_pl[$k1]) ? $request->cif_lcl_h_pl[$k1] : 0;
						$landed_fcl_nh = isset($request->landed_fcl_nh[$k1]) ? $request->landed_fcl_nh[$k1] : 0;
						$landed_lcl_nh_pl = isset($request->landed_lcl_nh_pl[$k1]) ? $request->landed_lcl_nh_pl[$k1] : 0;
						$landed_fcl_h_pl = isset($request->landed_fcl_h_pl[$k1]) ? $request->landed_fcl_h_pl[$k1] : 0;
						$landed_lcl_h_pl = isset($request->landed_lcl_h_pl[$k1]) ? $request->landed_lcl_h_pl[$k1] : 0;
						$ttlcost_inc_finance = isset($request->ttlcost_inc_finance[$k1]) ? $request->ttlcost_inc_finance[$k1] : 0;
						$recom_dis_sp_to_buyer = isset($request->recom_dis_sp_to_buyer[$k1]) ? $request->recom_dis_sp_to_buyer[$k1] : 0;
						$recom_sp_aft_credit_miniman = isset($request->recom_sp_aft_credit_miniman[$k1]) ? $request->recom_sp_aft_credit_miniman[$k1] : 0;
						
						$cmat_srno = ($cmat_srno == '') ? 0 : intval($cmat_srno);
						$unit_qty = ($unit_qty == '') ? 0 : floatval($unit_qty);
						$pack_size = ($pack_size == '') ? 0 : floatval($pack_size);
						$ship_vol_qty = ($ship_vol_qty == '') ? 0 : floatval($ship_vol_qty);
						$listprice_inr_unit = ($listprice_inr_unit == '') ? 0 : floatval($listprice_inr_unit);
						$listprice_usd_unit = ($listprice_usd_unit == '') ? 0 : floatval($listprice_usd_unit);
						$listprice_inr = ($listprice_inr == '') ? 0 : floatval($listprice_inr);
						$listprice_usd = ($listprice_usd == '') ? 0 : floatval($listprice_usd);
						$disc_val = ($disc_val == '') ? 0 : floatval($disc_val);
						$unp_exwork_inr = ($unp_exwork_inr == '') ? 0 : floatval($unp_exwork_inr);
						$unp_exwork_usd = ($unp_exwork_usd == '') ? 0 : floatval($unp_exwork_usd);
						$pack_fcl_nh = ($pack_fcl_nh == '') ? 0 : floatval($pack_fcl_nh);
						
						$pack_lcl_pallet = ($pack_lcl_pallet == '') ? 0 : (($pack_lcl_pallet == 'Too Large') ? -1 : floatval($pack_lcl_pallet));
						$fob_fcl_nh = ($fob_fcl_nh == '') ? 0 : floatval($fob_fcl_nh);
						
						$fob_lcl_nh_pl = ($fob_lcl_nh_pl == '') ? 0 : (($fob_lcl_nh_pl == 'Too Large') ? -1 : floatval($fob_lcl_nh_pl));
						$fob_fcl_h_pl = ($fob_fcl_h_pl == '') ? 0 : floatval($fob_fcl_h_pl);
						
						$fob_lcl_h_pl = ($fob_lcl_h_pl == '') ? 0 : (($fob_lcl_h_pl == 'Too Large') ? -1 : floatval($fob_lcl_h_pl));
						$cif_fcl_nh = ($cif_fcl_nh == '') ? 0 : floatval($cif_fcl_nh);
						$cif_lcl_nh_pl = ($cif_lcl_nh_pl == '') ? 0 : (($cif_lcl_nh_pl == 'Too Large') ? -1 : floatval($cif_lcl_nh_pl));
						
						$cif_fcl_h_pl = ($cif_fcl_h_pl == '') ? 0 : floatval($cif_fcl_h_pl);
						$cif_lcl_h_pl = ($cif_lcl_h_pl == '') ? 0 : (($cif_lcl_h_pl == 'Too Large') ? -1 : floatval($cif_lcl_h_pl));
						
						$landed_fcl_nh = ($landed_fcl_nh == '') ? 0 : floatval($landed_fcl_nh);
						$landed_lcl_nh_pl = ($landed_lcl_nh_pl == '') ? 0 : (($landed_lcl_nh_pl == 'Too Large') ? -1 : floatval($landed_lcl_nh_pl));
						
						$landed_fcl_h_pl = ($landed_fcl_h_pl == '') ? 0 : floatval($landed_fcl_h_pl);
						
						$landed_lcl_h_pl = ($landed_lcl_h_pl == '') ? 0 : (($landed_lcl_h_pl == 'Too Large') ? -1 : floatval($landed_lcl_h_pl));
						
						$ttlcost_inc_finance = ($ttlcost_inc_finance == '') ? 0 : floatval($ttlcost_inc_finance);
						$recom_dis_sp_to_buyer = ($recom_dis_sp_to_buyer == '') ? 0 : floatval($recom_dis_sp_to_buyer);
						$recom_sp_aft_credit_miniman = ($recom_sp_aft_credit_miniman == '') ? 0 : floatval($recom_sp_aft_credit_miniman);
						
						$prod_data = array(
							'prodtag' => $prodtag,
							'prdcd' => $prdcd,
							'disc_type' => $disc_type,
							'cmat_srno' => $cmat_srno,
							'pack_size' => $pack_size,
							'unit_qty' => $unit_qty,
							'ship_vol_qty' => $ship_vol_qty,
							'listprice_inr_unit' => $listprice_inr_unit,
							'listprice_usd_unit' => $listprice_usd_unit,
							'listprice_inr' => $listprice_inr,
							'listprice_usd' => $listprice_usd,
							'disc_val' => $disc_val,
							'unp_exwork_inr' => $unp_exwork_inr,
							'unp_exwork_usd' => $unp_exwork_usd,
							'pack_fcl_nh' => $pack_fcl_nh,
							'pack_lcl_pallet' => $pack_lcl_pallet,
							'fob_fcl_nh' => $fob_fcl_nh,
							'fob_lcl_nh_pl' => $fob_lcl_nh_pl,
							'fob_fcl_h_pl' => $fob_fcl_h_pl,
							'fob_lcl_h_pl' => $fob_lcl_h_pl,
							'cif_fcl_nh' => $cif_fcl_nh,
							'cif_lcl_nh_pl' => $cif_lcl_nh_pl,
							'cif_fcl_h_pl' => $cif_fcl_h_pl,
							'cif_lcl_h_pl' => $cif_lcl_h_pl,
							'landed_fcl_nh' => $landed_fcl_nh,
							'landed_lcl_nh_pl' => $landed_lcl_nh_pl,
							'landed_fcl_h_pl' => $landed_fcl_h_pl,
							'landed_lcl_h_pl' => $landed_lcl_h_pl,
							'ttlcost_inc_finance' => $ttlcost_inc_finance,
							'recom_dis_sp_to_buyer' => $recom_dis_sp_to_buyer,
							'recom_sp_aft_credit_miniman' => $recom_sp_aft_credit_miniman,
							'quotation_id' => $insert_id,
							'user_id' => $update_by
							
						);
						
						DB::table("quotations_master")->insert($prod_data);
						
						
					}
					
				}
				
			}
			
			$qtrefno_res2 = DB::table('quote_ref_no')
										->where('plant_code',$plantcode)
										->where('quoteyear',$year2)
										->increment('refno');
										
			return redirect()->route('quotations.index')->with('success', 'Quotation saved successfully.');
		}
		else
		{
			return redirect()->route('quotations.index')->with('error', 'Failed to save Quotation.');
		}
        //return redirect()->route('gateentries.index')->with('success', 'Gate Entry created successfully.');
		
		
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) //string $id
    {
        //$gateentry->load('materialDetails');
		$dtobj = Carbon::now();
		
		$quoteentry = DB::table('quotations')
				->select(array('quotations.*','users.name'))
				->leftjoin('users','users.id','=','quotations.user_id')
				->where('quotations.id',$id)
				->where('quotations.is_delete',0)
				->first();
		$materialDetails = DB::table('quotations_master')
				->where('quotation_id',$id)
				->where('is_delete',0)
				->get();
				
		$disc_res = DB::table('config_discount')->where('is_delete',0)->orderby('disc_type','asc')->get();
		$disc_arr = array();
		if($disc_res->count() > 0)
		{
			foreach($disc_res as $discobj)
			{
				$disc_arr[$discobj->id] = $discobj->disc_type;
			}
		}
		$cfg_res = DB::table('config_fields')->where('is_delete',0)->get();
		
		$showtable = true;
		$quote_type = $sheet_type = $shipment_type = $cargo_type = $currency_type = $exp_cred_cost_thr_months = $estd_miniman_cst = '';
		if($quoteentry)
		{
			$quote_type = $quoteentry->quote_type;
			$sheet_type = $quoteentry->sheet_type;
			$shipment_type = $quoteentry->shipment_type;
			$cargo_type = $quoteentry->cargo_type;
			$currency_type = $quoteentry->currency_type;
			$exp_cred_cost_thr_months = $quoteentry->exp_cred_cost_thr_months;
			$estd_miniman_cst = $quoteentry->estd_miniman_cst;
		}
		
		$maxshipvol = '-';
		$cargoarr = array('NH' => 'Non-Haz', 'H' => 'Haz');
		if($cargo_type != '')
		{
			
			$haztype = isset($cargoarr[$cargo_type]) ? $cargoarr[$cargo_type] : '';
			if($haztype != '')
			{
				$cfg_price_res = DB::table('config_prices')
									->where('is_delete',0)
									->where('haztype',$haztype)
									->first();
				$maxshipvol = ($cfg_price_res) ? (int) $cfg_price_res->shipment_vol : '-';
			}
		}
		
		$inr_usd = $aed_usd = 0;
		if($cfg_res->count() > 0)
		{
			foreach($cfg_res as $res)
			{
				if($res->para_name == 'inr_usd')
				{
					$inr_usd = $res->para_value;
				}
				if($res->para_name == 'aed_usd')
				{
					$aed_usd = $res->para_value;
				}
			}
		}
		
        
        return view('quotations.show', compact('quoteentry','materialDetails','dtobj','disc_res','cfg_res','maxshipvol','quote_type','sheet_type','shipment_type','cargo_type','cargoarr','currency_type','exp_cred_cost_thr_months','estd_miniman_cst','showtable','inr_usd','aed_usd','disc_arr'));
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quotation $gateentry) //string $id
    {
		$is_storeuser = false;
		$dtobj = Carbon::now();
		
		$userRoles = array();
		if(Auth::user())
		{
			$userRoles = Auth::user()->roles->pluck('name')->toArray();
		}
		
        //$gateentry->load('materialDetails');
		$materialDetails = DB::table('gate_entry_item_details')->where('gate_entry_id',$gateentry->id)->where('is_delete',0)->get();
		$containerDetails = DB::table('gate_entry_containers')->where('gate_entry_id',$gateentry->id)->where('is_delete',0)->get();
		
		list($operator_name,$plant_code,$loc_code,$loc_name,$plantsres) = $this->ini_gateentry_data();
		
		$op_name = (!empty($gateentry)) ? $gateentry->createdby_name : $operator_name;
		$sel_loc = (!empty($gateentry)) ? $gateentry->loc_code : '';
		$sel_dept = (!empty($gateentry)) ? $gateentry->dept_id : '';
		
		if(in_array('StoreUser', $userRoles))
		{
			//$is_storeuser = true;
		}
		
		if((in_array('Super Admin', $userRoles)) || (in_array('User', $userRoles))) {}
		else
		{
			$is_storeuser = true;
		}
		
		$deptlist = DB::table('departments')->where('is_delete',0)->get();
		$vehiclelist = DB::table('vehicle_types')->where('is_delete',0)->get();
		
        return view('quotations.edit', compact('gateentry','materialDetails','dtobj','plantsres','op_name','loc_code','loc_name','sel_loc','is_storeuser','deptlist','sel_dept','vehiclelist','containerDetails'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quotation $gateentry) //Request $request, string $id
    {		
        $validated = $request->validate([
            'gate_in_no' => 'nullable',
            'gate_in_date' => 'required|date',
            'gate_in_time' => 'required',
            'plant_code' => 'required|string',
            'plant_name' => 'nullable|string',
			'loc_code' => 'nullable|string',
			'loc_name' => 'nullable|string',
            'vehicle_no' => 'required|string',
            'vehicle_type_code' => 'required|string',
            'vehicle_type_desc' => 'nullable|string',
            'lr_number' => 'nullable|string',
            'doc_type_code' => 'required|string',
            'doc_type_name' => 'nullable|string',
            'wb_number' => 'nullable|string',
            'sec_id_gt_in' => 'required|string',
            'sec_id_gt_in_name' => 'nullable|string',
            'remarks' => 'nullable|string',
			'po_no' => 'nullable',
			'rgp_int_no' => 'nullable',
            'sec_reg_ref_no' => 'nullable',
            'vendor_name' => 'nullable|string',
			'createdby_name' => 'nullable|string',
			'dept_id' => 'nullable',
			'dept_name' => 'nullable|string',
			'security_name_in' => 'nullable|string',
			'security_name_out' => 'nullable|string',
            'del_person_name' => 'nullable',
			'del_person_mob' => 'nullable',
			'invoice_no' => 'nullable',
			'transporter' => 'nullable',
			'lr_number_prev1' => 'nullable',
			'transporter_prev1' => 'nullable',
			'lr_number_prev2' => 'nullable',
			'transporter_prev2' => 'nullable',
			'file1' => 'nullable|file|mimes:pdf,doc,docx,jpg,png,txt,xls,xlsx|max:10000', // 10MB
			'file2' => 'nullable|file|mimes:pdf,doc,docx,jpg,png,txt,xls,xlsx|max:10000', // 10MB
        ]);
		
		/*$gate_in_date = isset($validated->gate_in_date) ? $validated->gate_in_date : '';
		$gate_in_date1 = NULL;
		if($gate_in_date)
		{
			$gate_in_date_arr = explode("/",$gate_in_date);
			$gate_in_date_arr1tmp = $gate_in_date_arr[2]."-".$gate_in_date_arr[1]."-".$gate_in_date_arr[0];
			$gate_in_date1 = date('Y-m-d',strtotime($gate_in_date_arr1tmp));
		}*/
		
		if($request->hasFile('file1')) 
		{
			if($gateentry->file1) 
			{
				Storage::disk()->delete($gateentry->file1);
			}
			$validated['file1'] = $request->file('file1')->store('documents');
		}
		if($request->hasFile('file2')) 
		{
			if($gateentry->file2) 
			{
				Storage::disk()->delete($gateentry->file2);
			}
			$validated['file2'] = $request->file('file2')->store('documents');
		}
		
		$update_by = 0;
		if(Auth::user())
		{
			$update_by = Auth::user()->id;
		}
		//$request->merge(['created_by' => $prepared_by]);
		//$validated['created_by'] = $prepared_by;
		
		//GENERATE GATE REF No.
		$updt_gtref_no = false;
		if(empty($gateentry->gate_in_no))
		{
			$gate_ref_no = $this->generate_gaterefno($validated);
			$validated['gate_in_no'] = $gate_ref_no;
			$updt_gtref_no = true;
		}
		//GENERATE GATE REF No.
		
		$validated['status'] = ($gateentry->status == 0) ? 1 : $gateentry->status;
		$gateentry->update($validated);
		
		if($updt_gtref_no)
		{
			$this->generate_gaterefno($validated,1);
		}
		
        // Sync material details: update existing and create new
        $insert_id = $gateentry->id;
		
		$po_no = $validated['po_no'];
		$rgp_int_no = $validated['rgp_int_no'];
		
		$this->savemat_details($insert_id,$po_no,$rgp_int_no,$request,$update_by,1);
		$this->save_containers($insert_id, $request, 1);
		
		$gateentrynew = Quotation::where('id',$insert_id)->first();
		
		//Generate GATE ENTRY PDF
		if($gateentrynew->status == 1)
		{
			$this->generatepdf($gateentrynew);
			//Send Email
			$this->send_email($gateentrynew,1);
			//Send Email
			
		}
		
		//Generate GATE ENTRY PDF
		
		
		
        return redirect()->route('gateentries.index')->with('success', 'Gate Entry updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quotation $gateentry) //string $id
    {
        //$gateentry->delete();
        return redirect()->route('gateentries.index')->with('success', 'Gate Entry deleted successfully.');
    }
	
	public function savemat_details($insert_id,$po_no,$rgp_int_no,$request,$update_by,$isupdate=0)
	{
		//if(count($request->mat_code) > 0)
		if(count($request->mat_desc) > 0)
		{
			if($isupdate)
			{
				$del_arr = array('is_delete' => 1, 'delete_date' => date('Y-m-d H:i:s'));
				DB::table("gate_entry_item_details")->where('gate_entry_id',$insert_id)->update($del_arr);
			}
				
			//foreach($request->mat_code as $k1=>$mat_code)
			foreach($request->mat_desc as $k1=>$mat_desc)
			{
				if($mat_desc != '')
				{
					$mat_srno = isset($request->mat_srno[$k1]) ? $request->mat_srno[$k1] : '';
					//$mat_desc = isset($request->mat_desc[$k1]) ? $request->mat_desc[$k1] : '';
					$mat_code = isset($request->mat_code[$k1]) ? $request->mat_code[$k1] : '';
					$mat_unit = isset($request->mat_unit[$k1]) ? $request->mat_unit[$k1] : '';
					$mat_unit2 = isset($request->mat_unit2[$k1]) ? $request->mat_unit2[$k1] : '';
					$mat_remark = isset($request->mat_remark[$k1]) ? $request->mat_remark[$k1] : '';
					
					//compqty
					$mat_gateqty = isset($request->mat_gateqty[$k1]) ? $request->mat_gateqty[$k1] : 0;
					$mat_totalqty = isset($request->mat_totalqty[$k1]) ? $request->mat_totalqty[$k1] : 0;
					$mat_netweight = isset($request->mat_netweight[$k1]) ? $request->mat_netweight[$k1] : 0;
					$mat_po_chln_qty = isset($request->mat_po_chln_qty[$k1]) ? $request->mat_po_chln_qty[$k1] : 0;
					
					
					$mat_srno = ($mat_srno == '') ? 0 : intval($mat_srno);
					$mat_gateqty = ($mat_gateqty == '') ? 0 : floatval($mat_gateqty);
					$mat_totalqty = ($mat_totalqty == '') ? 0 : floatval($mat_totalqty);
					$mat_netweight = ($mat_netweight == '') ? 0 : floatval($mat_netweight);
					$mat_po_chln_qty = ($mat_po_chln_qty == '') ? 0 : floatval($mat_po_chln_qty);
					
					$prod_data = array(
						'gate_entry_id' => $insert_id,
						'po_no' => $po_no,
						'rgp_int_no' => $rgp_int_no,
						'sr_no' => $mat_srno,
						'material_code' => $mat_code,
						'material_desc' => $mat_desc,
						'gateentry_qty' => $mat_gateqty,
						'mat_unit' => $mat_unit,
						'total_qty' => $mat_totalqty,
						'unit2' => $mat_unit2,
						'net_weight' => $mat_netweight,
						'po_chln_qty' => $mat_po_chln_qty,
						'remark' => $mat_remark
					);
					
					DB::table("gate_entry_item_details")->insert($prod_data);
				}
				
			}
			
		}
		
		if($isupdate)
		{
			$log_arr = array('gate_entry_id' => $insert_id, 'updated_by' => $update_by);
			DB::table("gate_entry_log")->insert($log_arr);
		}
	}
	
	public function save_containers($insert_id, $request, $isupdate=0)
	{
		if(count($request->cont_type) > 0)
		{
			if($isupdate)
			{
				$del_arr = array('is_delete' => 1, 'delete_date' => date('Y-m-d H:i:s'));
				DB::table("gate_entry_containers")->where('gate_entry_id',$insert_id)->update($del_arr);
			}
				
			//foreach($request->mat_code as $k1=>$mat_code)
			foreach($request->cont_type as $k1=>$cont_type)
			{
				if($cont_type != '')
				{
					$cont_srno = isset($request->cmat_srno[$k1]) ? $request->cmat_srno[$k1] : 0;
					$no_of_cont = isset($request->no_of_cont[$k1]) ? $request->no_of_cont[$k1] : 0;
					$cont_remark = isset($request->cont_remark[$k1]) ? $request->cont_remark[$k1] : '';
					
					$cont_srno = ($cont_srno == '') ? 0 : intval($cont_srno);
					$no_of_cont = ($no_of_cont == '') ? 0 : floatval($no_of_cont);
					
					$cont_data = array(
						'gate_entry_id' => $insert_id,
						'sr_no' => $cont_srno,
						'cont_type' => $cont_type,
						'no_of_cont' => $no_of_cont,
						'cont_remark' => $cont_remark
					);
					
					DB::table("gate_entry_containers")->insert($cont_data);
				}
			}
		}
	}
	
	public function generate_gaterefno($validated,$updt_refno=0)
	{
		$plantcode = $validated['plant_code'];
		$p2 = (!empty($plantcode)) ? substr($plantcode,-2) : 'NA';
		$loc_code = (!empty($validated['loc_code'])) ? $validated['loc_code'] : 'NA';
		$dtobj = Carbon::now();
		$year2 = $dtobj->format('y');
		
		if($updt_refno)
		{
			$gtrefno_res = DB::table('gate_ref_no')
							->where('plant_code',$plantcode)
							->where('loc_code',$loc_code)
							->where('gateyear',$year2)
							->increment('refno');
			
		}
		else
		{
			$gtrefno_res = DB::table('gate_ref_no')
							->select('refno')
							->where('plant_code',$plantcode)
							->where('loc_code',$loc_code)
							->where('gateyear',$year2)
							->first();
			$ref_no = 0;
			if($gtrefno_res)
			{
				$ref_no = $gtrefno_res->refno;
			}
			$ref_no_5 = str_pad($ref_no,5,'0',STR_PAD_LEFT);
			$gate_ref_no = $p2.$loc_code.$year2.$ref_no_5;
			
			return $gate_ref_no;
		}
		
	}
	
	public function ini_gateentry_data()
	{
		$operator_name =   '';
		$plant_code = $loc_code = $loc_name = array();
		if(Auth::User())
		{
			$operator_name = Auth::User()->name;
			$loc_code = explode(',',Auth::User()->loc_code);
			$plant_code = explode(',',Auth::User()->plant_code);
			if($loc_code)
			{
				$locres = DB::table('location_master')->whereIn('loc_code',$loc_code)->where('is_delete',0)->get();
				if($locres->count() > 0)
				{
					foreach($locres as $loc)
					{
						$loc_name[$loc->loc_code] = $loc->loc_name;
					}
					
				}
				
			}
		}
		$plantsres = DB::table('plant_master')->whereIn('plant_code',$plant_code)->where('is_delete',0)->get();
		
		return array($operator_name,$plant_code,$loc_code,$loc_name,$plantsres);
	}
	
	public function getdata_location(Request $request)
	{
		$locarr = $deptarr = array();
		$plantcode = isset($request->plantcode) ? $request->plantcode : '';
		if($plantcode)
		{
			$loc_code = array();
			if(Auth::User())
			{
				$operator_name = Auth::User()->name;
				$loc_code = explode(',',Auth::User()->loc_code);
			}
			
			$locres = DB::table('location_master')->where('plant_code',$plantcode)->whereIn('loc_code',$loc_code)->where('is_delete',0)->get();
			if($locres)
			{
				foreach($locres as $loc)
				{
					$locarr[$loc->loc_code] = $loc->loc_name;
				}
			}
			
			$deptlist = DB::table('departments')->where('plant_code',$plantcode)->where('is_delete',0)->get();
			if($deptlist)
			{
				foreach($deptlist as $dept)
				{
					$deptarr[$dept->id] = $dept->dept_name;
				}
			}
		}
		
		return json_encode(array('locations' => $locarr, 'departments' => $deptarr));
		//return json_encode($locarr);
	}
	
	public function getpodetails(Request $request)
	{
		$data_resp = array();
		
		$po_no = isset($request->po_no) ? $request->po_no : '';
		if($po_no)
		{
			$url = env('SAP_UPI_URL', '');
			$sap_username = env('SAP_UPI_NAME', '');
			$sap_password = env('SAP_UPI_PASS', '');
			
			$json_arr = array();
			
			$url .= 'EBELN='.$po_no;
			//$json_arr['EBELN'] = $po_no;
			//echo '<pre>';
			//print_r($json_arr);
			//echo '</pre>';die;
			
			$json_arr_enc = json_encode($json_arr);
			
			$array_options = array(
     
							//set the url option
							CURLOPT_URL=>$url,
							 
							//switches the request type from get to post
							CURLOPT_POST=>true,
							 
							//attach the encoded string in the post field using CURLOPT_POSTFIELDS
							//CURLOPT_POSTFIELDS=>$json_arr_enc,
							 
							//setting curl option RETURNTRANSFER to true 
							//so that it returns the response
							//instead of outputting it 
							CURLOPT_RETURNTRANSFER=>true,
							
							CURLOPT_SSL_VERIFYPEER=>false,
							
							CURLOPT_HTTPAUTH=>CURLAUTH_BASIC,
							
							CURLOPT_USERPWD=>"$sap_username:$sap_password",
							 
							//Using the CURLOPT_HTTPHEADER set the Content-Type to application/json
							CURLOPT_HTTPHEADER=>array('Content-Type:application/json')
						 );
			
			$ch = curl_init();
			curl_setopt_array($ch,$array_options);
						
			$result = curl_exec($ch);
			$statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			//echo 'RESULT'.$statusCode.'<pre>';
			   //print_r($result);
			   //echo '</pre>';die;
			$result1 = $result3 =  '';
			$data_arr2 = $data_arr2_tmp = $data_arr2a_tmp = array();
			if($result === false)
			{
				//echo 'Curl Error : '.curl_error($ch).' => CODE : '.curl_errno($ch);die;
			}
			else
			{
				$result1 = $result;
				$result2 = json_decode($result,true); 
				//echo $sap_username.' => '.$sap_password.'<pre>';
			    //print_r($result2);
			    //echo '</pre>';die;
				$tmparr = $result_sorted = array();
				if($result2)
				{
					foreach($result2 as $orgkey=>$orgarr)
					{
						$tmparr[$orgkey] = $orgarr['EBELP'];
					}
					asort($tmparr);
										
					foreach($tmparr as $sortkey=>$sortserno)
					{
						$tmparr1 = $result2[$sortkey];
						$result_sorted[] = $tmparr1;
					}
					
				}
				
				$data_resp = json_encode($result_sorted);
				
			}
			
		}
		return $data_resp;
	}
	
	public function savestoreuserdata(Request $request)
	{
		$jsonarr = array('message' => 'fail');
		//if(count($request->matidarr) > 0 || count($request->contidarr) > 0)
		if(isset($request->matidarr) || isset($request->contidarr))
		{
			$gtidval = isset($request->gtidval) ? $request->gtidval : 0;
			$gtidval = ($gtidval == '') ? 0 : $gtidval;
			
			$sec_name_out = isset($request->sec_name_out) ? $request->sec_name_out : '';
			
			$storeuserid = 0;
			if(Auth::user())
			{
				$storeuserid = Auth::user()->id;
			}
			
			if(isset($request->matidarr))
			{
				if(count($request->matidarr) > 0)
				{
					foreach($request->matidarr as $k1=>$mat_id)
					{
						if(!empty($mat_id))
						{ 
							$storeqty = isset($request->storeqtyarr[$k1]) ? $request->storeqtyarr[$k1] : 0;
							$storerem = isset($request->storeremarr[$k1]) ? $request->storeremarr[$k1] : '';			
							
							$storeqty = ($storeqty == '') ? 0 : $storeqty;
							$dtobj = Carbon::now();
							$storedt = Carbon::now()->format('Y-m-d H:i:s');
							
							$prod_data = array(
								'storeqty' => $storeqty,
								'storeremark' => $storerem,
								'storeuserid' => $storeuserid,
								'storedt' => $storedt,
								
							);
							
							DB::table("gate_entry_item_details")->where('id',$mat_id)->update($prod_data);
						}
						
					}
				}
			}
			
			if(isset($request->contidarr))
			{	
				if(count($request->contidarr) > 0)
				{
					foreach($request->contidarr as $k2=>$cont_id)
					{
						if(!empty($cont_id))
						{ 
							$storeqty = isset($request->cont_storeqtyarr[$k2]) ? $request->cont_storeqtyarr[$k2] : 0;
							$storerem = isset($request->cont_storeremarr[$k2]) ? $request->cont_storeremarr[$k2] : '';			
							
							$storeqty = ($storeqty == '') ? 0 : $storeqty;
							$dtobj = Carbon::now();
							$storedt = Carbon::now()->format('Y-m-d H:i:s');
							
							$prod_data = array(
								'cont_storeqty' => $storeqty,
								'cont_storeremark' => $storerem,
								'storeuserid' => $storeuserid,
								'storedt' => $storedt,
								
							);
							
							DB::table("gate_entry_containers")->where('id',$cont_id)->update($prod_data);
						}
						
					}
				}
			}
			
			if($gtidval)
			{
				$deptuserid = 0;
				if(Auth::user())
				{
					$deptuserid = Auth::user()->id;
				}
				
				$dtobj = Carbon::now();
				$deptdt = Carbon::now()->format('Y-m-d H:i:s');
				
				$save_data = array('security_name_out' => $sec_name_out, 'dept_user' => $deptuserid, 'dept_dt' => $deptdt, 'status' => 2);
				DB::table("gate_entry_details")->where('id',$gtidval)->update($save_data);
				
				$gateentrynew = Quotation::where('id',$gtidval)->first();
				//Generate PDF
				$this->generatepdf($gateentrynew,1);
				//Send Email
				$this->send_email($gateentrynew,0);
				//Send Email
			}
			
			$jsonarr = array('message' => 'success');
			
		}
		
		return json_encode($jsonarr);
	}
	
	public function savecheckoutdata(Request $request)
	{
		$jsonarr = array('message' => 'fail');
		//gtidcheckout:gtidcheckout,secnamechkout:secnamechkout,chkoutremark:chkoutremark
		if(!(empty($request->gtidcheckout)))
		{
			$gtidcheckout = isset($request->gtidcheckout) ? $request->gtidcheckout : 0;
			$gtidcheckout = ($gtidcheckout == '') ? 0 : $gtidcheckout;
			
			$secnamechkout = isset($request->secnamechkout) ? $request->secnamechkout : '';
			$chkoutremark = isset($request->chkoutremark) ? $request->chkoutremark : '';
			
			$checkoutuserid = 0;
			if(Auth::user())
			{
				$checkoutuserid = Auth::user()->id;
			}
			
			$dtobj = Carbon::now();
			$checkoutdt = Carbon::now()->format('Y-m-d H:i:s');
			$gate_out_date = Carbon::now()->format('Y-m-d');
			$gate_out_time = Carbon::now()->format('H:i:s');
			
			$chk_data = array(
				'security_checkout_name' => $secnamechkout,
				'checkout_remark' => $chkoutremark,
				'checkout_by' => $checkoutuserid,
				'checkout_dt' => $checkoutdt,
				'gate_out_date' => $gate_out_date,
				'gate_out_time' => $gate_out_time,
				'status' => 3
			);
			
			DB::table("gate_entry_details")->where('id',$gtidcheckout)->update($chk_data);
			
			$gateentrynew = Quotation::where('id',$gtidcheckout)->first();
				//Generate PDF
				$this->generatepdf($gateentrynew);
				//Send Email
				$this->send_email($gateentrynew);
				//Send Email
			
			$jsonarr = array('message' => 'success');
			
		}
		
		return json_encode($jsonarr);
	}
	
	public function get_pdf_filename($id, $gate_no,$isrep=0)
    {
		$prefixn = ($isrep) ? 'GATEREP_' : 'GATE_';
        $pdf_name = $prefixn.'.pdf';
        if($id)
        {
            if($gate_no != '')
            {
                $pdf_name = $prefixn.$id.'_'.$gate_no.'.pdf';
            }
            else
            {
                $pdf_name = $prefixn.$id.'.pdf';
            }
        }
        return $pdf_name;
    }
	
	public function generatepdf($gateentry,$isrep=0)
	{
		//PDF
		
		$id = $gateentry->id;
		//$gateentry = Quotation::where('id',$id)->first();
		$materialDetails = DB::table('gate_entry_item_details')->where('gate_entry_id',$gateentry->id)->where('is_delete',0)->get();
		
		$containerDetails = DB::table('gate_entry_containers')->where('gate_entry_id',$gateentry->id)->where('is_delete',0)->get();
		
		$seldoclbl = 'NA';
		$location = $created_by = $dept_name = $addr1 = $addr2 = $dept_incharge = $closed_by = '';
		$selvehidlbl = 'Vehicle';
		$seldellbl = 'Driver';
		
		if($gateentry)
		{
			if($gateentry->doc_type_code == 'PO')
			{
				$seldoclbl = 'PO';
			}
			else if($gateentry->doc_type_code == 'DC')
			{
				$seldoclbl = 'DC';
			}
			else if($gateentry->doc_type_code == 'OTH')
			{
				$seldoclbl = 'Other Ref.';
			}
			
			if($gateentry->vehicle_type_code == '06')
			{
				$selvehidlbl = 'Identity';
				$seldellbl = 'Person';
			}
			else if( $gateentry->vehicle_type_code == '07')
			{
				$selvehidlbl = 'Porter/Courier/Online';
				$seldellbl = 'Person';
			}
			
			if($gateentry->loc_code != '')
			{
				$loc_obj = DB::table('location_master')->select(array('loc_name','Address1','Address2'))->where('loc_code',$gateentry->loc_code)->first();
				if($loc_obj)
				{
					$location = $loc_obj->loc_name;
					$addr1 = $loc_obj->Address1;
					$addr2 = $loc_obj->Address2;
				}
				
			}
			
			if($gateentry->dept_id)
			{
				$dept_obj = DB::table('departments')->select('dept_name')->where('id',$gateentry->dept_id)->first();
				if($dept_obj)
				{
					$dept_name = $dept_obj->dept_name;
				}
			}
			
			if($gateentry->dept_user)
			{
				$userobj = DB::table('users')->select('name')->where('id',$gateentry->dept_user)->first();
				if($userobj)
				{
					$dept_incharge = $userobj->name;
				}
				
			}
			
			if($gateentry->checkout_by)
			{
				$userobj2 = DB::table('users')->select('name')->where('id',$gateentry->checkout_by)->first();
				if($userobj2)
				{
					$closed_by = $userobj2->name;
				}
				
			}
			
			
		}
		
		$doc_no = '';
		if($materialDetails->count() > 0)
		{
			$doc_no = $materialDetails[0]->po_no;
		}
		
		$logo_img = public_path('img/logo.png');
        $live_url = config('app.url');
		
		//$pdf_name = $this->get_pdf_filename($id, $gateentry->gate_in_no,$isrep);
		$pdf_name = $this->get_pdf_filename($id, $gateentry->gate_in_no,0);
		
		//$prefixn = ($isrep) ? 'gateprintreport' : 'gateprint';
		$prefixn = 'quotationprint';
		$pdf = PDF::loadView('quotations.'.$prefixn,  compact('logo_img','gateentry','materialDetails','seldoclbl','doc_no','location','dept_name','addr1','addr2','dept_incharge','containerDetails','selvehidlbl','seldellbl','closed_by'));
		
		$folder = storage_path('uploads/quotations');
		$pdf_path = $folder.'/'.$pdf_name;
        $pdf->save($pdf_path);
		
		//CONFIRM BY STORE USER
		if($isrep)
		{
			$pdf_name = $this->get_pdf_filename($id, $gateentry->gate_in_no,1);
		
			//$prefixn = ($isrep) ? 'gateprintreport' : 'gateprint';
			$prefixn = 'quotationprintreport';
			$pdf = PDF::loadView('quotations.'.$prefixn,  compact('logo_img','gateentry','materialDetails','seldoclbl','doc_no','location','dept_name','addr1','addr2','dept_incharge','containerDetails','selvehidlbl','seldellbl','closed_by'));
			
			$folder = storage_path('uploads/quotations');
			$pdf_path = $folder.'/'.$pdf_name;
			$pdf->save($pdf_path);
		}
		//CONFIRM BY STORE USER
		
		//return view('gateentries.gateprint', compact('logo_img','gateentry','materialDetails','seldoclbl','doc_no','location','dept_name','addr1','addr2','dept_incharge'));
		//PDF
	}
	
	public function send_email($gateentrynew,$isgateentry=0)
	{
		$id = $gateentrynew->id;
		$is_enable_email = env("ENABLE_EMAIL",0);
		if($is_enable_email)
        {
			$isstore = ($isgateentry == 1 ) ? 1 : 0;
			$receiversarr = $this->getEmailReceivers($gateentrynew,$isstore);
			if($receiversarr)
			{	
				$dept_name = '';
				if($gateentrynew->dept_id)
				{
					$dept_obj = DB::table('departments')->select('dept_name')->where('id',$gateentrynew->dept_id)->first();
					if($dept_obj)
					{
						$dept_name = $dept_obj->dept_name;
					}
				}
			
				if($isgateentry == 1 || $gateentrynew->status == 3)
				{
					$sender = $gateentrynew->createdby_name;
					
				}
				else
				{
					if($gateentrynew->dept_user)
					{
						$userobj = DB::table('users')->select('name')->where('id',$gateentrynew->dept_user)->first();
						if($userobj)
						{
							$sender = $userobj->name;
						}
					}
					
				}
				
				if($gateentrynew->status == 3)
				{
					if($gateentrynew->checkout_by)
					{
						$userobj = DB::table('users')->select('name')->where('id',$gateentrynew->checkout_by)->first();
						if($userobj)
						{
							$sender = $userobj->name;
						}
					}
				}
				
				$form_url = url("/gateentries/");
				$isrep = 0;
				if($gateentrynew->status == 1)
				{
					$mail_body = 'Enclose please find that '.$sender.' has send following Gate Entry Request. Please click the below link for more details.';
					
					$subject = 'New Gate Entry ('.$gateentrynew->gate_in_no.') For '.$gateentrynew->doc_type_name.' From Gate : '.$gateentrynew->sec_id_gt_in_name;
					
					$form_url = url("/gateentries/".$id."/edit");
					$isrep = 0;
				}
				else if($gateentrynew->status == 2)
				{
					$mail_body = 'Enclose please find that Department '.$dept_name.' through '.$sender.' has confirmed the following Gate Entry Request. Please click the below link for more details.';
					
					$subject = 'Confirmed: Gate Entry ('.$gateentrynew->gate_in_no.') For '.$gateentrynew->doc_type_name.' From Gate : '.$gateentrynew->sec_id_gt_in_name;
					
					$form_url = url("/gateentries/".$id."/edit");
					$isrep = 1;
				}
				else if($gateentrynew->status == 3)
				{
					$mail_body = 'Enclose please find that '.$sender.' has closed the following Gate Entry Request. Please click the below link for more details.';
					
					$subject = 'Closed: Gate Entry ('.$gateentrynew->gate_in_no.') For '.$gateentrynew->doc_type_name.' From Gate : '.$gateentrynew->sec_id_gt_in_name;
					
				}
                
				
				$materialDetails = DB::table('gate_entry_item_details')->where('gate_entry_id',$gateentrynew->id)->where('is_delete',0)->get();
				$doc_no = '';
				if($materialDetails->count() > 0)
				{
					$doc_no = $materialDetails[0]->po_no;
				}
				
				$mailData = [
                         'gateentry_id' => $gateentrynew->id,
                         'title' => $subject,
                         'body' => $mail_body,
                         'vendorname' => $gateentrynew->vendor_name,
                         //'purchaseorg' => $purchase_org,
                         'gate_in_no' => $gateentrynew->gate_in_no,
                         'sec_id_gt_in_name' => $gateentrynew->sec_id_gt_in_name,
                         'gate_in_time' =>  ($gateentrynew) ? (($gateentrynew->gate_in_date) ? date('d-m-Y H:i', strtotime($gateentrynew->gate_in_date.' '.$gateentrynew->gate_in_time)) : '') : '',
                         'gate_out_time' => ($gateentrynew) ? (($gateentrynew->gate_out_date) ? date('d-m-Y H:i', strtotime($gateentrynew->gate_out_date.' '.$gateentrynew->gate_out_time)) : '') : '',
                         'form_url' => $form_url,
                         'doc_type_name' => $gateentrynew->doc_type_name,
                         'doc_no' => $doc_no,
						 'vehicle_type_desc' => $gateentrynew->vehicle_type_desc,
						 'vehicle_no' => $gateentrynew->vehicle_no,
						 'dept_name' => $dept_name,
						 'sender' => $sender
                         //'attachment' => public_path($pdf_name)
                    ];
					
					
					//$pdf_name = $this->get_pdf_filename($id, $gateentrynew->gate_in_no,$isrep);
					$pdf_name = $this->get_pdf_filename($id, $gateentrynew->gate_in_no,0);
					if($pdf_name != '')
					{
						$mailData['attachment'] = storage_path("uploads\\gateentries\\".$pdf_name);
					}
                
				$tmpuser = '';
				foreach($receiversarr as $useremail=>$username)
				{
					if($useremail != '')
					{
						$mailData['user_name'] = $username;
						//$tmpuser .= $username.' '.$useremail.', ';
						Mail::to($useremail)->send(new GateEntryMail($mailData));
					}
				}
				//CC
				$mailData['user_name'] = 'Admin';
				Mail::to('vickey.minj@growel.com')->send(new GateEntryMail($mailData));
				//CC
			}
		}
	}
	
	public function getEmailReceivers($gateentrynew,$isstore=0)
	{
		$userarr = array();
		$userrole = ($isstore==1) ? 'StoreUser' : 'User';
		$plant = ($gateentrynew) ? $gateentrynew->plant_code : '';
		$loc = ($gateentrynew) ? $gateentrynew->loc_code : '';
		
		$users = User::role($userrole)->get();
		if($users->count() > 0)
		{
			foreach($users as $userobj)
			{
				$isuserok = false;
				if($userobj->loc_code)
				{
					$user_loc = explode(",",$userobj->loc_code);
					if(in_array($loc,$user_loc))
					{
						$isuserok = true;
					}
				}
				if($isuserok)
				{
					$userarr[$userobj->email] = $userobj->name;
				}
				
			}
		}
		
		//DEPT. USER
		$dept_id = ($gateentrynew) ? $gateentrynew->dept_id : 0;
		if($dept_id)
		{
			$res1 = DB::table('departments')
					->select('roles.name')
					->join('roles','roles.id','=','departments.rolemapping')
					->where('departments.id',$dept_id)
					->first();
			if($res1)
			{
				if($res1->name != '')
				{
					$users2 = User::role($res1->name)->get();
					if($users2->count() > 0)
					{
						foreach($users2 as $userobj)
						{
							$isuserok = false;
							if($userobj->loc_code)
							{
								$user_loc = explode(",",$userobj->loc_code);
								if(in_array($loc,$user_loc))
								{
									$isuserok = true;
								}
							}
							if($isuserok)
							{
								$userarr[$userobj->email] = $userobj->name;
							}
							
						}
					}
				}
			}
		}
		//DEPT. USER
		
		return $userarr;
	}
	
	public function getpdfDownload(Request $request)
    {
        
            if($request->id)
            {
                $gateentrynew = Quotation::where('id',$request->id)->first();
				
				$is_storeuser = false;
				$userRoles = array();
				if(Auth::user())
				{
					$userRoles = Auth::user()->roles->pluck('name')->toArray();
				}
				
				//if(in_array('StoreUser', $userRoles))
				if((in_array('Super Admin', $userRoles)) || (in_array('User', $userRoles))) {}
				else
				{
					$is_storeuser = true;
				}
				
				$isrep = 0;
				if($is_storeuser)
				{
					$isrep = 1;
				}
				
				if($gateentrynew->status == 1)
				{
					$isrep = 0;
				}
				
                $pdf_name = $this->get_pdf_filename($request->id, $gateentrynew->gate_in_no,$isrep);

                //$folder = storage_path('uploads/vendorpdf');
                //$file = $folder.'/'.$pdf_name;
				
				$file = storage_path("uploads/gateentries/".$pdf_name);
                //$file= public_path(). "/pdf/".$pdf_name;

                $headers = array(
                      'Content-Type: application/pdf',
                    );
                return Response::download($file, $pdf_name, $headers);
            }
            else
            {
                return redirect(url("/"));
            }
        

        
    }
	
	public function getfpproductsname(Request $request)
    {
        $fptype = isset($request->fptype) ? $request->fptype : "";
        $table_name_fp = "product_master";

        if($request->prodname_s)
        {
            $sql1 = DB::table($table_name_fp);
            $sql1->select(DB::Raw("TOP 100 PROD_DESC, PROD_CODE, PROD_RATE, PACK_SIZE"));
            $sql1->where("PROD_DESC","LIKE","%".$request->prodname_s."%");
            $sql1->where("is_delete","0");
            $product_lists = $sql1->get(); 

            if($product_lists)
            {
                $prod_arr = array();
                foreach($product_lists as $valobj)
                {
                    $prod_arr[] = array("value"=>$valobj->PROD_CODE.'$$$'.$valobj->PACK_SIZE, "label"=>$valobj->PROD_DESC);
                }
                $return_json = $prod_arr;
                echo json_encode($return_json); die();
            }
            else
            {
                $return_json = 0;
                echo json_encode($return_json); die();
            }
        }
    }
	
	public function getprddata(Request $request)
	{
		$prod_rate = $prod_rate_total = $pack_size = $inr_usd = $aed_usd = $cost_pallet = $pallet_weight = $india_thc_cbm = $included_pallets = $uae_thc_pallet  = $import_duty = $handling_loss = $warehouse_month = $warehouse_cost_kg_month  = $cost_of_interest_month = $interest_duration = $listprice_usd = $listprice_usd_total = $pack_fcl_nh = $fob_fcl_nh = $pack_lcl_nh_pl = $fob_fcl_h_pl = $fob_lcl_h_pl = $cif_fcl_nh = $cif_fcl_nh_tmp = $cif_lcl_nh_pl = $cif_lcl_nh_pl_tmp = $cif_fcl_h_pl = $cif_fcl_h_pl_tmp = $cif_lcl_h_pl = $cif_lcl_h_pl_tmp = $landed_fcl_nh = $landed_lcl_nh_pl = $landed_fcl_h_pl = $landed_lcl_h_pl = $unp_exwork_usd = $tmp_pk_fcl_nh_usd = $tmp_cfr_fcl_nh_usd = $tmp_landed_fcl_nh_usd = $tmp_pk_fcl_h_usd = $disc_margin = $recom_dis_sp_to_buyer = $recom_sp_aft_credit_miniman = $exp_cred_cost_thr_months = $estd_miniman_cst = $cst_fwd_distr_buyer = $tmp_cfr_fcl_h_usd = $tmp_landed_fcl_h_usd = $kondm_val = $ttlcost_inc_finance = 0;
		$kondm = '';
		$resp_arr = array(
			'prod_rate_inr' => 0, 
			'prod_rate_usd' => 0, 
			'prod_rate_inr_total' => 0, 
			'prod_rate_usd_total' => 0, 
			'unp_exwork_inr' => 0, 
			'disc_type' => '',
			'disc_val' => 0,
			'pack_fcl_nh' => 0,
			'pack_lcl_nh_pl' => 0,
			'fob_fcl_nh' => 0,
			'fob_lcl_nh_pl' => 0,
			'fob_fcl_h_pl' => 0,
			'fob_lcl_h_pl' => 0,
			'cif_fcl_nh' => 0,
			'cif_lcl_nh_pl' => 0,
			'cif_fcl_h_pl' => 0,
			'cif_lcl_h_pl' => 0,
			'landed_fcl_nh' => 0,
			'landed_lcl_nh_pl' => 0,
			'landed_fcl_h_pl' => 0,
			'landed_lcl_h_pl' => 0,
			'ttlcost_inc_finance' => 0,
			'recom_dis_sp_to_buyer' => 0,
			'recom_sp_aft_credit_miniman' => 0,
			'kondm' => '',
			'kondm_val' => 0
		);
				
		$fptype = isset($request->fptype) ? $request->fptype : "";
		$cargo_type = isset($request->cargo_type) ? $request->cargo_type : "";
		$currency_type = isset($request->currency_type) ? $request->currency_type : "USD";
		
        $table_name_fp = "product_master";

        if($request->prodcode_s)
        {
			$sql1_res = DB::table($table_name_fp)
					->select(array('PROD_RATE','PACK_SIZE','KONDM'))
					->where('PROD_CODE',$request->prodcode_s)
					->where('is_delete',0)
					->first();
			if($sql1_res)
			{
				$prod_rate_total = $sql1_res->PROD_RATE;
				$pack_size = $sql1_res->PACK_SIZE;
				$kondm = $sql1_res->KONDM;
				
			}
			
			if($prod_rate_total)
			{
				if($pack_size > 0)
				{
					$prod_rate = $prod_rate_total / $pack_size;
				}
				$cfg_results = DB::table('config_fields')
							->select(array('para_name','para_value'))
							->where('para_name','inr_usd')
							->orWhere('para_name','aed_usd')
							->orWhere('para_name','cost_pallet')
							->orWhere('para_name','pallet_capacity')
							->orWhere('para_name','india_thc_cbm')
							->orWhere('para_name','included_pallets')
							->orWhere('para_name','uae_thc_pallet')
							->orWhere('para_name','import_duty')
							->orWhere('para_name','handling_loss')
							->orWhere('para_name','warehouse_month')
							->orWhere('para_name','warehouse_cost_kg_month')
							->orWhere('para_name','cost_of_interest_month')
							->orWhere('para_name','interest_duration')
							->orWhere('para_name','exp_cred_cost_thr_months')
							->orWhere('para_name','estd_miniman_cst')
							->orWhere('para_name','cst_fwd_distr_buyer')
							->where('is_delete',0)
							->get();
				if($cfg_results->count() > 0)
				{
					foreach($cfg_results as $cfg_res)
					{
						if($cfg_res->para_name == 'inr_usd')
						{
							$inr_usd = $cfg_res->para_value;
						}
						else if($cfg_res->para_name == 'aed_usd')
						{
							$aed_usd = $cfg_res->para_value;
						}
						else if($cfg_res->para_name == 'cost_pallet')
						{
							$cost_pallet = $cfg_res->para_value;
						}
						else if($cfg_res->para_name == 'pallet_capacity')
						{
							$pallet_weight = $cfg_res->para_value;
						}
						else if($cfg_res->para_name == 'india_thc_cbm')
						{
							$india_thc_cbm = $cfg_res->para_value;
						}
						else if($cfg_res->para_name == 'included_pallets')
						{
							$included_pallets = $cfg_res->para_value;
						}
						else if($cfg_res->para_name == 'uae_thc_pallet')
						{
							$uae_thc_pallet = $cfg_res->para_value;
						}
						else if($cfg_res->para_name == 'import_duty')
						{
							$import_duty = $cfg_res->para_value;
						}
						else if($cfg_res->para_name == 'handling_loss')
						{
							$handling_loss = $cfg_res->para_value;
						}
						else if($cfg_res->para_name == 'warehouse_month')
						{
							$warehouse_month = $cfg_res->para_value;
						}
						else if($cfg_res->para_name == 'warehouse_cost_kg_month')
						{
							$warehouse_cost_kg_month = $cfg_res->para_value;
						}
						else if($cfg_res->para_name == 'cost_of_interest_month')
						{
							$cost_of_interest_month = $cfg_res->para_value;
						}
						else if($cfg_res->para_name == 'interest_duration')
						{
							$interest_duration = $cfg_res->para_value;
						}
						else if($cfg_res->para_name == 'exp_cred_cost_thr_months')
						{
							$exp_cred_cost_thr_months = $cfg_res->para_value;
						}
						else if($cfg_res->para_name == 'estd_miniman_cst')
						{
							$estd_miniman_cst = $cfg_res->para_value;
						}
						else if($cfg_res->para_name == 'cst_fwd_distr_buyer')
						{
							$cst_fwd_distr_buyer = $cfg_res->para_value;
						}
						
					}
					
				}
			}
			
			$exp_cred_cost_thr_months3 = isset($request->exp_cred_cost_thr_months3) ? $request->exp_cred_cost_thr_months3 : 0;
			$exp_cred_cost_thr_months = ($exp_cred_cost_thr_months3=='' || $exp_cred_cost_thr_months3 == NULL) ? 0 : $exp_cred_cost_thr_months3;
			
			$estd_miniman_cst3 = isset($request->estd_miniman_cst3) ? $request->estd_miniman_cst3 : 0;
			$estd_miniman_cst = ($estd_miniman_cst3=='' || $estd_miniman_cst3 == NULL) ? 0 : $estd_miniman_cst3;
			
			if($inr_usd)
			{
				$listprice_usd = $prod_rate / $inr_usd;
				$listprice_usd_total = $prod_rate_total / $inr_usd;
			}
			
			$disc_val = isset($request->disc_val) ? $request->disc_val : 0;
			$calltypdisc = isset($request->calltypdisc) ? $request->calltypdisc : 0;
			$disc_type = isset($request->disc_type) ? $request->disc_type : '';
			$ship_vol_qty = isset($request->ship_vol_qty) ? $request->ship_vol_qty : 0;
			$ttlshipvol = isset($request->ttlshipvol) ? $request->ttlshipvol : 0;
			$ttlshipvol = (ceil($ttlshipvol / 100)) * 100;
			
			if(!$calltypdisc)
			{
				if($disc_type == '')
				{
					if($kondm)
					{
						$disc_obj2 = DB::table('config_discount')
							->select(array('id'))
							->where('disc_type',$kondm)
							->where('is_delete',0)
							->first();
							
						if($disc_obj2)
						{
							$disc_type = $disc_obj2->id;
						}
					}
				}
			}
			
			
			$disc_obj = DB::table('config_discount')
							->select(array('disc_val','disc_margin'))
							->where('id',$disc_type)
							->where('is_delete',0)
							->first();
							
			if($calltypdisc)
			{
				if($disc_obj)
				{
					$disc_val = $disc_obj->disc_val;
					$disc_val = ($disc_val=='' || $disc_val == NULL) ? 0 : $disc_val;
					
					$disc_margin = $disc_obj->disc_margin;
					$disc_margin = ($disc_margin=='' || $disc_margin == NULL) ? 0 : $disc_margin;
				}
			}
			else if($kondm)
			{
				if(!$disc_val)
				{
					if($disc_obj)
					{
						$disc_val = $disc_obj->disc_val;
						$disc_val = ($disc_val=='' || $disc_val == NULL) ? 0 : $disc_val;
						
						$disc_margin = $disc_obj->disc_margin;
						$disc_margin = ($disc_margin=='' || $disc_margin == NULL) ? 0 : $disc_margin;
					}
				}
			}
			
			
			if($disc_obj)
			{				
				$disc_margin = $disc_obj->disc_margin;
				$disc_margin = ($disc_margin=='' || $disc_margin == NULL) ? 0 : $disc_margin;
			}
												
			$resp_arr['prod_rate_inr'] = round($prod_rate,2);
			$resp_arr['prod_rate_usd'] = round($listprice_usd,2);
			if($currency_type == 'AED')
			{
				$listprice_aed = $listprice_usd * $aed_usd;
				$resp_arr['prod_rate_usd'] = round($listprice_aed,2);
			}
			
			$resp_arr['prod_rate_inr_total'] = round($prod_rate_total,2);
			$resp_arr['prod_rate_usd_total'] = round($listprice_usd_total,2);
			if($currency_type == 'AED')
			{
				$listprice_aed_total = $listprice_usd_total * $aed_usd;
				$resp_arr['prod_rate_usd_total'] = round($listprice_aed_total,2);
			}
			
			$unp_exwork_inr = $prod_rate * (1 - ($disc_val/100));
			$unp_exwork_inr_tmp = round($unp_exwork_inr, 2);
			$unp_exwork_usd = $listprice_usd * (1 - ($disc_val/100));
			$unp_exwork_usd_tmp = round($unp_exwork_usd,2);
			
			//$resp_arr['unp_exwork_inr'] = number_format($unp_exwork_inr_tmp, 2);
			$resp_arr['unp_exwork_inr'] = $unp_exwork_inr_tmp;
			$resp_arr['unp_exwork_usd'] = round($unp_exwork_usd_tmp, 2);
			if($currency_type == 'AED')
			{
				$unp_exwork_aed_tmp = $unp_exwork_usd_tmp * $aed_usd;
				$resp_arr['unp_exwork_usd'] = round($unp_exwork_aed_tmp,2);
			}
			
			//Packed FCL NH UNPALLETIZED
			if($ship_vol_qty > 0)
			{
				//pack_fcl_nh
				$PriceWt = 0;
				$tmp_shipvol = $this->getRates("Non-Haz","FCL", 0,$ttlshipvol,"Packed"); 
				
				//CargoType,ShipmentType,isPalletized,Weight,Sheetname
				
				if($tmp_shipvol > 0)
				{
					//get Lashing
					$lashing_weight = $lashing = 0;
					$shipcap_res = DB::table('config_fclnh_shipvolcap')
									->whereRaw("upto_weight >= $tmp_shipvol")
									->orderby("upto_weight","asc")
									->first();
					if($shipcap_res)
					{
						$lashing_weight = $shipcap_res->set_weight_cap;
					}
					
					$lash_res = DB::table('config_working')
								->where('CargoType','Non-Haz')
								->where('ShipmentType','FCL')
								->where('Palletized',0)
								->where('Weight',$lashing_weight)
								->where('is_delete',0)
								->first();
					if($lash_res)
					{
						$lashing = $lash_res->Lashing;
						if($lashing=='' || $lashing=='null') {$lashing = 0;}
					}
					//get Lashing
					//$tmp_pk_fcl_nh_inr = ($lashing / $ttlshipvol);
					$tmp_pk_fcl_nh_inr = ($lashing / $tmp_shipvol);
					
					if($inr_usd > 0)
					{
						$tmp_pk_fcl_nh_usd = round(($tmp_pk_fcl_nh_inr / $inr_usd), 2);
					}
					$pack_fcl_nh = $unp_exwork_usd + $tmp_pk_fcl_nh_usd;
					$pack_fcl_nh = round($pack_fcl_nh,2);
				}
				
			}
			
			
			$resp_arr['pack_fcl_nh'] = round($pack_fcl_nh, 2);
			if($currency_type == 'AED')
			{
				$pack_fcl_nh_aed = $pack_fcl_nh * $aed_usd;
				$resp_arr['pack_fcl_nh'] = round($pack_fcl_nh_aed,2);
			}
			
			//Packed FCL NH UNPALLETIZED
			
			//Packed LCL NH PALLETIZED
			if($ship_vol_qty > 0)
			{
				//pack_fcl_nh
				$PriceWt = 0;
				if($ttlshipvol < 10000)
				{
					$tmp_shipvol = $this->getRates("Non-Haz","LCL", 1,$ttlshipvol,"Packed"); 
					//CargoType,ShipmentType,isPalletized,Weight,Sheetname
					
					if($tmp_shipvol > 0)
					{
						//get Lashing
						//Pallet Cost * $ship_vol_qty
						
						if($cost_pallet)
						{
							if($pallet_weight)
							{
								$tmp_pl_weight = ceil($tmp_shipvol / $pallet_weight);
								$tmp_pk_lcl_nh_inr = ($cost_pallet * $tmp_pl_weight) / $tmp_shipvol;
							}
							
						}
						
						$tmp_pk_lcl_nh_usd = 0;
						if($inr_usd > 0)
						{
							$tmp_pk_lcl_nh_usd = $tmp_pk_lcl_nh_inr / $inr_usd;
						}
						$pack_lcl_nh_pl = $unp_exwork_usd + $tmp_pk_lcl_nh_usd;
						$pack_lcl_nh_pl = round($pack_lcl_nh_pl,2);
					}
					
					$resp_arr['pack_lcl_nh_pl'] = $pack_lcl_nh_pl;
					if($currency_type == 'AED')
					{
						$pack_lcl_nh_pl_aed = $pack_lcl_nh_pl * $aed_usd;
						$resp_arr['pack_lcl_nh_pl'] = round($pack_lcl_nh_pl_aed,2);
					}
				}
				else
				{
					$resp_arr['pack_lcl_nh_pl'] = "Too Large";
				}
			}
			
			//Packed LCL NH PALLETIZED
			
			//FOB FCL NH UNPALLETIZED
			if($ship_vol_qty > 0)
			{
				//pack_fcl_nh
				$PriceWt = 0;
				$tmp_shipvol = $this->getRates("Non-Haz","FCL", 0,$ttlshipvol,"FOB"); 
				//CargoType,ShipmentType,isPalletized,Weight,Sheetname
				
				if($tmp_shipvol > 0)
				{
					//get Lashing
					$lashing_weight2 = $lashing2 = $transport2 = $forwarding2 = $indhaz2 = 0;
					//Following table only for FCL Non-Haz
					$shipcap_res = DB::table('config_fclnh_shipvolcap')
									->whereRaw("upto_weight >= $tmp_shipvol")
									->orderby("upto_weight","asc")
									->first();
					if($shipcap_res)
					{
						$lashing_weight2 = $shipcap_res->set_weight_cap;
					}
					
					$lash_res2 = DB::table('config_working')
								->where('CargoType','Non-Haz')
								->where('ShipmentType','FCL')
								->where('Palletized',0)
								->where('Weight',$lashing_weight2)
								->where('is_delete',0)
								->first();
					if($lash_res2)
					{
						$lashing2 = $lash_res2->Lashing;
						$transport2 = $lash_res2->Transport;
						$forwarding2 = $lash_res2->Forwarding;
						$indhaz2 = $lash_res2->IndHAZ;
						
						if($lashing2=='' || $lashing2=='null') {$lashing2 = 0;}
						if($transport2=='' || $transport2=='null') {$transport2 = 0;}
						if($forwarding2=='' || $forwarding2=='null') {$forwarding2 = 0;}
						if($indhaz2=='' || $indhaz2=='null') {$indhaz2 = 0;}
					}
					//get Lashing
					
					//$tmp_fob_fcl_nh_inr = (($transport2 + $lashing2 + $forwarding2 + ($indhaz2 * $inr_usd)) / $ttlshipvol);
					
					$tmp_fob_fcl_nh_inr = (($transport2 + $lashing2 + $forwarding2 + ($indhaz2 * $inr_usd)) / $tmp_shipvol);
					
					$tmp_fob_fcl_nh_inr = $tmp_fob_fcl_nh_inr;
					$tmp_fob_fcl_nh_usd = 0;
					if($inr_usd > 0)
					{
						$tmp_fob_fcl_nh_usd = $tmp_fob_fcl_nh_inr / $inr_usd;
					}
					
					$fob_fcl_nh = $unp_exwork_usd + $tmp_fob_fcl_nh_usd;
					$fob_fcl_nh = round($fob_fcl_nh,2);
					
				}
				
			}
						
			$resp_arr['fob_fcl_nh'] = $fob_fcl_nh;
			if($currency_type == 'AED')
			{
				$fob_fcl_nh_aed = $fob_fcl_nh * $aed_usd;
				$resp_arr['fob_fcl_nh'] = round($fob_fcl_nh_aed,2);
			}
			
			//FOB FCL NH UNPALLETIZED
			
			//FOB LCL NH PALLETIZED
			if($ship_vol_qty > 0)
			{
				//pack_fcl_nh
				$PriceWt = 0;
				
				if($ttlshipvol < 10000)
				{
					$tmp_shipvol = $this->getRates("Non-Haz","LCL", 1,$ttlshipvol,"FOB"); 
					//CargoType,ShipmentType,isPalletized,Weight,Sheetname
					
					if($tmp_shipvol > 0)
					{
						//get Lashing
						//Pallet Cost * $ship_vol_qty
						
						$lashing_weight3 = $lashing3 = $transport3 = $forwarding3 = $indhaz3 = 0;
						/*$shipcap_res3 = DB::table('config_fclnh_shipvolcap')
										->whereRaw("upto_weight >= $tmp_shipvol")
										->orderby("upto_weight","asc")
										->first();
						if($shipcap_res3)
						{
							$lashing_weight3 = $shipcap_res3->set_weight_cap;
						}*/
						
						$lashing_weight3 = $ttlshipvol;
						
						$lash_res3 = DB::table('config_working')
								->where('CargoType','Non-Haz')
								->where('ShipmentType','LCL')
								->where('Palletized',1)
								->whereRaw('Weight >= '.$lashing_weight3)
								->where('is_delete',0)
								->orderby('Weight','asc')
								->first();
								
						if($lash_res3)
						{
							
							$transport3 = $lash_res3->Transport;
							$forwarding3 = $lash_res3->Forwarding;
							$indhaz3 = $lash_res3->IndHAZ;
							
							
							if($transport3=='' || $transport3=='null') {$transport3 = 0;}
							if($forwarding3=='' || $forwarding3=='null') {$forwarding3 = 0;}
							if($indhaz3=='' || $indhaz3=='null') {$indhaz3 = 0;}
						}
						
						if($cost_pallet)
						{
							if($pallet_weight)
							{
								//$tmp_pl_weight = ceil($ship_vol_qty / $pallet_weight);
								//$tmp_fob_lcl_nh_inr = ($transport3 + ($cost_pallet * $tmp_pl_weight) + $forwarding3 + ($tmp_pl_weight * $india_thc_cbm) + ($indhaz3 * $inr_usd)) / $ship_vol_qty;
								
								$tmp_pl_weight = ceil($ttlshipvol / $pallet_weight);
								
								$tmp_fob_lcl_nh_inr = ($transport3 + ($cost_pallet * $tmp_pl_weight) + $forwarding3 + ($tmp_pl_weight * $india_thc_cbm) + ($indhaz3 * $inr_usd)) / $ttlshipvol;
								
								
								//echo 'Debug : '.$tmp_fob_lcl_nh_inr;die;
								//(7000 + 12000 + 8700 + 5000 + 0) / 
								//=(INDEX(Inputs[Transport],C22)+IF(OR(B$3,C$3,D$3),Pallet_Cost*D22,0)+IF(NOT(D$3),INDEX(Inputs[Lashing],C22),0)+INDEX(Inputs[Forwarding],C22)+IF(B$3,D22*THC_Per_CBM,0)+INDEX(Inputs[IndHAZ],C22)*Exchange_Rate)/$A22
								
							}
							
						}
						
						$tmp_fob_lcl_nh_usd = 0;
						if($inr_usd > 0)
						{
							$tmp_fob_lcl_nh_usd = $tmp_fob_lcl_nh_inr / $inr_usd;
						}
						$fob_lcl_nh_pl = $unp_exwork_usd + $tmp_fob_lcl_nh_usd;
						$fob_lcl_nh_pl = round($fob_lcl_nh_pl,2);
					}
					
					$resp_arr['fob_lcl_nh_pl'] = $fob_lcl_nh_pl;
					if($currency_type == 'AED')
					{
						$fob_lcl_nh_pl_aed = $fob_lcl_nh_pl * $aed_usd;
						$resp_arr['fob_lcl_nh_pl'] = round($fob_lcl_nh_pl_aed,2);
					}
				}
				else
				{
					$resp_arr['fob_lcl_nh_pl'] = "Too Large";
				}
			}
			
			//FOB LCL NH PALLETIZED
			
			//FOB FCL H PALLETIZED
			if($ship_vol_qty > 0)
			{
				//pack_fcl_nh
				
				$tmp_shipvol = $this->getRates("Haz","FCL", 1,$ttlshipvol,"FOB"); 
				//CargoType,ShipmentType,isPalletized,Weight,Sheetname
				
				if($tmp_shipvol > 0)
				{
					//get Lashing
					//Pallet Cost * $ship_vol_qty
										
					//$lashing_weight4 = $ship_vol_qty;
					$lashing_weight4 = $lashing4 = $transport4 = $forwarding4 = $indhaz4 = 0;
					
					$lash_res4 = DB::table('config_working')
							->where('CargoType','Haz')
							->where('ShipmentType','FCL')
							->where('Palletized',1)
							->whereRaw('Weight >= '.$ttlshipvol)
							->where('is_delete',0)
							->orderby('Weight','asc')
							->first();
						
					if($lash_res4)
					{
						
						$transport4 = $lash_res4->Transport;
						$forwarding4 = $lash_res4->Forwarding;
						$indhaz4 = $lash_res4->IndHAZ;
						
						
						if($transport4=='' || $transport4=='null') {$transport4 = 0;}
						if($forwarding4=='' || $forwarding4=='null') {$forwarding4 = 0;}
						if($indhaz4=='' || $indhaz4=='null') {$indhaz4 = 0;}
					}
					
					if($cost_pallet)
					{
						if($pallet_weight)
						{
							//$tmp_pl_weight = ceil($ship_vol_qty / $pallet_weight);
							//$tmp_fob_fcl_h_inr = ($transport4 + ($cost_pallet * $tmp_pl_weight) + $forwarding4 + ($indhaz4 * $inr_usd)) / $ship_vol_qty;
							$tmp_pl_weight = ceil($ttlshipvol / $pallet_weight);
							$tmp_fob_fcl_h_inr = ($transport4 + ($cost_pallet * $tmp_pl_weight) + $forwarding4 + ($indhaz4 * $inr_usd)) / $ttlshipvol;
						}
						
					}
					
					$tmp_fob_fcl_h_usd = 0;
					if($inr_usd > 0)
					{
						$tmp_fob_fcl_h_usd = $tmp_fob_fcl_h_inr / $inr_usd;
					}
					$fob_fcl_h_pl = $unp_exwork_usd + $tmp_fob_fcl_h_usd;
					$fob_fcl_h_pl = round($fob_fcl_h_pl,2);
				}
				
				$resp_arr['fob_fcl_h_pl'] = $fob_fcl_h_pl;
				if($currency_type == 'AED')
				{
					$fob_fcl_h_pl_aed = $fob_fcl_h_pl * $aed_usd;
					$resp_arr['fob_fcl_h_pl'] = round($fob_fcl_h_pl_aed,2);
				}
				
			}
			
			//FOB FCL H PALLETIZED
			
			//FOB LCL H PALLETIZED
			if($ship_vol_qty > 0)
			{
				//pack_fcl_nh
				$PriceWt = 0;
				if($ttlshipvol < 10000)
				{
					$tmp_shipvol = $this->getRates("Haz","LCL", 1,$ttlshipvol,"FOB"); 
					//CargoType,ShipmentType,isPalletized,Weight,Sheetname
					
					if($tmp_shipvol > 0)
					{
						//get Lashing
						//Pallet Cost * $ship_vol_qty
						
						$lashing_weight5 = $lashing5 = $transport5 = $forwarding5 = $indhaz5 = 0;
						/*$shipcap_res3 = DB::table('config_fclnh_shipvolcap')
										->whereRaw("upto_weight >= $tmp_shipvol")
										->orderby("upto_weight","asc")
										->first();
						if($shipcap_res3)
						{
							$lashing_weight3 = $shipcap_res3->set_weight_cap;
						}*/
						
						//$lashing_weight5 = $ship_vol_qty;
						$lashing_weight5 = $ttlshipvol;
						
						$lash_res5 = DB::table('config_working')
								->where('CargoType','Haz')
								->where('ShipmentType','LCL')
								->where('Palletized',1)
								->whereRaw('Weight >= '.$lashing_weight5)
								->where('is_delete',0)
								->orderby('Weight','asc')
								->first();
								
						if($lash_res5)
						{
							
							$transport5 = $lash_res5->Transport;
							$forwarding5 = $lash_res5->Forwarding;
							$indhaz5 = $lash_res5->IndHAZ;
							
							
							if($transport5=='' || $transport5=='null') {$transport5 = 0;}
							if($forwarding5=='' || $forwarding5=='null') {$forwarding5 = 0;}
							if($indhaz5=='' || $indhaz5=='null') {$indhaz5 = 0;}
						}
						
						if($cost_pallet)
						{
							if($pallet_weight)
							{
								//$tmp_pl_weight = ceil($ship_vol_qty / $pallet_weight);
								//$tmp_fob_lcl_h_inr = ($transport5 + ($cost_pallet * $tmp_pl_weight) + $forwarding5 + ($tmp_pl_weight * $india_thc_cbm) + ($indhaz5 * $inr_usd)) / $ship_vol_qty;
								
								$tmp_pl_weight = ceil($ttlshipvol / $pallet_weight);
								$tmp_fob_lcl_h_inr = ($transport5 + ($cost_pallet * $tmp_pl_weight) + $forwarding5 + ($tmp_pl_weight * $india_thc_cbm) + ($indhaz5 * $inr_usd)) / $ttlshipvol;
							}
							
						}
						
						$tmp_fob_lcl_h_usd = 0;
						if($inr_usd > 0)
						{
							$tmp_fob_lcl_h_usd = $tmp_fob_lcl_h_inr / $inr_usd;
						}
						$fob_lcl_h_pl = $unp_exwork_usd + $tmp_fob_lcl_h_usd;
						$fob_lcl_h_pl = round($fob_lcl_h_pl,2);
					}
					
					$resp_arr['fob_lcl_h_pl'] = $fob_lcl_h_pl;
					if($currency_type == 'AED')
					{
						$fob_lcl_h_pl_aed = $fob_lcl_h_pl * $aed_usd;
						$resp_arr['fob_lcl_h_pl'] = round($fob_lcl_h_pl_aed,2);
					}
				}
				else
				{
					$resp_arr['fob_lcl_h_pl'] = "Too Large";
				}
			}
			
			//FOB LCL H PALLETIZED
			
			//CIR/CFR FCL NH UNPALLETIZED
			if($ship_vol_qty > 0)
			{
				//pack_fcl_nh
				
				$tmp_shipvol = $this->getRates("Non-Haz","FCL", 0,$ttlshipvol,"CFR"); 
				//CargoType,ShipmentType,isPalletized,Weight,Sheetname
				
				if($tmp_shipvol > 0)
				{
					//get Lashing
					//Pallet Cost * $ship_vol_qty
					$lashing_weight6 = $lashing6 = $transport6 = $forwarding6 = $indhaz6 = $freight6 = 0;
					//FCL NH UNPALLETIZED
					$shipcap_res6 = DB::table('config_fclnh_shipvolcap')
									->whereRaw("upto_weight >= $tmp_shipvol")
									->orderby("upto_weight","asc")
									->first();
					if($shipcap_res6)
					{
						$lashing_weight6 = $shipcap_res6->set_weight_cap;
					}			
					//FCL NH UNPALLETIZED
					//$lashing_weight4 = $ship_vol_qty;
					
					$lash_res6 = DB::table('config_working')
							->where('CargoType','Non-Haz')
							->where('ShipmentType','FCL')
							->where('Palletized',0)
							->whereRaw('Weight >= '.$lashing_weight6)
							->where('is_delete',0)
							->orderby('Weight','asc')
							->first();
							
					if($lash_res6)
					{
						$lashing6 = $lash_res6->Lashing;
						$transport6 = $lash_res6->Transport;
						$forwarding6 = $lash_res6->Forwarding;
						$indhaz6 = $lash_res6->IndHAZ;
						$freight6 = $lash_res6->Freight;
						
						if($lashing6=='' || $lashing6=='null') {$lashing6 = 0;}
						if($transport6=='' || $transport6=='null') {$transport6 = 0;}
						if($forwarding6=='' || $forwarding6=='null') {$forwarding6 = 0;}
						if($indhaz6=='' || $indhaz6=='null') {$indhaz6 = 0;}
						if($freight6=='' || $freight6=='null') {$freight6 = 0;}
					}
					
					//$tmp_cfr_fcl_nh_inr = ($transport6 + $lashing6 + $forwarding6 + (($freight6 + $indhaz6) * $inr_usd)) / $ship_vol_qty;
					
					//$tmp_cfr_fcl_nh_inr = ($transport6 + $lashing6 + $forwarding6 + (($freight6 + $indhaz6) * $inr_usd)) / $ttlshipvol;
					
					$tmp_cfr_fcl_nh_inr = ($transport6 + $lashing6 + $forwarding6 + (($freight6 + $indhaz6) * $inr_usd)) / $tmp_shipvol;
					
					if($inr_usd > 0)
					{
						$tmp_cfr_fcl_nh_usd = $tmp_cfr_fcl_nh_inr / $inr_usd;
					}
					$cif_fcl_nh = $unp_exwork_usd + $tmp_cfr_fcl_nh_usd;
					$cif_fcl_nh *= (1 + 0.03/100);
					$cif_fcl_nh_tmp = $cif_fcl_nh;
					$cif_fcl_nh = round($cif_fcl_nh,2);
				}
				
				$resp_arr['cif_fcl_nh'] = $cif_fcl_nh;
				if($currency_type == 'AED')
				{
					$cif_fcl_nh_aed = $cif_fcl_nh * $aed_usd;
					$resp_arr['cif_fcl_nh'] = round($cif_fcl_nh_aed,2);
				}
				
			}
			
			//CIR/CFR FCL NH UNPALLETIZED
			
			//CIR/CFR LCL NH PALLETIZED
			if($ship_vol_qty > 0)
			{
				//pack_fcl_nh
				$PriceWt = 0;
				if($ttlshipvol < 10000)
				{
					$tmp_shipvol = $this->getRates("Non-Haz","LCL", 1,$ttlshipvol,"CFR"); 
					//CargoType,ShipmentType,isPalletized,Weight,Sheetname
					
					if($tmp_shipvol > 0)
					{
						//get Lashing
						//Pallet Cost * $ship_vol_qty
						
						$lashing_weight7 = $lashing7 = $transport7 = $forwarding7 = $indhaz7 = $freight7 = 0;
												
						//$lashing_weight7 = $ship_vol_qty;
						$lashing_weight7 = $ttlshipvol;
						
						$lash_res7 = DB::table('config_working')
								->where('CargoType','Non-Haz')
								->where('ShipmentType','LCL')
								->where('Palletized',1)
								->whereRaw('Weight >= '.$lashing_weight7)
								->where('is_delete',0)
								->orderby('Weight','asc')
								->first();
								
						if($lash_res7)
						{
							
							$transport7 = $lash_res7->Transport;
							$forwarding7 = $lash_res7->Forwarding;
							$indhaz7 = $lash_res7->IndHAZ;
							$freight7 = $lash_res7->Freight;
							
							
							if($transport7=='' || $transport7=='null') {$transport7 = 0;}
							if($forwarding7=='' || $forwarding7=='null') {$forwarding7 = 0;}
							if($indhaz7=='' || $indhaz7=='null') {$indhaz7 = 0;}
							if($freight7=='' || $freight7=='null') {$freight7 = 0;}
						}
						
						if($cost_pallet)
						{
							if($pallet_weight)
							{
								//$tmp_pl_weight = ceil($ship_vol_qty / $pallet_weight);
								//$tmp_cfr_lcl_nh_inr = ($transport7 + ($cost_pallet * $tmp_pl_weight) + $forwarding7 + ($tmp_pl_weight * $india_thc_cbm) + (($freight7 + $indhaz7) * $inr_usd)) / $ship_vol_qty;
								
								$tmp_pl_weight = ceil($ttlshipvol / $pallet_weight);
								$tmp_cfr_lcl_nh_inr = ($transport7 + ($cost_pallet * $tmp_pl_weight) + $forwarding7 + ($tmp_pl_weight * $india_thc_cbm) + (($freight7 + $indhaz7) * $inr_usd)) / $ttlshipvol;
								
							}
							
						}
						
						$tmp_cfr_lcl_nh_usd = 0;
						if($inr_usd > 0)
						{
							$tmp_cfr_lcl_nh_usd = $tmp_cfr_lcl_nh_inr / $inr_usd;
						}
						$cif_lcl_nh_pl = $unp_exwork_usd + $tmp_cfr_lcl_nh_usd;
						$cif_lcl_nh_pl *= (1 + 0.03/100);
						$cif_lcl_nh_pl_tmp = $cif_lcl_nh_pl;
						
						$cif_lcl_nh_pl = round($cif_lcl_nh_pl,2);
					}
					
					$resp_arr['cif_lcl_nh_pl'] = $cif_lcl_nh_pl;
					
					if($currency_type == 'AED')
					{
						$cif_lcl_nh_pl_aed = $cif_lcl_nh_pl * $aed_usd;
						$resp_arr['cif_lcl_nh_pl'] = round($cif_lcl_nh_pl_aed,2);
					}
				}
				else
				{
					$resp_arr['cif_lcl_nh_pl'] = "Too Large";
				}
			}
			
			//CIR/CFR LCL NH PALLETIZED
			
			//CIR/CFR FCL H PALLETIZED
			if($ship_vol_qty > 0)
			{
				//pack_fcl_nh
				
				$tmp_shipvol = $this->getRates("Haz","FCL", 1,$ttlshipvol,"CFR"); 
				//CargoType,ShipmentType,isPalletized,Weight,Sheetname
				
				if($tmp_shipvol > 0)
				{
					//get Lashing
					//Pallet Cost * $ship_vol_qty
					$lashing_weight8 = $lashing8 = $transport8 = $forwarding8 = $indhaz8 = $freight8 = 0;
										
					//$lashing_weight4 = $ship_vol_qty;
					
					$lash_res8 = DB::table('config_working')
							->where('CargoType','Haz')
							->where('ShipmentType','FCL')
							->where('Palletized',1)
							->whereRaw('Weight >= '.$ttlshipvol)
							->where('is_delete',0)
							->orderby('Weight','asc')
							->first();
							
					if($lash_res8)
					{
						
						$transport8 = $lash_res8->Transport;
						$forwarding8 = $lash_res8->Forwarding;
						$indhaz8 = $lash_res8->IndHAZ;
						$freight8 = $lash_res8->Freight;
						
						if($transport8=='' || $transport8=='null') {$transport8 = 0;}
						if($forwarding8=='' || $forwarding8=='null') {$forwarding8 = 0;}
						if($indhaz8=='' || $indhaz8=='null') {$indhaz8 = 0;}
						if($freight8=='' || $freight8=='null') {$freight8 = 0;}
					}
					
					if($cost_pallet)
					{
						if($pallet_weight)
						{
							//$tmp_pl_weight = ceil($ship_vol_qty / $pallet_weight);
							//$tmp_cfr_fcl_h_inr = ($transport8 + ($cost_pallet * $tmp_pl_weight) + $forwarding8 + (($freight8 + $indhaz8) * $inr_usd)) / $ship_vol_qty;
							
							$tmp_pl_weight = ceil($ttlshipvol / $pallet_weight);
							$tmp_cfr_fcl_h_inr = ($transport8 + ($cost_pallet * $tmp_pl_weight) + $forwarding8 + (($freight8 + $indhaz8) * $inr_usd)) / $ttlshipvol;
						}
						
					}
					
					
					if($inr_usd > 0)
					{
						$tmp_cfr_fcl_h_usd = $tmp_cfr_fcl_h_inr / $inr_usd;
					}
					$cif_fcl_h_pl = $unp_exwork_usd + $tmp_cfr_fcl_h_usd;
					$cif_fcl_h_pl *= (1 + 0.03/100);
					$cif_fcl_h_pl_tmp = $cif_fcl_h_pl;
					
					$cif_fcl_h_pl = round($cif_fcl_h_pl,2);
				}
				
				$resp_arr['cif_fcl_h_pl'] = $cif_fcl_h_pl;
				
				if($currency_type == 'AED')
				{
					$cif_fcl_h_pl_aed = $cif_fcl_h_pl * $aed_usd;
					$resp_arr['cif_fcl_h_pl'] = round($cif_fcl_h_pl_aed,2);
				}
				
			}
			
			//CIR/CFR FCL H PALLETIZED
			
			//CIR/CFR LCL H PALLETIZED
			if($ship_vol_qty > 0)
			{
				//pack_fcl_nh
				$PriceWt = 0;
				if($ttlshipvol < 10000)
				{
					$tmp_shipvol = $this->getRates("Haz","LCL", 1,$ttlshipvol,"CFR"); 
					//CargoType,ShipmentType,isPalletized,Weight,Sheetname
					
					if($tmp_shipvol > 0)
					{
						//get Lashing
						//Pallet Cost * $ship_vol_qty
						
						$lashing_weight9 = $lashing9 = $transport9 = $forwarding9 = $indhaz9 = $freight9 = 0;
						/*$shipcap_res3 = DB::table('config_fclnh_shipvolcap')
										->whereRaw("upto_weight >= $tmp_shipvol")
										->orderby("upto_weight","asc")
										->first();
						if($shipcap_res3)
						{
							$lashing_weight3 = $shipcap_res3->set_weight_cap;
						}*/
						
						//$lashing_weight9 = $ship_vol_qty;
						$lashing_weight9 = $ttlshipvol;
						
						$lash_res9 = DB::table('config_working')
								->where('CargoType','Haz')
								->where('ShipmentType','LCL')
								->where('Palletized',1)
								->whereRaw('Weight >= '.$lashing_weight9)
								->where('is_delete',0)
								->orderby('Weight','asc')
								->first();
								
						if($lash_res9)
						{
							
							$transport9 = $lash_res9->Transport;
							$forwarding9 = $lash_res9->Forwarding;
							$indhaz9 = $lash_res9->IndHAZ;
							$freight9 = $lash_res9->Freight;
							
							if($transport9=='' || $transport9=='null') {$transport9 = 0;}
							if($forwarding9=='' || $forwarding9=='null') {$forwarding9 = 0;}
							if($indhaz9=='' || $indhaz9=='null') {$indhaz9 = 0;}
							if($freight9=='' || $freight9=='null') {$freight9 = 0;}
						}
						
						if($cost_pallet)
						{
							if($pallet_weight)
							{
								//$tmp_pl_weight = ceil($ship_vol_qty / $pallet_weight);
								//$tmp_cfr_lcl_h_inr = ($transport9 + ($cost_pallet * $tmp_pl_weight) + $forwarding9 + ($tmp_pl_weight * $india_thc_cbm) + (($freight9 + $indhaz9) * $inr_usd)) / $ship_vol_qty;
								
								$tmp_pl_weight = ceil($ttlshipvol / $pallet_weight);
								$tmp_cfr_lcl_h_inr = ($transport9 + ($cost_pallet * $tmp_pl_weight) + $forwarding9 + ($tmp_pl_weight * $india_thc_cbm) + (($freight9 + $indhaz9) * $inr_usd)) / $ttlshipvol;
							}
							
						}
						
						$tmp_cfr_lcl_h_usd = 0;
						if($inr_usd > 0)
						{
							$tmp_cfr_lcl_h_usd = $tmp_cfr_lcl_h_inr / $inr_usd;
						}
						$cif_lcl_h_pl = $unp_exwork_usd + $tmp_cfr_lcl_h_usd;
						$cif_lcl_h_pl *= (1 + 0.03/100);
						$cif_lcl_h_pl_tmp = $cif_lcl_h_pl;
						
						$cif_lcl_h_pl = round($cif_lcl_h_pl,2);
					}
					
					$resp_arr['cif_lcl_h_pl'] = $cif_lcl_h_pl;
					if($currency_type == 'AED')
					{
						$cif_lcl_h_pl_aed = $cif_lcl_h_pl * $aed_usd;
						$resp_arr['cif_lcl_h_pl'] = round($cif_lcl_h_pl_aed,2);
					}
				}
				else
				{
					$resp_arr['cif_lcl_h_pl'] = "Too Large";
				}
			}
			
			//CIR/CFR LCL H PALLETIZED
			
			//LandedCost/UAESide FCL NH UNPALLETIZED
			if($ship_vol_qty > 0)
			{
				//pack_fcl_nh
				
				$tmp_shipvol = $this->getRates("Non-Haz","FCL", 0,$ttlshipvol,"UAESide"); 
				//CargoType,ShipmentType,isPalletized,Weight,Sheetname
				
				if($tmp_shipvol > 0)
				{
					//get Lashing
					//Pallet Cost * $ship_vol_qty
					$lashing_weight10 = $DelO10 = $Customs10 = $THC10 = $GatePass10 = $Insp10 = $UAEHaz10 = $Transport210 = 0;
					//FCL NH UNPALLETIZED
					$shipcap_res10 = DB::table('config_fclnh_shipvolcap')
									->whereRaw("upto_weight >= $tmp_shipvol")
									->orderby("upto_weight","asc")
									->first();
					if($shipcap_res10)
					{
						$lashing_weight10 = $shipcap_res10->set_weight_cap;
					}			
					//FCL NH UNPALLETIZED
					//$lashing_weight4 = $ship_vol_qty;
					
					$lash_res10 = DB::table('config_working')
							->where('CargoType','Non-Haz')
							->where('ShipmentType','FCL')
							->where('Palletized',0)
							->whereRaw('Weight >= '.$lashing_weight10)
							->where('is_delete',0)
							->orderby('Weight','asc')
							->first();
							
					if($lash_res10)
					{
						$DelO10 = $lash_res10->DelO;
						$Customs10 = $lash_res10->Customs;
						$THC10 = $lash_res10->THC;
						$GatePass10 = $lash_res10->GatePass;
						$Insp10 = $lash_res10->Insp;
						$UAEHaz10 = $lash_res10->UAEHaz;
						$Transport210 = $lash_res10->Transport2;
						
						if($DelO10=='' || $DelO10=='null') {$DelO10 = 0;}
						if($Customs10=='' || $Customs10=='null') {$Customs10 = 0;}
						if($THC10=='' || $THC10=='null') {$THC10 = 0;}
						if($GatePass10=='' || $GatePass10=='null') {$GatePass10 = 0;}
						if($Insp10=='' || $Insp10=='null') {$Insp10 = 0;}
						if($UAEHaz10=='' || $UAEHaz10=='null') {$UAEHaz10 = 0;}
						if($Transport210=='' || $Transport210=='null') {$Transport210 = 0;}
					}
					
					//$tmp_landed_fcl_nh_inr = (($DelO10 + $Customs10 + $THC10 + $GatePass10 + $Insp10 + $UAEHaz10 + $Transport210) * $inr_usd) / $ship_vol_qty;
					
					//$tmp_landed_fcl_nh_inr = (($DelO10 + $Customs10 + $THC10 + $GatePass10 + $Insp10 + $UAEHaz10 + $Transport210) * $inr_usd) / $ttlshipvol;
					
					$tmp_landed_fcl_nh_inr = (($DelO10 + $Customs10 + $THC10 + $GatePass10 + $Insp10 + $UAEHaz10 + $Transport210) * $inr_usd) / $tmp_shipvol;
					
					
					if($inr_usd > 0)
					{
						$tmp_landed_fcl_nh_usd = $tmp_landed_fcl_nh_inr / $inr_usd;
					}
					$landed_fcl_nh = $cif_fcl_nh_tmp + $tmp_landed_fcl_nh_usd;
					$landed_fcl_nh = round($landed_fcl_nh,2);
					
				}
				
				$resp_arr['landed_fcl_nh'] = $landed_fcl_nh;
				if($currency_type == 'AED')
				{
					$landed_fcl_nh_aed = $landed_fcl_nh * $aed_usd;
					$resp_arr['landed_fcl_nh'] = round($landed_fcl_nh_aed,2);
				}
				
			}
			
			//LandedCost/UAESide FCL NH UNPALLETIZED
			
			//LandedCost/UAESide LCL NH PALLETIZED
			if($ship_vol_qty > 0)
			{
				//pack_fcl_nh
				$PriceWt = 0;
				if($ttlshipvol < 10000)
				{
					$tmp_shipvol = $this->getRates("Non-Haz","LCL", 1,$ttlshipvol,"UAESide"); 
					//CargoType,ShipmentType,isPalletized,Weight,Sheetname
					
					if($tmp_shipvol > 0)
					{
						//get Lashing
						//Pallet Cost * $ship_vol_qty
						
						$lashing_weight11 = $DelO11 = $Customs11 = $THC11 = $GatePass11 = $Insp11 = $UAEHaz11 = $Transport211 = 0;
												
						//$lashing_weight11 = $ship_vol_qty;
						$lashing_weight11 = $ttlshipvol;
						
						
						$lash_res11 = DB::table('config_working')
								->where('CargoType','Non-Haz')
								->where('ShipmentType','LCL')
								->where('Palletized',1)
								->whereRaw('Weight >= '.$lashing_weight11)
								->where('is_delete',0)
								->orderby('Weight','asc')
								->first();
								
						if($lash_res11)
						{
							
							$DelO11 = $lash_res11->DelO;
							$Customs11 = $lash_res11->Customs;
							$THC11 = $lash_res11->THC;
							$GatePass11 = $lash_res11->GatePass;
							$Insp11 = $lash_res11->Insp;
							$UAEHaz11 = $lash_res11->UAEHaz;
							$Transport211 = $lash_res11->Transport2;
							
							if($DelO11=='' || $DelO11=='null') {$DelO11 = 0;}
							if($Customs11=='' || $Customs11=='null') {$Customs11 = 0;}
							if($THC11=='' || $THC11=='null') {$THC11 = 0;}
							if($GatePass11=='' || $GatePass11=='null') {$GatePass11 = 0;}
							if($Insp11=='' || $Insp11=='null') {$Insp11 = 0;}
							if($UAEHaz11=='' || $UAEHaz11=='null') {$UAEHaz11 = 0;}
							if($Transport211=='' || $Transport211=='null') {$Transport211 = 0;}
						}
						
						$tmp_pl_weight = 0;
						
						if($pallet_weight)
						{
							//$tmp_pl_weight = ceil($ship_vol_qty / $pallet_weight);
							$tmp_pl_weight = ceil($ttlshipvol / $pallet_weight);
						}
						
						
						//$tmp_landed_lcl_nh_inr = (($DelO11 + $Customs11 + (($tmp_pl_weight > $included_pallets) ? $uae_thc_pallet * $tmp_pl_weight : 0) + $THC11 + $GatePass11 + $Insp11 + $UAEHaz11 + $Transport211) * $inr_usd) / $ship_vol_qty;
						
						$tmp_landed_lcl_nh_inr = (($DelO11 + $Customs11 + (($tmp_pl_weight > $included_pallets) ? $uae_thc_pallet * $tmp_pl_weight : 0) + $THC11 + $GatePass11 + $Insp11 + $UAEHaz11 + $Transport211) * $inr_usd) / $ttlshipvol;
						
						$tmp_landed_lcl_nh_usd = 0;
						if($inr_usd > 0)
						{
							$tmp_landed_lcl_nh_usd = $tmp_landed_lcl_nh_inr / $inr_usd;
						}
						//echo $tmp_landed_lcl_nh_usd;die;
						$landed_lcl_nh_pl = $cif_lcl_nh_pl_tmp + $tmp_landed_lcl_nh_usd;
						
						$landed_lcl_nh_pl = round($landed_lcl_nh_pl,2);
						
					}
					
					$resp_arr['landed_lcl_nh_pl'] = $landed_lcl_nh_pl;
					if($currency_type == 'AED')
					{
						$landed_lcl_nh_pl_aed = $landed_lcl_nh_pl * $aed_usd;
						$resp_arr['landed_lcl_nh_pl'] = round($landed_lcl_nh_pl_aed,2);
					}
					
				}
				else
				{
					$resp_arr['landed_lcl_nh_pl'] = "Too Large";
				}
			}
			
			//LandedCost/UAESide LCL NH PALLETIZED
			
			//LandedCost/UAESide FCL H PALLETIZED
			if($ship_vol_qty > 0)
			{
				//pack_fcl_nh
				
				$tmp_shipvol = $this->getRates("Haz","FCL", 1,$ttlshipvol,"UAESide"); 
				//CargoType,ShipmentType,isPalletized,Weight,Sheetname
				
				if($tmp_shipvol > 0)
				{
					//get Lashing
					//Pallet Cost * $ship_vol_qty
					$lashing_weight12 = $DelO12 = $Customs12 = $THC12 = $GatePass12 = $Insp12 = $UAEHaz12 = $Transport212 = 0;
										
					//$lashing_weight4 = $ship_vol_qty;
					
					$lash_res12 = DB::table('config_working')
							->where('CargoType','Haz')
							->where('ShipmentType','FCL')
							->where('Palletized',1)
							->whereRaw('Weight >= '.$ttlshipvol)
							->where('is_delete',0)
							->orderby('Weight','asc')
							->first();
							
					if($lash_res12)
					{
						
						$DelO12 = $lash_res12->DelO;
						$Customs12 = $lash_res12->Customs;
						$THC12 = $lash_res12->THC;
						$GatePass12 = $lash_res12->GatePass;
						$Insp12 = $lash_res12->Insp;
						$UAEHaz12 = $lash_res12->UAEHaz;
						$Transport212 = $lash_res12->Transport2;
						
						if($DelO12=='' || $DelO12=='null') {$DelO12 = 0;}
						if($Customs12=='' || $Customs12=='null') {$Customs12 = 0;}
						if($THC12=='' || $THC12=='null') {$THC12 = 0;}
						if($GatePass12=='' || $GatePass12=='null') {$GatePass12 = 0;}
						if($Insp12=='' || $Insp12=='null') {$Insp12 = 0;}
						if($UAEHaz12=='' || $UAEHaz12=='null') {$UAEHaz12 = 0;}
						if($Transport212=='' || $Transport212=='null') {$Transport212 = 0;}
					}
					
					//$tmp_landed_fcl_h_inr = (($DelO12 + $Customs12 + $THC12 + $GatePass12 + $Insp12 + $UAEHaz12 + $Transport212) * $inr_usd) / $ship_vol_qty;
					
					$tmp_landed_fcl_h_inr = (($DelO12 + $Customs12 + $THC12 + $GatePass12 + $Insp12 + $UAEHaz12 + $Transport212) * $inr_usd) / $ttlshipvol;
					
					
					if($inr_usd > 0)
					{
						$tmp_landed_fcl_h_usd = $tmp_landed_fcl_h_inr / $inr_usd;
					}
					$landed_fcl_h_pl = $cif_fcl_h_pl_tmp + $tmp_landed_fcl_h_usd;
					$landed_fcl_h_pl = round($landed_fcl_h_pl, 2);
				}
				
				$resp_arr['landed_fcl_h_pl'] = $landed_fcl_h_pl;
				if($currency_type == 'AED')
				{
					$landed_fcl_h_pl_aed = $landed_fcl_h_pl * $aed_usd;
					$resp_arr['landed_fcl_h_pl'] = round($landed_fcl_h_pl_aed,2);
				}
				
			}
			//punechem@growel.com	11850
			//LandedCost/UAESide FCL H PALLETIZED
			
			//LandedCost/UAESide LCL H PALLETIZED
			if($ship_vol_qty > 0)
			{
				//pack_fcl_nh
				$PriceWt = 0;
				if($ttlshipvol < 10000)
				{
					$tmp_shipvol = $this->getRates("Haz","LCL", 1,$ttlshipvol,"UAESide"); 
					//CargoType,ShipmentType,isPalletized,Weight,Sheetname
					
					if($tmp_shipvol > 0)
					{
						//get Lashing
						//Pallet Cost * $ship_vol_qty
						
						$lashing_weight13 = $DelO13 = $Customs13 = $THC13 = $GatePass13 = $Insp13 = $UAEHaz13 = $Transport213 = 0;
						
						//$lashing_weight13 = $ship_vol_qty;
						$lashing_weight13 = $ttlshipvol;
						
						$lash_res13 = DB::table('config_working')
								->where('CargoType','Haz')
								->where('ShipmentType','LCL')
								->where('Palletized',1)
								->whereRaw('Weight >= '.$lashing_weight13)
								->where('is_delete',0)
								->orderby('Weight','asc')
								->first();
								
						if($lash_res13)
						{
							
							$DelO13 = $lash_res13->DelO;
							$Customs13 = $lash_res13->Customs;
							$THC13 = $lash_res13->THC;
							$GatePass13 = $lash_res13->GatePass;
							$Insp13 = $lash_res13->Insp;
							$UAEHaz13 = $lash_res13->UAEHaz;
							$Transport213 = $lash_res13->Transport2;
							
							if($DelO13=='' || $DelO13=='null') {$DelO13 = 0;}
							if($Customs13=='' || $Customs13=='null') {$Customs13 = 0;}
							if($THC13=='' || $THC13=='null') {$THC13 = 0;}
							if($GatePass13=='' || $GatePass13=='null') {$GatePass13 = 0;}
							if($Insp13=='' || $Insp13=='null') {$Insp13 = 0;}
							if($UAEHaz13=='' || $UAEHaz13=='null') {$UAEHaz13 = 0;}
							if($Transport213=='' || $Transport213=='null') {$Transport213 = 0;}
						}
						
						$tmp_pl_weight = 0;
						
						if($pallet_weight)
						{
							$tmp_pl_weight = ceil($ttlshipvol / $pallet_weight);
							//$tmp_pl_weight = ceil($ship_vol_qty / $pallet_weight);
						}
						
						
						//$tmp_landed_lcl_h_inr = (($DelO13 + $Customs13 + (($tmp_pl_weight > $included_pallets) ? $uae_thc_pallet * $tmp_pl_weight : 0) + $THC13 + $GatePass13 + $Insp13 + $UAEHaz13 + $Transport213) * $inr_usd) / $ship_vol_qty;
						
						$tmp_landed_lcl_h_inr = (($DelO13 + $Customs13 + (($tmp_pl_weight > $included_pallets) ? $uae_thc_pallet * $tmp_pl_weight : 0) + $THC13 + $GatePass13 + $Insp13 + $UAEHaz13 + $Transport213) * $inr_usd) / $ttlshipvol;
						
						$tmp_landed_lcl_h_usd = 0;
						if($inr_usd > 0)
						{
							$tmp_landed_lcl_h_usd = $tmp_landed_lcl_h_inr / $inr_usd;
						}
						$landed_lcl_h_pl = $cif_lcl_h_pl_tmp + $tmp_landed_lcl_h_usd;
						$landed_lcl_h_pl = round($landed_lcl_h_pl, 2);
						
					}
					
					$resp_arr['landed_lcl_h_pl'] = $landed_lcl_h_pl;
					if($currency_type == 'AED')
					{
						$landed_lcl_h_pl_aed = $landed_lcl_h_pl * $aed_usd;
						$resp_arr['landed_lcl_h_pl'] = round($landed_lcl_h_pl_aed,2);
					}
				}
				else
				{
					$resp_arr['landed_lcl_h_pl'] = "Too Large";
				}
			}
			
			//LandedCost/UAESide LCL H PALLETIZED
			
			//Last Column 
			$importduty_cent = $import_duty / 100; //5%
			
			//Q3 =IF(D3>0,GetRates("Non-Haz",$O$2,PriceWt,"Packed"),0)
			//Q3 ==IF(D3>0,GetRates("Haz",$O$2,PriceWt,"Packed"),0)
			//W3=M3+Q3+T3 = unp_exwork_usd+tmp_pk_fcl_nh_usd+(tmp_pk_fcl_nh_usd-tmp_pk_fcl_nh_usd)
			$m3 = $unp_exwork_usd;
			
			if($cargo_type == 'NH')
			{
				$q3 = $tmp_pk_fcl_nh_usd; //For Non-Haz FCL UNPALLETIZED
				$t3 = ($tmp_pk_fcl_nh_usd - $tmp_pk_fcl_nh_usd);
			}
			else
			{
				//PACKED FCL H PALLETIZED
				if($ship_vol_qty > 0)
				{
					//pack_fcl_nh
					
					$tmp_shipvol = $this->getRates("Haz","FCL", 1,$ttlshipvol,"Packed"); 
					//CargoType,ShipmentType,isPalletized,Weight,Sheetname
					
					if($tmp_shipvol > 0)
					{
						//get Lashing
						//Pallet Cost * $ship_vol_qty
											
						//$lashing_weight4 = $ship_vol_qty;
						$lashing_weight15 = $lashing15 = $transport15 = $forwarding15 = $indhaz15 = 0;
						
						$lash_res15 = DB::table('config_working')
								->where('CargoType','Haz')
								->where('ShipmentType','FCL')
								->where('Palletized',1)
								->whereRaw('Weight >= '.$tmp_shipvol)
								->where('is_delete',0)
								->orderby('Weight','asc')
								->first();
							
						if($lash_res15)
						{
							
							$transport15 = $lash_res15->Transport;
							$forwarding15 = $lash_res15->Forwarding;
							$indhaz15 = $lash_res15->IndHAZ;
							
							
							if($transport15=='' || $transport15=='null') {$transport15 = 0;}
							if($forwarding15=='' || $forwarding15=='null') {$forwarding15 = 0;}
							if($indhaz15=='' || $indhaz15=='null') {$indhaz15 = 0;}
						}
						
						if($cost_pallet)
						{
							if($pallet_weight)
							{
								//=(IF(OR(Z$3,AA$3,AB$3),Pallet_Cost*AB203,0)+IF(NOT(AB$3),INDEX(Inputs[Lashing],AA203),0))/$A203
								//120000
								//$tmp_pl_weight = ceil($ship_vol_qty / $pallet_weight);
								//$tmp_fob_fcl_h_inr = ($transport4 + ($cost_pallet * $tmp_pl_weight) + $forwarding4 + ($indhaz4 * $inr_usd)) / $ship_vol_qty;
								$tmp_pl_weight = ceil($tmp_shipvol / $pallet_weight);
								$tmp_pk_fcl_h_inr = ($cost_pallet * $tmp_pl_weight) / $tmp_shipvol;
							}
							
						}
						
						
						if($inr_usd > 0)
						{
							$tmp_pk_fcl_h_usd = $tmp_pk_fcl_h_inr / $inr_usd;
						}
						//$pk_fcl_h_pl = $unp_exwork_usd + $tmp_pk_fcl_h_usd;
						//$pk_fcl_h_pl = round($pk_fcl_h_pl,2);
					}
					
					//$resp_arr['fob_fcl_h_pl'] = number_format($fob_fcl_h_pl, 2);
					
				}
				
				//PACKED FCL H PALLETIZED
				$q3 = $tmp_pk_fcl_h_usd; //For Haz FCL PALLETIZED
				$t3 = ($tmp_pk_fcl_h_usd - $tmp_pk_fcl_h_usd);
			}
			
			//$w3 = $m3 + $q3 + $t3;
			$w3 = $m3 + $q3;
			
			//Y3 =IF(D3>0,GetRates("Non-Haz",$O$2,PriceWt,"CFR")-T3-Q3,0)	O2 is FCL UNPALLETIZED
			//Y3 =IF(D3>0,GetRates("Haz",$O$2,PriceWt,"CFR")-T3-Q3,0)		O2 is FCL PALLETIZED
			if($cargo_type == 'NH')
			{
				$y3 = $tmp_cfr_fcl_nh_usd - $t3 - $q3;
			}
			else
			{
				 $tmp_cfr_fcl_h_inr_last = $tmp_cfr_fcl_h_usd_last = 0;
				//CIR/CFR FCL H PALLETIZED
				if($ship_vol_qty > 0)
				{
					//pack_fcl_nh
					
					$tmp_shipvol = $this->getRates("Haz","FCL", 1,$ttlshipvol,"CFR"); 
					//CargoType,ShipmentType,isPalletized,Weight,Sheetname
					
					if($tmp_shipvol > 0)
					{
						//get Lashing
						//Pallet Cost * $ship_vol_qty
						$lashing_weight8 = $lashing8 = $transport8 = $forwarding8 = $indhaz8 = $freight8 = 0;
											
						//$lashing_weight4 = $ship_vol_qty;
												
						$lash_res8 = DB::table('config_working')
								->where('CargoType','Haz')
								->where('ShipmentType','FCL')
								->where('Palletized',1)
								->whereRaw('Weight >= '.$tmp_shipvol)
								->where('is_delete',0)
								->orderby('Weight','asc')
								->first();
								
						if($lash_res8)
						{
							
							$transport8 = $lash_res8->Transport;
							$forwarding8 = $lash_res8->Forwarding;
							$indhaz8 = $lash_res8->IndHAZ;
							$freight8 = $lash_res8->Freight;
							
							if($transport8=='' || $transport8=='null') {$transport8 = 0;}
							if($forwarding8=='' || $forwarding8=='null') {$forwarding8 = 0;}
							if($indhaz8=='' || $indhaz8=='null') {$indhaz8 = 0;}
							if($freight8=='' || $freight8=='null') {$freight8 = 0;}
						}
						
						if($cost_pallet)
						{
							if($pallet_weight)
							{
								//$tmp_pl_weight = ceil($ship_vol_qty / $pallet_weight);
								//$tmp_cfr_fcl_h_inr = ($transport8 + ($cost_pallet * $tmp_pl_weight) + $forwarding8 + (($freight8 + $indhaz8) * $inr_usd)) / $ship_vol_qty;
								
								$tmp_pl_weight = ceil($tmp_shipvol / $pallet_weight);
								$tmp_cfr_fcl_h_inr_last = ($transport8 + ($cost_pallet * $tmp_pl_weight) + $forwarding8 + (($freight8 + $indhaz8) * $inr_usd)) / $tmp_shipvol;
							}
							
						}
						
						
						if($inr_usd > 0)
						{
							$tmp_cfr_fcl_h_usd_last = $tmp_cfr_fcl_h_inr_last / $inr_usd;
						}
						
					}
					
					
				}
				
				//CIR/CFR FCL H PALLETIZED
				$y3 = $tmp_cfr_fcl_h_usd_last - $t3 - $q3;
			}
			
			$aa3 = ($w3 + $y3) * (1 + 0.03/100);
			//AA3 =(W3+Y3)*(1+0.03%)
			//AD3 =AA3*(1+$AC$2)		AC2 is import duty
			$ad3 = $aa3 * (1 + $importduty_cent);
			
			//AF3 = =IF(D3>0,GetRates("Non-Haz",$O$2,PriceWt,"UAESide"),0)		O2 is FCL UNPALLETIZED
			//AF3 =	=IF(D3>0,GetRates("Haz",$O$2,PriceWt,"UAESide"),0)			O2 is FCL PALLETIZED
			if($cargo_type == 'NH')
			{
				$af3 = $tmp_landed_fcl_nh_usd;
			}
			else
			{
				$tmp_landed_fcl_h_inr_last = $tmp_landed_fcl_h_usd_last = 0;
				//LandedCost/UAESide FCL H PALLETIZED
				if($ship_vol_qty > 0)
				{
					//pack_fcl_nh
					
					$tmp_shipvol = $this->getRates("Haz","FCL", 1,$ttlshipvol,"UAESide"); 
					//CargoType,ShipmentType,isPalletized,Weight,Sheetname
					
					if($tmp_shipvol > 0)
					{
						//get Lashing
						//Pallet Cost * $ship_vol_qty
						$lashing_weight12 = $DelO12 = $Customs12 = $THC12 = $GatePass12 = $Insp12 = $UAEHaz12 = $Transport212 = 0;
											
						//$lashing_weight4 = $ship_vol_qty;
						
						$lash_res12 = DB::table('config_working')
								->where('CargoType','Haz')
								->where('ShipmentType','FCL')
								->where('Palletized',1)
								->whereRaw('Weight >= '.$tmp_shipvol)
								->where('is_delete',0)
								->orderby('Weight','asc')
								->first();
								
						if($lash_res12)
						{
							
							$DelO12 = $lash_res12->DelO;
							$Customs12 = $lash_res12->Customs;
							$THC12 = $lash_res12->THC;
							$GatePass12 = $lash_res12->GatePass;
							$Insp12 = $lash_res12->Insp;
							$UAEHaz12 = $lash_res12->UAEHaz;
							$Transport212 = $lash_res12->Transport2;
							
							if($DelO12=='' || $DelO12=='null') {$DelO12 = 0;}
							if($Customs12=='' || $Customs12=='null') {$Customs12 = 0;}
							if($THC12=='' || $THC12=='null') {$THC12 = 0;}
							if($GatePass12=='' || $GatePass12=='null') {$GatePass12 = 0;}
							if($Insp12=='' || $Insp12=='null') {$Insp12 = 0;}
							if($UAEHaz12=='' || $UAEHaz12=='null') {$UAEHaz12 = 0;}
							if($Transport212=='' || $Transport212=='null') {$Transport212 = 0;}
						}
						
						//$tmp_landed_fcl_h_inr = (($DelO12 + $Customs12 + $THC12 + $GatePass12 + $Insp12 + $UAEHaz12 + $Transport212) * $inr_usd) / $ship_vol_qty;
						
						$tmp_landed_fcl_h_inr_last = (($DelO12 + $Customs12 + $THC12 + $GatePass12 + $Insp12 + $UAEHaz12 + $Transport212) * $inr_usd) / $tmp_shipvol;
						
						
						if($inr_usd > 0)
						{
							$tmp_landed_fcl_h_usd_last = $tmp_landed_fcl_h_inr_last / $inr_usd;
						}
						
					}
					
					
					
				}
				//punechem@growel.com	11850
				//LandedCost/UAESide FCL H PALLETIZED
				$af3 = $tmp_landed_fcl_h_usd_last;
			}
			
			$ah3 = $ad3 + $af3;
			$ak3 = $warehouse_cost_kg_month * $warehouse_month;
			$aj3 = 0;
			$am3 = $cst_fwd_distr_buyer; //Hard coded in excel
			$al3 = 0;
			if($aed_usd > 0)
			{
				$aj3 = $ak3 / $aed_usd;
				$al3 = $am3 / $aed_usd;
			}
			$handling_loss_cent = $handling_loss / 100;
			
			$ao3 = ($ah3 + $aj3 + $al3) * (1 + $handling_loss_cent);
			
			//AQ3 =AO3/(1-(Working!$B$34*Working!$B$35))
			$cost_of_interest_month_cent = $cost_of_interest_month / 100;
			$tmpdiv1 = (1 - ($cost_of_interest_month_cent * $interest_duration));
			$aq3 = 0;
			if($tmpdiv1 > 0)
			{
				$aq3 = $ao3 / $tmpdiv1;
			}
			
			$disc_margin_cent = $disc_margin / 100;
			$au3 = $disc_margin_cent;
			$av3 = $aq3 / (1 - $au3);
			
			//Last Column
			$resp_arr['ttlcost_inc_finance'] = round($aq3,2);
			$resp_arr['kondm'] = $kondm;
			$resp_arr['kondm_val'] = round($kondm_val,2);
			$resp_arr['disc_val'] = round($disc_val,2);
			$resp_arr['disc_type'] = $disc_type;
			$resp_arr['recom_dis_sp_to_buyer'] = round($av3,2);
			if($currency_type == 'AED')
			{
				$av3_aed = $av3 * $aed_usd;
				$resp_arr['recom_dis_sp_to_buyer'] = round($av3_aed,2);
			}
			
			//BB3 =AV3/(1-$AZ$2-$BA$2)
			$exp_cred_cost_thr_months_cent = $exp_cred_cost_thr_months / 100;
			$estd_miniman_cst_cent = $estd_miniman_cst / 100;
			
			$tmpdiv2 = 1 - $exp_cred_cost_thr_months_cent - $estd_miniman_cst_cent;
			$bb3 = 0;
			if($tmpdiv2 > 0)
			{
				$bb3 = $av3 / $tmpdiv2;
			}
			
			$resp_arr['recom_sp_aft_credit_miniman'] = round($bb3,2);
			if($currency_type == 'AED')
			{
				$bb3_aed = $bb3 * $aed_usd;
				$resp_arr['recom_sp_aft_credit_miniman'] = round($bb3_aed,2);
			}
			
		}
		
		return json_encode($resp_arr);
	}
	
	public function getRates($haztype,$shiptype, $ispalletized,$totalshipvol,$sheetname)
	{
		$config_shipvol = 0;
		$othertype = array('Packed'=>'WORK', 'FOB' => 'FOB', 'CFR' => 'CFR', 'UAESide' => 'UAESide');
		$price_res = DB::table('config_prices')
				->where('haztype',$haztype)
				->where('othertype',$othertype[$sheetname])
				->where('is_delete',0)
				->first();
		if($price_res)
		{
			$config_shipvol = $price_res->shipment_vol;
		}
		
		if($totalshipvol == '') { $totalshipvol = 0; }
		
		$actualshipvol = max($totalshipvol, $config_shipvol);
		
		return round($actualshipvol, -3);
		
	}
	
	public function generate_quoterefno($plantcode,$year2)
	{
		$p2 = (!empty($plantcode)) ? substr($plantcode,-2) : 'NA';
		
		$qtrefno_res = DB::table('quote_ref_no')
						->select('refno')
						->where('plant_code',$plantcode)
						->where('quoteyear',$year2)
						->first();
		$ref_no = 0;
		if($qtrefno_res)
		{
			$ref_no = $qtrefno_res->refno;
		}
		$ref_no_6 = str_pad($ref_no,6,'0',STR_PAD_LEFT);
		$quote_ref_no = $p2.$year2.$ref_no_6;
		
		return $quote_ref_no;
		
	}
	
}
