<html>
<head>
	<script type="text/javascript">
		var aJaxURL	          = "server-side/view/loan_schedule.action.php";
		var aJaxURL_show_letter = "server-side/main.action.php";		//server side folder url
		var tName	          = "example";													//table name
		var fName	          = "add-edit-form";												//form name
		var change_colum_main = "<'dataTable_buttons'T><'F'Cfipl>";
		    	
		$(document).ready(function () {        	
			LoadTable(tName,11,change_colum_main,aJaxURL);	
 						
			/* Add Button ID, Delete Button ID */
			GetButtons("add_button", "delete_button");
			GetButtons("add_cat", "");
			SetEvents("add_button", "delete_button", "check-all", tName, fName, aJaxURL,'',tName,10,change_colum_main,aJaxURL,'','','');
			$("#filt_agr_id").chosen();
			$("#filt_agr_id_chosen").css('margin-top','-6px');
		});
        
		function LoadTable(tName,num,change_colum_main,aJaxURL){
			/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
			GetDataTable(tName, aJaxURL, 'get_list', num, "&agr_id="+$("#filt_agr_id").val(), 0, "", 2, "asc", "", change_colum_main);
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
			LoadTable(tName,11,change_colum_main,aJaxURL);
		});
		
		// Add - Save
	    $(document).on("click", "#save-dialog", function () {
		    param 						= new Object();
		    param.act		            = "save_schedule";
		    param.id		            = $("#id").val();
		    param.filt_agr_id           = $("#filt_agr_id").val();
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
							LoadTable(tName,11,change_colum_main,aJaxURL);
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

	    $(document).on("click", ".show_letter", function () {
    	    param 	  = new Object();
    		param.act = "get_edit_page";
    		param.id  = $("#filt_agr_id").val();
    		
    		$.ajax({
    	        url: aJaxURL_show_letter,
    		    data: param,
    	        success: function(data) {       
    				if(typeof(data.error) != "undefined"){
    					if(data.error != ""){
    						alert(data.error);
    					}else{
    						$("#add-edit-show_letter").html(data.page);
    						var buttons = {
    		    				"cancel": {
    		    		            text: "დახურვა",
    		    		            id: "cancel-dialog",
    		    		            click: function () {
    		    		            	$(this).dialog("close");
    		    		            }
    		    		        }
    		    		    };
    						GetDialog("add-edit-show_letter", 1200, "auto", buttons, 'left+43 top');
    			            $('#add-edit-show_letter, .add-edit-show_letter-class').css('overflow-y','scroll');
    			            $('#add-edit-show_letter, .add-edit-show_letter-class').css('overflow-x','scroll');
    			            var dLength = [[-1], ["ყველა"]];
    			            var total =	[4,5,6,7,17,18,19,20,23,24];
    			            GetDataTable1("table_letter", aJaxURL_show_letter, "get_list1", 26, "&id="+param.id+"&loan_currency_id="+$("#loan_currency_id").val(), 0, dLength, 4, "desc", total, "<'F'Cpl>");
    			            setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
    						$("#table_letter_length").css('top','0px');
    			            parame 		            = new Object();
    					    parame.act	            = "gel_footer";
    					    parame.id	            = $("#id").val();
    					    parame.loan_currency_id	= $("#loan_currency_id").val();
    					    
    					    $.ajax({
    		    		        url: aJaxURL_show_letter,
    		    			    data: parame,
    		    		        success: function(data) {			        
    		    					if(typeof(data.error) != 'undefined'){
    		    						if(data.error != ''){
    		    							alert(data.error);
    		    						}else{
    		    							gacema_lari      = $("#gacema_lari").html();
    	        							gacema_lari1     = $("#gacema_lari1").html();
    
    	        							darchenili_vali  = $("#darchenili_vali").html();
    	        							darchenili_vali1 = $("#darchenili_vali1").html();
    
    	        							daricxva_lari    = $("#daricxva_lari").html();
    	        							daricxva_lari1   = $("#daricxva_lari1").html();
    	        							
    	        							procenti_lari    = $("#procenti_lari").html();
    	        							procenti_lari1   = $("#procenti_lari1").html();
    	        							
    	        							dziri_lari       = $("#dziri_lari").html();
    	        							dziri_lari1      = $("#dziri_lari1").html();
    
    	        							if(darchenili_vali > 0){
    	        								
    	        								var delta  = ((parseFloat(darchenili_vali) + parseFloat(daricxva_lari)) - (parseFloat(procenti_lari)+parseFloat(dziri_lari))).toFixed(2);
    	        								var delta1 = ((parseFloat(darchenili_vali1) + parseFloat(daricxva_lari1)) - (parseFloat(procenti_lari1)+parseFloat(dziri_lari1))).toFixed(2);	
    	        							}else{
        	        							var delta  = ((parseFloat(gacema_lari) + parseFloat(daricxva_lari)) - (parseFloat(procenti_lari)+parseFloat(dziri_lari))).toFixed(2);
    	        								var delta1 = ((parseFloat(gacema_lari1) + parseFloat(daricxva_lari1)) - (parseFloat(procenti_lari1)+parseFloat(dziri_lari1))).toFixed(2);	
    											
        	                			    }
    
    	        							insurance_fee  = $("#insurance_fee").html();
    	        							insurance_fee1 = $("#insurance_fee1").html();
    	        							
    	        							insurance_payed  = $("#insurance_payed").html();
    	        							insurance_payed1 = $("#insurance_payed1").html();
    
    	        						    ins_delta = (parseFloat(insurance_fee) - parseFloat(insurance_payed)).toFixed(2);
    	        						    ins_delta1 = (parseFloat(insurance_fee1) - parseFloat(insurance_payed1)).toFixed(2);
    
    	        						    if(delta<=0.05 && delta>=-0.05){delta='0.00';}
    	        						    if(delta1<=0.05 && delta1>=-0.05 ){delta1='0.00';}
    	        						    other  = $("#other").html();
    	        						    other1 = $("#other1").html();
    	        							
    	        						    other_delta = (parseFloat(other) - parseFloat(other1)).toFixed(2);
    
    	        						    $("#insurance_delta").html(ins_delta);
    	        						    $("#insurance_delta1").html(ins_delta1);
    	        						    $("#other_delta").html(other_delta);
    	        						    
    	        							$("#remaining_root").html(delta);
    	        							$("#remaining_root_gel").html(delta1);
    	        							
    		    						}
    		    					}
    		    			    }
    		    		    });
    			        }
    				}
    	    	}
    	    });
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
	<button id="add_button">დამატება</button>
	<button id="delete_button">წაშლა</button>
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
                <th style="width: 8%;">#</th>
                <th style="width: 11%;">თარიღი</th>
                <th style="width: 11%;">ანუიტეტი</th>
                <th style="width: 11%;">ძირი</th>
                <th style="width: 10%;">პროცენტი</th>
                <th style="width: 10%;">ჯარიმა</th>
                <th style="width: 10%;">შეთანხმების<br>თანხა</th>
                <th style="width: 10%;">ნაშთი</th>
                <th style="width: 11%;">სტატუსი</th>
                <th style="width: 8%;">ქმედება</th>
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
	<div id="add-edit-show_letter" class="form-dialog" title="ბარათი"></div>
</body>
</html>


