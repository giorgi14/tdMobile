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
    var aJaxURL                  = "server-side/operations/client.action.php";
    var aJaxURL_cl_person        = "server-side/operations/subtables/client_person.action.php";
    var aJaxURL_cl_car_driver    = "server-side/operations/subtables/client_car_drivers.action.php";
    var aJaxURL_cl_car_insurance = "server-side/operations/subtables/client_car_insurance.action.php";
    var aJaxURL_cl_guarantors    = "server-side/operations/subtables/client_guarantors.action.php";
    var aJaxURL_b_letters        = "server-side/operations/subtables/b_letters.action.php";
    var aJaxURL_sms_histori      = "server-side/operations/sms.action.php";
    var aJaxURL_show_letter      = "server-side/main.action.php";
    var tName                    = "table_";
    var dialog                   = "add-edit-form";
    var colum_number             = 10;
    var main_act                 = "get_list";
    var change_colum_main        = "<'dataTable_buttons'T><'F'Cfipl>";
     
    $(document).ready(function () {
    	GetButtons("add_button","delete_button");
    	$("#b_letter").button();
    	LoadTable('index',colum_number,main_act,change_colum_main,aJaxURL,'','');
    	SetEvents("add_button", "delete_button", "check-all", tName+'index', dialog, aJaxURL,'','index',colum_number,main_act,change_colum_main,aJaxURL,'');

    });

    function LoadTable(tbl,col_num,act,change_colum,URL,leng,dataparam,total){

    	if(dataparam == undefined){dataparam = leng;}
    	if(tbl == 'person' || tbl == 'cardrivers' || tbl == 'guarantors' || tbl == 'sms_histori'){dataparam = 'local_id='+$("#local_id").val();}
    	GetDataTable(tName+tbl,URL,act,col_num,dataparam,0,"",3,"desc",total,change_colum);
    	$("#table_person_length").css('top', '2px');
    	$("#table_cardrivers_length").css('top', '2px');
    	$("#table_guarantors_length").css('top', '2px');
    	$("#table_b_letter_length").css('top', '2px');
    	setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 50);
    }

    function LoadDialog(fName){
        if(fName == 'add-edit-form'){
        	var buttons = {
        			"show_letter": {
    		            text: "ბარათის ნახვა",
    		            id: "show_letter",
    		            click: function () {
    		            	param 	  = new Object();
    		        		param.act = "get_edit_page";
    		        		param.id  = $("#id_hidden").val();
    		        		
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
        			"client_new_loan": {
    		            text: "დანართის გაკეთება",
    		            id: "client_new_loan",
    		            click: function () {
    		            	param 	       = new Object();
    		        		param.act      = "get_new_dialog";
    		        		param.hidde_id = $("#id_hidden").val();
    		        		
    		        		$.ajax({
    		        	        url: aJaxURL,
    		        		    data: param,
    		        	        success: function(data) {       
    		        				if(typeof(data.error) != "undefined"){
    		        					if(data.error != ""){
    		        						alert(data.error);
    		        					}else{
    		        						$("#add-edit-new_loan").html(data.page);
    		        						var buttons = {
		        			    				"save": {
		        			    		            text: "შენახვა",
		        			    		            id: "save-new_loan"
		        			    		        },
		        			    	        	"cancel": {
		        			    		            text: "დახურვა",
		        			    		            id: "cancel-dialog",
		        			    		            click: function () {
		        			    		            	$(this).dialog("close");
		        			    		            }
		        			    		        }
		        			    		    };
    		        			            GetDialog("add-edit-new_loan", 915, "auto", buttons, 'left+43 top');
    		        			            $("#new_loan_currency").chosen();
    		        			            $("#new_loan_agreement_type").chosen();
    		        			            $("#new_responsible_user_id").chosen();
    		        			            $("#new_agreement_type_id").chosen();
    		        			            $("#check_new_monthly_pay").button();
    		        			            get_local_id('client', '2');
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
		        			    		            text: "რესტრუქტურიზაცია",
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
    		        			            $("#sub_loan_agreement_type").chosen();
    		        			            $("#sub_agreement_type_id").chosen();
    		        			            $("#sub_loan_currency").chosen();
    		        			            get_local_id('client', '1');
    		        			            $('#add-edit-form-update_loan, .add-edit-form-update_loan-class').css('overflow','visible');
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
				$("#client_new_loan").button("disable");
            }else{
                if($("#hidde_status").val()==1){
                	$("#activate-dialog").button("disable");
    				$("#save-dialog").button("disable");
    				$("#update-loan").button("enable");
    				$("#show_letter").button("enable");
    				$('#add-edit-form, .idle').attr('disabled', true);
    				$('#loan_agreement_type').prop('disabled', true).trigger("chosen:updated");
    				$('#agreement_type_id').prop('disabled', true).trigger("chosen:updated");
    				$('#car_type').prop('disabled', true).trigger("chosen:updated");
    				$("#client_new_loan").button("enable");
                }else if($("#hidde_status").val()==0){
    				$("#update-loan").button("disable");
    				$("#client_new_loan").button("disable");
    				$("#show_letter").button("disable");
                }
            }

            if($("#hidde_canceled_status").val()==1){
				$("#update-loan").button("disable");
				$("#client_new_loan").button("disable");
            }
            
            if($("#car_insurance_info_hidde").val()==''){
            	$("#print_insurance").button("disable");
            	$("#download_insurance").button("disable");
            }
            $("#tld_responsible").chosen();
            if($("input[class=client_type]:checked").val() == 2){
                $("#tld_responsible_chosen").css('display', 'block');
                $("#tld_responsible_chosen").css('width', '180px');
            }else{
            	$("#tld_responsible_chosen").css('display', 'none');
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

            setTimeout(function(){
         		LoadTable('sms_histori',4,'get_list_sms_histori',"<'F'Cpl>",aJaxURL, '', 'local_id='+$("#local_id").val());
         		$("#table_sms_histori_length").css('top', '2px');
         		SetEvents("", "", "", tName+'sms_histori', 'add-edit-form_sms_histori', aJaxURL_sms_histori);
         	}, 50);

            setTimeout(function(){
         		LoadTable('car_insurance',5,'get_list',"<'F'Cpl>",aJaxURL_cl_car_insurance, '', 'local_id='+$("#local_id").val());
         		$("#table_car_insurance_length").css('top', '2px');
         		GetButtons("add_button_car_insurance","delete_button_car_insurance");
         		SetEvents("add_button_car_insurance", "delete_button_car_insurance", "check-all_car_insurance", tName+'car_insurance', 'add-edit-form_car_insurance', aJaxURL_cl_car_insurance,'','car_insurance',5,main_act,"<'F'Cpl>",aJaxURL_cl_car_insurance,'');
         	}, 50);
         	
        }else if(fName == 'add-edit-form_sms_histori'){
     		var buttons = {
    	        	"cancel": {
    		            text: "დახურვა",
    		            id: "cancel-dialog",
    		            click: function () {
    		            	$(this).dialog("close");
    		            }
    		        }
    		    };
                GetDialog("add-edit-form_sms_histori", 760, "auto", buttons, 'left+43 top');
                $("#get_number").css('display', 'none');
                
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
        }else if(fName == 'add-edit-b_letter1'){
        	var buttons = {
        			"show-b_letter": {
    		            text: "ჩვენება",
    		            id: "show-b_letter"
    		        },
    				"save": {
    		            text: "შენახვა",
    		            id: "save-b_letter"
    		        },
    		        "save-print": {
    		            text: "შენახვა+ბეჭდვა",
    		            id: "save-print"
    		        },
    		        "save-download": {
    		            text: "შენახვა+ჩამოტვირთვა",
    		            id: "save-download"
    		        },
    	        	"cancel": {
    		            text: "დახურვა",
    		            id: "cancel-dialog",
    		            click: function () {
    		            	$(this).dialog("close");
    		            }
    		        }
    		    };
                GetDialog("add-edit-b_letter1", 930, "auto", buttons, 'left+43 top');
                $("#client_agr_car_mark").chosen();
                $("#b_letter_responsible_id").chosen();
                $('#add-edit-b_letter1, .add-edit-b_letter1-class').css('overflow','visible');
                $('#add-edit-b_letter1, .add-edit-b_letter1-class').css('min-height','275px');

                if($("#b_letter_hidde").val()!=''){
                    $("#show-b_letter").css('display', '');
                }else{
                	$("#show-b_letter").css('display', 'none');
                }
         }else if(fName == 'add-edit-form_car_insurance'){
        	var buttons = {
        			"save": {
    		            text: "შენახვა",
    		            id: "save-car_insurance"
    		        },
    		        "save-print": {
    		            text: "შენახვა+ბეჭდვა",
    		            id: "save-print-car_insurance"
    		        },
    		        "save-download": {
    		            text: "შენახვა+ჩამოტვირთვა",
    		            id: "save-download-car_insurance"
    		        },
    	        	"cancel": {
    		            text: "დახურვა",
    		            id: "cancel-dialog",
    		            click: function () {
    		            	$(this).dialog("close");
    		            }
    		        }
    		    };
                GetDialog("add-edit-form_car_insurance", 490, "auto", buttons, 'left+43 top');
                $("#client_agr_car_mark").chosen();
                $("#b_letter_responsible_id").chosen();

                GetDateTimes('car_insurance_start');
                GetDateTimes('car_insurance_end');
                $('#add-edit-b_letter1, .add-edit-b_letter1-class').css('overflow','visible');
                $('#add-edit-b_letter1, .add-edit-b_letter1-class').css('min-height','275px');

                if($("#b_letter_hidde").val()!=''){
                    $("#show-b_letter").css('display', '');
                }else{
                	$("#show-b_letter").css('display', 'none');
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
						}else if(status == 2){
							$("#local_new_id").val(data.local_id);
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

    		param.local_id            = $("#local_id").val();
    		param.loan_agreement_type = $("#loan_agreement_type").val();
    		param.agreement_type_id   = $("#agreement_type_id").val();
    		param.loan_currency       = $("#loan_currency").val();
    		param.hidde_attachment_id = $("#hidde_attachment_id").val();
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
        $(".sms_histori").children('img').attr('src','media/images/icons/sms_histori.png');
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
		param.tld_responsible     = $("#tld_responsible").val();
		param.client_type         = $("input[class=client_type]:checked").val();
		param.trust_pers_checkbox = $("input[id='trust_pers_checkbox']:checked").val();
		param.client_sms          = $("input[id='client_sms']:checked").val();
		
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

		param.client_trust_pid_number   = $('#client_trust_pid_number').val();
		param.client_trust_pid_date     = $('#client_trust_pid_date').val();
		param.client_trust_born_date    = $('#client_trust_born_date').val();
		param.client_trust_sms          = $("input[id='client_trust_sms']:checked").val();

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
		}else if(param.phone.length != 12){
			alert('შეავსეთ "ტელეფონი" სწორი ფორმატით');
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
		}else if(param.trust_pers_checkbox == 1 && param.client_trust_phone.length != 12){
			alert('შეავსეთ "მ/პ ტელეფონი" სწორი ფორმატით');
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
		param.tld_responsible     = $("#tld_responsible").val();
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

		param.client_trust_pid_number   = $('#client_trust_pid_number').val();
		param.client_trust_pid_date     = $('#client_trust_pid_date').val();
		param.client_trust_born_date    = $('#client_trust_born_date').val();

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
		param.agreement_type_id           = $('#sub_agreement_type_id').val();
		param.loan_agreement_type	      = $('#sub_loan_agreement_type').val();
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
		param.loan_currency               = $('#sub_loan_currency').val();
		param.oris_code                   = $('#oris_code').val();
		param.loan_beforehand_percent     = $('#loan_beforehand_percent').val();
		param.responsible_user_id         = $('#responsible_user_id').val();
		
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

    $(document).on("click", "#save-new_loan", function () {
		   
		param 			= new Object();
		param.act		= "save_new_loan";

		param.local_id	          = $('#local_id').val();
		param.local_new_id	      = $('#local_new_id').val();
		
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
		param.tld_responsible     = $("#tld_responsible").val();
		param.client_type         = $("input[class=client_type]:checked").val();
		param.trust_pers_checkbox = $("input[id='new_trust_pers_checkbox']:checked").val();
		
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

		param.client_trust_pid_number   = $('#client_trust_pid_number').val();
		param.client_trust_pid_date     = $('#client_trust_pid_date').val();
		param.client_trust_born_date    = $('#client_trust_born_date').val();

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
		param.agreement_type_id           = $('#new_agreement_type_id').val();
		param.loan_agreement_type	      = $('#new_loan_agreement_type').val();
		param.agreement_number	          = $('#agreement_number').val();
		param.agreement_date	          = $('#agreement_date').val();
		param.loan_amount	              = $('#new_loan_amount').val();
		param.loan_months                 = $('#new_loan_month').val();
		param.insurance_fee               = $('#insurance_fee').val();
		param.pledge_fee                  = $('#pledge_fee').val();
		param.month_percent               = $('#new_month_percent').val();
		param.monthly_pay                 = $('#new_monthly_pay').val();
		param.rs_message_number           = $('#rs_message_number').val();
		param.pay_day                     = $('#pay_day').val();
		param.exchange_rate               = $('#exchange_rate').val();
		param.penalty_days                = $('#new_penalty_days').val();
		param.penalty_percent             = $('#new_penalty_percent').val();
		param.penalty_additional_percent  = $('#new_penalty_additional_percent').val();
		param.loan_fee                    = $('#new_loan_fee').val();
		param.proceed_fee                 = $('#proceed_fee').val();
		param.proceed_percent             = $('#proceed_percent').val();
		param.loan_currency               = $('#new_loan_currency').val();
		param.oris_code                   = $('#oris_code').val();
		param.loan_beforehand_percent     = $('#new_loan_beforehand_percent').val();
		param.responsible_user_id         = $('#new_responsible_user_id').val();
		param.new_attachment_number       = $('#new_attachment_number').val();
		
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

    $(document).on("click", "#save-b_letter", function () {
		param 	  = new Object();
		param.act = "save_b_letter";
		
		param.b_letter_hidde	               = $('#b_letter_hidde').val();
		param.client_agr_car_mark	           = $('#client_agr_car_mark').val();
		param.buyer_name	                   = $("#buyer_name").val();
		param.buyer_pid	                       = $('#buyer_pid').val();
		param.b_letter_car_mark                = $('#b_letter_car_mark').val();
		param.b_letter_car_id	               = $('#b_letter_car_id').val();
		param.b_letter_manufactur_date	       = $("#b_letter_manufactur_date").val();
		param.b_letter_car_color	           = $('#b_letter_car_color').val();
		param.b_letter_car_registracion_number = $('#b_letter_car_registracion_number').val();
		param.b_letter_car_selling_price	   = $('#b_letter_car_selling_price').val();
		param.b_letter_amount	               = $("#b_letter_amount").val();
		param.b_letter_payment_date	           = $('#b_letter_payment_date').val();
		param.b_letter_responsible_id          = $('#b_letter_responsible_id').val();
		
		$.ajax({
	        url: aJaxURL_b_letters,
		    data: param,
	        success: function(data) {       
				if(typeof(data.error) != "undefined"){
					if(data.error != ""){
						alert(data.error);
					}else{
						$("#add-edit-b_letter1").dialog("close");
						LoadTable('b_letter', 8, main_act, "<'F'Cpl>", aJaxURL_b_letters, '', '');
						$("#table_b_letter_length").css('top', '2px');
					}
				}
	    	}
	   });
	});

    $(document).on("click", "#save-print", function () {
		param 	  = new Object();
		param.act = "save_b_letter";
		
		param.b_letter_hidde	               = $('#b_letter_hidde').val();
		param.client_agr_car_mark	           = $('#client_agr_car_mark').val();
		param.buyer_name	                   = $("#buyer_name").val();
		param.buyer_pid	                       = $('#buyer_pid').val();
		param.b_letter_car_mark                = $('#b_letter_car_mark').val();
		param.b_letter_car_id	               = $('#b_letter_car_id').val();
		param.b_letter_manufactur_date	       = $("#b_letter_manufactur_date").val();
		param.b_letter_car_color	           = $('#b_letter_car_color').val();
		param.b_letter_car_registracion_number = $('#b_letter_car_registracion_number').val();
		param.b_letter_car_selling_price	   = $('#b_letter_car_selling_price').val();
		param.b_letter_amount	               = $("#b_letter_amount").val();
		param.b_letter_payment_date	           = $('#b_letter_payment_date').val();
		param.b_letter_responsible_id          = $('#b_letter_responsible_id').val();
		
		$.ajax({
	        url: aJaxURL_b_letters,
		    data: param,
	        success: function(data) {       
				if(typeof(data.error) != "undefined"){
					if(data.error != ""){
						alert(data.error);
					}else{
						$("#add-edit-b_letter1").dialog("close");
						LoadTable('b_letter', 8, main_act, "<'F'Cpl>", aJaxURL_b_letters, '', '');
						$("#table_b_letter_length").css('top', '2px');
						
						params  = "&file_type=b_agreement"+"&b_letter_hidde="+data.b_letter_id;
		        		win=window.open("server-side/operations/subtables/print_documents.action.php?"+params, "" , "scrollbars=no,toolbar=no,screenx=0,screeny=0,location=no,titlebar=no,directories=no,status=no,menubar=no");
					}
				}
	    	}
	   });
	});

    $(document).on("click", "#save-download", function () {
		param 	  = new Object();
		param.act = "save_b_letter";
		
		param.b_letter_hidde	               = $('#b_letter_hidde').val();
		param.client_agr_car_mark	           = $('#client_agr_car_mark').val();
		param.buyer_name	                   = $("#buyer_name").val();
		param.buyer_pid	                       = $('#buyer_pid').val();
		param.b_letter_car_mark                = $('#b_letter_car_mark').val();
		param.b_letter_car_id	               = $('#b_letter_car_id').val();
		param.b_letter_manufactur_date	       = $("#b_letter_manufactur_date").val();
		param.b_letter_car_color	           = $('#b_letter_car_color').val();
		param.b_letter_car_registracion_number = $('#b_letter_car_registracion_number').val();
		param.b_letter_car_selling_price	   = $('#b_letter_car_selling_price').val();
		param.b_letter_amount	               = $("#b_letter_amount").val();
		param.b_letter_payment_date	           = $('#b_letter_payment_date').val();
		param.b_letter_responsible_id          = $('#b_letter_responsible_id').val();
		
		$.ajax({
	        url: aJaxURL_b_letters,
		    data: param,
	        success: function(data) {       
				if(typeof(data.error) != "undefined"){
					if(data.error != ""){
						alert(data.error);
					}else{
						$("#add-edit-b_letter1").dialog("close");
						LoadTable('b_letter', 8, main_act, "<'F'Cpl>", aJaxURL_b_letters, '', '');
						$("#table_b_letter_length").css('top', '2px');

						URL="server-side/operations/subtables/download_doc.php?file_type=b_agreement"+"&b_letter_hidde="+data.b_letter_id+"&file_name=ბეს შეთანხმება";
		            	open(URL);
					}
				}
	    	}
	   });
	});
	
	$(document).on("click", "#show-b_letter", function () {
		param 	             = new Object();
		param.act            = "show_document";
		param.b_letter_hidde = $('#b_letter_hidde').val();
		param.file_type      = 'b_agreement';
		$.ajax({
	        url: aJaxURL,
		    data: param,
	        success: function(data) {       
				if(typeof(data.error) != "undefined"){
					if(data.error != ""){
						alert(data.error);
					}else{
						$("#add-edit-b_letter_show").html(data.documets_page);

						var buttons = {
				    	       "print_b_agreement": {
					            	text: "ბეჭდვა",
					            	id: "print_b_agreement",
					            	click: function () {
					            		params  = "&file_type=b_agreement"+"&b_letter_hidde="+$("#b_letter_show_id").val();
						        		win=window.open("server-side/operations/subtables/print_documents.action.php?"+params, "" , "scrollbars=no,toolbar=no,screenx=0,screeny=0,location=no,titlebar=no,directories=no,status=no,menubar=no");
						            }
					           },
					           "download_b_agreement": {
					              	text: "ჩამოტვირთვა",
					              	id: "download_b_agreement",
					              	click: function () {
					              		URL="server-side/operations/subtables/download_doc.php?file_type=b_agreement"+"&b_letter_hidde="+$("#b_letter_show_id").val()+"&file_name=ბეს შეთანხმება";
						            	open(URL);
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
						GetDialog("add-edit-b_letter_show", 1159, "auto", buttons, 'left+43 top');
					}
				}
	    	}
	   });
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

    $(document).on("click", "#check_new_monthly_pay", function () {
		param 	  = new Object();
		param.act = "check_monthly_pay";

		param.loan_amount	      = $('#new_loan_amount').val();
		param.month_percent	      = $("#new_month_percent").val();
		param.loan_months	      = $('#new_loan_month').val();
		param.loan_agreement_type = $('#new_loan_agreement_type').val();
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
    						$("#new_monthly_pay").val(data.monthly_pay);
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
		param.sms_sent_checkbox = $("input[id='sms_sent_person_checkbox']:checked").val();
		
		if(param.client_pers_phone.length!=12){
			alert('ნომერი არასწორი ფორმატითაა შეყვანილი');
		}else{
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
        }
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
		param.sms_sent_checkbox = $("input[id='sms_sent_checkbox']:checked").val();
		if(param.guarantor_phone.length!=12){
			alert('ნომერი არასწორი ფორმატითაა შეყვანილი');
		}else{	
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
        }
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
	
    $(document).on("click", "#save-car_insurance", function () {
		param 			= new Object();
		param.act		= "save_insurance_info";

		param.local_id 					  = $("#local_id").val();
		param.car_insurance_hidde	      = $("#car_insurance_hidde").val();
		
		param.car_loan_amount	          = $('#car_loan_amount').val();
		param.car_real_price	          = $('#car_real_price').val();
		param.car_ins_registration_number = $('#car_ins_registration_number').val();
		param.car_insurance_amount	      = $('#car_insurance_amount').val();
		param.car_insurance_start         = $('#car_insurance_start').val();
		param.car_insurance_end	          = $('#car_insurance_end').val();
		param.ins_payy	                  = $('#ins_payy').val();
		
		$.ajax({
	        url: aJaxURL_cl_car_insurance,
		    data: param,
	        success: function(data) {       
				if(typeof(data.error) != "undefined"){
					if(data.error != ""){
						alert(data.error);
					}else{
						CloseDialog("add-edit-form_car_insurance");
						LoadTable('car_insurance',5,main_act,"<'F'Cpl>",aJaxURL_cl_car_insurance,'','local_id='+$("#local_id").val());
						$("#table_car_insurance_length").css('top', '2px');
					}
				}
	    	}
	   });
	});

    $(document).on("click", "#save-print-car_insurance", function () {
    	param 			= new Object();
		param.act		= "save_insurance_info";

		param.local_id 					  = $("#local_id").val();
		param.car_insurance_hidde	      = $("#car_insurance_hidde").val();
		
		param.car_loan_amount	          = $('#car_loan_amount').val();
		param.car_real_price	          = $('#car_real_price').val();
		param.car_ins_registration_number = $('#car_ins_registration_number').val();
		param.car_insurance_amount	      = $('#car_insurance_amount').val();
		param.car_insurance_start         = $('#car_insurance_start').val();
		param.car_insurance_end	          = $('#car_insurance_end').val();
		param.ins_payy	                  = $('#ins_payy').val();
		
		$.ajax({
	        url: aJaxURL_cl_car_insurance,
		    data: param,
	        success: function(data) {       
				if(typeof(data.error) != "undefined"){
					if(data.error != ""){
						alert(data.error);
					}else{
						CloseDialog("add-edit-form_car_insurance");
						LoadTable('car_insurance',5,main_act,"<'F'Cpl>",aJaxURL_cl_car_insurance,'','local_id='+$("#local_id").val());
						$("#table_car_insurance_length").css('top', '2px');
						
						params  = "&file_type=car_insurance"+"&insurance_hidde="+data.insurance_id+"&local_id="+param.local_id;
		        		win=window.open("server-side/operations/subtables/print_documents.action.php?"+params, "" , "scrollbars=no,toolbar=no,screenx=0,screeny=0,location=no,titlebar=no,directories=no,status=no,menubar=no");
					}
				}
	    	}
	   });
	});

    $(document).on("click", "#save-download-car_insurance", function () {
    	param 	  = new Object();
		param.act = "save_insurance_info";

		param.local_id 					  = $("#local_id").val();
		param.car_insurance_hidde	      = $("#car_insurance_hidde").val();
		
		param.car_loan_amount	          = $('#car_loan_amount').val();
		param.car_real_price	          = $('#car_real_price').val();
		param.car_ins_registration_number = $('#car_ins_registration_number').val();
		param.car_insurance_amount	      = $('#car_insurance_amount').val();
		param.car_insurance_start         = $('#car_insurance_start').val();
		param.car_insurance_end	          = $('#car_insurance_end').val();
		param.ins_payy	                  = $('#ins_payy').val();
		
		$.ajax({
	        url: aJaxURL_cl_car_insurance,
		    data: param,
	        success: function(data) {       
				if(typeof(data.error) != "undefined"){
					if(data.error != ""){
						alert(data.error);
					}else{
						CloseDialog("add-edit-form_car_insurance");
						LoadTable('car_insurance',5,main_act,"<'F'Cpl>",aJaxURL_cl_car_insurance,'','local_id='+$("#local_id").val());
						$("#table_car_insurance_length").css('top', '2px');

						parame 			  = new Object();
		            	parame.local_id   = $("#local_id").val();
		            	parame.file_type  = 'download_insurance';
		            	parame.insurance_hidde=data.insurance_id;
		            	
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
    		$("#tld_responsible_chosen").css('display','none');
    		$(".hidde_label").css('display','none');
    	}else{
        	$("#ltd_name").css('display','block');
    		$("#ltd_id").css('display','block');
    		$("#tld_responsible_chosen").css('display','');
    		$("#tld_responsible_chosen").css('width','180px');
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

    $(document).on("click", "#new_trust_pers_checkbox", function () {

        
        if($(this).prop("checked") == false){
            $("#new_truste_table").css('display','none');
            $("#overflow_height").css('height', '332px');
       	}else{
       		$("#overflow_height").css('height', '540px');
       		$("#new_truste_table").css('display','block');
       		
        }
    });

    $(document).on("click", "#b_letter", function () {
        
        param1 			           = new Object();
        param1.act                 = 'get_b_letter';
    	param1.loan_agreement_type = $(this).val();
        $.ajax({
            url: aJaxURL,
    	    data: param1,
            success: function(data) {
            	if(typeof(data.error) != "undefined"){
					if(data.error != ""){
						alert(data.error);
					}else{
						$("#add-edit-b_letter").html(data.page);
						var buttons = {
			    				"cancel": {
			    		            text: "დახურვა",
			    		            id: "cancel-dialog",
			    		            click: function () {
			    		            	$(this).dialog("close");
			    		            }
			    		        }
			    		    };
    			            GetDialog("add-edit-b_letter", 1160, "auto", buttons, 'left+43 top');
    			            LoadTable('b_letter',8,main_act,"<'F'Cpl>",aJaxURL_b_letters, '', '');
    		         		$("#table_b_letter_length").css('top', '2px');
    		         		SetEvents("add_b_letter", "", "", tName+'b_letter', 'add-edit-b_letter1', aJaxURL_b_letters,'','b_letter',8,main_act,"<'F'Cpl>",aJaxURL_b_letters,'');
    		            	GetButtons("add_b_letter","");
					}
				}
    	    }
        });
	});
	
	$(document).on("change", "#sub_loan_agreement_type", function () {
        
        param1 			           = new Object();
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
						$("#sub_agreement_type_id").html(data.page).trigger("chosen:updated");
					}
				}
    	    }
        });
	});

	$(document).on("change", "#client_agr_car_mark", function () {
        
        param 			= new Object();
        param.act       ='get_client_car_mark';
    	param.client_id = $(this).val();
        $.ajax({
            url: aJaxURL_b_letters,
    	    data: param,
            success: function(data) {
            	if(typeof(data.error) != "undefined"){
					if(data.error != ""){
						alert(data.error);
					}else{
						$("#b_letter_car_mark").val(data.marc);
						$("#b_letter_car_id").val(data.car_id);
						$("#b_letter_manufactur_date").val(data.manufacturing_date);
						$("#b_letter_car_color").val(data.color);
						$("#b_letter_car_registracion_number").val(data.registration_number);
					}
				}
    	    }
        });
	});
	
	$(document).on("change", "#new_loan_agreement_type", function () {
        
        param1 			           = new Object();
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
						$("#new_agreement_type_id").html(data.page).trigger("chosen:updated");
					}
				}
    	    }
        });
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
		param.letter_debt_check             = $("input[id='letter_debt_check']:checked").val();
		
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
	
    function show_document(file_type,file_name,guarante_id){
    	$.ajax({
            url: aJaxURL,
            data: "act=show_document&file_type="+file_type+"&local_id="+$("#local_id").val()+"&id_hidden="+$("#id_hidden").val()+"&loan_agreement_type="+$("#loan_agreement_type").val()+"&guarante_id="+guarante_id,
            success: function(data) {
            	$("#add-edit-form-document").html(data.documets_page);
            	
            	var buttons = {
                    
            		"print": {
    		            text: "ბეჭდვა",
    		            id: "print-dialog",
    		            click: function () {
    		            	var local_id          = $("#local_id").val();
    		            	
    		            	var attachment_datee       = $("#attachment_datee").val();
    		            	var attachment_datee1      = $("#attachment_datee1").val();
    		            	var attachment_responsible = $("#attachment_responsible").val();
    		            	
    		            	var acceptance_act_sakomisio = $("#acceptance_act_sakomisio").val();
    		            	var acceptance_act_xelze     = $("#acceptance_act_xelze").val();
    		            	var acceptance_act_responses = $("#acceptance_act_responses").val();
    		            	
    		            	var client_debt_amount         = $("#client_debt_amount").val();
    		            	var client_debt_respons        = $("#client_debt_respons").val();
    		            	var client_debt_date           = $("#client_debt_date").val();
    		            	
    		            	var message_type               = $("#message_type").val();
    		            	var rename_payment_system_date = $("#rename_payment_system_date").val();
    		            	var added_respons              = $("#added_respons").val();
    		            	var pledge_removal_responsible = $("#pledge_removal_responsible").val();
    		            	var pledge_removal_date        = $("#pledge_removal_date").val();
    		            	
    		                local_id  = "&local_id="+local_id+"&file_type="+file_type+"&id_hidden="+$("#id_hidden").val()+"&execution_pickup_datee="+$("#execution_pickup_datee").val()+"&execution_pickup_datee1="+$("#execution_pickup_datee1").val()+"&client_car_driver_name="+$("#client_car_driver_name").val()+"&client_car_driver_datetime="+$("#client_car_driver_datetime").val()+'&registering_a_car_mogo_date='+$("#registering_a_car_mogo_date").val()+ '&registering_car_mogo_respons='+$("#registering_car_mogo_respons").val()+"&guarante_id="+guarante_id+"&message_type="+message_type+"&rename_payment_system_date="+rename_payment_system_date+"&added_respons="+added_respons+"&pledge_removal_responsible="+pledge_removal_responsible+"&pledge_removal_date="+pledge_removal_date+"&Client_car_confiscation_date="+$("#Client_car_confiscation_date").val()+"&client_debt_amount="+client_debt_amount+"&client_debt_respons="+client_debt_respons+"&client_debt_date="+client_debt_date+"&acceptance_act_sakomisio="+acceptance_act_sakomisio+"&acceptance_act_xelze="+acceptance_act_xelze+"&acceptance_act_responses="+acceptance_act_responses+"&attachment_datee="+attachment_datee+"&attachment_datee1="+attachment_datee1+"&attachment_responsible="+attachment_responsible;
    		        		win=window.open("server-side/operations/subtables/print_documents.action.php?"+local_id, "" , "scrollbars=no,toolbar=no,screenx=0,screeny=0,location=no,titlebar=no,directories=no,status=no,menubar=no");
    		            }
    		        },
    	        	"download": {
    		            text: "ჩამოტვირთვა",
    		            id: "download-dialog",
    		            click: function () {
        		            if(file_type != 'payment_schedule'){
        		            	var attachment_datee       = $("#attachment_datee").val();
        		            	var attachment_datee1      = $("#attachment_datee1").val();
        		            	var attachment_responsible = $("#attachment_responsible").val();
        		            	
        		            	var acceptance_act_sakomisio = $("#acceptance_act_sakomisio").val();
        		            	var acceptance_act_xelze     = $("#acceptance_act_xelze").val();
        		            	var acceptance_act_responses = $("#acceptance_act_responses").val();
        		            	
        		            	var client_debt_amount         = $("#client_debt_amount").val();
        		            	var client_debt_respons        = $("#client_debt_respons").val();
        		            	var client_debt_date           = $("#client_debt_date").val();
        		            	
        		            	var message_type               = $("#message_type").val();
        		            	var rename_payment_system_date = $("#rename_payment_system_date").val();
        		            	var added_respons              = $("#added_respons").val();
        		            	var pledge_removal_responsible = $("#pledge_removal_responsible").val();
        		            	var pledge_removal_date        = $("#pledge_removal_date").val();
        		            	
        		            	URL="server-side/operations/subtables/download_doc.php?file_type="+file_type+"&local_id="+$("#local_id").val()+"&file_name="+file_name+"&execution_pickup_datee="+$("#execution_pickup_datee").val()+"&execution_pickup_datee1="+$("#execution_pickup_datee1").val()+"&client_car_driver_name="+$("#client_car_driver_name").val()+"&client_car_driver_datetime="+$("#client_car_driver_datetime").val()+'&registering_a_car_mogo_date='+$("#registering_a_car_mogo_date").val()+'&registering_car_mogo_respons='+$("#registering_car_mogo_respons").val()+"&guarante_id="+guarante_id+"&message_type="+message_type+"&rename_payment_system_date="+rename_payment_system_date+"&added_respons="+added_respons+"&pledge_removal_responsible="+pledge_removal_responsible+"&pledge_removal_date="+pledge_removal_date+"&Client_car_confiscation_date="+$("#Client_car_confiscation_date").val()+"&client_debt_amount="+client_debt_amount+"&client_debt_respons="+client_debt_respons+"&client_debt_date="+client_debt_date+"&acceptance_act_sakomisio="+acceptance_act_sakomisio+"&acceptance_act_xelze="+acceptance_act_xelze+"&acceptance_act_responses="+acceptance_act_responses+"&attachment_datee="+attachment_datee+"&attachment_datee1="+attachment_datee1+"&attachment_responsible="+attachment_responsible;
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

    $(document).on("click", "#carsize", function () {
    	if($(this).is(':checked')){
        	$("#tech_test_price").css('display','');
        	$("#hidde_label").css('display','');
        	
    	}else{
    		$("#tech_test_price").css('display','none');
    		$("#hidde_label").css('display','none');
        }
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
    	<button id="b_letter" style="float: left;margin-bottom: 10px;margin-left: 10px;">ბეს შეთანხმება</button>
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
                    <th style="width: 16%;">სახელი გვარი</th>
                    <th style="width: 11%;">პირადი ნომერი</th>            
                    <th style="width: 11%;">ტელეფონი</th>
                    <th style="width: 11%;">ხელშეკრულების ნომერი</th>
                    <th style="width: 11%;">კოდი</th>
                    <th style="width: 12%;">სტატუსი</th>
                    <th style="width: 15%;">თანამშრომელი</th>
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
    <div id="add-edit-new_loan" class="form-dialog" title="ავტო ლომბარდი"></div>
    <div id="add-edit-b_letter" class="form-dialog" title="ბეს შეთანხმება"></div>
    <div id="add-edit-b_letter1" class="form-dialog" title="ბეს შეთანხმება"></div>
    <div id="add-edit-b_letter_show" class="form-dialog" title="ბეს შეთანხმება"></div>
    <div id="add-edit-show_letter" class="form-dialog" title="ბარათი"></div>
    <div id="add-edit-form_sms_histori" class="form-dialog" title="SMS"></div>
    <div id="add-edit-form_car_insurance" class="form-dialog" title="დაზღვევა"></div>
</body>