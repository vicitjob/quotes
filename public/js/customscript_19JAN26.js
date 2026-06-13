$(document).ready(function () {
		
	$('#materialselectall').on('change', function() {
        // Get the checked status of the "Select All" checkbox
        var isChecked = $(this).prop('checked');

        // Set the 'checked' property of all individual checkboxes to match
        $('input.matselect:visible').prop('checked', isChecked);
    });
	
	$(".onlydecimalval").keydown(function (event) { 
		//only_decimal_allow_func(event);
		if (event.shiftKey == true) {
				event.preventDefault();
			}
			if ((event.keyCode >= 48 && event.keyCode <= 57) || 
				(event.keyCode >= 96 && event.keyCode <= 105) || 
				event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 ||
				event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190|| event.keyCode == 110) {

			} else {
				event.preventDefault();
			}
			
			var tmpdot = $(this).val().indexOf('.');
			if((tmpdot !== -1 && event.keyCode == 190) || (tmpdot !== -1 && event.keyCode == 110))
				event.preventDefault(); 

			if(tmpdot !== -1 )
			{
				var tmpstart = this.selectionStart;
				//alert(tmpdot);alert(tmpstart);
				if(tmpstart > (tmpdot + 4)) 
				{
					if(event.keyCode == 8) {} else { event.preventDefault(); }
				}
				//var tmpvv = ($(this).val().split('.'));
				//if(tmpvv[1].length > 3) { event.preventDefault(); }
			}
			//if a decimal has been added, disable the "."-button
		});
		
		 $(".onlynumbers").keydown(function (event) {
			//only_decimal_allow_func(event);
			if (event.shiftKey == true) {
					event.preventDefault();
				}
				if ((event.keyCode >= 48 && event.keyCode <= 57) || 
					(event.keyCode >= 96 && event.keyCode <= 105) || 
					event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 ||
					event.keyCode == 39 || event.keyCode == 46 ) {

				} else {
					event.preventDefault();
				}
				
				//if a decimal has been added, disable the "."-button
			});
			
	setplantname();
	
	setvehiclename();
	setdoctypename(1);
	setgatename();
	//setlocname();
	storeusersetting();
	
	$('#gatecloseModal').on('shown.bs.modal', function (event) {
		const buttontmp = event.relatedTarget;
		const gaterowno = buttontmp.getAttribute('data-bs-gaterowno');
      // Do something when the modal is fully shown
      console.log('The modal is now completely visible (jQuery)!'+gaterowno);
	  var tmpgtrefno = $("#gtnorowid"+gaterowno).text();
	  $("#closegatehead").text(tmpgtrefno);
	  $("#security_name_checkout").val('');
	  $("#checkoutremarks").val('');
	  var tmpgtid = $("#gtnoid"+gaterowno).val();
	  $("#gtidcheckout").val(tmpgtid);
    });
	
	//PROD SEARCH
	if(document.getElementById("prodtag"))
	{
	  var funckeyvar = document.getElementById("prodtag");
	  if(funckeyvar.addEventListener){ //code for Moz
	   funckeyvar.addEventListener("keydown",customkeyenter,false); 
	  }else{
	   funckeyvar.attachEvent("onkeydown",customkeyenter); //code for IE
	  }

	  var funckeyvarup = document.getElementById("prodtag");
	  if(funckeyvarup.addEventListener){ //code for Moz
	   funckeyvarup.addEventListener("keyup",customkeyenterup,false); 
	  }else{
	   funckeyvarup.attachEvent("onkeyup",customkeyenterup); //code for IE
	  }

	}
	
	$(".prodcls").keydown(function (event) {
		let rowid = $(this).attr('id');
		let rowno = rowid.replace('prodtag','');
		customkeyenter(event,rowno);
	});
	//PROD SEARCH
	
});

function customkeyenter(e,rowno)
{
	var isautocomplete_open = $("#isautocomplete_open").val();
	if(isautocomplete_open == 1) { return; }
	if(typeof window.event!="undefined"){
		e=window.event;//code for IE
	}

	if(e.type=="keydown")
	{
		//t_cel[3].innerHTML=e.charCode;
		//alert(e.keyCode); return false;
		if(e.keyCode==13 || e.keyCode==40 || e.keyCode==38)
		{ 
			/*$("#prodtag").autocomplete( "disable" );
			ajxgetproddetails('prodtag',e.keyCode);

			e.preventDefault();*/
		}
		else if(e.keyCode==113)
		{ 
			/*$("#exampleprodsearch > tbody").empty();
			
			$("#prodsearchModal").modal({backdrop : false, show : true});
			$(".modal-dialog").draggable();*/
		}
		else
		{ 
			//$("#qcdetails").prop("disabled", true);
			//console.log(rowno);
			//$("#prodtag"+rowno).autocomplete( "enable" );
			//console.log('else');
			get_prod_name_list('prodtag',rowno);
		}
	}

}

function customkeyenterup(e)
{
	var isautocomplete_open = $("#isautocomplete_open").val();
	if(isautocomplete_open == 1) {  return; }
	//$("#qcdetails").prop("disabled", false);
	if(typeof window.event!="undefined"){
		e=window.event;//code for IE
	}

	if(e.type=="keyup"){
	//t_cel[3].innerHTML=e.charCode;
	//alert(e.keyCode); 
	//return false;
	if(e.keyCode==13 || e.keyCode==40 || e.keyCode==38) {}
	else
	{
		$("#prodname_hidden").val($("#prodtag"+rowno).val());
	}

	  e.preventDefault();

	}

}

//Product Name Search
function get_prod_name_list(elemname,rowno)
{
    //"#product_name"
	var prd_action = "/getfpproductsname";
	
    $( "#"+elemname+rowno ).autocomplete({
      //source: base_url + '/getfpproductsname'
        source: function( request, response ) {
                var fptype = '';
                $.ajax({
                          url: prd_action,
                          type: 'post',
                          dataType: "json",
                          data: {
							_token: $('input[name="_token"]').val(),
                            prodname_s: request.term,
                            fptype: fptype,
							istest: $('input[name="_token"]').val()
                          },
                          success: function( data ) {
                            response( data );
                          },
                    });
            },
        minLength: 2,
        focus: function(){
          //return false;
        },
        select: function (event, ui) {
          // Set selection
          
          var value_str = ui.item.value;
          var label_str = ui.item.label;
		  var label_str_new = label_str;
          if(elemname == "prodtag")
          {
              if(label_str != '')
              {
                //var label_str_new = label_str.replace(value_str+" (","");
                //label_str_new = label_str_new.replace(")","");
                //$("#product_code").val(label_str_new);
				
				/*var label_str_arr = label_str.split("$$$");
				var label_str_new = label_str_arr[0];
				var prod_val = label_str_arr[1];
				
				if(prod_val == '' || prod_val == 'null') { prod_val = 0; }
				prod_val = parseFloat(prod_val);
				$("#listprice_inr"+rowno).val(prod_val);
				
				ui.item.label = label_str_new;*/
				
				var value_str_arr = value_str.split("$$$");
				var value_str_new = value_str_arr[0];
				var pack_size = value_str_arr[1];
				
				ui.item.value = value_str_new;
				
				if(pack_size == '' || pack_size == 'null') { pack_size = 0; }
				pack_size = parseFloat(pack_size);
				$("#pack_size"+rowno).val(pack_size);
				
				$("#prdcd"+rowno).val(value_str_new);
				
				//Fetech Product Details
				var calltypdisc = 0;
				fetchproddet_calc(rowno,value_str_new,calltypdisc);
				fetchproddet_calc_others(rowno);
                //Fetech Product Details
              }
          }
		  
		  $("#"+elemname+rowno).val(ui.item.label); // display the selected text
          //$('#product_name').val(ui.item.value); // save selected id to input
          
          return false;
        },
        open: function( event, ui ) {},
        close: function( event, ui ) {}
    });

    if(elemname == "prodtag" )
    {
        $( "#"+elemname+rowno ).on( "autocompleteopen", function( event, ui ) {
            $("#isautocomplete_open").val(1);
        } );
        $( "#"+elemname+rowno ).on( "autocompleteclose", function( event, ui ) {
            $("#isautocomplete_open").val(0);
        } );
    }
    
}
//Product Name Search

function shownextbatch()
{
	//$(".next10").show();
	var roweditcnt = parseInt($("#roweditcnt").val());
	var roweditcntnew = roweditcnt + 10;
	
	for(var m=roweditcnt;m<roweditcntnew;m++)
	{
		$("#rowno"+m).show();
	}
	$("#roweditcnt").val(roweditcntnew);
}

function setplantname()
{
	
	var selplantcode = $("#plant_code").find('option:selected').val();
	var selplanttext = $("#plant_code").find('option:selected').text();
	if(selplanttext == 'None') { selplanttext = ''; }
	$("#plant_name").val(selplanttext);
	var selloc = $("#sel_loc").val();
	var seldept = $("#sel_dept").val();
	//fill location
	var frm_action = "/getdata_location";
        $.ajax({
            url : frm_action,
             type: "GET",
            dataType : "json",
			data:{plantcode:selplantcode},
            success: function(data, textStatus, jqXHR)
            {
                $('#loc_code').empty();
				$('#dept_id').empty();
				
                if(data)
                {
					var locdata = data.locations;
					
					$.each(locdata, function (key,value) {
						var locseltext = '';
						if(key == selloc)
						{
							locseltext = 'selected';
						}
						$('#loc_code').append('<option value="'+key+'" '+locseltext+'>'+value+'</option>');
					});
					setlocname();
					
					var deptdata = data.departments;
					
					$.each(deptdata, function (key1,value1) {
						var deptseltext = '';
						if(key1 == seldept)
						{
							deptseltext = 'selected';
						}
						$('#dept_id').append('<option value="'+key1+'" '+deptseltext+'>'+value1+'</option>');
					});
					
                    
                }

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                //$("#ajxload-custom").hide();
                
            }
        });
	//fill location
	
}

function setvehiclename()
{
	var selvehtext = $("#vehicle_type_code").find('option:selected').text();
	if(selvehtext == 'None') { selvehtext = ''; }
	$("#vehicle_type_desc").val(selvehtext);
	
	setvehiclelbl();
	setdeliverymanlbl();
	
}

function setvehiclelbl(retlbl=0)
{
	var selvehcode = $("#vehicle_type_code").val();
	var selvehlbl = 'Vehicle No.';
	if(selvehcode == '06')
	{
		selvehlbl = "Identity No.";
	}
	else if(selvehcode == '07')
	{
		selvehlbl = "Porter No.";
	}
	
	if(retlbl == 1)
	{
		return selvehlbl;
	}
	else
	{
		$("#vehiclelbl").text(selvehlbl);
	}
	
}

function setdeliverymanlbl(retlbl=0)
{
	var selvehcode = $("#vehicle_type_code").val();
	var seldelmanbl = 'Driver';
	if(selvehcode == '06' || selvehcode == '07')
	{
		seldelmanbl = "Person";
	}
	
	if(retlbl == 1)
	{
		return seldelmanbl;
	}
	else
	{
		$("#delpernamelbl").text(seldelmanbl);
		$("#delpermobilelbl").text(seldelmanbl);
	}
	
}

function setdoctypename(isini=0)
{
	var seldoccode = $("#doc_type_code").val();
	var seldoctext = $("#doc_type_code").find('option:selected').text();
	if(seldoctext == 'None') { seldoctext = ''; }
	$("#doc_type_name").val(seldoctext);
	
	var seldoclbl = 'NA';
	if(seldoccode == 'PO')
	{
		seldoclbl = 'PO No.';
		$("#getpodetails").show();
		$("#shownextbatch").hide();
	}
	else if(seldoccode == 'DC')
	{
		seldoclbl = 'DC No.';
		$("#getpodetails").hide();
		$("#shownextbatch").show();
	}
	else if(seldoccode == 'OTH')
	{
		seldoclbl = 'Other Ref. No.';
		$("#getpodetails").hide();
		$("#shownextbatch").show();
	}
	else
	{
		$("#getpodetails").hide();
		$("#shownextbatch").show();
	}
	$("#doctyptext").text(seldoclbl);
	
	if(isini==0)
	{
		$("#roweditcnt").val(0);
		$("#vendor_name").val('');
		$("#po_no").val('');
		$('#tblbatchprod').find('input, select, textarea').val('');
	}
	
}

function setgatename()
{
	var selgatetext = $("#sec_id_gt_in").find('option:selected').text();
	if(selgatetext == 'None') { selgatetext = ''; }
	$("#sec_id_gt_in_name").val(selgatetext);
	
}

function setlocname()
{
	var selloctext = $("#loc_code").find('option:selected').text();
	if(selloctext == 'None') { selloctext = ''; }
	console.log('Location : '+selloctext);
	$("#loc_name").val(selloctext);
	
}

function storeusersetting()
{
	var isstoreuserhid = $("#isstoreuser").val();
	if(isstoreuserhid == '1') 
	{  
		$("#shownextbatch").hide();
		$("#addcontainerbtn").hide();
		$("#getpodetails").hide();
		$("#removeselectedmat").hide();
		
		$('input, textarea').prop('readonly', true);
		$('select').attr('disabled', 'disabled');
		$('input[type="checkbox"]').prop('disabled', true);
		
		$('input[name="mat_storeqty[]"]').prop('readonly', false);
		$('input[name="mat_storeremark[]"]').prop('readonly', false);
		
		$('input[name="cont_storeqty[]"]').prop('readonly', false);
		$('input[name="cont_storeremark[]"]').prop('readonly', false);
		
		$('#security_name_out').prop('readonly', false);
	}
}

function getpodetailsfunc()
{
	var seldoccode = $("#doc_type_code").val();
	if(seldoccode == 'PO')
	{
		var po_no = $("#po_no").val();
		if(po_no != '')
		{
			var frm_action = "/getpodetails";
			$("#getpodetails").attr('DISABLED',true);
			$("#matloader").show();
				$.ajax({
					url : frm_action,
					type: "GET",
					dataType: 'json',
					data:{po_no:po_no},
					success: function(data, textStatus, jqXHR)
					{
						if(data)
						{
							//var sappo_arr = JSON.parse(data);
							//console.log(sappo_arr);
							if(data.length > 0)
							{
								var roweditcnt = $("#roweditcnt").val();
								for(var m=0;m<data.length;m++)
								//for(var m=roweditcnt;m<data.length;m++)
								{
									var tmpsapobj = data[m];
									
									const matWithpreZeros = tmpsapobj.MATNR;
									const matWithoutpreZeros = matWithpreZeros.replace(/^0+/, '');
									
									let poqty = '';
									if(tmpsapobj.MENGE != '')
									{
										poqty = parseFloat(tmpsapobj.MENGE).toFixed(2);
									}

									var rowno = m+1;
									$("#mat_srno"+rowno).val(tmpsapobj.EBELP);
									$("#mat_srno"+rowno).attr('READONLY', true);
									$("#mat_code"+rowno).val(matWithoutpreZeros);
									$("#mat_code"+rowno).attr('READONLY', true);
									$("#mat_desc"+rowno).val(tmpsapobj.TXZ01);
									$("#mat_desc"+rowno).attr('READONLY', true);
									$("#mat_po_chln_qtyy"+rowno).val(poqty);
									$("#mat_po_chln_qtyy"+rowno).attr('READONLY', true);
									$("#mat_unit"+rowno).val(tmpsapobj.MEINS);
									$("#mat_unit"+rowno).attr('READONLY', true);
									
									$("#rowno"+rowno).show();
									
									if(m==0)
									//if(m==roweditcnt)
									{
										$("#vendor_name").val(tmpsapobj.NAME1);
										$("#vendor_name").attr('READONLY', true);
									}
									
								}
								
								$("#roweditcnt").val(data.length);
								
								
								
							}
							
						}
						$("#matloader").hide();
						$("#getpodetails").attr('DISABLED',false);
					},
					error: function (jqXHR, textStatus, errorThrown)
					{
						
						$("#matloader").hide();
						$("#getpodetails").attr('DISABLED',false);
						
					}
				});
		}
		
	}
	return false;
}

function removeselectedmat()
{
	//var remove_mat_len = $("input:checkbox[name=materialselect]:checked").length;
	var remove_mat_len = $(".matselect:checked").length;
	
	if(remove_mat_len > 0)
	{
		var isconfirm = confirm("Do you really want to remove selected item/material from the this list?");
		if(isconfirm)
		{
		
			//$("input:checkbox[name=materialselect]:checked").each(function(){
			$(".matselect:checked").each(function(){
				let rowid = $(this).attr('id');
				let rowno = rowid.replace('matsel','');
				$("#rowno"+rowno).remove();
				
			});
			
		}
	}
	
}

function validatentryform()
{
	console.log('validatentryform'); 
	$("#savebtn").attr('disabled',true);
	var err_msg = '';
	
	var t=0;
	var isquotationfilled = false;
	/*$('input[name="unit_qty[]"]').each(function(){
		
		var contdescid = $(this).attr('id');
		var controwno = contdescid.replace('unit_qty','');
		
		let contdesc = $("#unit_qty"+controwno).val();
		if(contdesc != '')
		{
			isquotationfilled = true;
		}
		t++;
	});*/
	
	var s=0;
	$('input[name="prodtag[]"]').each(function(){
		
		var matdescid = $(this).attr('id');
		var matrowno = matdescid.replace('prodtag','');
		var matdesc = $("#prodtag"+matrowno).val();
		var unitqty = $("#unit_qty"+matrowno).val();
		if(s==0)
		{
			if(matdesc == '')
			{
				err_msg += "Please Enter first Quotation item\n";
			}
			else
			{
				if(unitqty == '' || unitqty == 0)
				{
					err_msg += "Please Enter Unit Quantity\n";
				}
			}
		}
		else
		{
			if(matdesc != '')
			{
				if(unitqty == '' || unitqty == 0)
				{
					err_msg += "Please check all Unit Quantities are filled\n";
				}
			}
		}
		s++;
		
	});
	
	if(err_msg == '') { return true; }
	else
	{
		$("#savebtn").attr('disabled',false);
		alert(err_msg);
		return false;
	}
	
}

function save_storeuser_data()
{
	var matidarr = [];
	var contidarr = []; 
	var storeqtyarr = [];
	var storeremarr = [];
	var cont_storeqtyarr = [];
	var cont_storeremarr = [];
	var x=0;
	var y=0;
	var gtidval = $("#gtid").val();
	var sec_name_out = $("#security_name_out").val();
	
	$('input[name="mat_id[]"]').each(function(){
		//console.log($(this).val());
		if($(this).val() == '' || $(this).val() == '0') {}
		else
		{
			matidarr.push($(this).val());
			
			var matidid = $(this).attr('id');
			let matrowno = matidid.replace('mat_id','');
			let matstoreqty = $("#mat_storeqty"+matrowno).val();
			let matstorerem = $("#mat_storeremark"+matrowno).val();
			
			storeqtyarr.push(matstoreqty);
			storeremarr.push(matstorerem);
		}
		
		x++;
		
	});
	
	$('input[name="container_id[]"]').each(function(){
		//console.log($(this).val());
		if($(this).val() == '' || $(this).val() == '0') {}
		else
		{
			contidarr.push($(this).val());
			
			var contidid = $(this).attr('id');
			let controwno = contidid.replace('container_id','');
			let contstoreqty = $("#cont_storeqty"+controwno).val();
			let contstorerem = $("#cont_storeremark"+controwno).val();
			
			cont_storeqtyarr.push(contstoreqty);
			cont_storeremarr.push(contstorerem);
		}
		
		y++;
		
	});
	
	//console.log(gtidval);
	//console.log(sec_name_out);
	//console.log(matidarr);
	//console.log(storeqtyarr);
	//console.log(storeremarr);
	
	var frm_action = "/savestoreuserdata";
	//$("#savestoredata").attr('DISABLED',true);
	$("#savestoredata").hide();
	//$("#matloader").show();
		$.ajax({
			url : frm_action,
			type: "POST",
			dataType: 'json',
			data:{_token: $('input[name="csrf-token"]').attr('content'),gtidval:gtidval,sec_name_out:sec_name_out,matidarr:matidarr,storeqtyarr:storeqtyarr,storeremarr:storeremarr,contidarr:contidarr,cont_storeqtyarr:cont_storeqtyarr,cont_storeremarr:cont_storeremarr},
			success: function(data, textStatus, jqXHR)
			{
				if(data)
				{
					//var sappo_arr = JSON.parse(data);
					//console.log(sappo_arr);
					if(data.message == 'success')
					{
						alert('Saved Successfully');
						window.location.href = '/gateentries';
					}
					else
					{
						alert('Error while saving');
					}
					
				}
				//$("#matloader").hide();
				//$("#getpodetails").attr('DISABLED',false);
				$("#savestoredata").show();
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				
				//$("#matloader").hide();
				//$("#getpodetails").attr('DISABLED',false);
				$("#savestoredata").show();
			}
		});
	
	return false;
}

function validateIndianVehicleNumber(vehicleNumber) 
{
  // Regex to match the Indian vehicle number format
  // ^[A-Z]{2} : Start with two uppercase letters (state code)
  // [ -]? : Optional space or hyphen
  // [0-9]{2} : Two digits (district code)
  // [ -]? : Optional space or hyphen
  // [A-Z]{1,2} : One or two uppercase letters (series)
  // [ -]? : Optional space or hyphen
  // [0-9]{4}$ : Four digits (unique number) at the end
  const regex = /^[A-Z]{2}[ -]?[0-9]{2}[ -]?[A-Z]{1,2}[ -]?[0-9]{4}$/;

  // Test the input string against the regex
  return regex.test(vehicleNumber);
}

function save_checkout_data()
{
	var secnamechkout = $("#security_name_checkout").val();
	var chkoutremark = $("#checkoutremarks").val();
	var gtidcheckout = $("#gtidcheckout").val();
	
	var frm_action = "/savecheckoutdata";
	
	$("#checkoutloader").show();
	$("#savecheckoutdata").hide();
		$.ajax({
			url : frm_action,
			type: "POST",
			dataType: 'json',
			data:{_token: $('input[name="csrf-token1"]').attr('content'),gtidcheckout:gtidcheckout,secnamechkout:secnamechkout,chkoutremark:chkoutremark},
			success: function(data, textStatus, jqXHR)
			{
				var issuccess = false;
				if(data)
				{
					//var sappo_arr = JSON.parse(data);
					//console.log(sappo_arr);
					if(data.message == 'success')
					{
						//alert('Gate Entry Closed Successfully');
						$("#checkoutmsg").text('Gate Entry Closed Successfully');
						var issuccess = true;
					}
					else
					{
						//alert('Error while saving');
						$("#checkoutmsg").text('Error while saving');
						$("#savecheckoutdata").show();
					}
					$("#checkoutmsg").show();
					
				}
				$("#checkoutloader").hide();
				if(issuccess)
				{
					setTimeout(function() {
						window.location.reload();
					}, 2000); // 2000 milliseconds = 2 seconds
				}
				//$("#getpodetails").attr('DISABLED',false);
			},
			error: function (jqXHR, textStatus, errorThrown)
			{
				$("#savecheckoutdata").show();
				$("#checkoutloader").hide();
				//$("#getpodetails").attr('DISABLED',false);
				
			}
		});
}



function removecontainer(rowno)
{
	if(document.getElementById('controw'+rowno))
	{
		var isconfirm = confirm("Do you really want to remove this item?");
		if(isconfirm)
		{
			$('#controw'+rowno).remove();
		}
	}
}

function resetsrchform()
{
	$('#srchfrm_quoteentry').find('input:text, select, textarea').val('');
	$('#srchfrm_quoteentry input[type="date"], #srchfrm_gateentry input[type="time"]').val('');
	//location.reload();
	$('#srchfrm_quoteentry').submit();
}

function validatesearchform()
{
	return true;
}

function quotationconfig()
{
	return true;
}

function setshiptoqty(rowno)
{
	var shiptovol = 0;
	var prodcode = $("#prdcd"+rowno).val();
	//console.log('PRODCODE : '+prodcode);
	if(prodcode == '' || prodcode == 'null' || typeof prodcode === "undefined") {}
	else
	{
		//var prdcd_arr = prodcode.split("-");
		//var prod_size = prdcd_arr[1];
		
		var prod_size = $("#pack_size"+rowno).val();
		
		console.log('PRODSIZE : '+prod_size);
		
		if(prod_size == '' || prod_size == 'null' || typeof prod_size === "undefined") { prod_size = 0; }
		prod_size = parseInt(prod_size);
		
		var unit_qty = $("#unit_qty"+rowno).val();
		if(unit_qty == '' || unit_qty == 'null' || typeof unit_qty === "undefined") { unit_qty = 0; }
		unit_qty = parseInt(unit_qty);
		
		shiptovol = prod_size * unit_qty;
	}
	$("#ship_vol_qty"+rowno).val(shiptovol);
	$("#ship_vol_qty_span"+rowno).text(shiptovol);
	
	settotalshipvol();
	
	if(prodcode == '' || prodcode == 'null' || typeof prodcode === "undefined") {}
	else
	{
		//Fetech Product Details
		
		var calltypdisc = 0;
		fetchproddet_calc(rowno,prodcode,calltypdisc);
		fetchproddet_calc_others(rowno);
		//Fetech Product Details
	}
}

function fetchproddet_calc(rowno,prodcode,calltypdisc=0)
{
	console.log('Product : '+prodcode);
	if(prodcode != '')
	{
		var prd_data_action = "/getprddata";
		
		var disc_val = $("#disc_val"+rowno).val();
		if(disc_val == '' || disc_val == 'null') { disc_val = 0; }
		disc_val = parseFloat(disc_val);
		
		var ship_vol_qty = $("#ship_vol_qty"+rowno).val();
		if(ship_vol_qty == '' || ship_vol_qty == 'null') { ship_vol_qty = 0; }
		ship_vol_qty = parseFloat(ship_vol_qty);
		
		var disc_type = $("#disc_type"+rowno).val();
		if(disc_type == '' || disc_type == 'null') { disc_type = ''; }
		
		var ttlshipvol = $("#total_ship_vol_qty").val();
		if(ttlshipvol == '' || ttlshipvol == 'null') { ttlshipvol = 0; }
		ttlshipvol = parseFloat(ttlshipvol);
		
		var pack_size_tmp = $("#pack_size"+rowno).val();
		if(pack_size_tmp == '' || pack_size_tmp == 'null') { pack_size_tmp = 0; }
		pack_size_tmp = parseFloat(pack_size_tmp);
		
		var cargo_type = $("#cargo_type").val();
		
		
		$.ajax({
				  url: prd_data_action,
				  type: 'post',
				  dataType: "json",
				  data: {
					_token: $('input[name="_token"]').val(),
					prodcode_s: prodcode,
					disc_val: disc_val,
					calltypdisc:calltypdisc,
					disc_type:disc_type,
					ship_vol_qty:ship_vol_qty,
					ttlshipvol:ttlshipvol,
					cargo_type:cargo_type
				  },
				  success: function( data ) {
					//console.log( data );
					//console.log(data.prod_rate_inr);
					
					if(calltypdisc)
					{
						var tmp_disc_val = data.disc_val;
						if(tmp_disc_val == '' || tmp_disc_val == 'null') { tmp_disc_val = 0; }
						tmp_disc_val = parseFloat(tmp_disc_val);
						$("#disc_val"+rowno).val(tmp_disc_val);
					}
					
					var list_rate_inr = data.prod_rate_inr;
					if(list_rate_inr == '' || list_rate_inr == 'null') { list_rate_inr = 0; }
					list_rate_inr = parseFloat(list_rate_inr);
					
					var list_rate_inr_total = data.prod_rate_inr_total;
					if(list_rate_inr_total == '' || list_rate_inr_total == 'null') { list_rate_inr_total = 0; }
					list_rate_inr_total = parseFloat(list_rate_inr_total);
					
					var list_rate_usd = data.prod_rate_usd;
					if(list_rate_usd == '' || list_rate_usd == 'null') { list_rate_usd = 0; }
					list_rate_usd = parseFloat(list_rate_usd);
					
					var list_rate_usd_total = data.prod_rate_usd_total;
					if(list_rate_usd_total == '' || list_rate_usd_total == 'null') { list_rate_usd_total = 0; }
					list_rate_usd_total = parseFloat(list_rate_usd_total);
					
					var unp_exwork_inr = data.unp_exwork_inr;
					if(unp_exwork_inr == '' || unp_exwork_inr == 'null') { unp_exwork_inr = 0; }
					unp_exwork_inr = parseFloat(unp_exwork_inr);
					
					var unp_exwork_usd = data.unp_exwork_usd;
					if(unp_exwork_usd == '' || unp_exwork_usd == 'null') { unp_exwork_usd = 0; }
					unp_exwork_usd = parseFloat(unp_exwork_usd);
					
					var pack_fcl_nh = data.pack_fcl_nh;
					if(pack_fcl_nh == '' || pack_fcl_nh == 'null') { pack_fcl_nh = 0; }
					pack_fcl_nh = parseFloat(pack_fcl_nh);
					
					var pack_lcl_nh_pl = data.pack_lcl_nh_pl;
					if(pack_lcl_nh_pl != "Too Large")
					{
						if(pack_lcl_nh_pl == '' || pack_lcl_nh_pl == 'null') { pack_lcl_nh_pl = 0; }
						pack_lcl_nh_pl = parseFloat(pack_lcl_nh_pl);
					}
					
					var fob_fcl_nh = data.fob_fcl_nh;
					if(fob_fcl_nh == '' || fob_fcl_nh == 'null') { fob_fcl_nh = 0; }
					fob_fcl_nh = parseFloat(fob_fcl_nh);
					
					var fob_lcl_nh_pl = data.fob_lcl_nh_pl;
					if(fob_lcl_nh_pl != "Too Large")
					{
						if(fob_lcl_nh_pl == '' || fob_lcl_nh_pl == 'null') { fob_lcl_nh_pl = 0; }
						fob_lcl_nh_pl = parseFloat(fob_lcl_nh_pl);
					}
					
					var fob_fcl_h_pl = data.fob_fcl_h_pl;
					if(fob_fcl_h_pl == '' || fob_fcl_h_pl == 'null') { fob_fcl_h_pl = 0; }
					fob_fcl_h_pl = parseFloat(fob_fcl_h_pl);
					
					var fob_lcl_h_pl = data.fob_lcl_h_pl;
					if(fob_lcl_h_pl != "Too Large")
					{
						if(fob_lcl_h_pl == '' || fob_lcl_h_pl == 'null') { fob_lcl_h_pl = 0; }
						fob_lcl_h_pl = parseFloat(fob_lcl_h_pl);
					}
					
					var cif_fcl_nh = data.cif_fcl_nh;
					if(cif_fcl_nh == '' || cif_fcl_nh == 'null') { cif_fcl_nh = 0; }
					cif_fcl_nh = parseFloat(cif_fcl_nh);
					
					var cif_lcl_nh_pl = data.cif_lcl_nh_pl;
					if(cif_lcl_nh_pl != "Too Large")
					{
						if(cif_lcl_nh_pl == '' || cif_lcl_nh_pl == 'null') { cif_lcl_nh_pl = 0; }
						cif_lcl_nh_pl = parseFloat(cif_lcl_nh_pl);
					}
					
					var cif_fcl_h_pl = data.cif_fcl_h_pl;
					if(cif_fcl_h_pl == '' || cif_fcl_h_pl == 'null') { cif_fcl_h_pl = 0; }
					cif_fcl_h_pl = parseFloat(cif_fcl_h_pl);
					
					var cif_lcl_h_pl = data.cif_lcl_h_pl;
					if(cif_lcl_h_pl != "Too Large")
					{
						if(cif_lcl_h_pl == '' || cif_lcl_h_pl == 'null') { cif_lcl_h_pl = 0; }
						cif_lcl_h_pl = parseFloat(cif_lcl_h_pl);
					}
					
					var landed_fcl_nh = data.landed_fcl_nh;
					if(landed_fcl_nh == '' || landed_fcl_nh == 'null') { landed_fcl_nh = 0; }
					landed_fcl_nh = parseFloat(landed_fcl_nh);
					
					var landed_lcl_nh_pl = data.landed_lcl_nh_pl;
					if(landed_lcl_nh_pl != "Too Large")
					{
						if(landed_lcl_nh_pl == '' || landed_lcl_nh_pl == 'null') { landed_lcl_nh_pl = 0; }
						landed_lcl_nh_pl = parseFloat(landed_lcl_nh_pl);
					}
					
					var landed_fcl_h_pl = data.landed_fcl_h_pl;
					if(landed_fcl_h_pl == '' || landed_fcl_h_pl == 'null') { landed_fcl_h_pl = 0; }
					landed_fcl_h_pl = parseFloat(landed_fcl_h_pl);
					
					var landed_lcl_h_pl = data.landed_lcl_h_pl;
					if(landed_lcl_h_pl != "Too Large")
					{
						if(landed_lcl_h_pl == '' || landed_lcl_h_pl == 'null') { landed_lcl_h_pl = 0; }
						landed_lcl_h_pl = parseFloat(landed_lcl_h_pl);
					}
					
					var recom_dis_sp_to_buyer = data.recom_dis_sp_to_buyer;
					if(recom_dis_sp_to_buyer == '' || recom_dis_sp_to_buyer == 'null') { recom_dis_sp_to_buyer = 0; }
					recom_dis_sp_to_buyer = parseFloat(recom_dis_sp_to_buyer);
					
					var recom_sp_aft_credit_miniman = data.recom_sp_aft_credit_miniman;
					if(recom_sp_aft_credit_miniman == '' || recom_sp_aft_credit_miniman == 'null') { recom_sp_aft_credit_miniman = 0; }
					recom_sp_aft_credit_miniman = parseFloat(recom_sp_aft_credit_miniman);
					
					$("#listprice_inr_unit"+rowno).val(list_rate_inr);
					$("#listprice_inr_unit_span"+rowno).text(list_rate_inr);
					
					$("#listprice_usd_unit"+rowno).val(list_rate_usd);
					$("#listprice_usd_unit_span"+rowno).text(list_rate_usd);
					
					$("#listprice_inr"+rowno).val(list_rate_inr_total);
					$("#listprice_inr_span"+rowno).text(list_rate_inr_total);
					
					$("#listprice_usd"+rowno).val(list_rate_usd_total);
					$("#listprice_usd_span"+rowno).text(list_rate_usd_total);
					
					$("#unp_exwork_inr"+rowno).val(unp_exwork_inr);
					$("#unp_exwork_inr_span"+rowno).text(unp_exwork_inr);
					
					$("#unp_exwork_usd"+rowno).val(unp_exwork_usd);
					$("#unp_exwork_usd_span"+rowno).text(unp_exwork_usd);
					
					$("#pack_fcl_nh"+rowno).val(pack_fcl_nh);
					$("#pack_fcl_nh_span"+rowno).text(pack_fcl_nh);
					
					$("#pack_lcl_pallet"+rowno).val(pack_lcl_nh_pl);
					$("#pack_lcl_pallet_span"+rowno).text(pack_lcl_nh_pl);
					
					$("#fob_fcl_nh"+rowno).val(fob_fcl_nh);
					$("#fob_fcl_nh_span"+rowno).text(fob_fcl_nh);
					
					$("#fob_lcl_nh_pl"+rowno).val(fob_lcl_nh_pl);
					$("#fob_lcl_nh_pl_span"+rowno).text(fob_lcl_nh_pl);
					
					$("#fob_fcl_h_pl"+rowno).val(fob_fcl_h_pl);
					$("#fob_fcl_h_pl_span"+rowno).text(fob_fcl_h_pl);
					
					$("#fob_lcl_h_pl"+rowno).val(fob_lcl_h_pl);
					$("#fob_lcl_h_pl_span"+rowno).text(fob_lcl_h_pl);
					
					$("#cif_fcl_nh"+rowno).val(cif_fcl_nh);
					$("#cif_fcl_nh_span"+rowno).text(cif_fcl_nh);
					
					$("#cif_lcl_nh_pl"+rowno).val(cif_lcl_nh_pl);
					$("#cif_lcl_nh_pl_span"+rowno).text(cif_lcl_nh_pl);
					
					$("#cif_fcl_h_pl"+rowno).val(cif_fcl_h_pl);
					$("#cif_fcl_h_pl_span"+rowno).text(cif_fcl_h_pl);
					
					$("#cif_lcl_h_pl"+rowno).val(cif_lcl_h_pl);
					$("#cif_lcl_h_pl_span"+rowno).text(cif_lcl_h_pl);
					
					$("#landed_fcl_nh"+rowno).val(landed_fcl_nh);
					$("#landed_fcl_nh_span"+rowno).text(landed_fcl_nh);
					
					$("#landed_lcl_nh_pl"+rowno).val(landed_lcl_nh_pl);
					$("#landed_lcl_nh_pl_span"+rowno).text(landed_lcl_nh_pl);
					
					$("#landed_fcl_h_pl"+rowno).val(landed_fcl_h_pl);
					$("#landed_fcl_h_pl_span"+rowno).text(landed_fcl_h_pl);
					
					$("#landed_lcl_h_pl"+rowno).val(landed_lcl_h_pl);
					$("#landed_lcl_h_pl_span"+rowno).text(landed_lcl_h_pl);
					
					$("#recom_dis_sp_to_buyer"+rowno).val(recom_dis_sp_to_buyer);
					$("#recom_dis_sp_to_buyer_span"+rowno).text(recom_dis_sp_to_buyer);
					
					$("#recom_sp_aft_credit_miniman"+rowno).val(recom_sp_aft_credit_miniman);
					$("#recom_sp_aft_credit_miniman_span"+rowno).text(recom_sp_aft_credit_miniman);
					
				  },
			});
	}
}

function setdisvalue(selval, rowno)
{
	if(selval == '')
	{
		$("#disc_val"+rowno).val(0);
		var listprice_inr = $("#listprice_inr"+rowno).val();
		if(listprice_inr == '' || listprice_inr == 'null' || typeof listprice_inr == "undefined")
		{
			listprice_inr = 0;
		}
		var listprice_usd = $("#listprice_usd"+rowno).val();
		if(listprice_usd == '' || listprice_usd == 'null' || typeof listprice_usd == "undefined")
		{
			listprice_usd = 0;
		}
		
		$("#unp_exwork_inr"+rowno).val(listprice_inr);
		$("#unp_exwork_usd"+rowno).val(listprice_usd);
	}
	
	//Fetech Product Details
	var prodcode = $("#prdcd"+rowno).val();
	var calltypdisc = 1;
	fetchproddet_calc(rowno,prodcode,calltypdisc);
	//fetchproddet_calc_others(rowno);
	//Fetech Product Details
}

function settotalshipvol()
{
	var ttlshipvol = 0;
	$('.shipvol').each(function(index, element) {
		let tmpshipvol = $(this).val();
		if(tmpshipvol == '' || tmpshipvol == 'null' || typeof tmpshipvol === "undefined") { tmpshipvol = 0; }
		tmpshipvol = parseFloat(tmpshipvol);
		ttlshipvol += tmpshipvol;
	});
	$("#total_ship_vol_qty").val(ttlshipvol);
}

function fetchproddet_calc_others(rownoby)
{
	var calltypdisc = 0;
	$('input[name="prdcd[]"]').each(function(){
		//console.log($(this).val());
		if($(this).val() == '' || $(this).val() == '0') {}
		else
		{
			//Fetech Product Details
			var prodcode = $(this).val();
			var contidid = $(this).attr('id');
			let rowno = contidid.replace('prdcd','');
			if(rownoby != rowno)
			{
				console.log(prodcode);
				fetchproddet_calc(rowno,prodcode,calltypdisc);
			}
			//Fetech Product Details
		}
		
	});
	
}

function exportexcel(elemid, filename_prefix)
{ console.log('exporting ....');
	var $clonedTable = $("#"+elemid).clone();
	
	setinputvalues($clonedTable);
	
	$clonedTable.appendTo("#clonetbl");
	
	$("#clonetbl").hide();
	//return false;
	
  var today_date = gettodaysdate();
    //$("#"+elemid).table2excel({
	//$clonedTable.table2excel({
	$("#clonetbl").table2excel({
        // exclude CSS class
        exclude:".noExl",
        name:"Quotations Report", //Sheet Name
        filename:`${filename_prefix}_${today_date}`,//do not include extension
        fileext:".xls", // file extension
		preserveColors: true,
		exclude_inputs: false
    });
}

function gettodaysdate()
{
  let today = new Date();
  let dd = today.getDate();
  let mm = today.getMonth() + 1;
  let yyyy = today.getFullYear();

  if(dd < 10)
  {
    dd = `0${dd}`;
  }
  if(mm < 10)
  {
    mm = `0${mm}`;
  }
  return `${dd}_${mm}_${yyyy}`;
}

function setinputvalues(clonedTable)
{
	console.log('cloning');
	
	/*clonedTable.find("input, select, textarea").each(function() {
        //var val = $(this).val();
        //$(this).replaceWith(val); // Replaces the input element with its text value
		$(this).after($(this).val());
    });*/
	clonedTable.find('input[name="cmat_srno[]"]').each(function() {
        //var val = $(this).val();
        //$(this).replaceWith(val); // Replaces the input element with its text value
		$(this).after($(this).val());
    });
	clonedTable.find('input[name="prodtag[]"]').each(function() {
        //var val = $(this).val();
        //$(this).replaceWith(val); // Replaces the input element with its text value
		$(this).after($(this).val());
    });
	clonedTable.find('input[name="unit_qty[]"]').each(function() {
        //var val = $(this).val();
        //$(this).replaceWith(val); // Replaces the input element with its text value
		$(this).after($(this).val());
    });
	clonedTable.find('input[name="disc_val[]"]').each(function() {
        //var val = $(this).val();
        //$(this).replaceWith(val); // Replaces the input element with its text value
		$(this).after($(this).val());
    });
	clonedTable.find('select').each(function() {
        //var val = $(this).val();
        //$(this).replaceWith(val); // Replaces the input element with its text value
		var selectedText = $(this).find(":selected").val();
		console.log(selectedText);
		$(this).after($(this).val());
    });
	
	clonedTable.attr("border", "1");
	/*clonedTable.find('td').css({
            'border': '2px solid red',      // Adds a 2px solid red border
            'background-color': '#ffcccb'  // Adds a light red background color
        });*/
		
	/*clonedTable.find("th").each(function() {
        $(this).attr("style", function(i, s) {
            return (s || "") + 
                "font-weight: 700; " +               // Standard Bold
                "mso-font-weight-alt: 700; " +       // Microsoft Bold
                "background: #D3D3D3; " +            // Standard Highlight
                "mso-pattern: black gray-125; " +    // Excel Fill Pattern
                "mso-background-source: auto;";      // Force Excel to use background
        });
    });
		*/
		
	/*clonedTable.find("th[colspan]").each(function() {
        $(this).attr("align", "center"); // Legacy attribute for compatibility
        $(this).css({
            "text-align": "center",
            "font-weight": "bold",
            "mso-number-format": "\\@", // Keep as text
            "mso-alignment": "center"   // Specific Microsoft alignment hint
        });
    });*/
		
	 // Apply inline styles to the clone
	clonedTable.find("th").css({
		"text-align" : "right !important",
		"font-weight" : "bold"
	});
    clonedTable.find("th, td").css({
		"background-color": "transparent",
		"border" : "1px solid #000000"
	});
    clonedTable.css("border-collapse", "collapse");
	
}


