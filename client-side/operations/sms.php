<html>
<head>
	<script type="text/javascript">
		var aJaxURL	          = "server-side/operations/sms.action.php";		//server side folder url
		var tName	          = "example";	//table name
		var fName	          = "add-edit-form"; //form name
		var change_colum_main = "<'dataTable_buttons'T><'F'Cfipl>";
		    	
		$(document).ready(function () {        	
			LoadTable(tName,4,change_colum_main,aJaxURL);	
 						
			/* Add Button ID, Delete Button ID */
			GetButtons("add_button", "delete_button");
			SetEvents("add_button", "delete_button", "check-all", tName, fName, aJaxURL,'',tName,4,change_colum_main,aJaxURL,'','','');
		});
        
		function LoadTable(tName,num,change_colum_main,aJaxURL){
			
			/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
			GetDataTable(tName, aJaxURL, 'get_list', num, "", 0, "", 1, "desc", "", change_colum_main);
			setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
		}
		
		function LoadDialog(fname){
			var id		= $("#id").val();
			
			if(fname=='add-edit-form'){
				var buttons = {
						"sent": {
				            text: "გაგზავნა",
				            id: "sent-dialog",
				            click: function () {
				            }
				        },
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
    			GetDialog(fName, 400, "auto", buttons, "top center");
    			$("#get_number").button();
    			$("#get_shablons").button();
			}
		}

		$(document).on("click", "#get_number", function () {
			param 	  = new Object();
			param.act = "get_client_number";
			$.ajax({
		        url: aJaxURL,
			    data: param,
		        success: function(data) {			        
					if(typeof(data.error) != 'undefined'){
						if(data.error != ''){
							alert(data.error);
						}else{
							$("#add-edit-form-phone").html(data.page);
							var buttons = {
									"update_phone": {
							            text: "განახლება",
							            id: "update_phone",
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
							GetDialog('add-edit-form-phone', 400, "auto", buttons, "top center");
							$(".copy_number").button();
						}
					}
			    }
		    });
			
		});

		$(document).on("click", "#get_shablons", function () {
			param 	  = new Object();
			param.act = "get_client_number";
			$.ajax({
		        url: aJaxURL,
			    data: param,
		        success: function(data) {			        
					if(typeof(data.error) != 'undefined'){
						if(data.error != ''){
							alert(data.error);
						}else{
							$("#add-edit-form-phone").html(data.page);
							var buttons = {
									"update_phone": {
							            text: "განახლება",
							            id: "update_phone",
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
							GetDialog('add-edit-form-phone', 400, "auto", buttons, "top center");
							$(".copy_number").button();
						}
					}
			    }
		    });
			
		});

		$(document).on("click", "#update_phone", function () {
		    param 			= new Object();

		    param.act = "get_client_number";
	    	
		    $.ajax({
		        url: aJaxURL,
			    data: param,
		        success: function(data) {			        
					if(typeof(data.error) != 'undefined'){
						if(data.error != ''){
							alert(data.error);
						}else{
							$("#add-edit-form-phone").html(data.page);
							$(".copy_number").button();
						}
					}
			    }
		    });
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
								LoadTable(tName,4,change_colum_main,aJaxURL);
				        		CloseDialog(fName);
							}
						}
				    }
			    });
			}
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
<div class="callapp_head">SMS<hr class="callapp_head_hr"></div>
<div id="button_area">
	<button id="add_button">ახალი SMS</button>
	<button id="delete_button">წაშლა</button>
</div>
<table id="table_right_menu">
<tr>
<td style="cursor: pointer;padding: 4px;border-right: 1px solid #E6E6E6;background:#2681DC;"><img alt="table" src="media/images/icons/table_w.png" height="14" width="14">
</td>
<td style="cursor: pointer;padding: 4px;border-right: 1px solid #E6E6E6;"><img alt="log" src="media/images/icons/log.png" height="14" width="14">
</td>
<td style="cursor: pointer;padding: 4px;" id="show_copy_prit_exel" myvar="0"><img alt="link" src="media/images/icons/select.png" height="14" width="14">
</td>
</tr>
</table>
    <table class="display" id="example" >
        <thead>
            <tr id="datatable_header">
                <th>ID</th>
                <th style="width: 11%;">ნომერი</th>
                <th style="width: 76%;">შინაარსი</th>
                <th style="width: 10%;">სტატუსი</th>
            	<th class="check" style="width: 3%;">#</th>
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


