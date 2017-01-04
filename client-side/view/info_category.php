<html>
<head>
	<script type="text/javascript">
		var aJaxURL	          = "server-side/view/info_category.action.php";
		var tName             = "table_";
	    var dialog            = "add-edit-form";
	    var colum_number      = 3;
	    var main_act          = "get_list";
	    var change_colum_main = "<'dataTable_buttons'T><'F'Cfipl>";
 		    	
		$(document).ready(function () {        	
			GetButtons("add_button","delete_button");
	    	LoadTable('index',colum_number,main_act,change_colum_main);
	    	SetEvents("add_button", "delete_button", "check-all", tName+'index', dialog, aJaxURL);
		});
        
		function LoadTable(tbl,col_num,act,change_colum){
	    	GetDataTable(tName+tbl, aJaxURL, act, col_num, "", 0, "", 1, "asc", '', change_colum);
	    	setTimeout(function(){
    	    	$('.ColVis, .dataTable_buttons').css('display','none');
  	    	}, 90);
	    }
		
		function LoadDialog(fName){
			GetDialog(fName, 500, "auto", '', 'center top');
			$('#parent_id,#client_id').chosen({ search_contains: true });
			$('#add-edit-form, .add-edit-form-class').css('overflow','visible');
		}
		
	    // Add - Save
	    $(document).on("click", "#save-dialog", function () {
		    param 			= new Object();

		    param.act		="save_category";
	    	param.id		= $("#cat_id").val();
	    	param.cat		= $("#category").val();
	    	param.parent_id	= $("#parent_id").val();
	    	param.client_id = $('#client_id').val();
			
			if(param.cat == ""){
				alert("შეავსეთ პროდუქტის კატეგორია!");
			}else {
			    $.ajax({
			        url: aJaxURL,
				    data: param,
			        success: function(data) {			        
						if(typeof(data.error) != 'undefined'){
							if(data.error != ''){
								alert(data.error);
							}else{
								LoadTable('index',colum_number,main_act,change_colum_main);
				        		CloseDialog(dialog);
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
    </style>
</head>

<body>
<div id="tabs">
<div class="callapp_head">კატეგორიები<hr class="callapp_head_hr"></div>
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
<table  class="display" id="table_index">
    <thead>
        <tr id="datatable_header">
            <th>ID</th>
            <th style="width: 50%;">ინფორმაციის ქვე კატეგორია</th>
            <th style="width: 50%;">ინფორმაციის კატეგორია</th>
            <th class="check">#</th>
        </tr>
    </thead>
    <thead>
        <tr class="search_header">
            <th class="colum_hidden">
            	<input type="text" name="search_id" value="ფილტრი" class="search_init" />
            </th>
            <th>
                <input type="text" name="search_category" value="ფილტრი" class="search_init" />
            </th>
            <th>
                <input type="text" name="search_sub_category" value="ფილტრი" class="search_init" />
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
    <div id="add-edit-form" class="form-dialog" title="ინფორმაციის კატეგორიები">
    	<!-- aJax -->
	</div>
</body>
</html>
