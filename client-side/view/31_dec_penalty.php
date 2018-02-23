<html>
<head>
	<script type="text/javascript">
		var aJaxURL	          = "server-side/view/31_dec_penalty.action.php";
		var aJaxURL_show_letter = "server-side/main.action.php";		//server side folder url
		var tName	          = "example";													//table name
		var fName	          = "add-edit-form";												//form name
		var change_colum_main = "<'dataTable_buttons'T><'F'Cfipl>";
		    	
		$(document).ready(function () {        	
			LoadTable(tName,8,change_colum_main,aJaxURL);	
 						
			/* Add Button ID, Delete Button ID */
			$("#check_penalty").button();
			SetEvents("", "check_penalty", "check-all", tName, fName, aJaxURL,'',tName,8,change_colum_main,aJaxURL,'','','');
		});
        
		function LoadTable(tName,num,change_colum_main,aJaxURL){
			/* Table ID, aJaxURL, Action, Colum Number, Custom Request, Hidden Colum, Menu Array */
			GetDataTable(tName, aJaxURL, 'get_list', num, "&agr_id="+$("#filt_agr_id").val(), 0, "", 2, "asc", "", change_colum_main);
			setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
		}
		
// 		function LoadDialog(fname){
// 			var id		= $("#id").val();
			
// 			if(fname=='add-edit-form'){
//     			GetDialog(fName, 270, "auto", "","top");
//     			GetDate("schedule_date");
//     		}
// 		}

	    
	    
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
			param 	               = new Object();
    		param.act              = "get_edit_page";
    		param.id               = $(this).attr('client_id');
    		param.loan_currency_id = $(this).attr('loan_currency_id');
    		
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
   		    							 setTimeout(function(){
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
        	        						    if(delta1<=0.05 && delta1>=-0.05){delta1='0.00';}
        	        						    
        	        						    other  = $("#other").html();
        	        						    other1 = $("#other1").html();
        	        							
        	        						    other_delta = (parseFloat(other) - parseFloat(other1)).toFixed(2);
        	        						    if(ins_delta == '0.00' || ins_delta1=='0.00'){
        	        						    	ins_delta  = '0.00';
        	        						    	ins_delta1 = '0.00';
	        	        						}
        	        						    $("#insurance_delta").html(ins_delta);
        	        						    $("#insurance_delta1").html(ins_delta1);
        	        						    $("#other_delta").html(other_delta);
        	        						    
        	        							$("#remaining_root").html(delta);
        	        							$("#remaining_root_gel").html(delta1);
    		    							}, 90);
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
<div class="callapp_head">დავალიანება 31 დეკემბრის მდგომარეობით<hr class="callapp_head_hr"></div>
<div id="button_area">
	<button id="check_penalty">დარიცვხა</button>
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
                <th style="width: 17%;">კლიენტი</th>
                <th style="width: 17%;">ხელშეკრულების ნომერი</th>
                <th style="width: 17%;">ორისის კოდი</th>
                <th style="width: 17%;">გადაუხდელი <br>თვეების <br>რაოდენობა</th>
                <th style="width: 15%;">გადახდის თარიღი</th>
                <th style="width: 17%;">სტატუსი</th>
                <th style="width: 150px;">ქმედება</th>
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


