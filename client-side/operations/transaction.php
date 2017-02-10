<html>
<head>
	<script type="text/javascript">
		var aJaxURL	          = "server-side/operations/transaction.action.php";		//server side folder url
		var tName	          = "example";													//table name
		var fName	          = "add-edit-form";	
		var tbName		      = "tabs1";											//form name
		var change_colum_main = "<'dataTable_buttons'T><'F'Cfipl>";
		    	
		$(document).ready(function () {  
			GetTabs(tbName);       	
			LoadTable(tName,6,change_colum_main,aJaxURL);	
			$(".ui-widget-content").css('border', '0px solid #aaaaaa');
 						
			/* Add Button ID, Delete Button ID */
			GetButtons("add_button");
			SetEvents("add_button", "", "", tName, fName, aJaxURL,'',tName,10,change_colum_main,aJaxURL,'','','');
		});

		$(document).on("tabsactivate", "#tabs1", function() {
        	tab = GetSelectedTab(tbName);
        	if (tab == 0) {
        		LoadTable(tName,6,change_colum_main,aJaxURL);	
         	}else if(tab == 1){
         		GetButtons("add_button1", "");
             	GetDataTable("example1", aJaxURL, 'get_list', 9, "tab=1", 0, "", 1, "desc", "", change_colum_main);
             	SetEvents("", "", "", "example1", fName, aJaxURL,'',tName,10,change_colum_main,aJaxURL,'','','');
             	
             	setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
			}else if(tab == 2){
				GetButtons("add_button2", "");
				GetDataTable("example2", aJaxURL, 'get_list', 9, "tab=2", 0, "", 1, "desc", "", change_colum_main);
				SetEvents("", "", "", "example2", fName, aJaxURL,'',tName,10,change_colum_main,aJaxURL,'','','');
				
				setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
          	}else{
          		GetButtons("add_button3", "");
          		GetDataTable("example3", aJaxURL, 'get_list', 9, "tab=3", 0, "", 1, "desc", "", change_colum_main);
          		SetEvents("", "", "", "example3", fName, aJaxURL,'',tName,10,change_colum_main,aJaxURL,'','','');
          		
          		setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
            }
        });
        
		function LoadTable(tName,num,change_colum_main,aJaxURL){
			
			/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
			GetDataTable(tName, aJaxURL, 'get_list', num, "tab=0", 0, "", 1, "desc", "", change_colum_main);
			setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
		}
		
		function LoadDialog(fname){
			var id		= $("#id").val();
			var buttons = {
				"save": {
		            text: "დადასტურება",
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
			/* Dialog Form Selector Name, Buttons Array */
			if(fname=='add-edit-form'){
    			GetDialog(fName, 560, "auto", buttons,"top");
    			$('#type_id').chosen();
    	        $('#client_id').chosen();
    	        $('#currency_id').chosen();
    	        $('#add-edit-form, .add-edit-form-class').css('overflow','visible');
			}
		}

		// Add - Save
	    $(document).on("click", "#save-dialog", function () {
		    param 		= new Object();
		    
			param.act	= "save_transaction";
		    param.id	= $("#id").val();
		    
		    param.diff		    = $("#month_fee1").val() - $("#month_fee").val();
		    param.month_fee		= $("#month_fee").val();
	    	param.root		    = $("#root").val();
	    	param.percent		= $("#percent").val();
	    	param.penalti_fee	= $("#penalti_fee").val();
	    	param.type_id	    = $("#type_id").val();
	    	
	    	param.currency_id	= $("#currency_id").val();
	    	param.course	    = $("#course").val();
	    	
	    	param.hidde_id		= $("#hidde_id").val();
	    	
	    	$.ajax({
		        url: aJaxURL,
			    data: param,
		        success: function(data) {			        
					if(typeof(data.error) != 'undefined'){
						if(data.error != ''){
							alert(data.error);
						}else{
							LoadTable(tName,10,change_colum_main,aJaxURL);
			        		CloseDialog(fName);
						}
					}
			    }
		    });
			
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
	    							$("#hidde_id").val(data.id);
		    					}else if(data.status==2){
		    						$("#month_fee1").val(data.insurance_fee);
		    						$("#root1").val('');
	    							$("#percent1").val('');
	    							$("#penalti_fee1").val('');

	    							$("#hidde_id").val(data.id);
	    						}else if(data.status==3){
			    					$("#month_fee1").val(data.pledge_fee);
			    					$("#root1").val('');
	    							$("#percent1").val('');
	    							$("#penalti_fee1").val('');

	    							$("#hidde_id").val(data.id);
	    						}
    						}
    					}
    			    }
    		    });
	        }else{
	        	if($(this).val() == 1 && $("#client_id").val()>0){
		        	
	        		param         =  new Object();
	    		    param.act     = "get_shedule";
	    		    
	    		    param.id      =  $("#client_id").val();
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
		    							$("#hidde_id").val(data.id);
			    					}else if(data.status==2){
			    						$("#month_fee1").val(data.insurance_fee);
			    						$("#root1").val('');
		    							$("#percent1").val('');
		    							$("#penalti_fee1").val('');

		    							$("#hidde_id").val(data.id);
		    						}else if(data.status==3){
				    					$("#month_fee1").val(data.pledge_fee);
				    					$("#root1").val('');
		    							$("#percent1").val('');
		    							$("#penalti_fee1").val('');

		    							$("#hidde_id").val(data.id);
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
		    param.id      =  $(this).val();
		    param.type_id =  $("#type_id").val();
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
    							$("#hidde_id").val(data.id);
	    					}else if(data.status==2){
	    						$("#month_fee1").val(data.insurance_fee);
	    						$("#root1").val('');
    							$("#percent1").val('');
    							$("#penalti_fee1").val('');

    							$("#hidde_id").val(data.id);
    						}else if(data.status==3){
		    					$("#month_fee1").val(data.pledge_fee);
		    					$("#root1").val('');
    							$("#percent1").val('');
    							$("#penalti_fee1").val('');

    							$("#hidde_id").val(data.id);
    						}
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

<div id="tabs1" style="width: 100%; margin: 0 auto; min-height: 768px; margin-top: 25px;">
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
            <table class="display" id="example">
                <thead>
                    <tr id="datatable_header">
                        <th>ID</th>
                        <th style="width: 12%;">თარიღი</th>
                        <th style="width: 18%;">მსესხებელი</th>
                        <th style="width: 12%;">ჩარიცხული თანხა</th>
                        <th style="width: 12%;">კურსი</th>
                        <th style="width: 12%;">ვალუტა</th>
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
            <table class="display" id="example1">
                <thead>
                    <tr id="datatable_header">
                        <th>ID</th>
                        <th style="width: 12%;">თარიღი</th>
                        <th style="width: 18%;">მსესხებელი</th>
                        <th style="width: 12%;">ჩარიცხული<br>თანხა</th>
                        <th style="width: 12%;">კურსი</th>
                        <th style="width: 12%;">ვალუტა</th>
                        <th style="width: 12%;">დაფარული<br>ძირი</th>
                        <th style="width: 11%;">დაფარული<br>პროცენტი</th>
                        <th style="width: 11%;">სხვაობა</th>
                        
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
            <table class="display" id="example2">
                <thead>
                    <tr id="datatable_header">
                        <th>ID</th>
                        <th style="width: 12%;">თარიღი</th>
                        <th style="width: 18%;">მსესხებელი</th>
                        <th style="width: 12%;">ჩარიცხული<br>თანხა</th>
                        <th style="width: 12%;">კურსი</th>
                        <th style="width: 12%;">ვალუტა</th>
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
            <table class="display" id="example3">
                <thead>
                    <tr id="datatable_header">
                        <th>ID</th>
                        <th style="width: 12%;">თარიღი</th>
                        <th style="width: 18%;">მსესხებელი</th>
                        <th style="width: 12%;">ჩარიცხული<br>თანხა</th>
                        <th style="width: 12%;">კურსი</th>
                        <th style="width: 12%;">ვალუტა</th>
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
                    </tr>
                </thead>
            </table>
       </div>
    </div>
    <!-- jQuery Dialog -->
    <div id="add-edit-form" class="form-dialog" title="ძირითადი ველები">
    	<!-- aJax -->
	</div>
</body>
</html>


