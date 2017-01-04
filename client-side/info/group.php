<html>
<head>
	<script type="text/javascript">
		var aJaxURL	= "server-side/info/group.action.php";		//server side folder url
		var tName	= "example";											//table name
		var fName	= "add-edit-form";										//form name
		var img_name		= "0.jpg";
		var change_colum_main = "<'dataTable_buttons'T><'F'Cfipl>";

		$(document).ready(function () {
			LoadTable();

			/* Add Button ID, Delete Button ID */
			GetButtons("add_button", "delete_button");

			SetEvents("add_button", "delete_button", "check-all", tName, fName, aJaxURL);
		});

		function LoadTable(){
			/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
			GetDataTable(tName, aJaxURL, "get_list", 2, "", 0, "", 1, "asc", "", change_colum_main);
			setTimeout(function(){
		    	$('.ColVis, .dataTable_buttons').css('display','none');
		    }, 90);
		}

		function LoadDialog(){

			/* Dialog Form Selector Name, Buttons Array */
			GetDialog(fName, 575, "auto", "");

			var group_id = $("#group_id").val();

			GetDataTable("pages", aJaxURL, "get_pages_list&group_id=" + group_id, 2, "", 0, "", 1, "asc", "", "<'F'lip>");

		}

	    // Add - Save
		$(document).on("click", "#save-dialog", function () {

		    var data = $(".check1:checked").map(function () { //Get Checked checkbox array
		        return this.value;
		    }).get();

			var pages = new Array;

 		    for (var i = 0; i < data.length; i++) {
 		    	pages.push(data[i]);
 		    }

     		param = new Object();
     	    //Action
     		param.act	   = "save_group";
 			param.nam	   = $("#group_name").val();
 			param.pag	   = JSON.stringify(pages);
 			param.group_id = $("#group_id").val();

 			//var link	=  GetAjaxData(param);

 			if( param.nam == "" ){
 				alert("შეიყვანეთ ჯგუფის სახელი!");
 			}else{
 	    	    $.ajax({
 	    	        url: aJaxURL,
 	    		    data: param,
 	    	        success: function(data) {
 	    				if(typeof(data.error) != "undefined"){
 	    					if(data.error != ""){
 	    						alert(data.error);
 	    					}else{
 	    						$("#add-edit-form").dialog("close");
 	    						LoadTable();
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
	        	$(this).css('background','#E6F2F8');
	            $(this).children('img').attr('src','media/images/icons/select.png');
	            $(this).attr('myvar','0');
	        }
	    });

    </script>
    <style type="text/css">
#pages_length{
	position: inherit;
    width: 0px;
	float: left;
}
#pages_length label select{
	width: 60px;
    font-size: 10px;
    padding: 0;
    height: 18px;
}
#pages_info{
	width: 32%;
}
#pages_paginate{
	margin-left: 0px;
}
    </style>
</head>

<body>
<div id="tabs" style="width: 90%">
<div class="callapp_head">ჯგუფები<hr class="callapp_head_hr"></div>
    
    <div style="margin-top: 15px;">
        <button id="add_button">დამატება</button>
        <button id="delete_button">წაშლა</button>
    </div>
    
<div class="callapp_filter_show">
<table id="table_right_menu">
<tr>
<td><img alt="table" src="media/images/icons/table_w.png" height="14" width="14">
</td>
<td><img alt="log" src="media/images/icons/log.png" height="14" width="14">
</td>
<td id="show_copy_prit_exel" myvar="0"><img alt="link" src="media/images/icons/select.png" height="14" width="14">
</td>
</tr>
</table>
<table class="display" id="example">
    <thead>
        <tr id="datatable_header">
            <th>ID</th>
            <th style="width: 100%">ჯგუფის სახელი</th>
                <th class="check">#</th>
            </tr>
        </thead>
        <thead>
            <tr class="search_header">
                <th class="colum_hidden">
                	<input type="text" name="search_id" value="ფილტრი" class="search_init" />
                </th>
                <th>
                    <input type="text" name="search_address" value="ფილტრი" class="search_init" />
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
</div>

    <!-- jQuery Dialog -->
    <div id="add-edit-form" class="form-dialog" title="თანამშრომლები">
    	<!-- aJax -->
	</div>
    <!-- jQuery Dialog -->
    <div id="image-form" class="form-dialog" title="პროდუქციის სურათი">
    	<img id="view_img" src="media/uploads/images/worker/0.jpg">
	</div>
	 <!-- jQuery Dialog -->
    <div id="add-group-form" class="form-dialog" title="ჯგუფი">
	</div>
</body>
</html>