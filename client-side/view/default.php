<html>
<head>
	<script type="text/javascript">
		var aJaxURL	          = "server-side/view/default.action.php";		//server side folder url
		var tName	          = "example";													//table name
		var fName	          = "add-edit-form";												//form name
		var change_colum_main = "<'dataTable_buttons'T><'F'Cfipl>";
		    	
		$(document).ready(function () {        	
			LoadTable(tName,10,change_colum_main,aJaxURL);	
 						
			/* Add Button ID, Delete Button ID */
			GetButtons("add_button", "delete_button");
			GetButtons("add_cat", "");
			SetEvents("add_button", "delete_button", "check-all", tName, fName, aJaxURL,'',tName,10,change_colum_main,aJaxURL,'','','');
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
    			GetDialog(fName, 800, "auto", "","top");
    			$('#loan_agreement_type').chosen();
    	        $('#agreement_type_id').chosen();
    	        $('#add-edit-form, .add-edit-form-class').css('overflow','visible');
			}
		}

		// Add - Save
	    $(document).on("click", "#save-dialog", function () {
		    param 							 = new Object();
		    
			param.act		                 = "save_default";
		    param.id		                 = $("#id").val();
		    
		    param.loan_agreement_type		 = $("#loan_agreement_type").val();
	    	param.agreement_type_id		     = $("#agreement_type_id").val();
	    	param.month_percent		         = $("#month_percent").val();
	    	param.loan_fee		             = $("#loan_fee").val();
	    	param.proceed_percent		     = $("#proceed_percent").val();
	    	param.proceed_fee		         = $("#proceed_fee").val();
	    	param.rs_message_number		     = $("#rs_message_number").val();
	    	param.penalty_days		         = $("#penalty_days").val();
	    	param.penalty_percent		     = $("#penalty_percent").val();
	    	param.penalty_additional_percent = $("#penalty_additional_percent").val();
	    	
	    	
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
								LoadTable(tName,10,change_colum_main,aJaxURL);
				        		CloseDialog(fName);
							}
						}
				    }
			    });
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
<div id="tabs" style="width: 95%">
<div class="callapp_head">ხელშეკრულების ძირითადი ველები<hr class="callapp_head_hr"></div>
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
                <th style="width: 10%;">სესხის ტიპი</th>
                <th style="width: 18%;">ხელშეკრულების<br>ტიპი</th>
                <th style="width: 10%;">ყოველთ.<br>პროცენტი</th>
                <th style="width: 10%;">სესხის <br>გაცემის<br>საკომისიო</th>
                <th style="width: 10%;">ხელშკრ.<br>გაგრძ.<br>საფასური</th>
                <th style="width: 10%;">პროცენტი</th>
                <th style="width: 11%;">ვადაგადაც.<br>პირგასამტეხლო%</th>
                <th style="width: 10%;">ვადაგადაც.<br>დღეები</th>
                <th style="width: 11%;">ვადაგადაც.<br>პირგასამტეხლო%</th>
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
                	<div class="callapp_checkbox">
                        <input type="checkbox" id="check-all" name="check-all" />
                        <label for="check-all"></label>
                    </div>
                </th>
            </tr>
        </thead>
    </table>
    <!-- jQuery Dialog -->
    <div id="add-edit-form" class="form-dialog" title="ძირითადი ველები">
    	<!-- aJax -->
	</div>
</body>
</html>


