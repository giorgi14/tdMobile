<html>
<head>
	<script type="text/javascript">
		var aJaxURL	            = "server-side/operations/transaction.action.php";
		var aJaxURL_det	        = "server-side/operations/transaction_detail.action.php";
		var aJaxURL_show_letter = "server-side/main.action.php";		//server side folder url
		var tName	            = "example";													//table name
		var fName	            = "add-edit-form";	
		var tbName		        = "tabs1";											//form name
		var change_colum_main   = "<'dataTable_buttons'T><'F'Cfipl>";
		    	
		$(document).ready(function () {  
			GetTabs(tbName);       	
			LoadTable(tName,9,change_colum_main,aJaxURL);	
			$(".ui-widget-content").css('border', '0px solid #aaaaaa');
 						
			/* Add Button ID, Delete Button ID */
			GetButtons("add_button");
			SetEvents("add_button", "", "", tName, fName, aJaxURL,'',tName,10,change_colum_main,aJaxURL,'','','');
			
		});

		$(document).on("tabsactivate", "#tabs1", function() {
        	tab = GetSelectedTab(tbName);
        	if (tab == 0) {
        		LoadTable(tName,9,change_colum_main,aJaxURL);	
         	}else if(tab == 1){
             	
             	GetDataTable1("example1", aJaxURL, 'get_list', 14, "tab=1", 0, "", "", "desc", "", change_colum_main);
             	SetEvents("", "", "", "example1", fName, aJaxURL,'',tName,10,change_colum_main,aJaxURL,'','','');
             	setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
            }else if(tab == 2){
				
				GetDataTable1("example2", aJaxURL, 'get_list_pledge', 11, "tab=2", 0, "", "", "desc", "", change_colum_main);
				SetEvents("", "", "", "example2", fName, aJaxURL,'',tName,10,change_colum_main,aJaxURL,'','','');
				
				setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
			}else if(tab == 3){
          		
          		GetDataTable1("example3", aJaxURL, 'get_list_other', 11, "tab=3", 0, "", "", "desc", "", change_colum_main);
          		SetEvents("", "", "", "example3", fName, aJaxURL,'',tName,10,change_colum_main,aJaxURL,'','','');
          		//$("#add_other").button();
          		setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
          	}else{
          		GetDataTable1("example4", aJaxURL, 'get_list', 11, "tab=4", 0, "", "", "desc", "", change_colum_main);
          		SetEvents("", "", "", "example4", fName, aJaxURL,'',tName,10,change_colum_main,aJaxURL,'','','');

          		setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
          	}
        });
        
		function LoadTable(tName,num,change_colum_main,aJaxURL){
			
			GetDataTable(tName, aJaxURL, 'get_list', num, "tab=0", 0, "", 0, "desc", "", change_colum_main);
			setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
			$("#table_right_menu").css('top','37px');
		}
		
		function LoadDialog(fname){
			var id		= $("#id").val();
			if(fname=='add-edit-form'){
    			var buttons = {
					"restore-transaction": {
    		            text: "აღდგენა",
    		            id: "restore-transaction"
    		        },
					"move-transaction": {
    		            text: "გაუქმება",
    		            id: "move-transaction"
    		        },
    				"show_letter": {
    		            text: "ბარათის ნახვა",
    		            id: "show_letter",
    		            click: function () {
    		            	param 	  = new Object();
    		        		param.act = "get_edit_page";
    		        		param.id  = $("#hidde_cl_id1").val();
    		        		
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
    		        			            var dLength = [[-1], ["ყველა"]];
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
    		        		    							setTimeout(function(){
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
    
        		        	        						    if(delta<=0.05 && delta>=-0.05){delta='0.00';}
        		        	        						    if(delta1<=0.05 && delta1>=-0.05 ){delta1='0.00';}
        		        	        						    
        		        	        						    other_delta = (parseFloat(other) - parseFloat(other1)).toFixed(2);
        		        	        						    if(ins_delta == '0.00' || ins_delta1=='0.00'){
        		        	        						    	ins_delta  = '0.00';
        		        	        						    	ins_delta1 = '0.00';
            		        	        						}
        		        	        						    $("#insurance_delta").html(ins_delta);
        		        	        						    $("#insurance_delta1").html(ins_delta1);
        		        	        						    $("#other_delta").html(other_delta);
        		        	        						    
        		        	        							$("#remaining_root").html(delta);
        		        	        							$("#remaining_root_gel").html(delta1);
    		        		    							}, 90);
    		        		    						}
    		        		    					}
    		        		    			    }
    		        		    		    });
    		        			        }
    		        				}
    		        	    	}
    		        	    });
    		            }
    		        },
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
			
    			GetDialog(fName, 1180, "auto", buttons,"top");
    			
    			
    	        $('#received_currency_id').chosen();
    	        $('#add-edit-form, .add-edit-form-class').css('overflow','visible');
    	        GetDataTable("table_transaction_detail", aJaxURL_det, 'get_list', 7, "&transaction_id="+$("#hidde_transaction_id").val(), 0, "", 0, "desc", "", "<'F'Cpl>");
				setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 50);
				$("#table_transaction_detail_length").css('top', '2px');
				GetButtons("add_button_dettail","");
         		SetEvents("add_button_dettail", "", "", 'table_transaction_detail', 'add-edit-form-det', aJaxURL_det);
    	        GetDateTimes('transaction_date');
    	        $("#delete_detail").button();
    	        $("#pledge_distribution").button();
    	        $("#add_other").button();

    	        if($('#transaction_date').val() != ''){
    		        $('#add_button_dettail').button("enable");
    		        $('#delete_detail').button("enable");
    		        $('#pledge_distribution').button("enable");
    		        $('#add_other').button("enable");
    		    }else{
    		    	$('#add_button_dettail').button("disable");
    		    	$('#delete_detail').button("disable");
    		        $('#pledge_distribution').button("disable");
    		        $('#add_other').button("disable");
    			}

    			if($("#hidde_actived").val() == 0){
        			$("#restore-transaction").show();
        			$("#move-transaction").hide();
        		}else{
        			$("#restore-transaction").hide();
        			$("#move-transaction").show();
            	}

            	if($("#hidde_statusss").val()==1){
            		$('#save-dialog1').button("disable");
            	}else{
            		$('#save-dialog1').button("enable");
                }
    	        
			}else if(fname=='add-edit-form-det'){
				var buttons = {
						"save": {
				            text: "შენახვა",
				            id: "save-dialog"
				        },
			        	"cancel": {
				            text: "დახურვა",
				            id: "cancel-dialog",
				            click: function () {
				            	$(this).dialog("close");
				            	GetDataTable("example1", aJaxURL, 'get_list', 13, "tab=1", 0, "", 0, "desc", "", change_colum_main);
				            }
				        }
				    };
				GetDialog("add-edit-form-det", 700, "auto", buttons,"top");
				$('#type_id').chosen();
    	        $('#client_id').chosen();
    	        $('#client_loan_number').chosen();
    	        $('#attachment_client_id').chosen();
    	        $('#currency_id').chosen();
    	        $('#attachment_client_id').attr('disabled', true).trigger("chosen:updated");

    	        if($("#tr_id").val()!=''){
        	        param 	    = new Object();
        		    
    				param.act   = "check_transaction";
        		    param.tr_id = $("#tr_id").val();
        		    
        			$.ajax({
        		        url: aJaxURL,
        			    data: param,
        		        success: function(data) {			        
        					if(typeof(data.error) != 'undefined'){
        						if(data.error != ''){
        							alert(data.error);
        						}else{
            						if(data.check == 1){
            							$("#month_payed_gel").val('0');
            	                        $("#month_payed_usd").val('0');
            	                        $("#month_fee_trasaction").val('0');
             			    	        $("#pledge_or_other_mont_fee").val('0');
             			    	        $("#pledge_or_other_extra_fee").val('0');
                					}else{
                						
                                        var pay_amount = $('#client_amount').val();
                						if($('#client_amount').val()=='' || $('#client_amount').val() == null){pay_amount == 0;}
                						$("#month_fee_trasaction").val(pay_amount);
             			    	        $("#pledge_or_other_mont_fee").val(pay_amount);
             			    	        $("#pledge_or_other_extra_fee").val(pay_amount);

              			    	        var amount = $("#client_amount").val();
                  						if($("#client_amount").val() == '' || $("#client_amount").val() == null){amount = 0;}else{amount = $("#client_amount").val();}
                						if($("#received_currency_id").val()==1){
                    						lari   = amount;
                	                        dolari = (parseFloat(amount)/parseFloat($("#course").val())).toFixed(2);
                	                        $("#month_payed_gel").val(lari);
                	                        $("#month_payed_usd").val(dolari);
                	                    }else{
                	                        lari   = (parseFloat(amount)*parseFloat($("#course").val())).toFixed(2);
                	                        dolari = parseFloat(amount);
                	                        $("#month_payed_gel").val(lari);
                	                        $("#month_payed_usd").val(dolari);
                	                    }
                   					}
        						}
        					}
        			    }
        			});
                }else{
                    
                	var pay_amount = $('#client_amount').val();
					if($('#client_amount').val()=='' || $('#client_amount').val() == null){pay_amount == 0;}
					$("#month_fee_trasaction").val(pay_amount);
	    	        $("#pledge_or_other_mont_fee").val(pay_amount);
	    	        $("#pledge_or_other_extra_fee").val(pay_amount);

	    	        var amount = $("#client_amount").val();
					if($("#client_amount").val() == '' || $("#client_amount").val() == null){amount = 0;}else{amount = $("#client_amount").val();}

	    	        if($("#received_currency_id").val()==1){
						lari   = amount;
                        dolari = (parseFloat(amount)/parseFloat($("#course").val())).toFixed(2);
                        $("#month_payed_gel").val(lari);
                        $("#month_payed_usd").val(dolari);
                    }else{
                        lari   = (parseFloat(amount)*parseFloat($("#course").val())).toFixed(2);
                        dolari = parseFloat(amount);
                        $("#month_payed_gel").val(lari);
                        $("#month_payed_usd").val(dolari);
                    }
                    
                }
    	        
				$('#currency_id').prop('disabled', false).trigger("chosen:updated");
    	        $('#type_id').prop('disabled', false).trigger("chosen:updated");
        	    
    	        $('#client_id').prop('disabled', true).trigger("chosen:updated");
    	        $('#client_loan_number').prop('disabled', true).trigger("chosen:updated");
    	        $('#surplus_type').chosen();
    	        $('#surplus_type_chosen').css('width','175px');
    	    }
		}

		$(document).on("click", "#pledge_distribution",  function (event) {
			param 	  = new Object();
		    param.act = "get_pledge_dialog";
		    $.ajax({
		        url: aJaxURL_det,
			    data: param,
		        success: function(data) {			        
					if(typeof(data.error) != 'undefined'){
						if(data.error != ''){
							alert(data.error);
						}else{
							$("#pledge_dialog").html(data.page);

							var buttons = {
								"save_pledge_distribution": {
						            text: "შენახვა",
						            id: "save_pledge_distribution"
						        },
					        	"cancel": {
						            text: "დახურვა",
						            id: "cancel-dialog",
						            click: function () {
						            	$(this).dialog("close");
						            }
						        }
						    };
							GetDialog("pledge_dialog", 700, "auto", buttons,"top");

							$("#pledge_client_id").chosen();
							$("#pledge_client_loan_number").chosen();
							$("#client_pledge_amount").val($("#client_amount").val());
							$('#pledge_dialog, .pledge_dialog-class').css('overflow','visible');
						}
					}
			    }
		    });
	    });

		$(document).on("click", "#add_other",  function (event) {
			param 	  = new Object();
		    param.act = "get_other_dialog";
		    $.ajax({
		        url: aJaxURL_det,
			    data: param,
		        success: function(data) {			        
					if(typeof(data.error) != 'undefined'){
						if(data.error != ''){
							alert(data.error);
						}else{
							$("#other_dialog").html(data.page);

							var buttons = {
								"save_other_dialog": {
						            text: "შენახვა",
						            id: "save_other_dialog"
						        },
					        	"cancel": {
						            text: "დახურვა",
						            id: "cancel-dialog",
						            click: function () {
						            	$(this).dialog("close");
						            }
						        }
						    };
							GetDialog("other_dialog", 700, "auto", buttons,"top");

							$("#other_client_id").chosen();
							$("#other_client_loan_number").chosen();
							$('#other_dialog, .other_dialog-class').css('overflow','visible');
							$("#other_pledge_amount").val($("#client_amount").val());
						}
					}
			    }
		    });
	    });

		$(document).on("change", "#other_date",  function (event) {
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
							$("#other_cource").val(data.cource);
						}
					}
			    }
			});
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
		        $('#delete_detail').button("enable");
		        $('#pledge_distribution').button("enable");
		        $('#add_other').button("enable");
		    }else{
		    	$('#add_button_dettail').button("disable");
		    	$('#delete_detail').button("disable");
		        $('#pledge_distribution').button("disable");
		        $('#add_other').button("disable");
			}
	    });

	    
		
		// Add - Save
	    $(document).on("click", "#save-dialog", function () {
		    param 		               = new Object();
		    
			param.act	               = "save_transaction";
			
		    param.id	               = $("#idd").val();
		    param.tr_id	               = $("#tr_id").val();
		    param.client_id            = $("#client_id").val();
		    param.client_loan_number   = $("#client_loan_number").val();
		    param.type_id	           = $("#type_id").val();
		    param.car_out              = $("input[id='car_out']:checked").val();
		    param.other_penalty        = $("input[id='other_penalty']:checked").val();
		    param.exception_agr        = $("input[id='exception_agr']:checked").val();
		    
		    param.month_fee		       = $("#month_fee").val();
		    param.month_fee1		   = $("#month_fee1").val();

		    param.payable_Fee		   = $("#payable_Fee").val();
		    param.yield		           = $("#yield").val();

		    param.other_payed		   = $("#other_payed").val();
		    
	    	param.root		           = $("#root").val();
	    	
	    	param.percent		       = $("#percent").val();
	    	param.penalti_fee	       = $("#penalti_fee").val();
	    	param.percent1		       = $("#percent1").val();
	    	param.penalti_fee1	       = $("#penalti_fee1").val();
	    	
	    	param.surplus	           = $("#surplus").val();
	    	param.surplus1             = $("#surplus1").val();
	    	param.client_id	           = $("#client_id").val();

	    	param.month_fee_trasaction = $("#month_fee_trasaction").val();
	    	param.extra_fee            = $("#extra_fee").val();
	    	
	    	param.currency_id	       = $("#currency_id").val();
	    	param.received_currency_id = $('#received_currency_id').val();
	    	param.course	           = $("#course").val();
	    	param.course_pledge	       = $("#course").val();
	    	param.transaction_date	   = $("#transaction_date").val();


	    	param.pledge_or_other_payed	    = $("#pledge_or_other_payed").val();
	    	param.pledge_or_other_surplus   = $('#pledge_or_other_surplus').val();
	    	param.pledge_or_other_surplus1  = $("#pledge_or_other_surplus1").val();
	    	param.pledge_or_other_extra_fee	= $("#pledge_or_other_extra_fee").val();

	    	param.pledge_or_other_balance_gel = $("#pledge_or_other_balance_gel").val();
	    	param.pledge_or_other_balance_usd = $("#pledge_or_other_balance_usd").val();

	    	if(param.pledge_or_other_balance_gel == '' || param.pledge_or_other_balance_gel == null){param.pledge_or_other_balance_gel = 0;}
	    	if(param.pledge_or_other_balance_usd == '' || param.pledge_or_other_balance_usd == null){param.pledge_or_other_balance_usd = 0;}

	    	param.month_fee_gel = $("#month_fee_gel").val();
	    	param.month_fee_usd = $("#month_fee_usd").val();
	    	param.restr_cource  = $("input[id='restr_cource']:checked").val();
	    	if(param.type_id == 2 && param.restr_cource == 1){
		    	course = (parseFloat(param.month_fee_gel)/parseFloat(param.month_fee_usd)).toFixed(4);
		    	if(course > 0){
		    		param.course_pledge = course;
		    	}
			}
	    	param.month_payed_gel = parseFloat($("#month_payed_gel").val())+parseFloat(param.pledge_or_other_balance_gel);
	    	param.month_payed_usd = parseFloat($("#month_payed_usd").val())+parseFloat(param.pledge_or_other_balance_usd);

	    	param.surplus_type         = $("#surplus_type").val();
	    	param.attachment_client_id = $("#attachment_client_id").val();
	    	
	    	param.hidde_id		       = $("#hidde_id").val();
	    	param.hidde_transaction_id = $("#hidde_transaction_id").val();
	    	param.hidde_status         = $("#hidde_status").val();

	    	

	    	if(param.type_id == 0){
		    	alert('შეავსე ტიპი');
			}else if(param.client_id == 0){
		    	alert('შეავსე კლიენტი');
			}else if(param.surplus_type == 0 && param.id == '' && (param.pledge_or_other_surplus != '' || param.surplus != '')){
		    	alert('შეავსე მეტობის ტიპი');
			}else{
				$.ajax({
    		        url: aJaxURL_det,
    			    data: param,
    		        success: function(data) {			        
    					if(typeof(data.error) != 'undefined'){
    						if(data.error != ''){
    							alert(data.error);
    						}else{
    							$("#tr_id").val(data.tr_id);
    							$("#hidde_cl_id1").val($("#client_id").val());
    							GetDataTable("table_transaction_detail", aJaxURL_det, 'get_list', 9, "&transaction_id="+$("#tr_id").val(), 0, "", 0, "desc", "", "<'F'Cpl>");
    							LoadTable(tName,9,change_colum_main,aJaxURL);
    							setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 50);
    							$("#table_transaction_detail_length").css('top', '2px');
    			        		CloseDialog('add-edit-form-det');
    			        	}
    					}
    			    }
    		    });
			}
		});

	    $(document).on("change", "#surplus_type", function () {
	    	surplus_type = $(this).val();
	    	
	    	if(surplus_type == 3 && $("#type_id").val()<=1){
	    		$(".surplus").css('display','');
		    	$("#surplus_label").html('მეტობა(სესხი)');
		    	$('#attachment_client_id').attr('disabled', false).trigger("chosen:updated");
		    	if(surplus_type==0){
		    		$('#attachment_client_id').attr('disabled', true).trigger("chosen:updated");
		    	}
		    }else if(surplus_type == 3 && $("#type_id").val()>1){
		    	$(".pledge_or_other_surplus1").css('display','');
		    	$("#pledge_or_other_surplus1_label").html('მეტობა(სესხი)');
		    	$('#attachment_client_id').attr('disabled', false).trigger("chosen:updated");
		    	if(surplus_type==0){
		    		$('#attachment_client_id').attr('disabled', true).trigger("chosen:updated");
		    	}
	    	}else{
	    		$(".pledge_or_other_surplus1").css('display','none');
	    		$(".surplus").css('display','none');
	    		$("#surplus_label").html('მეტობა');
	    		$("#pledge_or_other_surplus1_label").html('მეტობა');
	    		
	    		$('#attachment_client_id').attr('disabled', false).trigger("chosen:updated");
	    		if(surplus_type==0){
		    		$('#attachment_client_id').attr('disabled', true).trigger("chosen:updated");
		    	}
		    }
		    
		});
    	
		$(document).on("click", "#move-transaction", function () {
		    param = new Object();
		    
			param.act	= "delete_transaction";
		    param.tr_id	= $("#tr_id").val();
		    
		    $.ajax({
		        url: aJaxURL,
			    data: param,
		        success: function(data) {			        
					if(typeof(data.error) != 'undefined'){
						if(data.error != ''){
							alert(data.error);
						}else{
							LoadTable(tName,9,change_colum_main,aJaxURL);
							CloseDialog('add-edit-form');
			        	}
					}
			    }
		    });
    	});

		$(document).on("click", "#delete_detail", function () {
		    param = new Object();
		    
			param.act	= "delete_detail";
		    param.tr_id	= $("#tr_id").val();
		    
		    $.ajax({
		        url: aJaxURL,
			    data: param,
		        success: function(data) {			        
					if(typeof(data.error) != 'undefined'){
						
						if(data.error != ''){
							alert(data.error);
						}else{
							GetDataTable("table_transaction_detail", aJaxURL_det, 'get_list', 9, "&transaction_id="+$("#tr_id").val(), 0, "", 0, "desc", "", "<'F'Cpl>");
						}
					}
			    }
		    });
    	});

		$(document).on("click", "#restore-transaction", function () {
		    param = new Object();
		    
			param.act	= "restore_transaction";
		    param.tr_id	= $("#tr_id").val();
		    
		    $.ajax({
		        url: aJaxURL,
			    data: param,
		        success: function(data) {			        
					if(typeof(data.error) != 'undefined'){
						if(data.error != ''){
							alert(data.error);
						}else{
							LoadTable(tName,9,change_colum_main,aJaxURL);
							CloseDialog('add-edit-form');
			        	}
					}
			    }
		    });
    	});
		
	    $(document).on("click", "#save-dialog1", function () {
		    param = new Object();
		    
			param.act	= "save_transaction";
		    param.tr_id	= $("#tr_id").val();
		    
		    param.client_amount		   = $("#client_amount").val();
	    	param.received_currency_id = $("#received_currency_id").val();
	    	param.transaction_date     = $("#transaction_date").val();
	    	
	    	if(param.tr_id == ''){
		    	alert('ჩარიცხული თანხა არაა გადანაწილებული!');
			}else{
    	    	$.ajax({
    		        url: aJaxURL,
    			    data: param,
    		        success: function(data) {			        
    					if(typeof(data.error) != 'undefined'){
    						if(data.error != ''){
    							alert(data.error);
    						}else{
    							LoadTable(tName,9,change_colum_main,aJaxURL);
    							CloseDialog('add-edit-form');
    			        	}
    					}
    			    }
    		    });
    	    }
		});

	    $(document).on("change", "#exception_agr", function () {
	    	exception_agr = $("input[id='exception_agr']:checked").val();
	    	
	    	if(exception_agr == 1){
	    		document.getElementById("car_out").disabled = true;
	    		document.getElementById("other_penalty").disabled = true;
	    		param = new Object();
		    	
	    		param.act              = "get_canceled-loan";
	    		param.client_id        = $("#client_id").val();
	    		param.transaction_date = $("#transaction_date").val();
	    		param.exception_agr    = 1;
	    		
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
	        					$("#month_fee2").val(data.pay_amount1);
	        				}
	    				}
	    	    	}
	    	    });
		    }else{
		    	document.getElementById("car_out").disabled = false;
		    	document.getElementById("other_penalty").disabled = false;
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
	    							$("#extra_fee").val(parseFloat(data.loan_pay_amount)+parseFloat(data.pay_amount1));
	    							$("#daricxvis_tarigi").html(data.schedule_date);
	    							$("#hidde_id").val(data.id);
	    							$("#currency_id").html(data.currenc).trigger("chosen:updated");
	    							$('#currency_id').prop('disabled', true).trigger("chosen:updated");
	    							$('#client_loan_number').html(data.agrement_data).trigger("chosen:updated");
	    							$('#attachment_client_id').html(data.client_attachment_data).trigger("chosen:updated");
	    							
		    					}else if(data.status==2){
		    						$("#month_fee1").val(data.insurance_fee);
		    						$("#month_fee").val(data.loan_pay_amount);
		    						$("#extra_fee").val(parseFloat(data.loan_pay_amount)+parseFloat(data.pay_amount1));
		    						
		    						$("#root1").val('');
	    							$("#percent1").val('');
	    							$("#penalti_fee1").val('');

	    							$("#month_fee2").val('');
	    							$("#root2").val('');
	    							$("#percent2").val('');
	    							$("#penalti_fee2").val('');

	    							$('#currency_id').prop('disabled', false).trigger("chosen:updated");
	    							$('#client_loan_number').html(data.agrement_data).trigger("chosen:updated");
	    							$('#attachment_client_id').html(data.client_attachment_data).trigger("chosen:updated");
	    						}else if(data.status==3){
			    					$("#month_fee1").val(data.pledge_fee);
			    					$("#month_fee").val(data.loan_pay_amount);
			    					$("#extra_fee").val(parseFloat(data.loan_pay_amount)+parseFloat(data.pay_amount1));
			    					$("#month_fee_gel").val(data.other_pay);
			    					
			    					$("#root1").val('');
	    							$("#percent1").val('');
	    							$("#penalti_fee1").val('');

	    							$("#month_fee2").val('');
	    							$("#root2").val('');
	    							$("#percent2").val('');
	    							$("#penalti_fee2").val('');

	    							$('#currency_id').prop('disabled', false).trigger("chosen:updated");
	    							$('#attachment_client_id').html(data.client_attachment_data).trigger("chosen:updated");
	    						}
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
	    		document.getElementById("exception_agr").disabled = true;
	    		param = new Object();
		    	
	    		param.act              = "get_canceled-loan";
	    		param.client_id        = $("#client_id").val();
	    		param.transaction_date = $("#transaction_date").val();
	    		param.other_penalty    = $("input[id='other_penalty']:checked").val();
	    		
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
	        					$("#month_fee2").val(data.pay_amount1);
	        					$("#other_payed1").val(data.other_amount);
	        					//$("#add-edit-form-canceled").html(data.page);
	    					}
	    				}
	    	    	}
	    	    });
		    }else{
		    	$(".car_out_class").css('display', 'none');
		    	document.getElementById("exception_agr").disabled = false;
		    	param         =  new Object();
			    param.act     = "get_shedule";
			    param.status  = 1;
			    param.id                   = $("#client_id").val();
			    param.type_id              = $('#type_id').val();
			    param.transaction_date     = $("#transaction_date").val();
			    param.month_fee_trasaction = $("#month_fee_trasaction").val();
			    param.received_currency_id = $("#received_currency_id").val();
			    param.course               = $("#course").val();
			    param.other_penalty        = $("input[id='other_penalty']:checked").val();
			    
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
	    							$("#extra_fee").val(parseFloat(data.loan_pay_amount)+parseFloat(data.pay_amount1));
	    							$("#daricxvis_tarigi").html(data.schedule_date);
	    							$("#hidde_id").val(data.id);
	    							$("#currency_id").html(data.currenc).trigger("chosen:updated");
	    							$('#currency_id').prop('disabled', true).trigger("chosen:updated");
	    							$('#client_loan_number').html(data.agrement_data).trigger("chosen:updated");
	    							$('#attachment_client_id').html(data.client_attachment_data).trigger("chosen:updated");
	    							$("#other_payed1").val(data.other_amount);
		    					}else if(data.status==2){
		    						$("#month_fee1").val(data.insurance_fee);
		    						$("#month_fee").val(data.loan_pay_amount);
		    						$("#extra_fee").val(parseFloat(data.loan_pay_amount)+parseFloat(data.pay_amount1));
		    						
		    						$("#root1").val('');
	    							$("#percent1").val('');
	    							$("#penalti_fee1").val('');

	    							$("#month_fee2").val('');
	    							$("#root2").val('');
	    							$("#percent2").val('');
	    							$("#penalti_fee2").val('');

	    							$('#currency_id').prop('disabled', false).trigger("chosen:updated");
	    							$('#client_loan_number').html(data.agrement_data).trigger("chosen:updated");
	    							$('#attachment_client_id').html(data.client_attachment_data).trigger("chosen:updated");
	    						}else if(data.status==3){
			    					$("#month_fee1").val(data.pledge_fee);
			    					$("#month_fee").val(data.loan_pay_amount);
			    					$("#extra_fee").val(parseFloat(data.loan_pay_amount)+parseFloat(data.pay_amount1));
			    					$("#month_fee_gel").val(data.other_pay);
			    					
			    					$("#root1").val('');
	    							$("#percent1").val('');
	    							$("#penalti_fee1").val('');

	    							$("#month_fee2").val('');
	    							$("#root2").val('');
	    							$("#percent2").val('');
	    							$("#penalti_fee2").val('');

	    							$('#currency_id').prop('disabled', false).trigger("chosen:updated");
	    							$('#attachment_client_id').html(data.client_attachment_data).trigger("chosen:updated");
	    						}
							}
						}
				    }
			    });
		    }

	    	
		});
		
	    $(document).on("change", "#type_id", function () {
	    	
	        if($(this).val() > 1 ){
	        	$("#restr_cource").attr('disabled', false);
		        $("#loan_table").css('display','none');
	            $("#loan_table1").css('display','none');
	            $("#pledge_table").css('display','');
	            $("#month_fee1").val('');
				$("#root1").val('');
				$("#percent1").val('');
				$("#penalti_fee1").val('');
				document.getElementById("car_out").disabled = true;
				
	            param         =  new Object();
    		    param.act     = "get_shedule";
    		    
    		    param.id                   = $("#client_id").val();
    		    param.agr_id               = $("#client_loan_number").val();
    		    param.transaction_date     = $("#transaction_date").val();
    		    param.month_fee_trasaction = $("#month_fee_trasaction").val();
    		    param.received_currency_id = $("#received_currency_id").val();
    		    param.course               = $("#course").val();
    		    param.type_id              = $(this).val();
    			$.ajax({
    		        url: aJaxURL,
    			    data: param,
    		        success: function(data) {			        
    					if(typeof(data.error) != 'undefined'){
    						if(data.error != ''){
    							alert(data.error);
    						}else{
    							$("#month_fee_gel").val(data.fee_lari);
    							$("#month_fee_usd").val(data.fee_dolari);
    							$("#pledge_or_other_balance_usd").val(data.pay_amount1);
    							$("#pledge_or_other_balance_gel").val(data.pay_amount2);
    							
    							var pay_amount = $('#client_amount').val();
        						if($('#client_amount').val()=='' || $('#client_amount').val() == null){pay_amount == 0;}
        						
								if(data.fee_lari == ''  || data.fee_lari==null){month_fee_gel = 0;}else{month_fee_gel = data.fee_lari;}
    							
    							if(data.pay_amount1 == '' || data.pay_amount1==null){pledge_or_other_balance_gel = 0;}else{pledge_or_other_balance_gel = data.pay_amount1;}
    							if(data.pay_amount2 == '' || data.pay_amount2==null){pledge_or_other_balance_usd = 0;}else{pledge_or_other_balance_usd = data.pay_amount2;}
    							
    							pledge_or_other_extra_fee = $("#pledge_or_other_extra_fee").val();
    							
    							if(pledge_or_other_extra_fee == ''){pledge_or_other_extra_fee = 0;}
    							
                                if($("#received_currency_id").val() == 1){
                                    $("#pledge_or_other_extra_fee").val(parseFloat(pledge_or_other_extra_fee)+parseFloat(pledge_or_other_balance_gel));   
                                }else{
                                	$("#pledge_or_other_extra_fee").val(parseFloat(pledge_or_other_extra_fee)+parseFloat(pledge_or_other_balance_usd));
                                } 
                                 														
    							$("#root1").val('');
    							$("#percent1").val('');
    							$("#penalti_fee1").val('');

    							$("#month_fee2").val('');
    							$("#root2").val('');
    							$("#percent2").val('');
    							$("#penalti_fee2").val('');

    							$('#currency_id').html(data.currency_data).trigger("chosen:updated");
    							$('#currency_id').prop('disabled', true).trigger("chosen:updated");
    						}
    					}
    			    }
    		    });

    			surplus_type = $("#surplus_type").val();

		    	if(surplus_type == 3 && $("#type_id").val()>1){
			    	$(".pledge_or_other_surplus1").css('display','');
			    	$("#pledge_or_other_surplus1_label").html('მეტობა(სესხი)');
		    	}else{
		    		$(".pledge_or_other_surplus1").css('display','none');
		    		$(".surplus").css('display','none');
		    		$("#surplus_label").html('მეტობა');
		    		$("#pledge_or_other_surplus1_label").html('მეტობა');
			    }
			    
	        }else{
	        	if($(this).val() == 1 && $("#client_id").val()>0){
		        	
	        		
	        		document.getElementById("car_out").disabled = false;
	        		param         =  new Object();
	    		    param.act     = "get_shedule";
	    		    
	    		    param.id                   = $("#client_id").val();
	    		    param.agr_id               = $("#client_loan_number").val();
	    		    param.transaction_date     = $("#transaction_date").val();
	    		    param.month_fee_trasaction = $("#month_fee_trasaction").val();
	    		    param.check_loan_penalty   = $("input[id='other_penalty']:checked").val();
	    		    param.exception_agr        = $("input[id='exception_agr']:checked").val();
	    		    param.received_currency_id = $("#received_currency_id").val();
	    		    param.course               = $("#course").val();
	    		    param.type_id              = $(this).val();
	    			$.ajax({
	    		        url: aJaxURL,
	    			    data: param,
	    		        success: function(data) {			        
	    					if(typeof(data.error) != 'undefined'){
	    						if(data.error != ''){
	    							alert(data.error);
	    						}else{
	    							$("#month_fee1").val(data.pay_amount);
	    							$("#root1").val(data.root);
	    							$("#percent1").val(data.percent);
	    							$("#penalti_fee1").val(data.penalty);
	    							$("#month_fee").val(data.loan_pay_amount);
	    							$("#month_fee2").val(data.pay_amount1);
	    							$("#root2").val(data.root1);
	    							$("#percent2").val(data.percent1);
	    							$("#penalti_fee2").val(data.penalty1);
	    							$("#daricxvis_tarigi").html(data.schedule_date);
	    							$("#info_mesage").html(data.info_message);
	    							$("#extra_fee").val(parseFloat(data.loan_pay_amount)+parseFloat(data.pay_amount1));
	    							$("#hidde_id").val(data.id);
	    							$("#other_payed1").val(data.other_amount);
	    							$('#currency_id').prop('disabled', true).trigger("chosen:updated");
								}
	    					}
	    			    }
	    		    });
	        	}
	        	
	       		$("#loan_table").css('display','block');
	       		$("#loan_table1").css('display','block');
	       		$("#pledge_table").css('display','none');

	       		var surplus_type = $("#surplus_type").val();

		    	if(surplus_type == 3 && $("#type_id").val()<=1){
		    		$(".surplus").css('display','');
			    	$("#surplus_label").html('მეტობა(სესხი)');
			    }else{
		    		$(".pledge_or_other_surplus1").css('display','none');
		    		$(".surplus").css('display','none');
		    		$("#surplus_label").html('მეტობა');
		    		$("#pledge_or_other_surplus1_label").html('მეტობა');
			    }
	        }

	        if($(this).val()>0){
		        if($(this).val() == 1){
	    			$("#restr_cource").attr('disabled', true);
			    }
	        	
		        $('#client_id').attr('disabled', false).trigger("chosen:updated");
    	        $('#client_loan_number').attr('disabled', false).trigger("chosen:updated");
		    }else{
		    	$('#client_id').attr('disabled', true).trigger("chosen:updated");
    	        $('#client_loan_number').attr('disabled', true).trigger("chosen:updated");
			} 
	    });

		$(document).on("change", "#client_id", function () {
			
			if($(this).val()>0 && $('#type_id').val() == 1){
            	document.getElementById("car_out").disabled = false;
            	document.getElementById("exception_agr").disabled = false;
            }else{
            	document.getElementById("car_out").disabled = true;
            	document.getElementById("exception_agr").disabled = true;
            }
            
			param         =  new Object();
		    param.act     = "get_shedule";
		    param.status  = 1;
		    param.id                   = $(this).val();
		    param.type_id              = $('#type_id').val();
		    param.transaction_date     = $("#transaction_date").val();
		    param.month_fee_trasaction = $("#month_fee_trasaction").val();
		    param.check_loan_penalty   = $("input[id='other_penalty']:checked").val();
		    param.exception_agr        = $("input[id='exception_agr']:checked").val();
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
    							$("#daricxvis_tarigi").html(data.schedule_date);
    							$("#info_mesage").html(data.info_message);
    							$("#other_payed1").val(data.other_amount);
    							extra_fee = data.loan_pay_amount;
    							if(data.loan_pay_amount==''){
    								extra_fee = 0;
        						}
    							$("#extra_fee").val(parseFloat(extra_fee)+parseFloat(data.pay_amount1));
    							
    							$("#hidde_id").val(data.id);
    							$("#currency_id").html(data.currenc).trigger("chosen:updated");
    							$('#currency_id').prop('disabled', true).trigger("chosen:updated");
    							$('#client_loan_number').html(data.agrement_data).trigger("chosen:updated");
    							$('#attachment_client_id').html(data.client_attachment_data).trigger("chosen:updated");
    							$('#attachment_client_id').html(data.client_attachment_data).trigger("chosen:updated");
	    					}else if(data.status==2){
	    						$("#month_fee_gel").val(data.fee_lari);
    							$("#month_fee_usd").val(data.fee_dolari);
    							$("#pledge_or_other_balance_gel").val(data.pay_amount1);
    							$("#pledge_or_other_balance_usd").val(data.pay_amount2);

    							var pay_amount = $('#client_amount').val();
        						if($('#client_amount').val()=='' || $('#client_amount').val() == null){pay_amount == 0;}
//     							$("#month_fee_trasaction").val(pay_amount);
//     			    	        $("#pledge_or_other_mont_fee").val(pay_amount);
//     			    	        $("#pledge_or_other_extra_fee").val(pay_amount);
     			    	        
    							if(data.fee_lari == ''){month_fee_gel = 0;}else{month_fee_gel = data.fee_lari;}
    							
    							if(data.pay_amount1 == '' || data.pay_amount1 == null){pledge_or_other_balance_gel = 0;}else{pledge_or_other_balance_gel = data.pay_amount1;}
    							if(data.pay_amount2 == '' || data.pay_amount2 == null){pledge_or_other_balance_usd = 0;}else{pledge_or_other_balance_usd = data.pay_amount2;}

    							pledge_or_other_extra_fee = $("#pledge_or_other_extra_fee").val();
    							
    							if(pledge_or_other_extra_fee == ''){pledge_or_other_extra_fee = 0;}
    							if($("#received_currency_id").val() == 1){
    								$("#pledge_or_other_extra_fee").val(parseFloat(pledge_or_other_extra_fee)+parseFloat(pledge_or_other_balance_gel));   
                                }else{
                                	$("#pledge_or_other_extra_fee").val(parseFloat(pledge_or_other_extra_fee)+parseFloat(pledge_or_other_balance_usd));
                                } 
    							
    							$('#client_loan_number').html(data.agrement_data).trigger("chosen:updated");
    						}else if(data.status==3){
    							$('#client_loan_number').html(data.agrement_data).trigger("chosen:updated");
    							$("#month_fee_gel").val(data.other_pay);
    							$('#attachment_client_id').html(data.client_attachment_data).trigger("chosen:updated");
    						}
						}
					}
			    }
		    });
    	});

		$(document).on("change", "#client_loan_number", function () {
			
            if($(this).val()>0 && $('#type_id').val() == 1){
            	document.getElementById("exception_agr").disabled = false;
            	document.getElementById("car_out").disabled = false;
            }else{
            	document.getElementById("exception_agr").disabled = true;
            	document.getElementById("car_out").disabled = true;
            }
			
			param         =  new Object();
		    param.act     = "get_shedule";
		    param.agr_id  =  $(this).val();
		    param.status  = 2;
		    param.type_id =  $('#type_id').val();
		    param.transaction_date  =  $("#transaction_date").val();
		    param.month_fee_trasaction = $("#month_fee_trasaction").val();
		    param.check_loan_penalty   = $("input[id='other_penalty']:checked").val();
		    param.exception_agr        = $("input[id='exception_agr']:checked").val();
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
    							$("#daricxvis_tarigi").html(data.schedule_date);
    							$("#info_mesage").html(data.info_message);
    							$("#other_payed1").val(data.other_amount);
    							
    							$("#month_fee").val(data.loan_pay_amount);
    							extra_fee = data.loan_pay_amount;
    							if(data.loan_pay_amount==''){
    								extra_fee = 0;
        						}
    							$("#extra_fee").val(parseFloat(extra_fee)+parseFloat(data.pay_amount1));
    							
    							$("#hidde_id").val(data.id);
    							$("#currency_id").html(data.currenc).trigger("chosen:updated");
    							$('#currency_id').prop('disabled', true).trigger("chosen:updated");
    							$('#client_id').html(data.client_data).trigger("chosen:updated");
    							$('#attachment_client_id').html(data.client_attachment_data).trigger("chosen:updated");
	    					}else if(data.status==2){
		    					$("#month_fee_gel").val(data.fee_lari);
    							$("#month_fee_usd").val(data.fee_dolari);
    							$("#pledge_or_other_balance_gel").val(data.pay_amount1);
    							$("#pledge_or_other_balance_usd").val(data.pay_amount2);

    							var pay_amount = $('#client_amount').val(); 
    							if($('#client_amount').val()=='' || $('#client_amount').val() == null){pay_amount == 0;}
    							
//     							$("#month_fee_trasaction").val(pay_amount);
//     			    	        $("#pledge_or_other_mont_fee").val(pay_amount);
//     			    	        $("#pledge_or_other_extra_fee").val(pay_amount);
    			    	        
    							if(data.fee_lari == ''){month_fee_gel = 0;}else{month_fee_gel = data.fee_lari;}
    							
    							if(data.pay_amount1 == '' || data.pay_amount1 == null){pledge_or_other_balance_gel = 0;}else{pledge_or_other_balance_gel = data.pay_amount1;}
    							if(data.pay_amount2 == '' || data.pay_amount2 == null){pledge_or_other_balance_usd = 0;}else{pledge_or_other_balance_usd = data.pay_amount2;}

    							pledge_or_other_extra_fee = $("#pledge_or_other_extra_fee").val();
    							
    							if(pledge_or_other_extra_fee == ''){pledge_or_other_extra_fee = 0;}
    							
    							if($("#received_currency_id").val() == 1){
        							$("#pledge_or_other_extra_fee").val(parseFloat(pledge_or_other_extra_fee)+parseFloat(pledge_or_other_balance_gel));   
                                }else{
                                	$("#pledge_or_other_extra_fee").val(parseFloat(pledge_or_other_extra_fee)+parseFloat(pledge_or_other_balance_usd));
                                }    
    														
    							$('#client_id').html(data.client_data).trigger("chosen:updated");
    							$('#attachment_client_id').html(data.client_attachment_data).trigger("chosen:updated");
    						}else if(data.status==3){
    							$('#client_id').html(data.client_data).trigger("chosen:updated");
    							$("#month_fee_gel").val(data.other_pay);
    							$('#attachment_client_id').html(data.client_attachment_data).trigger("chosen:updated");
    						}
						}
					}
			    }
		    });
    	});

		$(document).on("change", "#other_penalty", function () {
			if($("input[id='other_penalty']:checked").val() == 1){
    			document.getElementById("exception_agr").disabled = true;
            }else{
				document.getElementById("exception_agr").disabled = false;
	        }
			param         =  new Object();
		    param.act     = "get_shedule";
		    param.id      = $("#client_id").val();
		    param.agr_id  = $("#client_loan_number").val();
		    param.status  = 1;
		    param.type_id =  $('#type_id').val();
		    param.transaction_date  =  $("#transaction_date").val();
		    param.month_fee_trasaction = $("#month_fee_trasaction").val();
		    param.check_loan_penalty   = $("input[id='other_penalty']:checked").val();
		    param.received_currency_id = $("#received_currency_id").val();
		    param.course               = $("#course").val();

		    if(param.type_id>0 && param.agr_id>0 && param.id >0){
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
        							extra_fee = data.loan_pay_amount;
        							if(data.loan_pay_amount==''){
        								extra_fee = 0;
            						}
        							$("#extra_fee").val(parseFloat(extra_fee)+parseFloat(data.pay_amount1));
        							
        							$("#hidde_id").val(data.id);
        							$("#currency_id").html(data.currenc).trigger("chosen:updated");
        							$('#currency_id').prop('disabled', true).trigger("chosen:updated");
        							$('#client_id').html(data.client_data).trigger("chosen:updated");
        							$('#attachment_client_id').html(data.client_attachment_data).trigger("chosen:updated");
    	    					}else if(data.status==2){
    		    					$("#month_fee_gel").val(data.fee_lari);
        							$("#month_fee_usd").val(data.fee_dolari);
        							$("#pledge_or_other_balance_gel").val(data.pay_amount1);
        							$("#pledge_or_other_balance_usd").val(data.pay_amount2);
    
        							var pay_amount = $('#client_amount').val(); 
        							if($('#client_amount').val()=='' || $('#client_amount').val() == null){pay_amount == 0;}
        							
									if(data.fee_lari == ''){month_fee_gel = 0;}else{month_fee_gel = data.fee_lari;}
        							
        							if(data.pay_amount1 == '' || data.pay_amount1 == null){pledge_or_other_balance_gel = 0;}else{pledge_or_other_balance_gel = data.pay_amount1;}
        							if(data.pay_amount2 == '' || data.pay_amount2 == null){pledge_or_other_balance_usd = 0;}else{pledge_or_other_balance_usd = data.pay_amount2;}
    
        							pledge_or_other_extra_fee = $("#pledge_or_other_extra_fee").val();
        							
        							if(pledge_or_other_extra_fee == ''){pledge_or_other_extra_fee = 0;}
        							
        							if($("#received_currency_id").val() == 1){
            							$("#pledge_or_other_extra_fee").val(parseFloat(pledge_or_other_extra_fee)+parseFloat(pledge_or_other_balance_gel));   
                                    }else{
                                    	$("#pledge_or_other_extra_fee").val(parseFloat(pledge_or_other_extra_fee)+parseFloat(pledge_or_other_balance_usd));
                                    }    
        														
        							$('#client_id').html(data.client_data).trigger("chosen:updated");
        							$('#attachment_client_id').html(data.client_attachment_data).trigger("chosen:updated");
        						}else if(data.status==3){
        							$('#client_id').html(data.client_data).trigger("chosen:updated");
        							$('#attachment_client_id').html(data.client_attachment_data).trigger("chosen:updated");
        						}
    						}
    					}
    			    }
    		    });
    		}
    	});
		$(document).on("change", "#pledge_client_loan_number", function () {
			
            param         =  new Object();
		    param.act     = "get_client_chosen";
		    param.id  =  $(this).val();
		    
		    $.ajax({
		        url: aJaxURL_det,
			    data: param,
		        success: function(data) {			        
					if(typeof(data.error) != 'undefined'){
						if(data.error != ''){
							alert(data.error);
						}else{
							$('#pledge_client_id').html(data.client_data).trigger("chosen:updated");
	    				}
					}
			    }
		    });
    	});

		$(document).on("change", "#other_client_loan_number", function () {
			
            param         =  new Object();
		    param.act     = "get_client_chosen";
		    param.id  =  $(this).val();
		    
		    $.ajax({
		        url: aJaxURL_det,
			    data: param,
		        success: function(data) {			        
					if(typeof(data.error) != 'undefined'){
						if(data.error != ''){
							alert(data.error);
						}else{
							$('#other_client_id').html(data.client_data).trigger("chosen:updated");
	    				}
					}
			    }
		    });
    	});

		$(document).on("change", "#pledge_client_id", function () {
			
            param         =  new Object();
		    param.act     = "get_loan_number_chosen";
		    param.id  =  $(this).val();
		    
		    $.ajax({
		        url: aJaxURL_det,
			    data: param,
		        success: function(data) {			        
					if(typeof(data.error) != 'undefined'){
						if(data.error != ''){
							alert(data.error);
						}else{
							$('#pledge_client_loan_number').html(data.loan_number_data).trigger("chosen:updated");
	    				}
					}
			    }
		    });
    	});

		$(document).on("change", "#other_client_id", function () {
			
            param         =  new Object();
		    param.act     = "get_loan_number_chosen";
		    param.id  =  $(this).val();
		    
		    $.ajax({
		        url: aJaxURL_det,
			    data: param,
		        success: function(data) {			        
					if(typeof(data.error) != 'undefined'){
						if(data.error != ''){
							alert(data.error);
						}else{
							$('#other_client_loan_number').html(data.loan_number_data).trigger("chosen:updated");
	    				}
					}
			    }
		    });
    	});

		$(document).on("click", "#save_pledge_distribution", function () {
			
            param                           =  new Object();
		    param.act                       = "save_pledge_distribution";
		    
		    param.tr_id	                    =  $("#tr_id").val();
		    param.course                    =  $("#course").val();
		    param.client_amount             =  $("#client_amount").val();
		    param.received_currency_id      =  $("#received_currency_id").val();
		    param.transaction_date          =  $("#transaction_date").val();
		    param.pledge_client_id          =  $("#pledge_client_id").val();
		    param.pledge_client_loan_number =  $("#pledge_client_loan_number").val();
		    param.client_pledge_amount      =  $("#client_pledge_amount").val();
		    param.pledge_comment            =  $("#pledge_comment").val();

		    if(param.pledge_client_loan_number == 0){
			    alert('შეავსე ხელშეკრულების ნომერი');
			}else if(param.pledge_client_id == 0){
				alert('შეავსე კლიენტი');
			}else{
    		    $.ajax({
    		        url: aJaxURL_det,
    			    data: param,
    		        success: function(data) {			        
    					if(typeof(data.error) != 'undefined'){
    						if(data.error != ''){
    							alert(data.error);
    						}else{
    							CloseDialog('pledge_dialog');
    							$("#tr_id").val(data.tr_id);
    							GetDataTable("table_transaction_detail", aJaxURL_det, 'get_list', 9, "&transaction_id="+$("#tr_id").val(), 0, "", 0, "desc", "", "<'F'Cpl>");
    							$("#hidde_cl_id1").val(data.pledge_client_id);
    							
    		    			}
    					}
    			    }
    		    });
			}
    	});

		$(document).on("click", "#save_other_dialog", function () {
			
            param                         =  new Object();
		    param.act                     = "save_other_distribution";
		    
		    param.tr_id	                  =  $("#tr_id").val();
		    param.client_amount           =  $("#client_amount").val();
		    param.other_cource            =  $("#course").val();
		    param.other_currency_id       =  $("#received_currency_id").val();
		    param.other_date              =  $("#transaction_date").val();
		    
		    param.other_client_id         =  $("#other_client_id").val();
		    param.other_client_loan_number =  $("#other_client_loan_number").val();
		    param.client_other_amount     =  $("#other_pledge_amount").val();
		    param.other_comment           =  $("#other_comment").val();
		    
		    $.ajax({
		        url: aJaxURL_det,
			    data: param,
		        success: function(data) {			        
					if(typeof(data.error) != 'undefined'){
						if(data.error != ''){
							alert(data.error);
						}else{
							CloseDialog('other_dialog');
							$("#tr_id").val(data.tr_id);
							GetDataTable("table_transaction_detail", aJaxURL_det, 'get_list', 9, "&transaction_id="+$("#tr_id").val(), 0, "", 0, "desc", "", "<'F'Cpl>");
			          		setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
							$("#hidde_cl_id1").val(data.pledge_client_id);
		    			}
					}
			    }
		    });
    	});
    	
		$(document).on("keypress", "#client_amount, #course",  function (event) {
	        var ew = event.which;
	        if((48 <= ew && ew <= 57) || ew==46){
				if($('#client_amount').val() != '' && $('#course').val() != ''){
			        $('#add_button_dettail').button("enable");
			    }
	        	return true;
	        }else{
	          alert('შეიყვანეთ სწორი სიმბოლო!');
	          return false;
	        }
	    });

		$(document).on("keydown", "#client_amount, #course",  function (event) {
	        if($('#client_amount').val() == '' || $('#course').val() == ''){
    	        $('#add_button_dettail').button("disable");
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
        				$("#other_payed").focus();
        				$("#error_mesage").html('');
    				}
				}else{
					$("#other_payed").focus();
        		}
            }
		});

		$(document).on("keydown", "#other_payed", function (event) {
			if (event.keyCode == $.ui.keyCode.ENTER){
				if($(this).val()==''){this_value = 0;}else{this_value = $(this).val();}
				if($("#hidde_other_amount").val()==0){
    				delta = (parseFloat($("#extra_fee").val())-parseFloat(this_value)).toFixed(2);
    				if(delta<0){
    					alert('მიუთითეთ კორექტული თანხა');
    					$("#other_payed").css('background','#fb5959');
    					$("#error_mesage").html('არასწორი განაწილება!');
    				}else{
    					$("#hidde_other_amount").val(1);
        				$("#extra_fee").val(delta);
        				$("#other_payed").css('background','rgb(255, 255, 255)');
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
    					if($("#surplus_type").val() != 3){
        					$("#surplus").focus();
    					}else{
    						$("#surplus1").focus();
    					}
    				}
				}
				if($("#surplus_type").val() != 3){
					$("#surplus").focus();
				}else{
					$("#surplus1").focus();
				}
            }
		});

		$(document).on("keydown", "#surplus1", function (event) {
			if (event.keyCode == $.ui.keyCode.ENTER){
				if($(this).val()==''){this_value = 0;}else{this_value = $(this).val();}
				if($("#hidde_surplus1").val()==0){
    				delta = (parseFloat($("#extra_fee").val())-parseFloat(this_value)).toFixed(2);
    				if(delta<0){
    					alert('მიუთითეთ კორექტული თანხა');
    					$("#surplus1").css('background','#fb5959');
    				}else{
    					$("#hidde_surplus1").val(1);
    					$("#extra_fee").val(delta);
    					$("#surplus1").css('background','rgb(255, 255, 255)');
    				}
				}
            }
		});

		$(document).on("keydown", "#pledge_or_other_payed", function (event) {
			if (event.keyCode == $.ui.keyCode.ENTER){
				if($(this).val()==''){this_value = 0;}else{this_value = $(this).val();}
				if($("#hidde_pledge_amount").val()==0){
    				delta = (parseFloat($("#pledge_or_other_extra_fee").val())-parseFloat(this_value)).toFixed(2);
    				if(delta<0){
    					alert('მიუთითეთ კორექტული თანხა');
    					$("#pledge_or_other_payed").css('background','#fb5959');
    				}else{
    					$("#hidde_pledge_amount").val(1);
        				$("#pledge_or_other_extra_fee").val(delta);
        				$("#pledge_or_other_payed").css('background','rgb(255, 255, 255)');
        				$("#pledge_or_other_surplus").focus();
        			}
				}else{
    				$("#pledge_or_other_surplus").focus();
        		}
            }
		});

		$(document).on("keydown", "#pledge_or_other_surplus", function (event) {
			if (event.keyCode == $.ui.keyCode.ENTER){
				if($(this).val()==''){this_value = 0;}else{this_value = $(this).val();}
				if($("#hidde_pledge_surplus").val()==0){
    				delta = (parseFloat($("#pledge_or_other_extra_fee").val())-parseFloat(this_value)).toFixed(2);
    				if(delta<0){
    					alert('მიუთითეთ კორექტული თანხა');
    					$("#pledge_or_other_surplus").css('background','#fb5959');
    				}else{
    					$("#hidde_pledge_surplus").val(1);
        				$("#pledge_or_other_extra_fee").val(delta);
        				$("#pledge_or_other_surplus").css('background','rgb(255, 255, 255)');
        				if($("#surplus_type").val() != 3){
        					$("#pledge_or_other_surplus").focus();
    					}else{
    						$("#pledge_or_other_surplus1").focus();
    					}
        			}
				}else{
					if($("#surplus_type").val() != 3){
    					$("#pledge_or_other_surplus").focus();
					}else{
						$("#pledge_or_other_surplus1").focus();
					}
        		}
            }
		});

		$(document).on("keydown", "#pledge_or_other_surplus1", function (event) {
			if (event.keyCode == $.ui.keyCode.ENTER){
				if($(this).val()==''){this_value = 0;}else{this_value = $(this).val();}
				if($("#hidde_pledge_surplus1").val()==0){
    				delta = (parseFloat($("#pledge_or_other_extra_fee").val())-parseFloat(this_value)).toFixed(2);
    				if(delta<0){
    					alert('მიუთითეთ კორექტული თანხა');
    					$("#pledge_or_other_surplus1").css('background','#fb5959');
    				}else{
    					$("#hidde_pledge_surplus1").val(1);
        				$("#pledge_or_other_extra_fee").val(delta);
        				$("#pledge_or_other_surplus1").css('background','rgb(255, 255, 255)');
        				$("#pledge_or_other_surplus1").focus();
        			}
				}else{
    				$("#pledge_or_other_surplus1").focus();
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

		$(document).on("click", "#delete_other_payed", function () {
			other_payed = $("#other_payed").val();
	        if(other_payed == ''){other_payed = 0;}

	        if($("#hidde_other_amount").val() == 1){
	        	$("#extra_fee").val(parseFloat($("#extra_fee").val()) + parseFloat(other_payed));
	        	$("#other_payed").val('');
	        	$("#hidde_other_amount").val(0);
	        	$("#other_payed").focus();
	        }else{
	        	$("#extra_fee").val(parseFloat($("#extra_fee").val()));
	        	$("#other_payed").val('');
	        	$("#other_payed").focus();
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

		$(document).on("click", "#delete_surplus1", function () {
			surplus1 = $("#surplus1").val();
	        if(surplus1 == ''){surplus1 = 0;}

	        if($("#hidde_surplus1").val() == 1){
	        	$("#extra_fee").val(parseFloat($("#extra_fee").val()) + parseFloat(surplus1));
	        	$("#surplus1").val('');
	        	$("#hidde_surplus1").val(0);
	        	$("#surplus1").focus();
	        }else{
	        	$("#extra_fee").val(parseFloat($("#extra_fee").val()));
	        	$("#surplus1").val('');
	        	$("#surplus1").focus();
		    }
	    });
	    
		$(document).on("click", "#delete_pledge_surplus", function () {
			pledge_or_other_surplus = $("#pledge_or_other_surplus").val();
	        if(pledge_or_other_surplus == ''){pledge_or_other_surplus = 0;}

	        if($("#hidde_pledge_surplus").val() == 1){
	        	$("#pledge_or_other_extra_fee").val(parseFloat($("#pledge_or_other_extra_fee").val()) + parseFloat(pledge_or_other_surplus));
	        	$("#pledge_or_other_surplus").val('');
	        	$("#hidde_pledge_surplus").val(0);
	        	$("#pledge_or_other_surplus").focus();
	        }else{
	        	$("#pledge_or_other_extra_fee").val(parseFloat($("#pledge_or_other_extra_fee").val()));
	        	$("#pledge_or_other_surplus").val('');
	        	$("#pledge_or_other_surplus").focus();
		    }
	    });

		$(document).on("click", "#delete_pledge_surplus1", function () {
			pledge_or_other_surplus1 = $("#pledge_or_other_surplus1").val();
	        if(pledge_or_other_surplus1 == ''){pledge_or_other_surplus1 = 0;}

	        if($("#hidde_pledge_surplus1").val() == 1){
	        	$("#pledge_or_other_extra_fee").val(parseFloat($("#pledge_or_other_extra_fee").val()) + parseFloat(pledge_or_other_surplus1));
	        	$("#pledge_or_other_surplus1").val('');
	        	$("#hidde_pledge_surplus1").val(0);
	        	$("#pledge_or_other_surplus1").focus();
	        }else{
	        	$("#pledge_or_other_extra_fee").val(parseFloat($("#pledge_or_other_extra_fee").val()));
	        	$("#pledge_or_other_surplus1").val('');
	        	$("#pledge_or_other_surplus1").focus();
		    }
	    });
	    
		$(document).on("click", "#delete_amount", function () {
			pledge_or_other_payed = $("#pledge_or_other_payed").val();
	        if(pledge_or_other_payed == ''){pledge_or_other_payed = 0;}

	        if($("#hidde_pledge_amount").val() == 1){
	        	$("#pledge_or_other_extra_fee").val(parseFloat($("#pledge_or_other_extra_fee").val()) + parseFloat(pledge_or_other_payed));
	        	$("#pledge_or_other_payed").val('');
	        	$("#hidde_pledge_amount").val(0);
	        	$("#pledge_or_other_payed").focus();
	        }else{
	        	$("#pledge_or_other_extra_fee").val(parseFloat($("#pledge_or_other_extra_fee").val()));
	        	$("#pledge_or_other_payed").val('');
	        	$("#pledge_or_other_payed").focus();
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
            top: 42px;
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
<div id="tabs" style="width: 98%">
<div class="callapp_head">ტრანზაქციები (ჩარიცხვები)<hr class="callapp_head_hr"></div>

<div id="tabs1" style="width: 100%; margin: 0 auto;">
	<ul>
		<li><a href="#tab-0">დასადასტურებელი</a></li>
		<li><a href="#tab-1">სესხი</a></li>
		<li><a href="#tab-2">ალდაგი</a></li>
		<li><a href="#tab-3">სხვა ხარჯი</a></li>
		<li><a href="#tab-4">გაუქმებული</a></li>
	</ul>
	<div id="tab-0">
        <div id="button_area">
        	<button id="add_button">დამატება</button>
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
            <table class="display" id="example" style="width: 100%;">
                <thead>
                    <tr id="datatable_header">
                        <th>ID</th>
                        <th style="width: 10%;">თარიღი</th>
                        <th style="width: 10%;">კოდი</th>
                        <th style="width: 34%;">მსესხებელი</th>
                        <th style="width: 8%;">ჩარიცხული თანხა</th>
                        <th style="width: 8%;">ვალუტა</th>
                        <th style="width: 8%;">კურსი</th>
                        <th style="width: 11%;">სტატუსი</th>
                        <th style="width: 11%;">user</th>
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
                    </tr>
                </thead>
            </table>
       </div>
       <div id="tab-1">
            <div id="button_area">
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
            <table class="display" id="example1" style="width: 100%;">
                <thead>
                    <tr id="datatable_header">
                        <th>ID</th>
                        <th style="width: 7%;">თარიღი</th>
                        <th style="width: 7%;">კოდი</th>
                        <th style="width: 26%;">მსესხებელი</th>
                        <th style="width: 6%;">ჩარიც.<br>თანხა</th>
                        <th style="width: 6%;">ვალუტა</th>
                        <th style="width: 5%;">კურსი</th>
                        <th style="width: 6%;">დაფარ.<br>ძირი</th>
                        <th style="width: 6%;">დაფარ.<br>პროცენტი</th>
                        <th style="width: 6%;">მეტობა</th>
                        <th style="width: 8%;">სტატუსი</th>
                        <th style="width: 9%;">user</th>
                        <th style="width: 7%;">შევსების<br>თარიღი</th>
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
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                        <th>
                            <input type="text" name="search_category" value="ფილტრი" class="search_init" />
                        </th>
                    </tr>
                </thead>
            </table>
       </div>
       <div id="tab-2">
            <div id="button_area">
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
            <table class="display" id="example2" style="width: 100%;">
                <thead>
                    <tr id="datatable_header">
                        <th>ID</th>
                        <th style="width: 7%;">თარიღი</th>
                        <th style="width: 7%;">კოდი</th>
                        <th style="width: 25%;">მსესხებელი</th>
                        <th style="width: 6%;">ჩარიცხ.<br>თანხა</th>
                        <th style="width: 6%;">ვალუტა</th>
                        <th style="width: 6%;">კურსი</th>
                        <th style="width: 10%;">სტატუსი</th>
                        <th style="width: 11%;">user</th>
                        <th style="width: 7%;">შევსების<br>თარიღი</th>
                        <th style="width: 13%;">დანიშნულება</th>
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
            </table>
       </div>
       <div id="tab-3">
            <div id="button_area">
            	
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
            <table class="display" id="example3" style="width: 100%;">
                <thead>
                    <tr id="datatable_header">
                        <th>ID</th>
                        <th style="width: 7%;">თარიღი</th>
                        <th style="width: 7%;">კოდი</th>
                        <th style="width: 25%;">მსესხებელი</th>
                        <th style="width: 6%;">ჩარიცხ.<br>თანხა</th>
                        <th style="width: 6%;">ვალუტა</th>
                        <th style="width: 6%;">კურსი</th>
                        <th style="width: 10%;">სტატუსი</th>
                        <th style="width: 11%;">user</th>
                        <th style="width: 7%;">შევსების<br>თარიღი</th>
                        <th style="width: 13%;">დანიშნულება</th>
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
            </table>
       </div>
       <div id="tab-4">
            <div id="button_area">
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
            <table class="display" id="example4" style="width: 100%;">
                <thead>
                    <tr id="datatable_header">
                        <th>ID</th>
                        <th style="width: 9%;">თარიღი</th>
                        <th style="width: 9%;">კოდი</th>
                        <th style="width: 30%;">მსესხებელი</th>
                        <th style="width: 7%;">ჩარიცხული<br>თანხა</th>
                        <th style="width: 8%;">ვალუტა</th>
                        <th style="width: 10%;">კურსი</th>
                        <th style="width: 8%;">სტატუსი</th>
                        <th style="width: 10%;">user</th>
                        <th style="width: 9%;">შევსების<br>თარღი</th>
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
                    </tr>
                </thead>
            </table>
       </div>
    </div>
    <!-- jQuery Dialog -->
    <div id="add-edit-form" class="form-dialog" title="ძირითადი ველები"></div>
	<div id="add-edit-form-det" class="form-dialog" title="განაწილება"></div>
	<div id="add-edit-show_letter" class="form-dialog" title="ბარათი"></div>
	<div id="pledge_dialog" class="form-dialog" title="დაზღვევის დარიცხვა"></div>
	<div id="other_dialog" class="form-dialog" title="სხვა ხარჯი"></div>
</body>
</html>


