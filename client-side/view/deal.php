<html>
<head>
	<script type="text/javascript">
		var aJaxURL	          = "server-side/view/deal.action.php";
		var aJaxURL_show_letter = "server-side/main.action.php";		//server side folder url
		var tName	          = "example";													//table name
		var fName	          = "add-edit-form";												//form name
		var change_colum_main = "<'dataTable_buttons'T><'F'Cfipl>";
		    	
		$(document).ready(function () {        	
			LoadTable(tName,11,change_colum_main,aJaxURL);	
 						
			/* Add Button ID, Delete Button ID */
			GetButtons("add_button", "delete_button");
			GetButtons("add_cat", "");
			SetEvents("add_button", "delete_button", "check-all", tName, fName, aJaxURL,'',tName,8,change_colum_main,aJaxURL,'','','');
			$("#filt_agr_id").chosen();
			$("#filt_agr_id_chosen").css('margin-top','-6px');
		});
        
		function LoadTable(tName,num,change_colum_main,aJaxURL){
			/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
			GetDataTable(tName, aJaxURL, 'get_list', num, "&agr_id="+$("#filt_agr_id").val(), 0, "", 2, "asc", "", change_colum_main);
			setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
		}
		
		function LoadDialog(fname){
			var id		= $("#id").val();
			
			if(fname=='add-edit-form'){
				var buttons = {
						"დასრულება": {
				            text: "დასრულება",
				            id: "done-dialog"
				        },
						"save": {
				            text: "შენახვა",
				            id: "save-dialog"
				        },
			        	"cancel": {
				            text: "დახურვა",
				            id: "cancel-dialog",
				            click: function () {
				            	$(this).dialog("close");
				            }
				        }
				    };
// 				document.getElementById("#deal_amount").focus();
				GetDialog(fName, 900, "auto", buttons,"top");
    			GetDate("deal_penalty_start");
    			GetDate("deal_penalty_end");
    			GetDate("deal_end");
    			GetDate("payed_date");
    			if($("#hidde_id").val() == ''){
        			$("#deal_penalty_start").val('');
        			$("#deal_penalty_end").val('');
        			$("#deal_end").val('');
        			$("#payed_date").val('');
        		}
    			$("#payed_amount").focus();
    			$("#client_id").chosen();
    			$("#client_loan_number").chosen();
    			$("#received_currency_id").chosen();
    			$("#loan_currency_id").chosen();
    			$("#add-edit-form, .add-edit-form-class").css('overflow', 'visible');
    			
    		}
		}

		$(document).on("change", "#filt_agr_id", function () {
			LoadTable(tName,11,change_colum_main,aJaxURL);
		});
		
		// Add - Save
	    $(document).on("click", "#save-dialog", function () {
		    
		    param 					   = new Object();
		    param.act		           = "save_deal";
		    param.id		           = $("#hidde_id").val();
		    param.hidde_schedule_id    = $("#hidde_schedule_id").val();
		    param.payed_date		   = $("#payed_date").val();
	    	param.payed_amount		   = $("#payed_amount").val();
	    	param.received_currency_id = $("#received_currency_id").val();
	    	param.cource		       = $("#cource").val();
	    	param.loan_payed_date	   = $("#loan_payed_date").val();
	    	param.deal_penalty_start   = $("#deal_penalty_start").val();
	    	param.deal_penalty_end	   = $("#deal_penalty_end").val();
	    	param.penalty_day_count	   = $("#penalty_day_count").val();
	    	param.deal_amount	       = $("#deal_amount").val();
	    	param.deal_end	           = $("#deal_end").val();
	    	param.root1	               = $("#root1").val();
	    	param.pescent1	           = $("#pescent1").val();
	    	param.penalty1	           = $("#penalty1").val();
	    	param.unda_daericxos	   = $("#unda_daericxos").val();
	    	//param.penalty_stoped        = $("input[id='penalty_stoped']:checked").val();

            if(param.deal_amount == ''){
                alert('შეავსე შეთანხმების თანხა');
            }else if(param.deal_end == ''){
            	alert('შეავსე შეთანხმების დასასრული');
            }else{
    	    	$.ajax({
    		        url: aJaxURL,
    			    data: param,
    		        success: function(data) {			        
    					if(typeof(data.error) != 'undefined'){
    						if(data.error != ''){
    							alert(data.error);
    						}else{
    							LoadTable(tName,8,change_colum_main,aJaxURL);
    			        		CloseDialog(fName);
    						}
    					}
    			    }
    		    });
            }
		});

		$(document).on("click", "#done-dialog", function () {
		    
		    param 					   = new Object();
		    param.act		           = "done_deal";
		    param.id		           = $("#hidde_id").val();
		    

            if(param.id == ''){
                alert('შეთანხმება არ არსებობს');
            }else{
    	    	$.ajax({
    		        url: aJaxURL,
    			    data: param,
    		        success: function(data) {			        
    					if(typeof(data.error) != 'undefined'){
    						if(data.error != ''){
    							alert(data.error);
    						}else{
    							LoadTable(tName,8,change_colum_main,aJaxURL);
    			        		CloseDialog(fName);
    			        		alert('შეთანხმება წარმატებით დასრულდა');
    						}
    					}
    			    }
    		    });
            }
		});

        $(document).on("change", "#client_id", function () {
			
			param                      =  new Object();
		    param.act                  = "get_shedule";
		    param.status               = 1;
		    param.id                   = $(this).val();
		    param.transaction_date     = $("#payed_date").val();
		    param.month_fee_trasaction = $("#payed_amount").val();
		    param.received_currency_id = $("#received_currency_id").val();
		    
		    $.ajax({
		        url: aJaxURL,
			    data: param,
		        success: function(data) {			        
					if(typeof(data.error) != 'undefined'){
						if(data.error != ''){
							alert(data.error);
						}else{
							if(data.status==1){
    							$("#all_fee").val(data.pay_amount);
    							$("#root").val(data.root);
    							$("#pescent").val(data.percent);
    							$("#penalty").val(data.penalty);

    							$("#surplus").val(data.avans);
    							$("#daricxvis_tarigi").html('დარიცხვის თარიღი '+data.schedule_date);
    							
    							$("#hidde_schedule_id").val(data.id);
    							$("#loan_currency_id").html(data.currenc).trigger("chosen:updated");
    							$('#loan_currency_id').prop('disabled', true).trigger("chosen:updated");
    							$('#client_loan_number').html(data.agrement_data).trigger("chosen:updated");

    							if($("#payed_amount").val() == '' || $("#payed_amount").val() == null){
        							amount = 0;
    							}else{
        							amount = $("#payed_amount").val();
        						}
        						
        						if($("#received_currency_id").val()==$("#loan_currency_id").val()){
            						$("#loan_payed_date").val(amount);
        	                    }else{
            	                    
            	                    if($("#loan_currency_id").val() == 1){
            	                    	amount = (parseFloat(amount)*parseFloat($("#cource").val())).toFixed(2);
            	                    }else{
            	                    	amount = (parseFloat(amount)/parseFloat($("#cource").val())).toFixed(2);
                	                }
            	                    $("#loan_payed_date").val(amount);
        	                    }
    							
	    					}
						}
					}
			    }
		    });
    	});

		$(document).on("change", "#client_loan_number", function () {
			
			param                      =  new Object();
		    param.act                  = "get_shedule";
		    param.status               = 2;
		    param.agr_id               = $(this).val();
		    param.transaction_date     = $("#payed_date").val();
		    param.month_fee_trasaction = $("#payed_amount").val();
		    param.received_currency_id = $("#received_currency_id").val();
		    
		    $.ajax({
		        url: aJaxURL,
			    data: param,
		        success: function(data) {			        
					if(typeof(data.error) != 'undefined'){
						if(data.error != ''){
							alert(data.error);
						}else{
							if(data.status==1){
    							$("#all_fee").val(data.pay_amount);
    							$("#root").val(data.root);
    							$("#pescent").val(data.percent);
    							$("#penalty").val(data.penalty);

    							$("#surplus").val(data.avans);
    							$("#daricxvis_tarigi").html('დარიცხვის თარიღი '+data.schedule_date);
    							
    							$("#hidde_schedule_id").val(data.id);
    							$("#loan_currency_id").html(data.currenc).trigger("chosen:updated");
    							$('#loan_currency_id').prop('disabled', true).trigger("chosen:updated");
    							$('#client_id').html(data.client_data).trigger("chosen:updated");

    							if($("#payed_amount").val() == '' || $("#payed_amount").val() == null){
        							amount = 0;
    							}else{
        							amount = $("#payed_amount").val();
        						}
        						
        						if($("#received_currency_id").val()==$("#loan_currency_id").val()){
            						$("#loan_payed_date").val(amount);
        	                    }else{
            	                    
            	                    if($("#loan_currency_id").val() == 1){
            	                    	amount = (parseFloat(amount)*parseFloat($("#cource").val())).toFixed(2);
            	                    }else{
            	                    	amount = (parseFloat(amount)/parseFloat($("#cource").val())).toFixed(2);
                	                }
            	                    $("#loan_payed_date").val(amount);
        	                    }
    							
	    					}
						}
					}
			    }
		    });
    	});
    	
	    $(document).on("change", "#payed_date", function () {
	        
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
							$("#cource").val(data.cource);
						}
					}
			    }
		    });
	    });

		$(document).on("change", "#deal_penalty_start", function () {
	        get_penalty();
	    });

		$(document).on("change", "#deal_penalty_end", function () {
			get_penalty();
	    });

		function get_penalty(){
			param 	                  = new Object();
		    
			param.act                 = "get_penalty";
		    param.deal_penalty_start  = $("#deal_penalty_start").val();
		    param.deal_penalty_end    = $("#deal_penalty_end").val();
		    param.payed_date          = $("#payed_date").val();
		    param.client_id           = $("#client_id").val();
		    param.client_loan_number  = $("#client_loan_number").val();

		    if(param.deal_penalty_start != '' && param.deal_penalty_end != ''){
    			$.ajax({
    		        url: aJaxURL,
    			    data: param,
    		        success: function(data) {			        
    					if(typeof(data.error) != 'undefined'){
    						if(data.error != ''){
    							alert(data.error);
    						}else{
    							$("#penalty_amount").val(data.penalty);
    							$("#penalty_day_count").val(data.gadacilebuli_day_count);
    						}
    					}
    			    }
    		    });
		    }
		    
		}
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

	    $(document).on("click", ".show_letter", function () {
    	    param 	  = new Object();
    		param.act = "get_edit_page";
    		param.id  = $("#filt_agr_id").val();
    		
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
    			            $('#add-edit-show_letter, .add-edit-show_letter-class').css('overflow-x','scroll');
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
    
    	        						    if(delta<=0.05 && delta>=-0.05){delta='0.00';}
    	        						    if(delta1<=0.05 && delta1>=-0.05 ){delta1='0.00';}
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
<div id="tabs" style="width: 100%">
<div class="callapp_head">შეთანხმება<hr class="callapp_head_hr"></div>
<div id="button_area">
	<button id="add_button">ახალი შეთანხმება</button>
	<button id="delete_button">გაუქმება</button>
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
    <table class="display" id="example">
        <thead>
            <tr id="datatable_header">
                <th>ID</th>
                <th style="width: 15%;">თარიღი</th>
                <th style="width: 15%;">მსესხებელი</th>
                <th style="width: 15%;">ხელშეკრულების ნომერი</th>
                <th style="width: 10%;">ორისის კოდი</th>
                <th style="width: 15%;">შეთანხმების თანხა</th>
                <th style="width: 15%;">შეთანხმების<br>დასრულების<br>ტარიღი</th>
                <th style="width: 15%;">სტატუსი</th>
                <th class="check" style="width: 30px;">#</th>
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
                	<div class="callapp_checkbox">
                        <input type="checkbox" id="check-all" name="check-all" />
                        <label for="check-all"></label>
                    </div>
                </th>
           </tr>
        </thead>
    </table>
    <!-- jQuery Dialog -->
    <div id="add-edit-form" class="form-dialog" title="ძირითადი ველები">
    	<!-- aJax -->
	</div>
	<div id="add-edit-show_letter" class="form-dialog" title="შეთანხმება"></div>
</body>
</html>


