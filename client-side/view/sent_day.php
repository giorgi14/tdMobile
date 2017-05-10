<html>
<head>
	<script type="text/javascript">
		var aJaxURL	          = "server-side/view/sent_day.action.php";		//server side folder url
		var tName	          = "example";													//table name
		var fName	          = "add-edit-form";												//form name
		var change_colum_main = "<'dataTable_buttons'T><'F'Cfipl>";
		    	
		$(document).ready(function () {        	
			LoadTable(tName,3,change_colum_main,aJaxURL);	
 						
			/* Add Button ID, Delete Button ID */
			GetButtons("add_button", "delete_button");
			GetButtons("add_cat", "");
			SetEvents("add_button", "delete_button", "check-all", tName, fName, aJaxURL,'',tName,3,change_colum_main,aJaxURL,'','','');
		});
        
		function LoadTable(tName,num,change_colum_main,aJaxURL){
			
			/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
			GetDataTable(tName, aJaxURL, 'get_list', num, "", 0, "", 1, "desc", "", change_colum_main);
			setTimeout(function(){
    	    	$('.ColVis, .dataTable_buttons').css('display','none');
  	    	}, 90);
		}
		
		function LoadDialog(fname){
			var id		= $("#id").val();
			
			/* Dialog Form Selector Name, Buttons Array */
			if(fname=='add-edit-form'){
    			GetDialog(fName, 400, "auto", "");
    			GetDate1('date');
			}
			if(fname=='add-edit-form-cat-add'){
				var buttons = {
						"save": {
				            text: "შენახვა",
				            id: "save-cat"
				        },
			        	"cancel": {
				            text: "დახურვა",
				            id: "cancel-dialog",
				            click: function () {
				            	$(this).dialog("close");
				            }
				        }
				    };
    			GetDialog('add-edit-form-cat-add', 400, "auto", buttons);
			}
		}

		$(document).on("click", "#add_cat", function () {
			var buttons = {
		        	"cancel": {
			            text: "დახურვა",
			            id: "cancel-dialog",
			            click: function () {
			            	$(this).dialog("close");
			            }
			        }
			};
			GetDialog('add-edit-form-cat', 800, "auto", buttons);
		});
		
	    // Add - Save
	    $(document).on("click", "#save-dialog", function () {
		    param 		= new Object();
			param.act	= "save_holidays";
			
	    	param.id	= $("#id").val();
	    	param.name	= $("#name").val();
	    	param.count	= $("#caunt").val();
	    	
			if(param.name == ""){
				alert("შეავსეთ ველი!");
			}else {
			    $.ajax({
			        url: aJaxURL,
				    data: param,
			        success: function(data) {			        
						if(typeof(data.error) != 'undefined'){
							if(data.error != ''){
								alert(data.error);
							}else{
								LoadTable(tName,3,change_colum_main,aJaxURL);
				        		CloseDialog(fName);
							}
						}
				    }
			    });
			}
		});

	    $(document).on("click", "#save-cat", function () {
		    param 			= new Object();

		    param.act		                = "save_cat";
	    	param.id		                = $("#id").val();
	    	param.name		                = $("#name").val();
	    	
			if(param.name == ""){
				alert("შეავსეთ ველი!");
			}else {
			    $.ajax({
			        url: aJaxURLCT,
				    data: param,
			        success: function(data) {			        
						if(typeof(data.error) != 'undefined'){
							if(data.error != ''){
								alert(data.error);
							}else{
								LoadTable('example-cat',2,"<'F'lip>",aJaxURLCT);
				        		CloseDialog('add-edit-form-cat-add');
							}
						}
				    }
			    });
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
<div id="tabs">
<div class="callapp_head">დაგზავნის შუალედები<hr class="callapp_head_hr"></div>
<div id="button_area">
	<button id="add_button">დამატება</button>
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
    <table class="display" id="example">
        <thead>
            <tr id="datatable_header">
                <th>ID</th>
                <th style="width: 70%;">დასახელება</th>
                <th style="width: 30%;">რამდენი დღით ადრე</th>
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
                	<div class="callapp_checkbox">
                        <input type="checkbox" id="check-all" name="check-all" />
                        <label for="check-all"></label>
                    </div>
                </th>
            </tr>
        </thead>
    </table>
    <!-- jQuery Dialog -->
    <div id="add-edit-form" class="form-dialog" title="დასვენების დღეები">
    	<!-- aJax -->
	</div>
</body>
</html>


