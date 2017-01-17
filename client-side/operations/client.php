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
    var aJaxURL           = "server-side/operations/client.action.php";
    var aJaxURL_cl_person = "server-side/operations/subtables/client_person.action.php";

    var tName             = "table_";
    var dialog            = "add-edit-form";
    var colum_number      = 8;
    var main_act          = "get_list";
    var change_colum_main = "<'dataTable_buttons'T><'F'Cfipl>";
     
    $(document).ready(function () {
    	GetButtons("add_button","delete_button");
    	LoadTable('index',colum_number,main_act,change_colum_main,aJaxURL,'','');
    	SetEvents("add_button", "delete_button", "check-all", tName+'index', dialog, aJaxURL,'','index',colum_number,main_act,change_colum_main,aJaxURL,'');

    });

    function LoadTable(tbl,col_num,act,change_colum,URL,leng,dataparam,total){

    	if(dataparam == undefined){dataparam = leng;}
    	if(tbl == 'person'){dataparam = 'local_id='+$("#local_id").val();}
    	GetDataTable(tName+tbl,URL,act,col_num,dataparam,0,"",1,"desc",total,change_colum);
    	$("#table_person_length").css('top', '2px');
    	setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 50);
    }

    function LoadDialog(fName){
        if(fName == 'add-edit-form'){
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
            GetDialog(fName, 575, "auto", buttons, 'left+43 top');
            GetDateTimes('datetime');
            $('.info').click();
            $('#name').focus();
            $('#datetime').blur();
     		$('.ui-widget-overlay').css('z-index',99);
     		
     		GetDate('born_date');
     		GetDate('tin_date');
     		GetDate('car_born');
     		
            if($("#local_id").val()==''){
            	get_local_id('client');
            	$("#born_date").val('');
         		$("#tin_date").val('');
         		$("#car_born").val('');
            }
            
            setTimeout(function(){
         		LoadTable('person',3,main_act,"<'F'Cpl>",aJaxURL_cl_person, '', 'local_id='+$("#local_id").val());
         		$("#table_person_length").css('top', '2px');
         		SetEvents("add_button_pers", "delete_button_pers", "check-all_pers", tName+'person', 'add-edit-form-pers', aJaxURL_cl_person,'','person',4,main_act,"<'F'Cpl>",aJaxURL_cl_person,'');
            	GetButtons("add_button_pers","delete_button_pers");
         	}, 50);
         	
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

    function get_local_id(table_name){
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
						$("#local_id").val(data.local_id);
					}
				}
	    	}
	   });
    }
    
	function show_right_side(id){
        $("#right_side fieldset").hide();
        $("#" + id).show();
        if(id == 'auto_mobile'){
            $("#upload_picture").show();
        }else if(id == 'agreement'){
        	$("#agreement_grafic").show();
        }else if(id == 'info'){
        	$("#table_person_fieldset").show();
        }
        $(".add-edit-form-class").css("width", "1200");
        hide_right_side();
        var str = $("."+id).children('img').attr('src');
		str = str.substring(0, str.length - 4);
        $("."+id).children('img').attr('src',str+'_blue.png');
        $("."+id).children('div').css('color','#2681DC');
        $('#car_type').chosen();
        $('#loan_agreement_type').chosen();
        $('#agreement_type_id').chosen();
        $('#car_type_chosen').css('width', '206px');
        $('#loan_agreement_type_chosen').css('width', '206px');
        $('#agreement_type_id_chosen').css('width', '206px');
        $('#choose_button').button();
        $('#show_payment_schedule').button();
        $('#hidde_payment_schedule').button();
        GetDateTimes('agreement_date');
        GetDateTimes('trusting_date');
    }

    function hide_right_side(){
    	$("#side_menu").children('spam').children('div').css('color','#FFF');
        $(".info").children('img').attr('src','media/images/icons/info.png');
        $(".auto_mobile").children('img').attr('src','media/images/icons/car.png');
        $(".agreement").children('img').attr('src','media/images/icons/handshake.png');
        $(".pledge").children('img').attr('src','media/images/icons');
        $(".papers").children('img').attr('src','media/images/icons/file.png');
        $(".documents").children('img').attr('src','media/images/icons/document.png');
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

		//ცლიენტის მონაცემები//
		param.id_hidden	   = $('#id_hidden').val();
		param.local_id	   = $('#local_id').val();
		param.name	       = $('#name').val();
		param.surname	   = $('#surname').val();
		param.born_date	   = $('#born_date').val();
		param.tin	       = $('#tin').val();
		param.tin_number   = $('#tin_number').val();
		param.tin_date	   = $('#tin_date').val();
		param.comment	   = $('#comment').val();
		param.mail	       = $('#mail').val();
		param.phone	       = $('#phone').val();
		param.fact_address = $('#fact_address').val();
		param.jur_address  = $('#jur_address').val();
		param.ltd_name	   = $('#ltd_name').val();
		param.ltd_id       = $('#ltd_id').val();
		param.client_type  = $("input[class=client_type]:checked").val();

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
		param.car_model	              = $('#car_model').val();
		param.car_born	              = $('#car_born').val();
		param.car_color	              = $('#car_color').val();
		param.car_type	              = $('#car_type').val();
		param.car_engine	          = $('#car_engine').val();
		param.car_registration_number = $('#car_registration_number').val();
		param.car_owner               = $('#car_owner').val();
		param.car_ident               = $('#car_ident').val();
		param.car_ertificate          = $('#car_ertificate').val();

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
		
		
		$.ajax({
	        url: aJaxURL,
		    data: param,
	        success: function(data) {       
				if(typeof(data.error) != "undefined"){
					if(data.error != ""){
						alert(data.error);
					}else{
						LoadTable('index',colum_number,main_act,change_colum_main,aJaxURL);
					    //CloseDialog("add-edit-form");
					}
				}
	    	}
	   });
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

	$(document).on("change", "#agreement_type_id", function () {

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
                data: "act=show_document&file_type="+file_type+"&local_id="+$("#local_id").val()+"&id_hidden="+$("#id_hidden").val()+"&loan_amount="+$("#loan_amount").val()+"&month_percent="+$("#month_percent").val()+"&loan_months="+$("#loan_months").val()+"&loan_agreement_type="+$("#loan_agreement_type").val()+"&name="+$("#name").val()+"&surname="+$("#surname").val(),
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
            data: "act=show_document&file_type="+file_type+"&local_id="+$("#local_id").val()+"&id_hidden="+$("#id_hidden").val(),
            success: function(data) {
            	$("#add-edit-form-document").html(data.documets_page);

                var buttons = {
                    
            		"print": {
    		            text: "ბეჭდვა",
    		            id: "print-dialog",
    		            click: function () {
    		            	var local_id = $("#local_id").val();
    		                local_id  = "&local_id="+local_id+"&file_type="+file_type+"&id_hidden="+$("#id_hidden").val();
    		        		win=window.open("server-side/operations/subtables/print_documents.action.php?"+local_id, "" , "scrollbars=no,toolbar=no,screenx=0,screeny=0,location=no,titlebar=no,directories=no,status=no,menubar=no");
    		            }
    		        },
    	        	"download": {
    		            text: "ჩამოტვირთვა",
    		            id: "download-dialog",
    		            click: function () {
        		            if(file_type != 'payment_schedule'){
        		            	URL="server-side/operations/subtables/download_doc.php?file_type="+file_type+"&local_id="+$("#local_id").val()+"&file_name="+file_name;
        		            	open(URL);
        		            }else{
        		            	parame 			 = new Object();
        		            	parame.local_id  = $("#local_id").val();

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
               	GetDialog("add-edit-form-document", 790, "auto", buttons, 'left+43 top');
    		}
        });
    }

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
                    <th style="width: 15%;">თარიღი</th>
                    <th style="width: 25%;">სახელი გავარი</th>
                    <th style="width: 15%;">პირადი ნომერი</th>            
                    <th style="width: 15%;">ტელეფონი</th>
                    <th style="width: 15%;">ხელშეკრულების ნომერი</th>
                    <th style="width: 15%;">კოდი</th>
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
                    <th style="border-right: 1px solid #A3D0E4;">
                        <div class="callapp_checkbox">
                            <input type="checkbox" id="check-all" name="check-all" />
                            <label for="check-all"></label>
                        </div>
                    </th>            
                </tr>
            </thead>
        </table>
    
    <div  id="add-edit-form" class="form-dialog" title="ავტო ლომბარდი"></div>
    <div  id="add-edit-form-pers" class="form-dialog" title="საკონტაქტო პირი"></div>
    <div  id="add-edit-form-document" class="form-dialog" title="დოკუმენტი"></div>
    <div id="add-edit-form-img" class="form-dialog" title="ავტომობილის სურათი">
	</div>
</body>