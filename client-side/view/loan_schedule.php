<html>
<head>
	<script type="text/javascript">
		var aJaxURL	          = "server-side/view/loan_schedule.action.php";		//server side folder url
		var tName	          = "example";													//table name
		var fName	          = "add-edit-form";												//form name
		var change_colum_main = "<'dataTable_buttons'T><'F'Cfipl>";
		    	
		$(document).ready(function () {        	
			LoadTable(tName,10,change_colum_main,aJaxURL);	
 						
			/* Add Button ID, Delete Button ID */
			GetButtons("add_button", "delete_button");
			GetButtons("add_cat", "");
			SetEvents("add_button", "delete_button", "check-all", tName, fName, aJaxURL,'',tName,10,change_colum_main,aJaxURL,'','','');
			$("#filt_agr_id").chosen();
		});
        
		function LoadTable(tName,num,change_colum_main,aJaxURL){
			/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
			GetDataTable(tName, aJaxURL, 'get_list', num, "&agr_id="+$("#filt_agr_id").val(), 0, "", 1, "desc", "", change_colum_main);
			setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
		}
		
		function LoadDialog(fname){
			var id		= $("#id").val();
			
			if(fname=='add-edit-form'){
    			GetDialog(fName, 270, "auto", "","top");
    			GetDate("schedule_date");
    		}
		}

		$(document).on("change", "#filt_agr_id", function () {
			LoadTable(tName,10,change_colum_main,aJaxURL);
		});
		
		// Add - Save
	    $(document).on("click", "#save-dialog", function () {
		    param 						= new Object();
		    param.act		            = "save_schedule";
		    param.id		            = $("#id").val();
		    param.schedule_number		= $("#schedule_number").val();
	    	param.schedule_date		    = $("#schedule_date").val();
	    	param.schedule_amount		= $("#schedule_amount").val();
	    	param.schedule_root		    = $("#schedule_root").val();
	    	param.schedule_percent		= $("#schedule_percent").val();
	    	param.schedule_penalty		= $("#schedule_penalty").val();
	    	param.schedule_other_amount	= $("#schedule_other_amount").val();
	    	param.penalty_stoped        = $("input[id='penalty_stoped']:checked").val();
	    	
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
<div id="tabs" style="width: 100%">
<div class="callapp_head">გადახდის გრაფიკი<hr class="callapp_head_hr"></div>
<div id="button_area">
	<button style="display:none;" id="add_button">დამატება</button>
	<button style="display:none;" id="delete_button">წაშლა</button>
	<select id="filt_agr_id" style="width:  200px;">
		<?php 
		
    		mysql_connect('212.72.155.176','root','Gl-1114');
    		mysql_select_db('tgmobile');
    		mysql_set_charset ( 'utf8');
		
		    $c_date	= date('m');
            $req = mysql_query("SELECT  client_loan_agreement.id,
                                        CASE
        									 WHEN NOT ISNULL(client.sub_client) AND client_loan_agreement.agreement_id>0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id, IF(client_loan_agreement.attachment_number=''  OR ISNULL(client_loan_agreement.attachment_number),'',' დ.'), IF(client_loan_agreement.attachment_number='' OR ISNULL(client_loan_agreement.attachment_number), '', client_loan_agreement.attachment_number))
        									 WHEN client.attachment_id > 0 AND client_loan_agreement.agreement_id>0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id, ' დ.', client_loan_agreement.attachment_number)
        									 WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id > 0 THEN CONCAT('ს/ხ ', client_loan_agreement.agreement_id)
        									 WHEN ISNULL(client.sub_client) AND client.attachment_id = 0 AND client_loan_agreement.agreement_id = 0 THEN CONCAT('ს/ხ ', client_loan_agreement.oris_code)
                        			    END AS `name`
        
                                 FROM   client_loan_agreement
                                 JOIN   client ON client.id = client_loan_agreement.client_id
                                 WHERE  client_loan_agreement.actived = 1
                                 AND    client_loan_agreement.`status` = 1
                                 AND    client_loan_agreement.canceled_status = 0
                                 AND    client.actived = 1
                                 ORDER BY `name` ASC");
            $data = '<option value="0" selected="selected">-------</option>';
            while( $res = mysql_fetch_assoc($req)){
                $data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
            }
            
            echo $data;
		 ?>
    	</select>
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
                <th style="width: 9%;">#</th>
                <th style="width: 12%;">თარიღი</th>
                <th style="width: 12%;">ანუიტეტი</th>
                <th style="width: 12%;">ძირი</th>
                <th style="width: 11%;">პროცენტი</th>
                <th style="width: 11%;">ჯარიმა</th>
                <th style="width: 11%;">დამატებითი<br>თანხა</th>
                <th style="width: 10%;">ნაშთი</th>
                <th style="width: 12%;">სტატუსი</th>
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
           </tr>
        </thead>
    </table>
    <!-- jQuery Dialog -->
    <div id="add-edit-form" class="form-dialog" title="ძირითადი ველები">
    	<!-- aJax -->
	</div>
</body>
</html>


