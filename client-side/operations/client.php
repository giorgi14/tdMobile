<head>
<style type="text/css">
.add-edit-form-research_lab-class,
   .add-edit-form-function_detail-class,
   .add-edit-form-emergency-class,
   .add-edit-form-claim-class,
   .add-edit-form-service-class,
   .add-edit-form-diagnoses-class,
    #add-edit-form-function_detail,
    #add-edit-form-emergency,
    #dialog-form,
    #add-edit-form-research_lab,
    #add-edit-form-claim,
    #add-edit-form-service,
    #add-edit-form-diagnoses{
    	overflow: visible !important;
    }
    .callapp_tabs{
    	margin-top: 5px;
    	margin-bottom: 5px;
    	float: right;
    	width: 100%;
    	height: 0px;
    }
    .callapp_tabs span{
    	color: #FFF;
        border-radius: 5px;
        padding: 5px;
    	float: left;
    	margin: 0 3px 0 3px;
    	background: #2681DC;
    	font-weight: bold;
    	font-size: 11px;
        margin-bottom: 2px;
    }
    .callapp_tabs span close{
    	cursor: pointer;
    	margin-left: 5px;
    }
    #table_pet_length select{
    	height: 14px;
    }
    .callapp_head{
    	font-family: pvn;
    	font-weight: bold;
    	font-size: 20px;
    	color: #2681DC;
    }
    .callapp_head_hr{
    	border: 1px solid #2681DC;
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
    #table_index tbody td:last-child {
        padding: 0;
    }
    #table_index thead th:last-child .DataTables_sort_wrapper{
        display: none;
    }
    #table_right_menu{
    	top: 35px;
    }
    
</style>
<script src="js/exporting.js"></script>
<script type="text/javascript">
    var aJaxURL               = "server-side/operations/client.action.php";
    var aJaxURL_cl_person     = "server-side/operations/subtables/client_person.action.php";
    var aJaxURL_cl_car_driver = "server-side/operations/subtables/client_car_drivers.action.php";
    var aJaxURL_cl_guarantors = "server-side/operations/subtables/client_guarantors.action.php";
    var tName                 = "table_";
    var dialog                = "add-edit-form";
    var colum_number          = 9;
    var main_act              = "get_list";
    var change_colum_main     = "<'dataTable_buttons'T><'F'Cfipl>";
     
    $(document).ready(function () {
    	GetButtons("add_button","delete_button");
    	LoadTable('index',colum_number,main_act,change_colum_main,aJaxURL,'','');
    	SetEvents("add_button", "delete_button", "check-all", tName+'index', dialog, aJaxURL,'','index',colum_number,main_act,change_colum_main,aJaxURL,'');

    });

    function LoadTable(tbl,col_num,act,change_colum,URL,leng,dataparam,total){

    	if(dataparam == undefined){dataparam = leng;}
    	if(tbl == 'person' || tbl == 'cardrivers' || tbl == 'guarantors'){dataparam = 'local_id='+$("#local_id").val();}
    	GetDataTable(tName+tbl,URL,act,col_num,dataparam,0,"",1,"desc",total,change_colum);
    	$("#table_person_length").css('top', '2px');
    	$("#table_cardrivers_length").css('top', '2px');
    	$("#table_guarantors_length").css('top', '2px');
    	setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 50);
    }

    function LoadDialog(fName){
        if(fName == 'add-edit-form'){
        	var buttons = {
        			"calculation-dialog": {
    		            text: "წინასწარი კალკულაცია",
    		            id: "calculation-dialog",
    		            click: function () {
    		            	param 	       = new Object();
    		        		param.act      = "get_calculation";
    		        		param.hidde_id = $("#id_hidden").val();
    		        		
    		        		$.ajax({
    		        	        url: aJaxURL,
    		        		    data: param,
    		        	        success: function(data) {       
    		        				if(typeof(data.error) != "undefined"){
    		        					if(data.error != ""){
    		        						alert(data.error);
    		        					}else{
    		        						$("#add-edit-form-calculation").html(data.page);
    		        						var buttons = {
		        			    				"cancel": {
		        			    		            text: "დახურვა",
		        			    		            id: "cancel-dialog",
		        			    		            click: function () {
		        			    		            	$(this).dialog("close");
		        			    		            }
		        			    		        }
		        			    		    };
    		        			            GetDialog("add-edit-form-calculation", 521, "auto", buttons, 'left+43 top');
    		        			            $("#check_calculation").button();
    		        			            GetDate('pay_datee');
    		        			            $("#pay_datee").blur();
    		        			        }
    		        				}
    		        	    	}
    		        	    });
    		            }
    		        },
        			"update-loan": {
    		            text: "სესხის რესტრუქტურიზაცია",
    		            id: "update-loan",
    		            click: function () {
    		            	param 	       = new Object();
    		        		param.act      = "get_update-loan_dialog";
    		        		param.hidde_id = $("#id_hidden").val();
    		        		
    		        		$.ajax({
    		        	        url: aJaxURL,
    		        		    data: param,
    		        	        success: function(data) {       
    		        				if(typeof(data.error) != "undefined"){
    		        					if(data.error != ""){
    		        						alert(data.error);
    		        					}else{
    		        						$("#add-edit-form-update_loan").html(data.page);
    		        						var buttons = {
		        			    				"save": {
		        			    		            text: "შენახვა",
		        			    		            id: "save-sub_loan"
		        			    		        },
		        			    	        	"cancel": {
		        			    		            text: "დახურვა",
		        			    		            id: "cancel-dialog",
		        			    		            click: function () {
		        			    		            	$(this).dialog("close");
		        			    		            }
		        			    		        }
		        			    		    };
    		        			            GetDialog("add-edit-form-update_loan", 422, "auto", buttons, 'left+43 top');
    		        			            $("#sub_check_monthly_pay").button();
    		        			            get_local_id('client', '1');
    		        					}
    		        				}
    		        	    	}
    		        	    });
    		            }
    		        },
        			"cancel-loan": {
    		            text: "სესხის დახურვა",
    		            id: "cancel-loan",
    		            click: function () {
    		            	param 	       = new Object();
    		        		param.act      = "get_canceled-loan_dialog";
    		        		param.hidde_id = $("#id_hidden").val();
    		        		
    		        		$.ajax({
    		        	        url: aJaxURL,
    		        		    data: param,
    		        	        success: function(data) {       
    		        				if(typeof(data.error) != "undefined"){
    		        					if(data.error != ""){
    		        						alert(data.error);
    		        					}else{
    		        						$("#add-edit-form-canceled").html(data.page);
    		        						var buttons = {
		        			    				"save": {
		        			    		            text: "ხელშეკრულების დახურვა",
		        			    		            id: "canceled_client_loan"
		        			    		        },
		        			    	        	"cancel": {
		        			    		            text: "დახურვა",
		        			    		            id: "cancel-dialog",
		        			    		            click: function () {
		        			    		            	$(this).dialog("close");
		        			    		            }
		        			    		        }
		        			    		    };
    		        			            GetDialog("add-edit-form-canceled", 300, "auto", buttons, 'center top');
    		        			        }
    		        				}
    		        	    	}
    		        	    });
    		            }
    		        },
        			"activate-dialog": {
    		            text: "გააქტიურება",
    		            id: "activate-dialog",
    		            click: function () {
    		            	param 	       = new Object();
    		        		param.act      = "activate_agreement";
    		        		param.hidde_id = $("#id_hidden").val();
    		        		
    		        		$.ajax({
    		        	        url: aJaxURL,
    		        		    data: param,
    		        	        success: function(data) {       
    		        				if(typeof(data.error) != "undefined"){
    		        					if(data.error != ""){
    		        						alert(data.error);
    		        					}else{
    		        						alert('ხელშეკრულება წარმატებით გააქტიურდა');
    		        						$("#activate-dialog").button("disable");
    		        						$("#save-dialog").button("disable");
    		        						LoadTable('index',colum_number,main_act,change_colum_main,aJaxURL);
    		        						$('#add-edit-form, .idle').attr('disabled', true);
    		        						$("#hidde_status").val(1);
    		        						$('#loan_agreement_type').prop('disabled', true).trigger("chosen:updated");
    		        	    				$('#agreement_type_id').prop('disabled', true).trigger("chosen:updated");
    		        	    				$('#car_type').prop('disabled', true).trigger("chosen:updated");
    		        	    				$("#cancel-loan").button("enable");
    		        	    				$("#update-loan").button("enable");
    		        					}
    		        				}
    		        	    	}
    		        	    });
    		            }
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
            GetDialog(fName, 575, "auto", buttons, 'left+43 top');
            GetDateTimes('datetime');
            $('.info').click();
            $('#name').focus();
            $('#datetime').blur();
     		$('.ui-widget-overlay').css('z-index',99);
     		GetDate('car_sale_date');
     		GetDate('car_ins_start');
     		GetDate('car_ins_end');
     		GetDate('born_date');
     		GetDate('tin_date');
     		//GetDate('car_born');
     		GetDate('insurance_start_date');
     		GetDate('insurance_end_date');
            if($("#local_id").val()==''){
            	get_local_id('client');
            	$("#born_date").val('');
         		$("#tin_date").val('');
         		$("#car_born").val('');
         		$("#insurance_start_date").val('');
         		$("#insurance_end_date").val('');
         		$("#car_sale_date").val('');
     			$("#car_ins_start").val('');
     			$("#car_ins_end").val('');
            }
            if($("#id_hidden").val()==''){
            	$(".documents").css('filter','brightness(0.3)');
            	$(".client_insurance").css('filter','brightness(0.3)');
            	$("#activate-dialog").button("disable");
            	$("#cancel-loan").button("disable");
				$("#update-loan").button("disable");
            }else{
                if($("#hidde_status").val()==1){
                	$("#activate-dialog").button("disable");
    				$("#save-dialog").button("disable");
    				$("#cancel-loan").button("enable");
    				$("#update-loan").button("enable");
    				$('#add-edit-form, .idle').attr('disabled', true);
    				$('#loan_agreement_type').prop('disabled', true).trigger("chosen:updated");
    				$('#agreement_type_id').prop('disabled', true).trigger("chosen:updated");
    				$('#car_type').prop('disabled', true).trigger("chosen:updated");
                }
            }

            if($("#hidde_canceled_status").val()==1){
            	$("#cancel-loan").button("disable");
				$("#update-loan").button("disable");
				$("#calculation-dialog").button("disable");
            }
            
            if($("#car_insurance_info_hidde").val()==''){
            	$("#print_insurance").button("disable");
            	$("#download_insurance").button("disable");
            }
            
            setTimeout(function(){
         		LoadTable('person',3,main_act,"<'F'Cpl>",aJaxURL_cl_person, '', 'local_id='+$("#local_id").val());
         		$("#table_person_length").css('top', '2px');
         		SetEvents("add_button_pers", "delete_button_pers", "check-all_pers", tName+'person', 'add-edit-form-pers', aJaxURL_cl_person,'','person',4,main_act,"<'F'Cpl>",aJaxURL_cl_person,'');
            	GetButtons("add_button_pers","delete_button_pers");
         	}, 50);
         	
            setTimeout(function(){
         		LoadTable('cardrivers',5,main_act,"<'F'Cpl>",aJaxURL_cl_car_driver, '', 'local_id='+$("#local_id").val());
         		$("#table_cardrivers_length").css('top', '2px');
         		SetEvents("add_button_cardriver", "delete_button_cardriver", "check-all_car_driver", tName+'cardrivers', 'add-edit-form-car_driver', aJaxURL_cl_car_driver,'','cardrivers',5,main_act,"<'F'Cpl>",aJaxURL_cl_car_driver,'');
            	GetButtons("add_button_cardriver","delete_button_cardriver");
         	}, 50);
         	
            setTimeout(function(){
         		LoadTable('guarantors',6,main_act,"<'F'Cpl>",aJaxURL_cl_guarantors, '', 'local_id='+$("#local_id").val());
         		$("#table_guarantors_length").css('top', '2px');
         		SetEvents("add_button_guarantors", "delete_button_guarantors", "check-all_guarantors", tName+'guarantors', 'add-edit-form-guarantors', aJaxURL_cl_guarantors,'','guarantors',6,main_act,"<'F'Cpl>",aJaxURL_cl_guarantors,'');
            	GetButtons("add_button_guarantors","delete_button_guarantors");
         	}, 50);
         	
        }else if(fName == 'add-edit-form-car_driver'){
     		var buttons = {
    				"save": {
    		            text: "შენახვა",
    		            id: "save-driver"
    		        },
    	        	"cancel": {
    		            text: "დახურვა",
    		            id: "cancel-dialog",
    		            click: function () {
    		            	$(this).dialog("close");
    		            }
    		        }
    		    };
                GetDialog("add-edit-form-car_driver", 490, "auto", buttons, 'left+43 top');
                GetDate('car_driver_license_born');
                GetDate('car_driver_born');
         		if($("#car_driver_hidde").val()==''){
                	$("#car_driver_license_born").val('');
             		$("#car_driver_born").val('');
             		
                }
        }else if(fName == 'add-edit-form-guarantors'){
     		var buttons = {
    				"save": {
    		            text: "შენახვა",
    		            id: "save-guarantor"
    		        },
    	        	"cancel": {
    		            text: "დახურვა",
    		            id: "cancel-dialog",
    		            click: function () {
    		            	$(this).dialog("close");
    		            }
    		        }
    		    };
                GetDialog("add-edit-form-guarantors", 438, "auto", buttons, 'left+43 top');
                GetDate('car_driver_license_born');
                GetDate('car_driver_born');
         		if($("#car_driver_hidde").val()==''){
                	$("#car_driver_license_born").val('');
             		$("#car_driver_born").val('');
             		
                }
        }else{
            var buttons = {
				"save": {
		            text: "შენახვა",
		            id: "save-dialog-pers"
		        },
	        	"cancel": {
		            text: "დახურვა",
		            id: "cancel-dialog",
		            click: function () {
		            	$(this).dialog("close");
		            }
		        }
		    };
            GetDialog("add-edit-form-pers", 450, "auto", buttons, 'left+43 top');
        }
    }

    function get_local_id(table_name, status){
    	param 	         = new Object();
		param.act        = "get_local_id";

		param.table_name = table_name;
    	$.ajax({
	        url: aJaxURL,
		    data: param,
	        success: function(data) {       
				if(typeof(data.error) != "undefined"){
					if(data.error != ""){
						alert(data.error);
					}else{
						if(status == 1){
							$("#local_sub_id").val(data.local_id);
						}else{
							$("#local_id").val(data.local_id);
						}
						
						
					}
				}
	    	}
	   });
    }
    
	function show_right_side(id,value){
        $("#right_side fieldset").hide();
        
        if(id == 'auto_mobile'){
            $("#upload_picture").show();
            $("#car_drivers_fieldset").show();
        }else if(id == 'agreement'){
        	$("#agreement_grafic").show();
        }else if(id == 'info'){
        	$("#table_person_fieldset").show();
        	$("#table_guarantors_fieldset").show();
        }else if(id == 'documents'){
        	
        	param 	  = new Object();
    		param.act = "get_documentss";

    		param.local_id = $("#local_id").val();
    		param.loan_agreement_type = $("#loan_agreement_type").val();
    		param.agreement_type_id   = $("#agreement_type_id").val();
    		param.loan_currency       = $("#loan_currency").val();
    		param.car_type            = $("input[id='carsize']:checked").val();
    		
        	$.ajax({
    	        url: aJaxURL,
    		    data: param,
    	        success: function(data) {       
    				if(typeof(data.error) != "undefined"){
    					if(data.error != ""){
    						alert(data.error);
    					}else{
        					$("#documents_div").html(data.page);
        					$("#documents_div1").html(data.page1);
    						$("#id_hidden").val(data.local_id);
    					}
    				}
    	    	}
    	   });
        	setTimeout(function(){
                if(value==1 || $("#span_status").val()==1){
                    $("#other_documents").show();
            	}
            }, 100);
		}
        setTimeout(function(){
            if(value==1 || $("#span_status").val()==1){
                $("#" + id).show();
        	}
        }, 100);
        $(".add-edit-form-class").css("width", "1200");
        hide_right_side();
        var str = $("."+id).children('img').attr('src');
		str = str.substring(0, str.length - 4);
        $("."+id).children('img').attr('src',str+'_blue.png');
        $("."+id).children('div').css('color','#2681DC');
        $('#car_type').chosen();
        $('#loan_agreement_type').chosen();
        $('#agreement_type_id').chosen();
        $('#responsible_user_id').chosen();
        $('#loan_currency').chosen();
        $('#car_type_chosen').css('width', '206px');
        $('#loan_agreement_type_chosen').css('width', '200px');
        $('#responsible_user_id_chosen').css('width', '200px');
        $('#agreement_type_id_chosen').css('width', '332px');
        $('#loan_currency_chosen').css('width', '130px');
        $('#choose_button').button();
        $('#check_monthly_pay').button();
        $('#show_payment_schedule').button();
        $('#hidde_payment_schedule').button();
        $('#print_insurance').button();
        $('#download_insurance').button();
        $('#save_insurance_info').button();
        GetDateTimes('agreement_date');
        GetDateTimes('trusting_date');
    }

    function hide_right_side(){
    	$("#side_menu").children('spam').children('div').css('color','#FFF');
        $(".info").children('img').attr('src','media/images/icons/client_menu.png');
        $(".auto_mobile").children('img').attr('src','media/images/icons/car.png');
        $(".agreement").children('img').attr('src','media/images/icons/handshake.png');
        $(".pledge").children('img').attr('src','media/images/icons');
        $(".papers").children('img').attr('src','media/images/icons/file.png');
        $(".documents").children('img').attr('src','media/images/icons/document.png');
        $(".client_insurance").children('img').attr('src','media/images/icons/car-insurance.png');
    }

    function show_main(id,my_this){
    	$("#client_main,#client_other").hide();
    	$("#" + id).show();
    	$(".client_main,.client_other").css('border','none');
    	$(".client_main,.client_other").css('padding','6px');
    	$(my_this).css('border','1px solid #ccc');
    	$(my_this).css('border-bottom','1px solid #F1F1F1');
    	$(my_this).css('padding','5px');
    }

    $(document).on("click", "#save-dialog", function () {
		   
		param 			= new Object();
		param.act		= "save_client";

		param.id_hidden	          = $('#id_hidden').val();
		param.local_id	          = $('#local_id').val();
		
		//ცლიენტის მონაცემები//
		param.name	              = $('#name').val();
		param.surname	          = $('#surname').val();
		param.born_date	          = $('#born_date').val();
		param.tin	              = $('#tin').val();
		param.tin_number          = $('#tin_number').val();
		param.tin_date	          = $('#tin_date').val();
		param.comment	          = $('#comment').val();
		param.mail	              = $('#mail').val();
		param.phone	              = $('#phone').val();
		param.fact_address        = $('#fact_address').val();
		param.jur_address         = $('#jur_address').val();
		param.ltd_name	          = $('#ltd_name').val();
		param.ltd_id              = $('#ltd_id').val();
		param.client_type         = $("input[class=client_type]:checked").val();
		param.trust_pers_checkbox = $("input[id='trust_pers_checkbox']:checked").val();
		
		//მინდობილი პირის მონაცემები//
		param.client_trust_name	        = $('#client_trust_name').val();
		param.client_trust_surname	    = $('#client_trust_surname').val();
		param.client_trust_tin	        = $('#client_trust_tin').val();
		param.client_trust_phone	    = $('#client_trust_phone').val();
		param.client_trust_mail	        = $('#client_trust_mail').val();
		param.client_trust_fact_address = $('#client_trust_fact_address').val();
		param.client_trust_jur_address  = $('#client_trust_jur_address').val();
		param.trusting_number	        = $('#trusting_number').val();
		param.trusting_date	            = $('#trusting_date').val();
		param.trusting_notary	        = $('#trusting_notary').val();
		param.trusting_notary_address   = $('#trusting_notary_address').val();
		param.trusting_notary_phone     = $('#trusting_notary_phone').val();

		//მანქანის მონაცემები//
		param.car_marc	              = $('#car_marc').val();
		param.car_model	              = $('#car_model').val();
		param.car_born	              = $('#car_born').val();
		param.car_color	              = $('#car_color').val();
		param.car_type	              = $('#car_type').val();
		param.car_engine	          = $('#car_engine').val();
		param.car_registration_number = $('#car_registration_number').val();
		param.car_owner               = $('#car_owner').val();
		param.car_ident               = $('#car_ident').val();
		param.car_ertificate          = $('#car_ertificate').val();
		param.car_wheel               = $('#car_wheel').val();
		param.car_seats               = $('#car_seats').val();
		param.car_price               = $('#car_price').val();
		param.car_sale_date           = $('#car_sale_date').val();
		param.car_insurance_price     = $('#car_insurance_price').val();
		param.car_ins_start           = $('#car_ins_start').val();
		param.car_ins_end             = $('#car_ins_end').val();
		param.car_max_pledge          = $('#car_max_pledge').val();
		param.shss_number             = $('#shss_number').val();
		param.tech_test_price         = $('#tech_test_price').val();
		param.carsize                 = $("input[id='carsize']:checked").val();
		
		$("#name, #surname, #born_date, #tin, #tin_number,#tin_date,#phone,#fact_address,#jur_address,#ltd_name,#ltd_id,#client_trust_name,client_trust_surname,#client_trust_tin,#client_trust_phone,#client_trust_fact_address,#client_trust_jur_address,#trusting_number,#trusting_date,#trusting_notary,#trusting_notary_address,#trusting_notary_phone,#agreement_date,#loan_amount,#loan_months,#insurance_fee,#pledge_fee,#monthly_pay,#month_percent,#monthly_pay,#exchange_rate,#penalty_days,#penalty_percent,#penalty_additional_percent,#loan_fee,#proceed_fee,#proceed_percent").css('border','1px solid #42B4E6');
		$("#responsible_user_id, #loan_agreement_type_chosen, #agreement_type_id_chosen, #car_type_chosen").css('border','');

		//ხელშეკრულების მონაცემები//
		param.agreement_type_id           = $('#agreement_type_id').val();
		param.loan_agreement_type	      = $('#loan_agreement_type').val();
		param.agreement_number	          = $('#agreement_number').val();
		param.agreement_date	          = $('#agreement_date').val();
		param.loan_amount	              = $('#loan_amount').val();
		param.loan_months                 = $('#loan_months').val();
		param.insurance_fee               = $('#insurance_fee').val();
		param.pledge_fee                  = $('#pledge_fee').val();
		param.month_percent               = $('#month_percent').val();
		param.monthly_pay                 = $('#monthly_pay').val();
		param.rs_message_number           = $('#rs_message_number').val();
		param.pay_day                     = $('#pay_day').val();
		param.exchange_rate               = $('#exchange_rate').val();
		param.penalty_days                = $('#penalty_days').val();
		param.penalty_percent             = $('#penalty_percent').val();
		param.penalty_additional_percent  = $('#penalty_additional_percent').val();
		param.loan_fee                    = $('#loan_fee').val();
		param.proceed_fee                 = $('#proceed_fee').val();
		param.proceed_percent             = $('#proceed_percent').val();
		param.loan_currency               = $('#loan_currency').val();
		param.oris_code                   = $('#oris_code').val();
		param.loan_beforehand_percent     = $('#loan_beforehand_percent').val();
		param.responsible_user_id         = $('#responsible_user_id').val();

		param.hidde_loan_amount           = $('#hidde_loan_amount').val();
		param.hidde_loan_months           = $('#hidde_loan_months').val();
		param.hidde_agreement_datetime    = $('#hidde_agreement_datetime').val();
		param.hidde_agreement_percent     = $('#hidde_agreement_percent').val();
		param.hidde_loan_type_id          = $('#hidde_loan_type_id').val();
		
		if(param.name == ''){
			alert('შეავსეთ "სახელი"');
			$("#name").css('border','1px solid #F44336');
		}else if(param.surname == ''){
			alert('შეავსეთ "გვარი"');
			$("#surname").css('border','1px solid #F44336');
		}else if(param.born_date == ''){
			alert('შეავსეთ "დაბადების თარიღი"');
			$("#born_date").css('border','1px solid #F44336');
		}else if(param.tin == ''){
			alert('შეავსეთ "პირადი ნომერი"');
			$("#tin").css('border','1px solid #F44336');
		}else if(param.tin_number == ''){
			alert('შეავსეთ "პირ. მოწმ. ნომერი"');
			$("#tin_number").css('border','1px solid #F44336');
		}else if(param.tin_date == ''){
			alert('შეავსეთ "პირ. გაცემის თარიღი"');
			$("#tin_date").css('border','1px solid #F44336');
		}else if(param.phone == ''){
			alert('შეავსეთ "ტელეფონი"');
			$("#phone").css('border','1px solid #F44336');
		}else if(param.fact_address == '' && param.jur_address == ''){
			alert('შეავსეთ "მისამართი"');
			$("#fact_address").css('border','1px solid #F44336');
			$("#jur_address").css('border','1px solid #F44336');
		}else if(param.client_type == 2 && param.ltd_name == ''){
			alert('შეავსეთ "შპს დასახელება"');
			$("#ltd_name").css('border','1px solid #F44336');
		}else if(param.client_type == 2 && param.ltd_id == ''){
			alert('შეავსეთ "საიდენტიფიკაციო კოდი"');
			$("#ltd_id").css('border','1px solid #F44336');
		}else if(param.trust_pers_checkbox == 1 && param.client_trust_name == ''){
			alert('შეავსეთ "მ/პ სახელი"');
			$("#client_trust_name").css('border','1px solid #F44336');
		}else if(param.trust_pers_checkbox == 1 && param.client_trust_surname == ''){
			alert('შეავსეთ "მ/პ გვარი"');
			$("#client_trust_surname").css('border','1px solid #F44336');
		}else if(param.trust_pers_checkbox == 1 && param.client_trust_tin == ''){
			alert('შეავსეთ "მ/პ პირადი ნომერი"');
			$("#client_trust_tin").css('border','1px solid #F44336');
		}else if(param.trust_pers_checkbox == 1 && param.client_trust_phone == ''){
			alert('შეავსეთ "მ/პ ტელეფონი"');
			$("#client_trust_phone").css('border','1px solid #F44336');
		}else if(param.trust_pers_checkbox == 1 && param.client_trust_fact_address == '' && param.client_trust_jur_address == ''){
			alert('შეავსეთ "მ/პ მისამართი"');
			$("#client_trust_fact_address").css('border','1px solid #F44336');
			$("#client_trust_jur_address").css('border','1px solid #F44336');
		}else if(param.trust_pers_checkbox == 1 && param.trusting_number == ''){
			alert('შეავსეთ "სანოტ. რეგისტრ. ნომერი"');
			$("#trusting_number").css('border','1px solid #F44336');
		}else if(param.trust_pers_checkbox == 1 && param.trusting_date == ''){
			alert('შეავსეთ "სანოტ. რეგისტრ. თარიღი"');
			$("#trusting_date").css('border','1px solid #F44336');
		}else if(param.trust_pers_checkbox == 1 && param.trusting_notary == ''){
			alert('შეავსეთ "ნოტარიუსი"');
			$("#trusting_notary").css('border','1px solid #F44336');
		}else if(param.trust_pers_checkbox == 1 && param.trusting_notary_phone  == ''){
			alert('შეავსეთ "ნოტარიუსის ტელეფონი"');
			$("#trusting_notary_phone").css('border','1px solid #F44336');
		}else if(param.trust_pers_checkbox == 1 && param.trusting_notary_address  == ''){
			alert('შეავსეთ "ნოტარიუსის მისამართი"');
			$("#trusting_notary_address").css('border','1px solid #F44336');
		}else if(param.agreement_date == ''){
			alert('შეავსეთ "ხელშეკრულების თარიღი"');
			$("#agreement_date").css('border','1px solid #F44336');
		}else if(param.loan_agreement_type == 0){
			alert('შეავსეთ "სესხის ტიპი"');
			$("#loan_agreement_type_chosen").css('border','1px solid #F44336');
		}else if(param.agreement_type_id == 0){
			alert('შეავსეთ "ხელშეკრულების ტიპი"');
			$("#agreement_type_id_chosen").css('border','1px solid #F44336');
		}else if(param.loan_amount == ''){
			alert('შეავსეთ "სესხის სრული მოცულობა"');
			$("#loan_amount").css('border','1px solid #F44336');
		}else if(param.month_percent == ''){
			alert('შეავსეთ "ყოველთვიური პროცენტი"');
			$("#month_percent").css('border','1px solid #F44336');
		}else if(param.loan_months == ''){
			alert('შეავსეთ "სესხის სარგებლობის ვადა"');
			$("#loan_months").css('border','1px solid #F44336');
		}else if(param.loan_fee == ''){
			alert('შეავსეთ "სესხის გაცემის საკომისიო"');
			$("#loan_fee").css('border','1px solid #F44336');
		}else if(param.loan_agreement_type == 1 && param.proceed_fee == ''){
			alert('შეავსეთ "ხელშკრ. გაგრძ. საფასური"');
			$("#proceed_fee").css('border','1px solid #F44336');
		}else if(param.loan_agreement_type == 1 && param.proceed_percent == ''){
			alert('შეავსეთ "პროცენტი"');
			$("#proceed_percent").css('border','1px solid #F44336');
		}else if(param.insurance_fee == ''){
			alert('შეავსეთ "სადაზღვევო ხარჯი"');
			$("#insurance_fee").css('border','1px solid #F44336');
		}else if(param.pledge_fee == ''){
			alert('შეავსეთ "გირავნობის ხარჯი"');
			$("#pledge_fee").css('border','1px solid #F44336');
		}else if(param.monthly_pay == ''){
			alert('შეამოწმეთ "ყოველთვიურად შეს. თანხა"');
			$("#monthly_pay").css('border','1px solid #F44336');
		}else if(param.exchange_rate == ''){
			alert('შეავსეთ "ვალუტის კურსი"');
			$("#exchange_rate").css('border','1px solid #F44336');
		}else if(param.penalty_days == ''){
			alert('შეავსეთ "ვადაგადაცილებული დღეები"');
			$("#penalty_days").css('border','1px solid #F44336');
		}else if(param.penalty_percent == ''){
			alert('შეავსეთ "ვადაგადაც. პირგასამტეხლო%"');
			$("#penalty_percent").css('border','1px solid #F44336');
		}else if(param.penalty_additional_percent == ''){
			alert('შეავსეთ "ვადაგადაც. პირგასამტეხლო%"');
			$("#penalty_additional_percent").css('border','1px solid #F44336');
		}else if(param.responsible_user_id == 0){
			alert('შეავსეთ "ხელმომწერი პირი"');
			$("#responsible_user_id").css('border','1px solid #F44336');
		}else{
    		$.ajax({
    	        url: aJaxURL,
    		    data: param,
    	        success: function(data) {       
    				if(typeof(data.error) != "undefined"){
    					if(data.error != ""){
    						alert(data.error);
    					}else{
    						alert('ოპერაცია წარმატებით შესრულდა');
    						$("#activate-dialog").button("enable");
    						LoadTable('index',colum_number,main_act,change_colum_main,aJaxURL);
    						$("#span_status").val(1);
    						$(".documents").css('filter','');
    						$(".client_insurance").css('filter','');
    						$("#id_hidden").val($('#local_id').val());
    						
    						$('#hidde_loan_amount').val(param.loan_amount);
    						$('#hidde_loan_months').val(param.loan_months);
    						$('#hidde_agreement_datetime').val(param.agreement_date);
    						$('#hidde_agreement_percent').val(param.month_percent);
    						$('#hidde_loan_type_id').val(param.loan_agreement_type);
    					}
    				}
    	    	}
    	    });
		}
	});
    $(document).on("click", "#save-sub_loan", function () {
		   
		param 			= new Object();
		param.act		= "save_sub_client";

		param.local_id	          = $('#local_id').val();
		param.local_sub_id	      = $('#local_sub_id').val();
		
		//ცლიენტის მონაცემები//
		param.name	              = $('#name').val();
		param.surname	          = $('#surname').val();
		param.born_date	          = $('#born_date').val();
		param.tin	              = $('#tin').val();
		param.tin_number          = $('#tin_number').val();
		param.tin_date	          = $('#tin_date').val();
		param.comment	          = $('#comment').val();
		param.mail	              = $('#mail').val();
		param.phone	              = $('#phone').val();
		param.fact_address        = $('#fact_address').val();
		param.jur_address         = $('#jur_address').val();
		param.ltd_name	          = $('#ltd_name').val();
		param.ltd_id              = $('#ltd_id').val();
		param.client_type         = $("input[class=client_type]:checked").val();
		param.trust_pers_checkbox = $("input[id='trust_pers_checkbox']:checked").val();
		
		//მინდობილი პირის მონაცემები//
		param.client_trust_name	        = $('#client_trust_name').val();
		param.client_trust_surname	    = $('#client_trust_surname').val();
		param.client_trust_tin	        = $('#client_trust_tin').val();
		param.client_trust_phone	    = $('#client_trust_phone').val();
		param.client_trust_mail	        = $('#client_trust_mail').val();
		param.client_trust_fact_address = $('#client_trust_fact_address').val();
		param.client_trust_jur_address  = $('#client_trust_jur_address').val();
		param.trusting_number	        = $('#trusting_number').val();
		param.trusting_date	            = $('#trusting_date').val();
		param.trusting_notary	        = $('#trusting_notary').val();
		param.trusting_notary_address   = $('#trusting_notary_address').val();
		param.trusting_notary_phone     = $('#trusting_notary_phone').val();

		//მანქანის მონაცემები//
		param.car_marc	              = $('#car_marc').val();
		param.car_model	              = $('#car_model').val();
		param.car_born	              = $('#car_born').val();
		param.car_color	              = $('#car_color').val();
		param.car_type	              = $('#car_type').val();
		param.car_engine	          = $('#car_engine').val();
		param.car_registration_number = $('#car_registration_number').val();
		param.car_owner               = $('#car_owner').val();
		param.car_ident               = $('#car_ident').val();
		param.car_ertificate          = $('#car_ertificate').val();
		param.car_wheel               = $('#car_wheel').val();
		param.car_seats               = $('#car_seats').val();
		param.car_price               = $('#car_price').val();
		param.car_sale_date           = $('#car_sale_date').val();
		param.car_insurance_price     = $('#car_insurance_price').val();
		param.car_ins_start           = $('#car_ins_start').val();
		param.car_ins_end             = $('#car_ins_end').val();
		param.car_max_pledge          = $('#car_max_pledge').val();
		param.shss_number             = $('#shss_number').val();
		param.carsize                 = $("input[id='carsize']:checked").val();

		//ხელშეკრულების მონაცემები//
		param.agreement_type_id           = $('#agreement_type_id').val();
		param.loan_agreement_type	      = $('#loan_agreement_type').val();
		param.agreement_number	          = $('#agreement_number').val();
		param.agreement_date	          = $('#agreement_date').val();
		param.loan_amount	              = $('#sub_loan_amount').val();
		param.loan_months                 = $('#sub_loan_month').val();
		param.insurance_fee               = $('#insurance_fee').val();
		param.pledge_fee                  = $('#pledge_fee').val();
		param.month_percent               = $('#month_percent').val();
		param.monthly_pay                 = $('#sub_monthly_pay').val();
		param.rs_message_number           = $('#rs_message_number').val();
		param.pay_day                     = $('#pay_day').val();
		param.exchange_rate               = $('#exchange_rate').val();
		param.penalty_days                = $('#penalty_days').val();
		param.penalty_percent             = $('#penalty_percent').val();
		param.penalty_additional_percent  = $('#penalty_additional_percent').val();
		param.loan_fee                    = $('#loan_fee').val();
		param.proceed_fee                 = $('#proceed_fee').val();
		param.proceed_percent             = $('#proceed_percent').val();
		param.loan_currency               = $('#loan_currency').val();
		param.oris_code                   = $('#oris_code').val();
		param.loan_beforehand_percent     = $('#loan_beforehand_percent').val();
		
		if(param.loan_amount == ''){
			alert('შეავსეთ "სესხის მოცულობა"');
		}else if(param.loan_months == ''){
			alert('შეავსეთ "სესხის მოცულობა"');
		}else if(param.monthly_pay == ''){
			alert('შეავსეთ "ყოველთვიურად შეს. თანხა"');
		}else{
    		$.ajax({
    	        url: aJaxURL,
    		    data: param,
    	        success: function(data) {       
    				if(typeof(data.error) != "undefined"){
    					if(data.error != ""){
    						alert(data.error);
    					}else{
    						alert('ოპერაცია წარმატებით შესრულდა');
    						LoadTable('index',colum_number,main_act,change_colum_main,aJaxURL);
    						$("#add-edit-form-update_loan").dialog("close");
    						$("#add-edit-form").dialog("close");
    					}
    				}
    	    	}
    	    });
		}
	});

    $(document).on("click", "#canceled_client_loan", function () {
    	param 	       = new Object();
		param.act      = "cancel_loan";
		param.hidde_id = $("#id_hidden").val();
		
		$.ajax({
	        url: aJaxURL,
		    data: param,
	        success: function(data) {       
				if(typeof(data.error) != "undefined"){
					if(data.error != ""){
						alert(data.error);
					}else{
						alert('ხელშეკრულება წარმატებით დაიხურა');
						$("#add-edit-form").dialog("close");
						LoadTable('index',colum_number,main_act,change_colum_main,aJaxURL,'','');
					}
				}
	    	}
	    });
	});
	
    $(document).on("click", "#check_monthly_pay", function () {
		param 	  = new Object();
		param.act = "check_monthly_pay";

		param.loan_amount	      = $('#loan_amount').val();
		param.month_percent	      = $("#month_percent").val();
		param.loan_months	      = $('#loan_months').val();
		param.loan_agreement_type = $('#loan_agreement_type').val();
		if(param.loan_amount == ''){
			alert('შეავსეთ "სესხის სრული მოცულობა"');
		}else if(param.month_percent == ''){
			alert('შეავსეთ "ყოველთვიური პროცენტი"');
		}else if(param.loan_months == ''){
			alert('შეავსეთ "სესხის სარგებლობის ვადა"');
		}else if(param.loan_agreement_type==0){
			alert('შეავსეთ "სესხის ტიპი"');
		}else{
    		$.ajax({
    	        url: aJaxURL,
    		    data: param,
    	        success: function(data) {       
    				if(typeof(data.error) != "undefined"){
    					if(data.error != ""){
    						alert(data.error);
    					}else{
    						$("#monthly_pay").val(data.monthly_pay);
    					}
    				}
    	    	}
    	   });
		}
	});

    $(document).on("click", "#sub_check_monthly_pay", function () {
		param 	  = new Object();
		param.act = "check_monthly_pay";

		param.loan_amount	      = $('#sub_loan_amount').val();
		param.month_percent	      = $("#month_percent").val();
		param.loan_months	      = $('#sub_loan_month').val();
		param.loan_agreement_type = $('#loan_agreement_type').val();
		if(param.loan_amount == ''){
			alert('შეავსეთ "სესხის სრული მოცულობა"');
		}else if(param.month_percent == ''){
			alert('შეავსეთ "ყოველთვიური პროცენტი"');
		}else if(param.loan_months == ''){
			alert('შეავსეთ "სესხის სარგებლობის ვადა"');
		}else if(param.loan_agreement_type==0){
			alert('შეავსეთ "სესხის ტიპი"');
		}else{
    		$.ajax({
    	        url: aJaxURL,
    		    data: param,
    	        success: function(data) {       
    				if(typeof(data.error) != "undefined"){
    					if(data.error != ""){
    						alert(data.error);
    					}else{
    						$("#sub_monthly_pay").val(data.monthly_pay);
    					}
    				}
    	    	}
    	   });
		}
	});
    $(document).on("click", "#save-dialog-pers", function () {
		param 			= new Object();
		param.act		= "save_client_pers";

		param.client_pers_hidde	= $('#client_pers_hidde').val();
		param.local_id	        = $("#local_id").val();
		
		param.client_pers	    = $('#client_pers').val();
		param.client_pers_phone	= $('#client_pers_phone').val();
		
		$.ajax({
	        url: aJaxURL_cl_person,
		    data: param,
	        success: function(data) {       
				if(typeof(data.error) != "undefined"){
					if(data.error != ""){
						alert(data.error);
					}else{
						LoadTable('person',3,main_act,"<'F'Cpl>",aJaxURL_cl_person,'','local_id='+$("#local_id").val());
						$("#table_person_length").css('top', '2px');
					    CloseDialog("add-edit-form-pers");
					}
				}
	    	}
	   });
	});

    $(document).on("click", "#save-guarantor", function () {
		param 	  = new Object();
		param.act = "save_guarantor";

		param.guarantor_hidde	= $('#guarantor_hidde').val();
		param.local_id	        = $("#local_id").val();
		
		param.guarantor_name	= $('#guarantor_name').val();
		param.guarantor_pid	    = $('#guarantor_pid').val();
		param.guarantor_address	= $('#guarantor_address').val();
		param.guarantor_mail	= $('#guarantor_mail').val();
		param.guarantor_phone   = $('#guarantor_phone').val();
		
		$.ajax({
	        url: aJaxURL_cl_guarantors,
		    data: param,
	        success: function(data) {       
				if(typeof(data.error) != "undefined"){
					if(data.error != ""){
						alert(data.error);
					}else{
						LoadTable('guarantors',6,main_act,"<'F'Cpl>",aJaxURL_cl_guarantors,'','local_id='+$("#local_id").val());
						$("#table_guarantors_length").css('top', '2px');
					    CloseDialog("add-edit-form-guarantors");
					}
				}
	    	}
	   });
	});
    $(document).on("click", "#check_calculation", function () {
		param 	  = new Object();
		param.act = "check_calculation";

		param.local_id	= $("#local_id").val();
		param.pay_datee	= $('#pay_datee').val();
		
		$.ajax({
	        url: aJaxURL,
		    data: param,
	        success: function(data) {       
				if(typeof(data.error) != "undefined"){
					if(data.error != ""){
						alert(data.error);
					}else{
						$("#full_fee2").val(data.pay_amount);
						$("#root_fee2").val(data.root);
						$("#percent_fee2").val(data.percent);
						$("#penalty_fee2").val(data.penalty);
						$("#full_pay2").val(data.pay_amount1);
					}
				}
	    	}
	   });
	});

    $(document).on("click", "#save-driver", function () {
		param 	  = new Object();
		param.act = "save_car_drivers";

		param.car_driver_hidde	= $('#car_driver_hidde').val();
		param.local_id	        = $("#local_id").val();
		
		param.car_driver_name	        = $('#car_driver_name').val();
		param.car_driver_position	    = $('#car_driver_position').val();
		param.car_driver_born	        = $('#car_driver_born').val();
		param.car_driver_license_type	= $('#car_driver_license_type').val();
		param.car_driver_license_born   = $('#car_driver_license_born').val();
		
		$.ajax({
	        url: aJaxURL_cl_car_driver,
		    data: param,
	        success: function(data) {       
				if(typeof(data.error) != "undefined"){
					if(data.error != ""){
						alert(data.error);
					}else{
						LoadTable('cardrivers',5,main_act,"<'F'Cpl>",aJaxURL_cl_car_driver,'','local_id='+$("#local_id").val());
						$("#table_cardrivers_length").css('top', '2px');
					    CloseDialog("add-edit-form-car_driver");
					}
				}
	    	}
	   });
	});
	
    $(document).on("click", "#save_insurance_info", function () {
		param 			= new Object();
		param.act		= "save_insurance_info";

		param.local_id 								= $("#local_id").val();
		param.car_insurance_info_hidde	            = $("#car_insurance_info_hidde").val();
		
		param.insurance_price_gel	                = $('#insurance_price_gel').val();
		param.insurance_price_usd	                = $('#insurance_price_usd').val();
		param.insurance_start_date	                = $('#insurance_start_date').val();
		param.insurance_end_date	                = $('#insurance_end_date').val();
		
		$.ajax({
	        url: aJaxURL,
		    data: param,
	        success: function(data) {       
				if(typeof(data.error) != "undefined"){
					if(data.error != ""){
						alert(data.error);
					}else{
						alert('დაზღვევის მონაცემები წარმატებით შეინახა');
						$("#print_insurance").button('enable');
						$("#download_insurance").button('enable');
						if(data.ins_hidde_id!=0){
							$("#car_insurance_info_hidde").val(data.ins_hidde_id);
						}
					}
				}
	    	}
	   });
	});
	
    $(document).on("click", ".callapp_refresh", function () {
    	LoadTable('index',colum_number,main_act,change_colum_main,aJaxURL);
    });

    $(document).on("click", ".client_type", function () {
    	if($(this).val()==1){
    		$("#ltd_name").css('display','none');
    		$("#ltd_id").css('display','none');
    		$(".hidde_label").css('display','none');
        }else{
        	$("#ltd_name").css('display','block');
    		$("#ltd_id").css('display','block');
    		$(".hidde_label").css('display','block');
        }
    });

    $(document).on("click", "#trust_pers_checkbox", function () {
        if($(this).prop("checked") == false){
            $("#truste_table").css('display','none');
       	}else{
       		$("#truste_table").css('display','block');
        }
    });

    $(document).on("change", "#loan_agreement_type", function () {
        
        if($(this).val() == 2){
            $(".label_label").css('display','none');
            $("#proceed_fee").css('display','none');
            $("#proceed_percent").css('display','none');
       	}else{
       		$(".label_label").css('display','block');
       		$("#proceed_fee").css('display','block');
       		$("#proceed_percent").css('display','block');
        }
        
        param1 			          = new Object();
        param1.act                 = 'get_agreement';
    	param1.loan_agreement_type = $(this).val();
        $.ajax({
            url: aJaxURL,
    	    data: param1,
            success: function(data) {
            	if(typeof(data.error) != "undefined"){
					if(data.error != ""){
						alert(data.error);
					}else{
						$("#agreement_type_id").html(data.page).trigger("chosen:updated");
					}
				}
    	    }
        });
        
        param 			          = new Object();
        param.act                 = 'get_default';
    	param.loan_agreement_type = $(this).val();
    	param.agreement_type_id   = $("#agreement_type_id").val();
    	
    	if(param.agreement_type_id!=0 && $("#id_hidden").val() == ''){
        	$.ajax({
                url: aJaxURL,
        	    data: param,
                success: function(data) {
                	if(typeof(data.error) != "undefined"){
    					if(data.error != ""){
    						alert(data.error);
    					}else{
    						$("#month_percent").val(data.percent);
    						$("#loan_fee").val(data.loan_fee);
    						$("#proceed_fee").val(data.proceed_fee);
    						$("#proceed_percent").val(data.proceed_percent);
    						$("#rs_message_number").val(data.rs_message_number);
    						$("#penalty_days").val(data.penalty_days);
    						$("#penalty_percent").val(data.penalty_percent);
    						$("#penalty_additional_percent").val(data.penalty_additional_percent);
    					}
    				}
        	    }
            });
        }
    });

    $(document).on("change", "#Insured_yes_no", function () {
		if($(this).val() == 1){
			$(".insurance_table").css('display', 'block');
			$('#right_side').scrollTop(1000000);
		}else{
			$(".insurance_table").css('display', 'none');
		}
	});
    
	$(document).on("change", "#agreement_type_id", function () {

		if($(this).val() == 1 || $(this).val() == 4 || $(this).val() == 6 || $(this).val() == 8 || $(this).val() == 9 || $(this).val() == 11){
			$("#rs_message_number").css('display', 'block');
			$(".rs_message_number").css('display', 'block');
		}else{
			$("#rs_message_number").css('display', 'none');
			$(".rs_message_number").css('display', 'none');
		}
		
		param 			          = new Object();
		
        param.act                 = 'get_default';
    	param.agreement_type_id   = $(this).val();
    	param.loan_agreement_type = $("#loan_agreement_type").val();
    	
		if(param.loan_agreement_type!=0 && $("#id_hidden").val() == ''){
        	$.ajax({
                url: aJaxURL,
        	    data: param,
                success: function(data) {
                	if(typeof(data.error) != "undefined"){
    					if(data.error != ""){
    						alert(data.error);
    					}else{
    						$("#month_percent").val(data.percent);
    						$("#loan_fee").val(data.loan_fee);
    						$("#proceed_fee").val(data.proceed_fee);
    						$("#proceed_percent").val(data.proceed_percent);
    						$("#rs_message_number").val(data.rs_message_number);
    						$("#penalty_days").val(data.penalty_days);
    						$("#penalty_percent").val(data.penalty_percent);
    						$("#penalty_additional_percent").val(data.penalty_additional_percent);
    					}
    				}
        	    }
            });
        }
    });
    
    $(document).on("click", ".hide_said_menu", function () {
    	$("#right_side fieldset").hide();    	
    	$(".add-edit-form-class").css("width", "290");
        hide_right_side();
    });

    $(document).on("click", "#add_doc", function () {
    	
		
		param          = new Object();
		
		param.act      = 'get_other_doc';
		param.local_id = $("#local_id").val();
		
    	$.ajax({
            url: aJaxURL,
    	    data: param,
            success: function(data) {
            	if(typeof(data.error) != "undefined"){
					if(data.error != ""){
						alert(data.error);
					}else{
						$("#add-edit-form-other_doc").html(data.page);
						var buttons = {
					    	       "save": {
						            text: "დამატება",
						            id: "add_other_docs"
						        },
					        	"cancel": {
						            text: "დახურვა",
						            id: "cancel-dialog",
						            click: function () {
						            	$(this).dialog("close");
						            }
						        }
						    };
						GetDialog("add-edit-form-other_doc", 575, "auto", buttons, 'left+43 top');
					}
				}
    	    }
        });
    	
    });

    $(document).on("click", "#add_other_docs", function () {
    	param     = new Object();
		param.act = 'save_other_doc';
		
		param.local_id                      = $("#local_id").val();
		
		param.receipt_check                 = $("input[id='receipt_check']:checked").val();
		param.acceptance_act_check          = $("input[id='acceptance_act_check']:checked").val();
		param.Client_car_confiscation_check = $("input[id='Client_car_confiscation_check']:checked").val();
		param.approval1_check               = $("input[id='approval1_check']:checked").val();
		param.execution_pickup_check        = $("input[id='execution_pickup_check']:checked").val();
		param.b_agreement_check             = $("input[id='b_agreement_check']:checked").val();
		param.registering_a_car_mogo_check  = $("input[id='registering_a_car_mogo_check']:checked").val();
		param.pledge_removal_check          = $("input[id='pledge_removal_check']:checked").val();
		param.add_car_driver_check          = $("input[id='add_car_driver_check']:checked").val();
		param.rename_payment_system_check   = $("input[id='rename_payment_system_check']:checked").val();
		param.attachment_check              = $("input[id='attachment_check']:checked").val();
		
    	$.ajax({
            url: aJaxURL,
    	    data: param,
            success: function(data) {
            	if(typeof(data.error) != "undefined"){
					if(data.error != ""){
						alert(data.error);
					}else{
						$("#documents_div1").html(data.page);
						CloseDialog("add-edit-form-other_doc");
					}
				}
    	    }
        });
    });
    
    $(document).on("click", "#show_copy_prit_exel", function () {
        if($(this).attr('myvar') == 0){
            $('.ColVis,.dataTable_buttons').css('display','block');
            $(this).css('background','#2681DC');
            $(this).children('img').attr('src','media/images/icons/select_w.png');
            $(this).attr('myvar','1');
        }else{
        	$('.ColVis,.dataTable_buttons').css('display','none');
        	$(this).css('background','#E6F2F8');
            $(this).children('img').attr('src','media/images/icons/select.png');
            $(this).attr('myvar','0');
        }
    });
    $(document).on("click", "#show_payment_schedule", function () {
    	file_type = 'payment_schedule';

    	if($("#loan_amount").val() == ''){
        	alert('შეავსეთ "სესხის სრული მოცულობა"');
        }else if($("#month_percent").val() == ''){
        	alert('შეავსეთ "ყოველთვიური პროცენტი"');
        }else if($("#loan_months").val() == ''){
        	alert('შეავსეთ "სესხის სარგებლობის ვადა"');
        }else if($("#loan_agreement_type").val() == 0){
        	alert('შეავსეთ "სესხის ტიპი"');
        }else{
        	$.ajax({
                url: aJaxURL,
                data: "act=show_document&file_type="+file_type+"&local_id="+$("#local_id").val()+"&id_hidden="+$("#id_hidden").val()+"&loan_amount="+$("#loan_amount").val()+"&month_percent="+$("#month_percent").val()+"&loan_months="+$("#loan_months").val()+"&loan_agreement_type="+$("#loan_agreement_type").val()+"&name="+$("#name").val()+"&surname="+$("#surname").val()+"&agreement_date="+$("#agreement_date").val(),
                success: function(data) {
                	$("#payment_schedule_td").html(data.documets_page);
                }
        	});
        }
    });

    $(document).on("click", "#hidde_payment_schedule", function () {
    	
       $("#payment_schedule_td").html('');
         
    });
	
    function show_document(file_type,file_name){
    	$.ajax({
            url: aJaxURL,
            data: "act=show_document&file_type="+file_type+"&local_id="+$("#local_id").val()+"&id_hidden="+$("#id_hidden").val()+"&loan_agreement_type="+$("#loan_agreement_type").val(),
            success: function(data) {
            	$("#add-edit-form-document").html(data.documets_page);

                var buttons = {
                    
            		"print": {
    		            text: "ბეჭდვა",
    		            id: "print-dialog",
    		            click: function () {
    		            	var local_id          = $("#local_id").val();
    		            	var acceptance_amount = $("#acceptance_amount").val();
    		            	
    		                local_id  = "&local_id="+local_id+"&file_type="+file_type+"&id_hidden="+$("#id_hidden").val()+"&acceptance_amount="+$("#acceptance_amount").val()+"&execution_pickup_datee="+$("#execution_pickup_datee").val();
    		        		win=window.open("server-side/operations/subtables/print_documents.action.php?"+local_id, "" , "scrollbars=no,toolbar=no,screenx=0,screeny=0,location=no,titlebar=no,directories=no,status=no,menubar=no");
    		            }
    		        },
    	        	"download": {
    		            text: "ჩამოტვირთვა",
    		            id: "download-dialog",
    		            click: function () {
        		            if(file_type != 'payment_schedule'){
        		            	URL="server-side/operations/subtables/download_doc.php?file_type="+file_type+"&local_id="+$("#local_id").val()+"&file_name="+file_name+"&acceptance_amount="+$("#acceptance_amount").val()+"&execution_pickup_datee="+$("#execution_pickup_datee").val();
        		            	open(URL);
        		            }else{
            		            
        		            	parame 			  = new Object();
        		            	parame.local_id   = $("#local_id").val();
        		            	parame.file_type  = file_type;
        		            	
								$.ajax({
        		                    url: 'server-side/operations/subtables/excel.php',
        		            	    data: parame,
        		                    success: function(data) {
        		            	        if(data == 1){
        		            		        alert('ჩანაწერი არ მოიძებნა');
        		            	        }else{
        		                    		SaveToDisk('server-side/operations/subtables/excel.xls', 'excel.xls');
        		            	        }
        		            	    }
        		                });
            		        }
    		            }
    		        },
    		        "cancel": {
    		            text: "დახურვა",
    		            id: "cancel-dialog",
    		            click: function () {
    		            	$(this).dialog("close");
    		            }
    		        }
    		    };
               	GetDialog("add-edit-form-document", 1200, "auto", buttons, 'left+43 top');
               	GetDate('execution_pickup_datee');
               	$("#execution_pickup_datee").blur();
    		}
        });
    }
    function delete_document(value){
    	$.ajax({
            url: aJaxURL,
            data: "act=delete_other_document&local_id="+$("#local_id").val()+"&value="+value,
            success: function(data) {
            	$("#documents_div1").html(data.page);
    		}
        });
    }

    $(document).on("click", "#print_insurance", function () {
    	params = "&local_id="+$("#local_id").val()+"&file_type=car_insurance"+"&id_hidden="+$("#id_hidden").val();
		win = window.open("server-side/operations/subtables/print_documents.action.php?"+params, "" , "scrollbars=no,toolbar=no,screenx=0,screeny=0,location=no,titlebar=no,directories=no,status=no,menubar=no");
	});
	
    $(document).on("click", "#download_insurance", function () {
    	parame 			  = new Object();
    	parame.local_id   = $("#local_id").val();
    	parame.file_type  = 'download_insurance';
    	
		$.ajax({
            url: 'server-side/operations/subtables/excel.php',
    	    data: parame,
            success: function(data) {
    	        if(data == 1){
    		        alert('ჩანაწერი არ მოიძებნა');
    	        }else{
            		SaveToDisk('server-side/operations/subtables/excel.xls', 'excel.xls');
    	        }
    	    }
        });
	});
	//სურატები//
    $(document).on("click", "#choose_button", function () {
	    $("#choose_file").click();
	});

    $(document).on("change", "#choose_file", function () {
    	
	    
        var file_url  = $(this).val();
        var file_name = this.files[0].name;
        var file_size = this.files[0].size;
        var file_type = file_url.split('.').pop().toLowerCase();
        var path	  = "../../media/uploads/file/";

        if($.inArray(file_type, ['png','jpg']) == -1){
            alert("დაშვებულია მხოლოდ 'png', 'jpg'  გაფართოება");
        }else if(file_size > '15728639'){
            alert("ფაილის ზომა 15MB-ზე მეტია");
        }else{
            if($("#pers_id").val() == ''){
	            users_id = $("#is_user").val();
            }else{
            	users_id = $("#pers_id").val()
            }
        	$.ajaxFileUpload({
		        url: "server-side/upload/file.action.php",
		        secureuri: false,
     			fileElementId: "choose_file",
     			dataType: 'json',
			    data: {
					act: "file_upload",
					button_id: "choose_file",
					file_name: Math.ceil(Math.random()*99999999999),
					file_name_original: file_name,
					table_name: 'car_picture',
					file_type: file_type,
					file_size: file_size,
					path: path,
					table_id: $("#local_id").val(),

				},
		        success: function(data) {			        
			        if(typeof(data.error) != 'undefined'){
						if(data.error != ''){
							alert(data.error);
						}else{
							
						}						
					}					
			    }
			});
        	setTimeout(function(){
            	$.ajax({
        	        url: aJaxURL,
        		    data: {
        				act: "upload_picture",
        				local_id: $("#local_id").val(),
    				},
        	        success: function(data) {
        		        $("#img_colum").html(data.str_file_picture);
        		    }
        	    });
        	}, 500);
        }
    });
    function view_image(id){
		param = new Object();

        //Action
    	param.act	= "view_img";
    	param.id    = id;
    	
		$.ajax({
	        url: aJaxURL,
		    data: param,
	        success: function(data) {
				if(typeof(data.error) != "undefined"){
					if(data.error != ""){
						alert(data.error);
					}else{
						var buttons = {
					        	"cancel": {
						            text: "დახურვა",
						            id: "cancel-dialog",
						            click: function () {
						            	$(this).dialog("close");
						            }
						        }
						    };
						GetDialog("add-edit-form-img", 401, "auto", buttons, 'center top');
						$("#add-edit-form-img").html(data.page);
					}
				}
		    }
	    });
	}

    $(document).on("click", "#delete_image", function () {
	    $.ajax({
            url: aJaxURL,
            data: "act=delete_image&image_id="+$(this).attr('image_id') + "&local_id="+$("#local_id").val(),
            success: function(data) {
            	if(typeof(data.error) != "undefined"){
            		if(data.error != ""){
						alert(data.error);
					}else{
						$("#img_colum").html(data.str_file_picture);
					}
                }
            }
        });
	});

	
	//საბუთები//
    $(document).on("click", "#file_upload", function () {
	    $('#file_name1').click();
	});
	
	
    $(document).on("change", "#file_name1", function () {
        var file_url  = $(this).val();
        var file_name = this.files[0].name;
        var file_size = this.files[0].size;
        var file_type = file_url.split('.').pop().toLowerCase();
        var path	  = "../../media/uploads/file/";
        
		if($.inArray(file_type, ['pdf','png','xls','xlsx','jpg','docx','doc','csv']) == -1){
			alert("დაშვებულია მხოლოდ 'pdf', 'png', 'xls', 'xlsx', 'jpg', 'docx', 'doc', 'csv' გაფართოება");
        }else if(file_size > '15728639'){
            alert("ფაილის ზომა 15MB-ზე მეტია");
        }else{
        	$.ajaxFileUpload({
		        url: "server-side/upload/file.action.php",
		        secureuri: false,
     			fileElementId: "file_name1",
     			dataType: 'json',
			    data: {
					act: "file_upload",
					button_id: "file_name1",
					table_name: 'client_papers',
					file_name: Math.ceil(Math.random()*99999999999),
					file_name_original: file_name,
					file_type: file_type,
					file_size: file_size,
					path: path,
					table_id: $("#local_id").val(),

				},
				success: function(data) {
					
					if(typeof(data.error) != 'undefined'){
						if(data.error != ''){
							alert(data.error);
						}else{
							$("#paste_files1").html(data.page);
						}						
					}					
			    }
		    });
        	setTimeout(function(){
            	$.ajax({
        	        url: aJaxURL,
        		    data: {
        				act: "upload_papers",
        				local_id: $("#local_id").val(),
    
        			},
        	        success: function(data) {
        		        $("#paste_files1").html(data.papers);
        		    }
        	    });
        	}, 500);
        }
    });
    
    function download_file(file){
        var download_file	= "media/uploads/file/"+file;
    	var download_name 	= file;
    	SaveToDisk(download_file, download_name);
    }
    
    function delete_file(id, table_id){
    	$.ajax({
            url: "server-side/upload/file.action.php",
            data: "act=delete_file&file_id="+id+"&local_id="+$("#local_id").val()+"&table_id="+table_id,
            success: function(data) {
                if(table_id == 'client_papers'){
                	$("#paste_files1").html(data.documets);
                }
               	
			}
        });
    }
    
	function SaveToDisk(fileURL, fileName) {
		 var hyperlink = document.createElement('a');
		    hyperlink.href = fileURL;
		    hyperlink.target = '_blank';
		    hyperlink.download = fileName || fileURL;

		    (document.body || document.documentElement).appendChild(hyperlink);
		    hyperlink.onclick = function() {
		       (document.body || document.documentElement).removeChild(hyperlink);
		    };

		    var mouseEvent = new MouseEvent('click', {
		        view: window,
		        bubbles: true,
		        cancelable: true
		    });

		    hyperlink.dispatchEvent(mouseEvent);
		    if(!navigator.mozGetUserMedia) { // i.e. if it is NOT Firefox
		       window.URL.revokeObjectURL(hyperlink.href);
		    }
	    }

</script>
</head>
<body>
    <div id="tabs" style="width: 95%;">
        <div class="callapp_head">ავტო ლომბარდი<span class="callapp_refresh"><img alt="refresh" src="media/images/icons/refresh.png" height="14" width="14">   განახლება</span><hr class="callapp_head_hr"></div>
        <div class="callapp_tabs"></div>
    	<button id="add_button" style="float: left;margin-bottom: 10px;">დამატება</button>
    	<button id="delete_button" style="float: left;margin-bottom: 10px;margin-left: 10px;">გაუქმება</button>
        <table id="table_right_menu">
            <tr>
                <td>
                	<img alt="table" src="media/images/icons/table_w.png" height="14" width="14">
                </td>
                <td>
                	<img alt="log" src="media/images/icons/log.png" height="14" width="14">
                </td>
                <td id="show_copy_prit_exel" myvar="0">
                	<img alt="link" src="media/images/icons/select.png" height="14" width="14">
                </td>
            </tr>
        </table>
    	<table class="display" id="table_index" style="width: 100%;">
            <thead>
                <tr id="datatable_header">
                    <th>ID</th>
                    <th style="width: 46px;">№</th>
                    <th style="width: 13%;">თარიღი</th>
                    <th style="width: 23%;">სახელი გვარი</th>
                    <th style="width: 13%;">პირადი ნომერი</th>            
                    <th style="width: 13%;">ტელეფონი</th>
                    <th style="width: 13%;">ხელშეკრულების ნომერი</th>
                    <th style="width: 13%;">კოდი</th>
                    <th style="width: 12%;">სტატუსი</th>
                    <th style="width: 25px;">#</th>
                </tr>
            </thead>
            <thead>
                <tr class="search_header">
                    <th class="colum_hidden">
                	   <input type="text" name="search_id" value="ფილტრი" class="search_init" />
                    </th>
                    <th>
                    	<input type="text" name="search_number" value="ფილტრი" class="search_init" />
                    </th>
                    <th>
                        <input type="text" name="search_date" value="ფილტრი" class="search_init" />
                    </th>    
                    <th>
                        <input type="text" name="search_date" value="ფილტრი" class="search_init" />
                    </th>
                    <th>
                        <input type="text" name="search_date" value="ფილტრი" class="search_init" />
                    </th>    
                    <th>
                        <input type="text" name="search_date" value="ფილტრი" class="search_init" />
                    </th>
                    <th>
                        <input type="text" name="search_date" value="ფილტრი" class="search_init" />
                    </th>                         
                    <th>
                        <input type="text" name="search_date" value="ფილტრი" class="search_init" />
                    </th>
                    <th>
                        <input type="text" name="search_date" value="ფილტრი" class="search_init" />
                    </th>
                    <th style="border-right: 1px solid #A3D0E4;" >
                        <div class="callapp_checkbox">
                            <input type="checkbox" id="check-all" name="check-all" />
                            <label for="check-all"></label>
                        </div>
                    </th>            
                </tr>
            </thead>
        </table>
    
    <div id="add-edit-form" class="form-dialog" title="ავტო ლომბარდი"></div>
    <div id="add-edit-form-pers" class="form-dialog" title="საკონტაქტო პირი"></div>
    <div id="add-edit-form-document" class="form-dialog" title="დოკუმენტი"></div>
    <div id="add-edit-form-car_driver" class="form-dialog" title="პირი, რომელიც მართავს მანქანას"></div>
    <div id="add-edit-form-img" class="form-dialog" title="ავტომობილის სურათი"></div>
    <div id="add-edit-form-guarantors" class="form-dialog" title="თავდები პირი"></div>
    <div id="add-edit-form-other_doc" class="form-dialog" title="დამატებითი საბუტები"></div>
    <div id="add-edit-form-update_loan" class="form-dialog" title="ავტო ლომბარდი"></div>
    <div id="add-edit-form-calculation" class="form-dialog" title="წინასწარი კალკულაცია"></div>
    <div id="add-edit-form-canceled" class="form-dialog" title="დაანგარიშებული თანხა"></div>
</body>