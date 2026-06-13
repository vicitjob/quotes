<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\GateEntryDetailController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use App\Models\GateEntryDetail;
use App\Models\GateEntryItemDetail;
use Carbon\Carbon;
use PDF;
use App\Models\User;
use App\Mail\GateEntryMail;

use Illuminate\Support\Facades\Mail;

class GateEntryDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
		$is_storeuser = false;
		$show_plant_loc = false;
		$show_edit_del = true;
		
        //$gateEntries = GateEntryDetail::with('materialDetails')->paginate(10);
		$loc_code = $plant_code = array();
		//Filter by Plant and location
		if(Auth::User())
		{
			$loc_code = explode(',',Auth::User()->loc_code);
			$plant_code = explode(',',Auth::User()->plant_code);
		}
		//Filter by Plant and location
		
		$sql1 = GateEntryDetail::select('*')
				->selectRaw("(select TOP 1 po_no from gate_entry_item_details where gate_entry_id = gate_entry_details.id and is_delete = 0) AS doc_no");
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
			$sql1->whereIn('plant_code', $plant_code);
			$sql1->whereIn('loc_code', $loc_code);
			
		}
		
		if(in_array('StoreUser', $userRoles))
		{
			$store_stats = 1;
			$sql1->where('status', '>=',$store_stats);
			$is_storeuser = true;
		}
		
		$gateEntries = $sql1->orderby('id','DESC')->paginate(10);
		//print_r($plant_code);print_r($loc_code);
		//$gateEntries = $sql1->orderby('id','DESC')->toSql(); echo $gateEntries;die;
		
		
		
        return view('gateentries.index', compact('gateEntries','is_storeuser','show_plant_loc'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
		$dtobj = Carbon::now();
				
		list($operator_name,$plant_code,$loc_code,$loc_name,$plantsres) = $this->ini_gateentry_data();
		
		$op_name = (!empty($gateentry)) ? $gateentry->createdby_name : $operator_name;
		$sel_loc = (!empty($gateentry)) ? $gateentry->loc_code : '';
		$sel_dept = (!empty($gateentry)) ? $gateentry->dept_id : '';
		$is_storeuser = false;
		
		$deptlist = DB::table('departments')->where('is_delete',0)->get();
		
        return view('gateentries.create', compact('dtobj','operator_name','plantsres','op_name','loc_code','loc_name','sel_loc','is_storeuser','deptlist','sel_dept'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
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
           
        ]);
		
		/*$gate_in_date = isset($validated->gate_in_date) ? $validated->gate_in_date : '';
		$gate_in_date1 = NULL;
		if($gate_in_date)
		{
			$gate_in_date_arr = explode("/",$gate_in_date);
			$gate_in_date_arr1tmp = $gate_in_date_arr[2]."-".$gate_in_date_arr[1]."-".$gate_in_date_arr[0];
			$gate_in_date1 = date('Y-m-d',strtotime($gate_in_date_arr1tmp));
		}*/
		
		$update_by = 0;
		if(Auth::user())
		{
			$update_by = Auth::user()->id;
		}
		//$request->merge(['created_by' => $prepared_by]);
		$validated['created_by'] = $update_by;
		
		/*echo '<pre>';
		print_r($validated);
		echo '</pre>';
		echo '<pre>';
		print_r($request->mat_code);
		echo '</pre>';
		die;*/
		
		
		$gateEntry = GateEntryDetail::create($validated);
		$insert_id = $gateEntry->id;

		$po_no = $validated['po_no'];
		$rgp_int_no = $validated['rgp_int_no'];
		
		$this->savemat_details($insert_id,$po_no,$rgp_int_no,$request,$update_by);
			//
			
        //return redirect()->route('gateentries.index')->with('success', 'Gate Entry created successfully.');
		
		return redirect()->route('gateentries.edit',$gateEntry)->with('success', 'Gate Entry saved successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(GateEntryDetail $gateentry) //string $id
    {
        //$gateentry->load('materialDetails');
		$dtobj = Carbon::now();
		$materialDetails = DB::table('gate_entry_item_details')->where('gate_entry_id',$gateentry->id)->where('is_delete',0)->get();
        return view('gateentries.show', compact('gateentry','materialDetails','dtobj'));
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GateEntryDetail $gateentry) //string $id
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
		
		list($operator_name,$plant_code,$loc_code,$loc_name,$plantsres) = $this->ini_gateentry_data();
		
		$op_name = (!empty($gateentry)) ? $gateentry->createdby_name : $operator_name;
		$sel_loc = (!empty($gateentry)) ? $gateentry->loc_code : '';
		$sel_dept = (!empty($gateentry)) ? $gateentry->dept_id : '';
		
		if(in_array('StoreUser', $userRoles))
		{
			$is_storeuser = true;
		}
		
		$deptlist = DB::table('departments')->where('is_delete',0)->get();
		
        return view('gateentries.edit', compact('gateentry','materialDetails','dtobj','plantsres','op_name','loc_code','loc_name','sel_loc','is_storeuser','deptlist','sel_dept'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GateEntryDetail $gateentry) //Request $request, string $id
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
           
        ]);
		
		/*$gate_in_date = isset($validated->gate_in_date) ? $validated->gate_in_date : '';
		$gate_in_date1 = NULL;
		if($gate_in_date)
		{
			$gate_in_date_arr = explode("/",$gate_in_date);
			$gate_in_date_arr1tmp = $gate_in_date_arr[2]."-".$gate_in_date_arr[1]."-".$gate_in_date_arr[0];
			$gate_in_date1 = date('Y-m-d',strtotime($gate_in_date_arr1tmp));
		}*/
		
		
		
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
		
		$gateentrynew = GateEntryDetail::where('id',$insert_id)->first();
		
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
    public function destroy(GateEntryDetail $gateentry) //string $id
    {
        $gateentry->delete();
        return redirect()->route('gateentries.index')->with('success', 'Gate Entry deleted successfully.');
    }
	
	public function savemat_details($insert_id,$po_no,$rgp_int_no,$request,$update_by,$isupdate=0)
	{
		if(count($request->mat_code) > 0)
		{
			if($isupdate)
			{
				$del_arr = array('is_delete' => 1, 'delete_date' => date('Y-m-d H:i:s'));
				DB::table("gate_entry_item_details")->where('gate_entry_id',$insert_id)->update($del_arr);
			}
				
			foreach($request->mat_code as $k1=>$mat_code)
			{
				if($mat_code != '')
				{
					$mat_srno = isset($request->mat_srno[$k1]) ? $request->mat_srno[$k1] : '';
					$mat_desc = isset($request->mat_desc[$k1]) ? $request->mat_desc[$k1] : '';
					$mat_unit = isset($request->mat_unit[$k1]) ? $request->mat_unit[$k1] : '';
					$mat_unit2 = isset($request->mat_unit2[$k1]) ? $request->mat_unit2[$k1] : '';
					$mat_remark = isset($request->mat_remark[$k1]) ? $request->mat_remark[$k1] : '';
					
					//compqty
					$mat_gateqty = isset($request->mat_gateqty[$k1]) ? $request->mat_gateqty[$k1] : 0;
					$mat_totalqty = isset($request->mat_totalqty[$k1]) ? $request->mat_totalqty[$k1] : 0;
					$mat_netweight = isset($request->mat_netweight[$k1]) ? $request->mat_netweight[$k1] : 0;
					$mat_po_chln_qty = isset($request->mat_po_chln_qty[$k1]) ? $request->mat_po_chln_qty[$k1] : 0;
					
					
					$mat_srno = ($mat_srno == '') ? 0 : $mat_srno;
					$mat_gateqty = ($mat_gateqty == '') ? 0 : $mat_gateqty;
					$mat_totalqty = ($mat_totalqty == '') ? 0 : $mat_totalqty;
					$mat_netweight = ($mat_netweight == '') ? 0 : $mat_netweight;
					$mat_po_chln_qty = ($mat_po_chln_qty == '') ? 0 : $mat_po_chln_qty;
					
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
		if(count($request->matidarr) > 0)
		{
			$gtidval = isset($request->gtidval) ? $request->gtidval : 0;
			$gtidval = ($gtidval == '') ? 0 : $gtidval;
			
			$sec_name_out = isset($request->sec_name_out) ? $request->sec_name_out : '';
			
			$storeuserid = 0;
			if(Auth::user())
			{
				$storeuserid = Auth::user()->id;
			}
			
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
				
				$gateentrynew = GateEntryDetail::where('id',$gtidval)->first();
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
			
			$gateentrynew = GateEntryDetail::where('id',$gtidcheckout)->first();
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
		//$gateentry = GateEntryDetail::where('id',$id)->first();
		$materialDetails = DB::table('gate_entry_item_details')->where('gate_entry_id',$gateentry->id)->where('is_delete',0)->get();
		
		$seldoclbl = 'NA';
		$location = $created_by = $dept_name = $addr1 = $addr2 = $dept_incharge = '';
		
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
			
			
		}
		
		$doc_no = '';
		if($materialDetails->count() > 0)
		{
			$doc_no = $materialDetails[0]->po_no;
		}
		
		
		$pdf_name = $this->get_pdf_filename($id, $gateentry->gate_in_no,$isrep);
		
		$logo_img = 'img/logo.png';
        $live_url = config('app.url');
		
        //$logo_img = str_replace('http://127.0.0.1:8000/', $live_url, $logo_img);
		$logo_img = $live_url.'/img/logo.png';
		$logo_img = 'https://quiz.growel.com/img/logo.png';
		
		$prefixn = ($isrep) ? 'gateprintreport' : 'gateprint';
		$pdf = PDF::loadView('gateentries.'.$prefixn,  compact('logo_img','gateentry','materialDetails','seldoclbl','doc_no','location','dept_name','addr1','addr2','dept_incharge'));
		
		$folder = storage_path('uploads/gateentries');
		$pdf_path = $folder.'/'.$pdf_name;
        $pdf->save($pdf_path);
		
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
					
					
					$pdf_name = $this->get_pdf_filename($id, $gateentrynew->gate_in_no,$isrep);
					if($pdf_name != '')
					{
						$mailData['attachment'] = storage_path("uploads\\gateentries\\".$pdf_name);
					}
                        
				foreach($receiversarr as $useremail=>$username)
				{
					if($useremail != '')
					{
						$mailData['user_name'] = $username;
						Mail::to($useremail)->send(new GateEntryMail($mailData));
					}
				}
				//CC
				$mailData['user_name'] = 'Vickeypaul M';
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
		return $userarr;
	}
	
	public function getpdfDownload(Request $request)
    {
        
            if($request->id)
            {
                $gateentrynew = GateEntryDetail::where('id',$request->id)->first();
				
				$isrep = 0;
				if($gateentrynew->status == 1)
				{
					$isrep = 0;
				}
				else if($gateentrynew->status == 2)
				{
					$isrep = 1;
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
	
}
