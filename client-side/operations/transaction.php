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
			LoadTable(tName,8,change_colum_main,aJaxURL);	
			$(".ui-widget-content").css('border', '0px solid #aaaaaa');
 						
			/* Add Button ID, Delete Button ID */
			GetButtons("add_button");
			SetEvents("add_button", "", "", tName, fName, aJaxURL,'',tName,10,change_colum_main,aJaxURL,'','','');
		});

		$(document).on("tabsactivate", "#tabs1", function() {
        	tab = GetSelectedTab(tbName);
        	if (tab == 0) {
        		LoadTable(tName,8,change_colum_main,aJaxURL);	
         	}else if(tab == 1){
         		GetButtons("add_button1", "");
             	GetDataTable("example1", aJaxURL, 'get_list', 12, "tab=1", 0, "", 0, "desc", "", change_colum_main);
             	SetEvents("", "", "", "example1", fName, aJaxURL,'',tName,10,change_colum_main,aJaxURL,'','','');
             	
             	setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
			}else if(tab == 2){
				GetButtons("add_button2", "");
				GetDataTable("example2", aJaxURL, 'get_list', 11, "tab=2", 0, "", 0, "desc", "", change_colum_main);
				SetEvents("", "", "", "example2", fName, aJaxURL,'',tName,10,change_colum_main,aJaxURL,'','','');
				
				setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
          	}else{
          		GetButtons("add_button3", "");
          		GetDataTable("example3", aJaxURL, 'get_list', 11, "tab=3", 0, "", 0, "desc", "", change_colum_main);
          		SetEvents("", "", "", "example3", fName, aJaxURL,'',tName,10,change_colum_main,aJaxURL,'','','');
          		
          		setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
            }
        });
        
		function LoadTable(tName,num,change_colum_main,aJaxURL){
			
			GetDataTable(tName, aJaxURL, 'get_list', num, "tab=0", 0, "", 0, "desc", "", change_colum_main);
			setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
		}
		
		function LoadDialog(fname){
			var id		= $("#id").val();
			if(fname=='add-edit-form'){
    			var buttons = {
    				"show_letter": {
    		            text: "ბარათის ნახვა",
    		            id: "show_letter",
    		            click: function () {
    		            	param 	  = new Object();
    		        		param.act = "get_edit_page";
    		        		param.id  = $("#hidde_cl_id").val();
    		        		
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
    		        			            var total =	[4,5,14];
    		        			            GetDataTable1("table_letter", aJaxURL_show_letter, "get_list1", 16, "&id="+param.id+"&loan_currency_id="+$("#loan_currency").val(), 0, dLength, 4, "desc", total, "<'F'Cpl>");
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
    		        		    							$("#remaining_root").html(data.remaining_root);
    		        		    							$("#remaining_root_gel").html(data.remaining_root_gel);
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
    			
    			$('#currency_id').chosen();
    	        $('#received_currency_id').chosen();
    	        $('#add-edit-form, .add-edit-form-class').css('overflow','visible');
    	        
				GetDataTable("table_transaction_detail", aJaxURL_det, 'get_list', 7, "&transaction_id="+$("#hidde_transaction_id").val(), 0, "", 0, "desc", "", "<'F'Cpl>");
				setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 50);
				$("#table_transaction_detail_length").css('top', '2px');
				GetButtons("add_button_dettail","");
         		SetEvents("add_button_dettail", "", "", 'table_transaction_detail', 'add-edit-form-det', aJaxURL_det);
    	        GetDateTimes('transaction_date');

    	        if($('#transaction_date').val() != ''){
    		        $('#add_button_dettail').button("enable");
    		    }else{
    		    	$('#add_button_dettail').button("disable");
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
				            }
				        }
				    };
				GetDialog("add-edit-form-det", 700, "auto", buttons,"top");
				$('#type_id').chosen();
    	        $('#client_id').chosen();
    	        $('#client_loan_number').chosen();

    	        $("#month_fee").val($('#client_amount').val());
    	        if($("#id").val()!=''){
					$('#currency_id').prop('disabled', true).trigger("chosen:updated");
        	        $('#client_id').prop('disabled', true).trigger("chosen:updated");
        	        $('#type_id').prop('disabled', true).trigger("chosen:updated");
        	        $('#client_loan_number').prop('disabled', true).trigger("chosen:updated");
				}
			}
		}

		$(document).on("change", "#transaction_date",  function (event) {
			if($('#transaction_date').val() != ''){
		        $('#add_button_dettail').button("enable");
		    }else{
		    	$('#add_button_dettail').button("disable");
			}
	    });
	    
		// Add - Save
	    $(document).on("click", "#save-dialog", function () {
		    param 		= new Object();
		    
			param.act	               = "save_transaction";
		    param.id	               = $("#id").val();
		    param.tr_id	               = $("#tr_id").val();
		    
		    param.month_fee		       = $("#month_fee").val();
	    	param.root		           = $("#root").val();
	    	param.percent		       = $("#percent").val();
	    	param.penalti_fee	       = $("#penalti_fee").val();
	    	param.surplus	           = $("#surplus").val();
	    	param.type_id	           = $("#type_id").val();
	    	param.client_id	           = $("#client_id").val();
	    	
	    	param.currency_id	       = $("#currency_id").val();
	    	param.received_currency_id = $('#received_currency_id').val();
	    	param.course	           = $("#course").val();
	    	param.transaction_date	   = $("#transaction_date").val();
	    	
	    	param.hidde_id		       = $("#hidde_id").val();
	    	param.hidde_transaction_id = $("#hidde_transaction_id").val();
	    	param.hidde_status         = $("#hidde_status").val();

	    	param.client_id            = $("#client_id").val();

	    	if(param.type_id == 0){
		    	alert('შეავსე ტიპი');
			}else if(param.client_id == 0){
		    	alert('შეავსე კლიენტი');
			}else if(param.month_fee == ''){
		    	alert('შეავსე ჩარიცხული თანხა');
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
    							$("#hidde_cl_id").val($("#client_id").val());
    							GetDataTable("table_transaction_detail", aJaxURL_det, 'get_list', 7, "&transaction_id="+$("#tr_id").val(), 0, "", 0, "desc", "", "<'F'Cpl>");
    							setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 50);
    							$("#table_transaction_detail_length").css('top', '2px');
    			        		CloseDialog('add-edit-form-det');
    			        		
    						}
    					}
    			    }
    		    });
    	    }
		});

	    $(document).on("click", "#save-dialog1", function () {
		    param = new Object();
		    
			param.act	= "save_transaction";
		    param.tr_id	= $("#tr_id").val();
		    
		    param.client_amount		   = $("#client_amount").val();
	    	param.received_currency_id = $("#received_currency_id").val();
	    	
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
    							LoadTable(tName,8,change_colum_main,aJaxURL);
    							CloseDialog('add-edit-form');
    			        	}
    					}
    			    }
    		    });
    	    }
		});
		
	    $(document).on("change", "#type_id", function () {
	        
	        if($(this).val() > 1 ){
	            $(".label_label").css('display','none');
	            $("#month_fee1").val('');
				$("#root1").val('');
				$("#percent1").val('');
				$("#penalti_fee1").val('');

				param         =  new Object();
    		    param.act     = "get_shedule";
    		    
    		    param.id      =  $("#client_id").val();
    		    param.agr_id  =  $("#client_loan_number").val();
    		    param.transaction_date  =  $("#transaction_date").val();
    		    param.type_id =  $(this).val();
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
	    							
	    							$("#hidde_id").val(data.id);
	    							$('#currency_id').prop('disabled', true).trigger("chosen:updated");
		    					}else if(data.status==2){
		    						$("#month_fee1").val(data.insurance_fee);
		    						$("#root1").val('');
	    							$("#percent1").val('');
	    							$("#penalti_fee1").val('');

	    							$("#month_fee2").val('');
	    							$("#root2").val('');
	    							$("#percent2").val('');
	    							$("#penalti_fee2").val('');

	    							$("#hidde_id").val(data.id);
	    							$('#currency_id').prop('disabled', false).trigger("chosen:updated");
	    						}else if(data.status==3){
			    					$("#month_fee1").val(data.pledge_fee);
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
	        }else{
	        	if($(this).val() == 1 && $("#client_id").val()>0){
		        	
	        		param         =  new Object();
	    		    param.act     = "get_shedule";
	    		    
	    		    param.id                =  $("#client_id").val();
	    		    param.agr_id            =  $("#client_loan_number").val();
	    		    param.transaction_date  =  $("#transaction_date").val();
	    		    param.type_id           =  $(this).val();
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
		    							
		    							$("#hidde_id").val(data.id);
		    							$('#currency_id').prop('disabled', true).trigger("chosen:updated");
			    					}else if(data.status==2){
			    						$("#month_fee1").val(data.insurance_fee);
			    						$("#root1").val('');
		    							$("#percent1").val('');
		    							$("#penalti_fee1").val('');

		    							$("#month_fee2").val('');
		    							$("#root2").val('');
		    							$("#percent2").val('');
		    							$("#penalti_fee2").val('');

		    							$("#hidde_id").val(data.id);
		    							$('#currency_id').prop('disabled', false).trigger("chosen:updated");
		    						}else if(data.status==3){
				    					$("#month_fee1").val(data.pledge_fee);
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
	        	
	       		$(".label_label").css('display','block');
	       		
	        }
	    });

		$(document).on("change", "#client_id", function () {
			param         =  new Object();
		    param.act     = "get_shedule";
		    param.status  = 1;
		    param.id      =  $(this).val();
		    param.type_id =  $('#type_id').val();
		    param.transaction_date  =  $("#transaction_date").val();
		    
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
    							
    							$("#hidde_id").val(data.id);
    							$("#currency_id").html(data.currenc).trigger("chosen:updated");
    							$('#currency_id').prop('disabled', true).trigger("chosen:updated");
    							$('#client_loan_number').html(data.agrement_data).trigger("chosen:updated");
	    					}else if(data.status==2){
	    						$("#month_fee1").val(data.insurance_fee);
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
    	});

		$(document).on("change", "#client_loan_number", function () {
			param         =  new Object();
		    param.act     = "get_shedule";
		    param.agr_id  =  $(this).val();
		    param.status  = 2;
		    param.type_id =  $('#type_id').val();
		    param.transaction_date  =  $("#transaction_date").val();
		    
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
    							
    							$("#hidde_id").val(data.id);
    							$("#currency_id").html(data.currenc).trigger("chosen:updated");
    							$('#currency_id').prop('disabled', true).trigger("chosen:updated");
    							$('#client_id').html(data.client_data).trigger("chosen:updated");
	    					}else if(data.status==2){
	    						$("#month_fee1").val(data.insurance_fee);
	    						$("#root1").val('');
    							$("#percent1").val('');
    							$("#penalti_fee1").val('');

    							$("#month_fee2").val('');
    							$("#root2").val('');
    							$("#percent2").val('');
    							$("#penalti_fee2").val('');

    							$("#hidde_id").val(data.id);
    							$('#currency_id').prop('disabled', false).trigger("chosen:updated");
    							$('#client_id').html(data.client_data).trigger("chosen:updated");
    						}else if(data.status==3){
		    					$("#month_fee1").val(data.pledge_fee);
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
		<li><a href="#tab-2">დაზღვევა</a></li>
		<li><a href="#tab-3">გირავნობა</a></li>
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
                        <th style="width: 8%;">კოდი</th>
                        <th style="width: 36%;">მსესხებელი</th>
                        <th style="width: 8%;">ჩარიცხული თანხა</th>
                        <th style="width: 8%;">ვალუტა</th>
                        <th style="width: 8%;">კურსი</th>
                        <th style="width: 10%;">სტატუსი</th>
                        <th style="width: 10%;">user</th>
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
                        <th style="width: 6%;">კოდი</th>
                        <th style="width: 30%;">მსესხებელი</th>
                        <th style="width: 6%;">ჩარიც.<br>თანხა</th>
                        <th style="width: 7%;">ვალუტა</th>
                        <th style="width: 5%;">კურსი</th>
                        <th style="width: 6%;">დაფარ.<br>ძირი</th>
                        <th style="width: 6%;">დაფარ.<br>პროცენტი</th>
                        <th style="width: 6%;">მეტობა</th>
                        <th style="width: 8%;">სტატუსი</th>
                        <th style="width: 9%;">user</th>
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
                    </tr>
                </thead>
            </table>
       </div>
       <div id="tab-2">
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
                        <th style="width: 10%;">თარიღი</th>
                        <th style="width: 10%;">კოდი</th>
                        <th style="width: 40%;">მსესხებელი</th>
                        <th style="width: 10%;">ჩარიცხული<br>თანხა</th>
                        <th style="width: 10%;">ვალუტა</th>
                        <th style="width: 10%;">კურსი</th>
                        <th style="width: 10%;">user</th>
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
                    </tr>
                </thead>
            </table>
       </div>
       <div id="tab-3">
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
                        <th style="width: 10%;">თარიღი</th>
                        <th style="width: 10%;">კოდი</th>
                        <th style="width: 40%;">მსესხებელი</th>
                        <th style="width: 10%;">ჩარიცხული<br>თანხა</th>
                        <th style="width: 10%;">ვალუტა</th>
                        <th style="width: 10%;">კურსი</th>
                        <th style="width: 12%;">user</th>
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
                    </tr>
                </thead>
            </table>
       </div>
    </div>
    <!-- jQuery Dialog -->
    <div id="add-edit-form" class="form-dialog" title="ძირითადი ველები">
    	<!-- aJax -->
	</div>
	<div id="add-edit-form-det" class="form-dialog" title="განაწილება">
    	<!-- aJax -->
	</div>
	<div id="add-edit-show_letter" class="form-dialog" title="ბარათი"></div>
</body>
</html>


