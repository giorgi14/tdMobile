<html>
<head>
	<script type="text/javascript">
		var aJaxURL	            = "server-side/report/transaction_book.action.php";
		var aJaxURL_det	        = "server-side/operations/transaction_detail.action.php";
		var aJaxURL_show_letter = "server-side/main.action.php";
		var tName	            = "example";	//table name
		var fName	            = "add-edit-form"; //form name
		var change_colum_main   = "<'dataTable_buttons'T><'F'Cfipl>";
		    	
		$(document).ready(function () {
			$("#filt_day").chosen(); 
			$("#filt_month").chosen();       	
			LoadTable(tName,11,change_colum_main,aJaxURL);
			SetEvents("", "", "", tName, fName, aJaxURL,'',tName,11,change_colum_main,aJaxURL,'','','');
		});
        
		function LoadTable(tName,num,change_colum_main,aJaxURL){
			var total =	[6,7,8];
			GetDataTable(tName, aJaxURL, 'get_list', num, "&filt_day="+$("#filt_day").val()+"&filt_month="+$("#filt_month").val(), 0, "", 1, "desc", total, change_colum_main);
			setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
		}

		function LoadDialog(fname){
			var id		= $("#id").val();
			if(fname=='add-edit-form'){
    			var buttons = {
					"save": {
    		            text: "დადასტურება",
    		            id: "save-dialog1"
    		        },
    	        	"cancel": {
    		            text: "დახურვა",
    		            id: "cancel-dialog",
    		            click: function () {
    		            	$(this).dialog("close");
    		            	
    		            }
    		        }
    		    };
				/* Dialog Form Selector Name, Buttons Array */
			
    			GetDialog(fName, 730, "auto", buttons,"top");
    			
    			
    	        $('#received_currency_id').chosen();
    	        $('#add-edit-form, .add-edit-form-class').css('overflow','visible');
    	        
				GetDateTimes('transaction_date');
    	        

    	        $('#type_id').chosen();
    	        $('#client_id').chosen();
    	        $('#client_loan_number').chosen();
    	        $('#currency_id').chosen();

    	     }
		}
		
		$(document).on("change", "#filt_day", function () {
			LoadTable(tName,11,change_colum_main,aJaxURL);	 
	    });

		$(document).on("change", "#filt_month", function () {
			LoadTable(tName,11,change_colum_main,aJaxURL);	 
	    });

		$(document).on("click", ".callapp_refresh", function () {
			LoadTable(tName,11,change_colum_main,aJaxURL);	 
	    });

		$(document).on("click", ".show_letter", function () {
			param 	               = new Object();
    		param.act              = "get_edit_page";
    		param.id               = $(this).attr('client_id');
    		param.loan_currency_id = $(this).attr('loan_currency_id');
    		
    		$.ajax({
    	        url: aJaxURL_show_letter,
    		    data: param,
    	        success: function(data) {       
    				if(typeof(data.error) != "undefined"){
    					if(data.error != ""){
    						alert(data.error);
    					}else{
    						$("#add-edit-show_letter").html(data.page);
    						var buttons = {
			    				"cancel": {
			    		            text: "დახურვა",
			    		            id: "cancel-dialog",
			    		            click: function () {
			    		            	$(this).dialog("close");
			    		            }
			    		        }
			    		    };
    			            GetDialog("add-edit-show_letter", 1200, "auto", buttons, 'left+43 top');
    			            $('#add-edit-show_letter, .add-edit-show_letter-class').css('overflow-y','scroll');
    			            var dLength = [[10, 30, 50, -1], [10, 30, 50, "ყველა"]];
    			            var total =	[4,5,6,7,17,18,19,20,23,24];
    			            GetDataTable1("table_letter", aJaxURL_show_letter, "get_list1", 26, "&id="+param.id+"&loan_currency_id="+$("#loan_currency_id").val(), 0, dLength, 4, "desc", total, "<'F'Cpl>");
    			            setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
							$("#table_letter_length").css('top','0px');
    			            parame 		            = new Object();
    					    parame.act	            = "gel_footer";
    					    parame.id	            = $("#id").val();
    					    parame.loan_currency_id	= $("#loan_currency_id").val();
    					    
    					    $.ajax({
    		    		        url: aJaxURL_show_letter,
    		    			    data: parame,
    		    		        success: function(data) {			        
    		    					if(typeof(data.error) != 'undefined'){
    		    						if(data.error != ''){
    		    							alert(data.error);
    		    						}else{
    		    							gacema_lari      = $("#gacema_lari").html();
    	        							gacema_lari1     = $("#gacema_lari1").html();

    	        							darchenili_vali  = $("#darchenili_vali").html();
    	        							darchenili_vali1 = $("#darchenili_vali1").html();

    	        							daricxva_lari    = $("#daricxva_lari").html();
    	        							daricxva_lari1   = $("#daricxva_lari1").html();
    	        							
    	        							procenti_lari    = $("#procenti_lari").html();
    	        							procenti_lari1   = $("#procenti_lari1").html();
    	        							
    	        							dziri_lari       = $("#dziri_lari").html();
    	        							dziri_lari1      = $("#dziri_lari1").html();

    	        							if(darchenili_vali > 0){
    	        								
    	        								var delta  = ((parseFloat(darchenili_vali) + parseFloat(daricxva_lari)) - (parseFloat(procenti_lari)+parseFloat(dziri_lari))).toFixed(2);
    	        								var delta1 = ((parseFloat(darchenili_vali1) + parseFloat(daricxva_lari1)) - (parseFloat(procenti_lari1)+parseFloat(dziri_lari1))).toFixed(2);	
    	        							}else{
        	        							var delta  = ((parseFloat(gacema_lari) + parseFloat(daricxva_lari)) - (parseFloat(procenti_lari)+parseFloat(dziri_lari))).toFixed(2);
    	        								var delta1 = ((parseFloat(gacema_lari1) + parseFloat(daricxva_lari1)) - (parseFloat(procenti_lari1)+parseFloat(dziri_lari1))).toFixed(2);	
												
        	                			    }

    	        							insurance_fee  = $("#insurance_fee").html();
    	        							insurance_fee1 = $("#insurance_fee1").html();
    	        							
    	        							insurance_payed  = $("#insurance_payed").html();
    	        							insurance_payed1 = $("#insurance_payed1").html();

    	        						    ins_delta = (parseFloat(insurance_fee) - parseFloat(insurance_payed)).toFixed(2);
    	        						    ins_delta1 = (parseFloat(insurance_fee1) - parseFloat(insurance_payed1)).toFixed(2);

    	        						    other  = $("#other").html();
    	        						    other1 = $("#other1").html();
    	        							
    	        						    other_delta = (parseFloat(other) - parseFloat(other1)).toFixed(2);

    	        						    $("#insurance_delta").html(ins_delta);
    	        						    $("#insurance_delta1").html(ins_delta1);
    	        						    $("#other_delta").html(other_delta);
    	        						    
    	        							$("#remaining_root").html(delta);
    	        							$("#remaining_root_gel").html(delta1);
    	        							
    		    						}
    		    					}
    		    			    }
    		    		    });
    			        }
    				}
    	    	}
    	    });	 
	    });

		$(document).on("click", "#save-dialog1", function () {
		    param 		= new Object();
		    
			param.act	               = "save_transaction";
		    param.id	               = $("#id").val();
		    param.tr_id	               = $("#tr_id").val();
		    
		    param.month_fee		       = $("#month_fee").val();
		    param.month_fee1		   = $("#month_fee1").val();
		    param.month_fee2		   = $("#month_fee2").val();

		    param.payable_Fee		   = $("#payable_Fee").val();
		    param.yield		           = $("#yield").val();
		    
	    	param.root		           = $("#root").val();
	    	param.percent		       = $("#percent").val();
	    	param.penalti_fee	       = $("#penalti_fee").val();
	    	param.surplus	           = $("#surplus").val();
	    	param.type_id	           = $("#type_id").val();
	    	param.client_id	           = $("#client_id").val();

	    	param.month_fee_trasaction = $("#month_fee_trasaction").val();
	    	param.extra_fee            = $("#extra_fee").val();
	    	
	    	param.currency_id	       = $("#currency_id").val();
	    	param.received_currency_id = $('#received_currency_id').val();
	    	param.course	           = $("#course").val();
	    	param.transaction_date	   = $("#transaction_date").val();
	    	
			param.client_id            = $("#client_id").val();
			param.client_loan_number   = $("#client_loan_number").val();

	    	if(param.course == '' || param.course == '0.0000'){
		    	alert('შეავსე დარიცხვის კურსი');
			}else{
				$.ajax({
    		        url: aJaxURL,
    			    data: param,
    		        success: function(data) {			        
    					if(typeof(data.error) != 'undefined'){
    						if(data.error != ''){
    							alert(data.error);
    						}else{
    							var total =	[6,7,8];
    							GetDataTable1("example", aJaxURL, 'get_list', 11, "&filt_day="+$("#filt_day").val()+"&filt_month="+$("#filt_month").val(), 0, "", 1, "desc", total, change_colum_main);
    							setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
    			        		CloseDialog('add-edit-form');
    			        	}
    					}
    			    }
    		    });
			}
		});
		
		$(document).on("change", "#car_out", function () {
	    	car_out = $("input[id='car_out']:checked").val();
	    	
	    	if(car_out == 1){
	    		$(".car_out_class").css('display', '');
	    		param = new Object();
		    	
	    		param.act              = "get_canceled-loan";
	    		param.client_id        = $("#client_id").val();
	    		param.transaction_date = $("#transaction_date").val();
	    		
		    	$.ajax({
	    	        url: aJaxURL,
	    		    data: param,
	    	        success: function(data) {       
	    				if(typeof(data.error) != "undefined"){
	    					if(data.error != ""){
	    						alert(data.error);
	    					}else{
	        					$("#month_fee1").val(data.all_fee);
	        					$("#root1").val(data.remaining_root);
	        					$("#percent1").val(data.percent);
	        					$("#penalti_fee1").val(data.penalty);
	        					$("#payable_Fee1").val(data.sakomisio);
	        					$("#yield1").val(data.nasargeblebebi);
	    					}
	    				}
	    	    	}
	    	    });
		    }else{
		    	$(".car_out_class").css('display', 'none');
		    	param         =  new Object();
			    param.act     = "get_shedule";
			    param.status  = 1;
			    param.id                   = $("#client_id").val();
			    param.type_id              = $('#type_id').val();
			    param.transaction_date     = $("#transaction_date").val();
			    param.month_fee_trasaction = $("#month_fee_trasaction").val();
			    param.received_currency_id = $("#received_currency_id").val();
			    param.course               = $("#course").val();
			    
			    $.ajax({
			        url: aJaxURL,
				    data: param,
			        success: function(data) {			        
						if(typeof(data.error) != 'undefined'){
							if(data.error != ''){
								alert(data.error);
							}else{
								if(data.status==1){
	    							$("#month_fee1").val(data.pay_amount);
	    							$("#root1").val(data.root);
	    							$("#percent1").val(data.percent);
	    							$("#penalti_fee1").val(data.penalty);

	    							$("#month_fee2").val(data.pay_amount1);
	    							$("#root2").val(data.root1);
	    							$("#percent2").val(data.percent1);
	    							$("#penalti_fee2").val(data.penalty1);
	    							$("#month_fee").val(data.loan_pay_amount);
	    							$("#extra_fee").val((parseFloat(data.loan_pay_amount)+parseFloat(data.pay_amount1)).toFixed(2));
	    							
	    							$("#hidde_id").val(data.id);
	    							$("#currency_id").html(data.currenc).trigger("chosen:updated");
	    							$('#currency_id').prop('disabled', true).trigger("chosen:updated");
	    							$('#client_loan_number').html(data.agrement_data).trigger("chosen:updated");
		    					}else if(data.status==2){
		    						$("#month_fee1").val(data.insurance_fee);
		    						$("#month_fee").val(data.loan_pay_amount);
		    						$("#extra_fee").val((parseFloat(data.loan_pay_amount)+parseFloat(data.pay_amount1)).toFixed(2));
		    						
		    						$("#root1").val('');
	    							$("#percent1").val('');
	    							$("#penalti_fee1").val('');

	    							$("#month_fee2").val('');
	    							$("#root2").val('');
	    							$("#percent2").val('');
	    							$("#penalti_fee2").val('');

	    							$("#hidde_id").val(data.id);
	    							$('#currency_id').prop('disabled', false).trigger("chosen:updated");
	    							$('#client_loan_number').html(data.agrement_data).trigger("chosen:updated");
	    						}else if(data.status==3){
			    					$("#month_fee1").val(data.pledge_fee);
			    					$("#month_fee").val(data.loan_pay_amount);
			    					$("#extra_fee").val((parseFloat(data.loan_pay_amount)+parseFloat(data.pay_amount1)).toFixed(2));
			    					
			    					$("#root1").val('');
	    							$("#percent1").val('');
	    							$("#penalti_fee1").val('');

	    							$("#month_fee2").val('');
	    							$("#root2").val('');
	    							$("#percent2").val('');
	    							$("#penalti_fee2").val('');

	    							$("#hidde_id").val(data.id);
	    							$('#currency_id').prop('disabled', false).trigger("chosen:updated");
	    						}
							}
						}
				    }
			    });
		    }

	    	
		});

		$(document).on("keydown", "#month_fee_trasaction", function (event) {
			
			if (event.keyCode == $.ui.keyCode.ENTER){
				if($(this).val()==''){this_value = 0;}else{this_value = $(this).val();}
				if($("#month_fee2").val()==''){month_fee2 = 0;}else{month_fee2 = $("#month_fee2").val();}

				if($("#course").val() == ''){
					alert('შეავსეთ შესაბამისი კურსი');
				}else{
					if($("#received_currency_id").val() != $("#currency_id").val() && $("#currency_id").val() == 1){
						charicxuli_tanxa = (this_value * $("#course").val()).toFixed(2);
						zedmeti_tanxa = (parseFloat(charicxuli_tanxa) + parseFloat(month_fee2)).toFixed(2);
						$("#month_fee").val(charicxuli_tanxa);
						$("#extra_fee").val(zedmeti_tanxa);
					}else if($("#received_currency_id").val() != $("#currency_id").val() && $("#currency_id").val() == 2){
						charicxuli_tanxa = (parseFloat(this_value) / parseFloat($("#course").val())).toFixed(2);
						zedmeti_tanxa = (parseFloat(charicxuli_tanxa) + parseFloat(month_fee2)).toFixed(2);
						$("#month_fee").val(charicxuli_tanxa);
						$("#extra_fee").val(zedmeti_tanxa);
						
					}else{
						charicxuli_tanxa = this_value;
						zedmeti_tanxa = (parseFloat(charicxuli_tanxa) + parseFloat(month_fee2)).toFixed(2);
						$("#month_fee").val(charicxuli_tanxa);
						$("#extra_fee").val(zedmeti_tanxa);
					}
				}
			}
		});
		
		$(document).on("keydown", "#root", function (event) {
			if (event.keyCode == $.ui.keyCode.ENTER){
				
				if($(this).val()==''){this_value = 0;}else{this_value = $(this).val();}
				if($("#hidde_root").val()==0){
    				delta = (parseFloat($("#extra_fee").val())-parseFloat(this_value)).toFixed(2);
    				if(delta<0){
    					alert('მიუთითეთ კორექტული თანხა')
    					$("#root").css('background','#fb5959');
    					$("#error_mesage").html('არასწორი განაწილება!');
    				}else{
    					$("#hidde_root").val(1);
        				$("#extra_fee").val(delta);
        				$("#root").css('background','rgb(255, 255, 255)');
        				$("#percent").focus();
        				$("#error_mesage").html('');
    				}
    			}else{
    				$("#percent").focus();
        		}
            }
		});
		
		$(document).on("keydown", "#percent", function (event) {
			if (event.keyCode == $.ui.keyCode.ENTER){
				if($(this).val()==''){this_value = 0;}else{this_value = $(this).val();}
				if($("#hidde_percent").val()==0){
    				delta = (parseFloat($("#extra_fee").val())-parseFloat(this_value)).toFixed(2);
    				if(delta<0){
    					alert('მიუთითეთ კორექტული თანხა');
    					$("#percent").css('background','#fb5959');
    					$("#error_mesage").html('არასწორი განაწილება!');
    				}else{
    					$("#hidde_percent").val(1);
        				$("#extra_fee").val(delta);
        				$("#percent").css('background','rgb(255, 255, 255)');
        				$("#penalti_fee").focus();
        				$("#error_mesage").html('');
    				}
				}else{
    				$("#penalti_fee").focus();
        		}
            }
		});
		
		$(document).on("keydown", "#penalti_fee", function (event) {
			if (event.keyCode == $.ui.keyCode.ENTER){
				if($(this).val()==''){this_value = 0;}else{this_value = $(this).val();}
				if($("#hidde_penalty").val()==0){
    				delta = (parseFloat($("#extra_fee").val())-parseFloat(this_value)).toFixed(2);
    				if(delta<0){
    					alert('მიუთითეთ კორექტული თანხა');
    					$("#penalti_fee").css('background','#fb5959');
    					$("#error_mesage").html('არასწორი განაწილება!');
    				}else{
    					$("#hidde_penalty").val(1);
        				$("#extra_fee").val(delta);
        				$("#penalti_fee").css('background','rgb(255, 255, 255)');
        				if($("input[id='car_out']:checked").val()==1){
        					$("#payable_Fee").focus();
        				}else{
        					$("#surplus").focus();
            			}
        				$("#error_mesage").html('');
    				}
				}else{
					if($("input[id='car_out']:checked").val()==1){
    					$("#payable_Fee").focus();
    				}else{
    					$("#surplus").focus();
        			}
        		}
            }
		});

		$(document).on("keydown", "#payable_Fee", function (event) {
			if (event.keyCode == $.ui.keyCode.ENTER){
				if($(this).val()==''){this_value = 0;}else{this_value = $(this).val();}
				if($("#hidde_payable_Fee").val()==0){
    				delta = (parseFloat($("#extra_fee").val())-parseFloat(this_value)).toFixed(2);
    				if(delta<0){
    					alert('მიუთითეთ კორექტული თანხა');
    					$("#payable_Fee").css('background','#fb5959');
    					$("#error_mesage").html('არასწორი განაწილება!');
    				}else{
    					$("#hidde_payable_Fee").val(1);
        				$("#extra_fee").val(delta);
        				$("#payable_Fee").css('background','rgb(255, 255, 255)');
        				$("#yield").focus();
        				$("#error_mesage").html('');
    				}
				}else{
					$("#yield").focus();
        		}
            }
		});

		$(document).on("keydown", "#yield", function (event) {
			if (event.keyCode == $.ui.keyCode.ENTER){
				if($(this).val()==''){this_value = 0;}else{this_value = $(this).val();}
				if($("#hidde_yield").val()==0){
    				delta = (parseFloat($("#extra_fee").val())-parseFloat(this_value)).toFixed(2);
    				if(delta<0){
    					alert('მიუთითეთ კორექტული თანხა');
    					$("#yield").css('background','#fb5959');
    					$("#error_mesage").html('არასწორი განაწილება!');
    				}else{
    					$("#hidde_yield").val(1);
        				$("#extra_fee").val(delta);
        				$("#yield").css('background','rgb(255, 255, 255)');
        				$("#surplus").focus();
        				$("#error_mesage").html('');
    				}
				}else{
					$("#surplus").focus();
        		}
            }
		});

		
		$(document).on("keydown", "#surplus", function (event) {
			if (event.keyCode == $.ui.keyCode.ENTER){
				if($(this).val()==''){this_value = 0;}else{this_value = $(this).val();}
				if($("#hidde_surplus").val()==0){
    				delta = (parseFloat($("#extra_fee").val())-parseFloat(this_value)).toFixed(2);
    				if(delta<0){
    					alert('მიუთითეთ კორექტული თანხა');
    					$("#surplus").css('background','#fb5959');
    					$("#error_mesage").html('არასწორი განაწილება!');
    				}else{
    					$("#hidde_surplus").val(1);
    					$("#extra_fee").val(delta);
    					$("#surplus").css('background','rgb(255, 255, 255)');
    					$("#error_mesage").html('');
    				}
				}
            }
		});

		$(document).on("click", "#delete_root", function () {
	        root = $("#root").val();
	        if(root == ''){root = 0;}

	        if($("#hidde_root").val() == 1){
	        	$("#extra_fee").val(parseFloat($("#extra_fee").val()) + parseFloat(root));
	        	$("#root").val('');
	        	$("#hidde_root").val(0);
	        	$("#root").focus();
	        }else{
	        	$("#extra_fee").val(parseFloat($("#extra_fee").val()));
	        	$("#root").val('');
	        	$("#root").focus();
		    }
	    });

		$(document).on("click", "#delete_percent", function () {
			percent = $("#percent").val();
	        if(percent == ''){percent = 0;}

	        if($("#hidde_percent").val() == 1){
	        	$("#extra_fee").val(parseFloat($("#extra_fee").val()) + parseFloat(percent));
	        	$("#percent").val('');
	        	$("#hidde_percent").val(0);
	        	$("#percent").focus();
	        }else{
	        	$("#extra_fee").val(parseFloat($("#extra_fee").val()));
	        	$("#percent").val('');
	        	$("#percent").focus();
		    }
	    });

		$(document).on("click", "#delete_penalty", function () {
			penalti_fee = $("#penalti_fee").val();
	        if(penalti_fee == ''){penalti_fee = 0;}

	        if($("#hidde_penalty").val() == 1){
	        	$("#extra_fee").val(parseFloat($("#extra_fee").val()) + parseFloat(penalti_fee));
	        	$("#penalti_fee").val('');
	        	$("#hidde_penalty").val(0);
	        	$("#penalti_fee").focus();
	        }else{
	        	$("#extra_fee").val(parseFloat($("#extra_fee").val()));
	        	$("#penalti_fee").val('');
	        	$("#penalti_fee").focus();
		    }
	    });

		$(document).on("click", "#delete_payable_Fee", function () {
			payable_Fee = $("#payable_Fee").val();
	        if(payable_Fee == ''){payable_Fee = 0;}

	        if($("#hidde_payable_Fee").val() == 1){
	        	$("#extra_fee").val(parseFloat($("#extra_fee").val()) + parseFloat(payable_Fee));
	        	$("#payable_Fee").val('');
	        	$("#hidde_payable_Fee").val(0);
	        	$("#yield").focus();
	        }else{
	        	$("#extra_fee").val(parseFloat($("#extra_fee").val()));
	        	$("#payable_Fee").val('');
	        	$("#payable_Fee").focus();
		    }
	    });

		$(document).on("click", "#delete_yield", function () {
			yield = $("#yield").val();
	        if(yield == ''){yield = 0;}

	        if($("#hidde_yield").val() == 1){
	        	$("#extra_fee").val(parseFloat($("#extra_fee").val()) + parseFloat(yield));
	        	$("#yield").val('');
	        	$("#hidde_yield").val(0);
	        	$("#yield").focus();
	        }else{
	        	$("#extra_fee").val(parseFloat($("#extra_fee").val()));
	        	$("#yield").val('');
	        	$("#yield").focus();
		    }
	    });
	    
		$(document).on("click", "#delete_surplus", function () {
			surplus = $("#surplus").val();
	        if(surplus == ''){surplus = 0;}

	        if($("#hidde_surplus").val() == 1){
	        	$("#extra_fee").val(parseFloat($("#extra_fee").val()) + parseFloat(surplus));
	        	$("#surplus").val('');
	        	$("#hidde_surplus").val(0);
	        	$("#surplus").focus();
	        }else{
	        	$("#extra_fee").val(parseFloat($("#extra_fee").val()));
	        	$("#surplus").val('');
	        	$("#surplus").focus();
		    }
	    });

		$(document).on("change", "#transaction_date",  function (event) {
			param 	                = new Object();
		    
			param.act               = "get_cource";
		    param.transaction_date  = $(this).val();
		    
			$.ajax({
		        url: aJaxURL,
			    data: param,
		        success: function(data) {			        
					if(typeof(data.error) != 'undefined'){
						if(data.error != ''){
							alert(data.error);
						}else{
							$("#course").val(data.cource);
						}
					}
			    }
		    });
		    
			if($('#transaction_date').val() != ''){
		        $('#add_button_dettail').button("enable");
		    }else{
		    	$('#add_button_dettail').button("disable");
			}
	    });
	    
	    $(document).on("click", "#show_copy_prit_exel", function () {
	        if($(this).attr('myvar') == 0){
	            $('.ColVis,.dataTable_buttons').css('display','block');
	            $(this).css('background','#2681DC');
	            $(this).children('img').attr('src','media/images/icons/select_w.png');
	            $(this).attr('myvar','1');
	        }else{
	        	$('.ColVis,.dataTable_buttons').css('display','none');
	        	$(this).css('background','#FAFAFA');
	            $(this).children('img').attr('src','media/images/icons/select.png');
	            $(this).attr('myvar','0');
	        }
	    });
	    $(document).on("click", "#show_copy_prit_exel", function () {
	        if($(this).attr('myvar') == 0){
	            $('.ColVis,.dataTable_buttons').css('display','block');
	            $(this).css('background','#2681DC');
	            $(this).children('img').attr('src','media/images/icons/select_w.png');
	            $(this).attr('myvar','1');
	        }else{
	        	$('.ColVis,.dataTable_buttons').css('display','none');
	        	$(this).css('background','#FAFAFA');
	            $(this).children('img').attr('src','media/images/icons/select.png');
	            $(this).attr('myvar','0');
	        }
	    });
	   
    </script>
    <style type="text/css">
        #table_right_menu{
            position: relative;
            float: right;
            width: 70px;
            top: 37px;
        	z-index: 99;
        	border: 1px solid #E6E6E6;
        	padding: 4px;
        }
        
        .ColVis, .dataTable_buttons{
        	z-index: 100;
        }
        .callapp_head{
        	font-family: pvn;
        	font-weight: bold;
        	font-size: 20px;
        	color: #2681DC;
        }
        .callapp_refresh{
            padding: 5px;
            border-radius:3px;
            color:#FFF;
            background: #9AAF24;
            float: right;
            font-size: 13px;
            cursor: pointer;
        }
        #example-cat_length{
        	position: inherit;
            width: 0px;
        	float: left;
        }
        #example-cat_length label select{
        	width: 60px;
            font-size: 10px;
            padding: 0;
            height: 18px;
        }
    </style>
</head>

<body>
<div id="tabs" style="width: 95%;">
<div class="callapp_head">დარიცხვების ჟურნალი<span class="callapp_refresh"><img alt="refresh" src="media/images/icons/refresh.png" height="14" width="14">   განახლება</span><hr class="callapp_head_hr"></div>
	<div id="button_area">
        <select id="filt_month" style="width:  130px;">
    		<?php 
    		
        		mysql_connect('212.72.155.176','root','Gl-1114');
        		mysql_select_db('tgmobile');
        		mysql_set_charset ( 'utf8');
    		
    		    $c_date	= date('m');
                $req = mysql_query("SELECT id,
                                          `name`
                                    FROM   month");
    
                while( $res = mysql_fetch_assoc($req)){
                    if($res['id'] == $c_date){
                        $data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
                    } else {
                        $data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
                    }
                }
                
                echo $data;
    		 ?>
    	</select>
    	<select id="filt_day" style="width:  100px;">
    	    <option value="0">ყველა</option>
    		<option value="1">01</option>
    		<option value="2">02</option>
    		<option value="3">03</option>
    		<option value="4">04</option>
    		<option value="5">05</option>
    		<option value="6">06</option>
    		<option value="7">07</option>
    		<option value="8">08</option>
    		<option value="9">09</option>
    		<option value="10">10</option>
    		<option value="11">11</option>
    		<option value="12">12</option>
    		<option value="13">13</option>
    		<option value="14">14</option>
    		<option value="15">15</option>
    		<option value="16">16</option>
    		<option value="17">17</option>
    		<option value="18">18</option>
    		<option value="19">19</option>
    		<option value="20">20</option>
    		<option value="21">21</option>
    		<option value="22">22</option>
    		<option value="23">23</option>
    		<option value="24">24</option>
    		<option value="25">25</option>
    		<option value="26">26</option>
    		<option value="27">27</option>
    		<option value="28">28</option>
    		<option value="29">29</option>
    		<option value="30">30</option>
    		<option value="31">31</option>
    	</select>
    </div>
    <table id="table_right_menu">
        <tr>
            <td style="cursor: pointer;padding: 4px;border-right: 1px solid #E6E6E6;background:#2681DC;">
            	<img alt="table" src="media/images/icons/table_w.png" height="14" width="14">
            </td>
            <td style="cursor: pointer;padding: 4px;border-right: 1px solid #E6E6E6;">
            	<img alt="log" src="media/images/icons/log.png" height="14" width="14">
            </td>
            <td style="cursor: pointer;padding: 4px;" id="show_copy_prit_exel" myvar="0">
            	<img alt="link" src="media/images/icons/select.png" height="14" width="14">
            </td>
        </tr>
    </table>
    <table class="display" id="example" >
        <thead>
            <tr id="datatable_header">
                <th>ID</th>
                <th style="width: 8%;">თარიღი</th>
                <th style="width: 28%;">კლიენტი</th>
                <th style="width: 8%;">ორისის კოდი</th>
                <th style="width: 8%;">ს/ხ</th>
                <th style="width: 8%;">ვალუტა</th>
                <th style="width: 8%;">დარიცხვა%<br>დოლარი</th>
                <th style="width: 8%;">დარიცხვა%<br>ლარი</th>
            	<th style="width: 8%;">ზედმეტობა</th>
            	<th style="width: 8%;">სტატუსი</th>
            	<th style="width: 8%;">ქმედება</th>
            </tr>
        </thead>
        <thead>
            <tr class="search_header">
                <th class="colum_hidden">
                    <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                </th>                
                <th>
                    <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                </th>
                <th>
                    <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                </th>
                <th>
                    <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                </th>
                <th>
                    <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                </th>
                <th>
                    <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                </th>
                <th>
                    <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                </th>
                <th>
                    <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                </th>
                <th>
                    <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                </th>
                <th>
                    <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                </th>
                <th>
                    <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                </th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th style="text-align: left; font-weight: bold;"><p align="right">ჯამი<br>სულ ჯამი</p></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
        </tfoot>
    </table>
    
    <!-- jQuery Dialog -->
    <div id="add-edit-form" class="form-dialog" title="ძირითადი ველები"></div>
    <div id="add-edit-show_letter" class="form-dialog" title="ბარათი"></div>
</body>
</html>


