<html>
<head>
	<script type="text/javascript">
		var aJaxURL	          = "server-side/report/sms_report.action.php";		//server side folder url
		var tName	          = "example";	//table name
		var fName	          = "add-edit-form"; //form name
		var change_colum_main = "<'dataTable_buttons'T><'F'Cfipl>";
		    	
		$(document).ready(function () {        	
			GetDate('start_date');
			GetDate('end_date');
			$("#fillter").button();

			LoadTable(tName,6,change_colum_main,aJaxURL);
			GetButtons("add_button", "delete_button");
			SetEvents("add_button", "delete_button", "check-all", tName, fName, aJaxURL,'',tName,4,change_colum_main,aJaxURL,'','','');
			
		});
        
		function LoadTable(tName,num,change_colum_main,aJaxURL){
			
			/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
			
			GetDataTable(tName, aJaxURL, 'get_list', num, "&start="+$("#start_date").val()+"&end="+$("#end_date").val(), 0, "", 1, "desc", "", change_colum_main);
			setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
		}
		
		function LoadDialog(fname){
			var id		= $("#id").val();
			
			if(fname=='add-edit-form'){
				var buttons = {
					"cancel": {
			            text: "დახურვა",
			            id: "cancel-dialog",
			            click: function () {
			            	$(this).dialog("close");
			            }
			        }
				};
    			GetDialog(fName, 782, "auto", buttons, "top");
    			$("#get_number").button();
    			$("#get_shablons").button();
    			$("#client_id,#client_phone").chosen();
    		}
		}
		$(document).on("click", "#fillter", function () {
			LoadTable(tName,6,change_colum_main,aJaxURL);
	    });

		$(document).on("click", ".callapp_refresh", function () {
			LoadTable(tName,6,change_colum_main,aJaxURL);	 
	    });

		$(document).on("click", ".copy_number", function () {
	      	$('#sms_phone').val($(this).attr('phone'));
	      	$("#add-edit-form-phone").dialog("close");
	    });
	    
	    $(document).on("keyup  paste", "#sms_text", function () {
	      	 var sms_text = $('#sms_text').val(); 
	      	  isValid(sms_text);
	      	$('#simbol_caunt').html((sms_text.length)+'/150');
	    });

	    function isValid(str){
		     var check = false;
		     for(var i=0;i<str.length;i++){
		         if(str.charCodeAt(i)>127){
		        	 check = true;
		          }
		     }
		     if(check){
		    	 var string = $('#sms_text').val();
		    	 var replaced = string.replace(/[^\x00-\x7F]/g, "");
		    	 $('#sms_text').val(replaced);
		    	 alert('არასწორი სიმბოლო');
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
<div id="tabs">
<div class="callapp_head">SMS<span class="callapp_refresh"><img alt="refresh" src="media/images/icons/refresh.png" height="14" width="14">   განახლება</span><hr class="callapp_head_hr"></div>
	<div id="button_area">
    	<table style="width: 100%">
        	<tr>
            	<td style="width: 150px;">
            		<input class="idle" style="width: 137px; height: 18px;" id="start_date" type="text" value="<?php date('Y-d-m'); ?>">
            	</td>
            	<td style="width: 160px;">
            		<input class="idle" style="width: 137px; height: 18px;" id="end_date" type="text" value="<?php date('Y-d-m'); ?>">
            	</td>
            	<td>
            		<button id="fillter">გაფილტვრა</button>
            	</td>
            </tr>
    	</table>
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
                <th style="width: 14%;">თარიღი</th>
                <th style="width: 20%;">კლიენტი</th>
                <th style="width: 10%;">ნომერი</th>
                <th style="width: 47%;">შინაარსი</th>
                <th style="width: 9%;">სტატუსი</th>
            	<th class="check" style="width: 20px;">#</th>
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
                	<div class="callapp_checkbox">
                        <input type="checkbox" id="check-all" name="check-all" />
                        <label for="check-all"></label>
                    </div>
                </th>
            </tr>
        </thead>
    </table>
    <!-- jQuery Dialog -->
    <div id="add-edit-form" class="form-dialog" title="SMS">
    	<!-- aJax -->
	</div>
	<div id="add-edit-form-phone" class="form-dialog" title="ნომრები">
    	<!-- aJax -->
	</div>
</body>
</html>


