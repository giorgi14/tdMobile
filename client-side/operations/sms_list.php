<html>
<head>
	<script type="text/javascript">
		var aJaxURL	          = "server-side/operations/sms_list.action.php";		//server side folder url
		var aJaxURL_sms       = "includes/sendsmscron.php";
		var tName	          = "example";	//table name
		var fName	          = "add-edit-form"; //form name
		var change_colum_main = "<'dataTable_buttons'T><'F'Cfipl>";
		    	
		$(document).ready(function () {        	
			LoadTable(tName,6,change_colum_main,aJaxURL);	
 						
			/* Add Button ID, Delete Button ID */
			GetButtons("add_button", "delete_button");
			$("#sent_button").button();
			$("#fillt").button();
			SetEvents("add_button", "delete_button", "check-all", tName, fName, aJaxURL,'',tName,6,change_colum_main,aJaxURL,'','','');
			$("#status").chosen();
			GetDate('start_date');
            GetDate('end_date');
		});
        
		function LoadTable(tName,num,change_colum_main,aJaxURL){
			
			/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
			GetDataTable_sms(tName, aJaxURL, 'get_list', num, "status="+$("#status").val()+"&start_date="+$("#start_date").val()+"&end_date="+$("#end_date").val(), 0, "", 1, "desc", "", change_colum_main);
			setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
		}
		
		function LoadDialog(fname){
			var id		= $("#id").val();
			
			if(fname=='add-edit-form'){
				var buttons = {
					"save": {
			            text: "შენახვა",
			            id: "save-dialog",
			            click: function () {
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
    			GetDialog(fName, 782, "auto", buttons, "top");
    			$("#get_number").button();
    			$("#get_shablons").button();
    			$("#client_id,#client_phone").chosen();
    			
    			$('#add-edit-form, .add-edit-form-class').css('overflow','visible');

    			if($("#h_status").val()==1){
    				$("#sent-dialog").hide();
    				$("#save-dialog").hide();
        		}
			}
		}

		$(document).on("click", "#fillt", function () {
		    LoadTable(tName,6,change_colum_main,aJaxURL);
		});
		
	    // Add - Save
	    $(document).on("click", "#save-dialog", function () {
		    param 			= new Object();

		    param.act		= "save_sms";
	    	param.id		= $("#id").val();
	    	param.sms_phone	= $("#sms_phone").val();
	    	param.sms_text	= $("#sms_text").val();
	    	
			if(param.sms_phone == ""){
				alert("შეავსეთ ნომერი!");
			}else if(param.sms_text == ""){
				alert("შეავსეთ ტექსტი!");
			}else {
			    $.ajax({
			        url: aJaxURL,
				    data: param,
			        success: function(data) {			        
						if(typeof(data.error) != 'undefined'){
							if(data.error != ''){
								alert(data.error);
							}else{
								LoadTable(tName,6,change_colum_main,aJaxURL);
				        		CloseDialog(fName);
							}
						}
				    }
			    });
			}
		});

	    $(document).on("click", "#sent_button", function () {

			var sms_id = new Array();
			
			$(".check:checked").each(function() {
				sms_id.push(this.value); 
			});
			
			var jsonString  = JSON.stringify(sms_id);
			
		    param 			= new Object();

		    param.act		= "save_sms";

		    param.id 	= jsonString;
		    param.check = 1;
	        param.hidde = $(".check:checked").val();
	        
	    	if(param.hidde == ""){
				alert("არცერთი ვალი არაა მონიშნული");
			}else {
			    $.ajax({
			        url: aJaxURL_sms,
				    data: param,
			        success: function(data) {			        
						alert('სმს-ები წარმატებით გაიგზავნა');
						LoadTable(tName,6,change_colum_main,aJaxURL);
				    }
			    });
			}
		});
		
	    $(document).on("keyup  paste", "#sms_text", function () {
	      	 var sms_text = $('#sms_text').val(); 
	      	  isValid(sms_text);
	      	$('#simbol_caunt').html((sms_text.length)+'/300');
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
		    	 //$('#sms_text').val(replaced);
		    	 //alert('არასწორი სიმბოლო');
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
<div class="callapp_head">გასაგზავნი SMS-ების სია<hr class="callapp_head_hr"></div>
    <div id="button_area">
    	<table>
        	<tr>
            	<td>
            		<button id="add_button">დამატება</button>
            	</td>
            	<td>
            		<button id="delete_button">წაშლა</button>
            	</td>
            	<td style="width: 115px;">
            		<button style="height: 23px;" id="sent_button">გაგზავნა</button>
            	</td>
            	<td style="width: 208px;">
                	<select id="status" style="width: 200px;">
                		<option value="1">გასაგზავნი</option>
                		<option value="2">გაგზავნილი</option>
                	</select>
            	</td>
            	<td style="width: 110px;">
                    <input class="callapp_filter_body_span_input" type="text" id="start_date" style="width: 100px; margin-top: 2px;" value="<?php echo date('Y-m-d', strtotime('-1 month'))?>">
                </td>
            	<td style="width: 110px;">
                    <input class="callapp_filter_body_span_input" type="text" id="end_date" style="width: 100px; margin-top: 2px;" value="<?php echo date('Y-m-d')?>">
                </td>
                <td style="width: 110px;">
                    <button id="fillt">ფილტრი</button>
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
</body>
</html>


