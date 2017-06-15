<html>
<head>
	<script type="text/javascript">
		var aJaxURL	          = "server-side/main.action.php";		//server side folder url
		var aJaxURL1	      = "server-side/view/cource.action.php";		//server side folder url
		var tName	          = "table_";													//table name
		var dialog	          = "add-edit-form";												//form name
		var colum_number      = 17;
	    var main_act          = "get_list";
	    var tbName            = 'tabs1';
	    var change_colum_main = "<'dataTable_buttons'T><'F'Cfipl>"; 
	      	
		$(document).ready(function () { 
			$("#filt_year").chosen();
			$("#difference_cource").button();
			  
			LoadTable("example",colum_number,'get_list',change_colum_main,aJaxURL);	
			SetEvents("", "", "", tName+'example', dialog, aJaxURL,'','example',colum_number,main_act,change_colum_main,aJaxURL,'');
			
			param 	       = new Object();
    		param.act      = "get_add_page";
    		param.status   = 1;
    		
    		$.ajax({
    	        url: aJaxURL1,
    		    data: param,
    	        success: function(data) {       
    				if(typeof(data.error) != "undefined"){
    					if(data.error != ""){
    						alert(data.error);
    					}else{
        					if(data.page != 1){
        						$("#add-edit-form-cource").html(data.page);
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
        			            GetDialog("add-edit-form-cource", 270, "auto", buttons, 'center top');
        					}
    			        }
    				}
    	    	}
    	    });
    	    
			
 		});
        
		function LoadTable(tbl,num,act,change_colum_main,aJaxURL){
			var dLength = [[10, 30, 50, -1], [10, 30, 50, "ყველა"]];
			
			if(tbl == 'letter'){
				var total =	[4,5,14];
				GetDataTable1(tName+tbl, aJaxURL, act, num, "&id="+$("#id").val()+"&loan_currency_id="+$("#loan_currency_id").val()+"&loan_currency_id="+$("#loan_currency_id").val(), 0, dLength, 4, "desc", total, change_colum_main);
				
				param 		            = new Object();
			    param.act	            = "gel_footer";
			    param.id	            = $("#id").val();
			    param.loan_currency_id	= $("#loan_currency_id").val();
			    
			    $.ajax({
    		        url: aJaxURL,
    			    data: param,
    		        success: function(data) {			        
    					if(typeof(data.error) != 'undefined'){
    						if(data.error != ''){
    							alert(data.error);
    						}else{
    							$("#remaining_root").html(data.remaining_root);
    							$("#remaining_root_gel").html(data.remaining_root_gel);
    						}
    					}
    			    }
    		    });
			}else{
				GetDataTable(tName+tbl, aJaxURL, act, num, "&id="+$("#id").val()+"&filt_year="+$("#filt_year").val(), 0, dLength, 3, "asc", "", change_colum_main);
			}
			$("#table_letter_length").css('top', '2px');
			setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
		}

		$(document).on("tabsactivate", "#tabs1", function() {
        	var tab = GetSelectedTab(tbName);
        	if (tab == 0){
        		GetDate('pay_datee');
	            $("#pay_datee").blur();
        	}else{
        		GetDate('pay_datee1');
	            $("#pay_datee1").blur();
	            $("#check_calculation_out").button();
        	}
        });
        
		function LoadDialog(fName){
	        if(fName == 'add-edit-form'){
	        	var buttons = {
	        			"show_loan": {
	    		            text: "ხელშეკრულების პირობები",
	    		            id: "show_loan",
	    		            click: function () {
	    		            	param 	       = new Object();
	    		    			param.act      = "show_loan";
	    		    			param.hidde_id = $("#id").val();
	    		    			
	    		    			$.ajax({
	    		        	        url: aJaxURL,
	    		        		    data: param,
	    		        	        success: function(data) {       
	    		        				if(typeof(data.error) != "undefined"){
	    		        					if(data.error != ""){
	    		        						alert(data.error);
	    		        					}else{
	    		        						$("#add-edit-form-loan").html(data.page);
	    		        						var buttons = {
			        			    				"cancel": {
			        			    		            text: "დახურვა",
			        			    		            id: "cancel-dialog",
			        			    		            click: function () {
			        			    		            	$(this).dialog("close");
			        			    		            }
			        			    		        }
			        			    		    };
	    		        			            GetDialog("add-edit-form-loan", 890, "auto", buttons, 'left+43 top');
	    		        			            $("#check_monthly_pay").button();
	    		        			            $("#loan_agreement_type").chosen();
	    		        			            $("#agreement_type_id").chosen();
	    		        			            $("#loan_currency").chosen();
	    		        			            $("#responsible_user_id").chosen();
	    		        			            $("#check_monthly_pay").button("disable");
	    		        			            $('#loan_agreement_type').prop('disabled', true).trigger("chosen:updated");
	    		        			            $('#loan_currency').prop('disabled', true).trigger("chosen:updated");
	    		        			            $('#agreement_type_id').prop('disabled', true).trigger("chosen:updated");
	    		        			            $('#responsible_user_id').prop('disabled', true).trigger("chosen:updated");
	    		        			            
	    		        			        }
	    		        				}
	    		        	    	}
	    		        	    });
	    		            }
	    		        },
	        			"car-depriving": {
	    		            text: "მანქანის გადაფორმება შპს-ზე",
	    		            id: "car-depriving",
	    		            click: function () {
	    		            	param 	       = new Object();
	    		    			param.act      = "cancel_loan";
	    		    			param.hidde_id = $("#id").val();
	    		    			
	    		    			$.ajax({
	    		    		        url: aJaxURL,
	    		    			    data: param,
	    		    		        success: function(data) {       
	    		    					if(typeof(data.error) != "undefined"){
	    		    						if(data.error != ""){
	    		    							alert(data.error);
	    		    						}else{
	    		    							alert('ხელშეკრულება წარმატებით დაიხურა');
	    		    							$("#add-edit-form").dialog("close");
	    		    							LoadTable('index',colum_number,main_act,change_colum_main,aJaxURL,'','');
	    		    						}
	    		    					}
	    		    		    	}
	    		    		    });
	    		            }
	    		        },
	        			"calculation-dialog": {
	    		            text: "წინასწარი კალკულაცია",
	    		            id: "calculation-dialog",
	    		            click: function () {
	    		            	param 	       = new Object();
	    		        		param.act      = "get_calculation";
	    		        		param.hidde_id = $("#id").val();
	    		        		
	    		        		$.ajax({
	    		        	        url: aJaxURL,
	    		        		    data: param,
	    		        	        success: function(data) {       
	    		        				if(typeof(data.error) != "undefined"){
	    		        					if(data.error != ""){
	    		        						alert(data.error);
	    		        					}else{
	    		        						$("#add-edit-form-calculation").html(data.page);
	    		        						var buttons = {
			        			    				"cancel": {
			        			    		            text: "დახურვა",
			        			    		            id: "cancel-dialog",
			        			    		            click: function () {
			        			    		            	$(this).dialog("close");
			        			    		            }
			        			    		        }
			        			    		    };
	    		        			            GetDialog("add-edit-form-calculation", 521, "auto", buttons, 'left+43 top');
	    		        			            GetTabs('tabs1');
	    		        			            $("#check_calculation").button();
	    		        			            GetDate('pay_datee');
	    		        			            $("#pay_datee").blur();
	    		        			            
	    		        			        }
	    		        				}
	    		        	    	}
	    		        	    });
	    		            }
	    		        },
	    		        "cancel-loan": {
	    		            text: "სესხის დახურვა",
	    		            id: "cancel-loan",
	    		            click: function () {
	    		            	param 	       = new Object();
	    		        		param.act      = "get_canceled-loan_dialog";
	    		        		param.hidde_id = $("#id").val();
	    		        		
	    		        		$.ajax({
	    		        	        url: aJaxURL,
	    		        		    data: param,
	    		        	        success: function(data) {       
	    		        				if(typeof(data.error) != "undefined"){
	    		        					if(data.error != ""){
	    		        						alert(data.error);
	    		        					}else{
	    		        						$("#add-edit-form-canceled").html(data.page);
	    		        						var buttons = {
			        			    				"save": {
			        			    		            text: "ხელშეკრულების დახურვა",
			        			    		            id: "canceled_client_loan"
			        			    		        },
			        			    	        	"cancel": {
			        			    		            text: "დახურვა",
			        			    		            id: "cancel-dialog",
			        			    		            click: function () {
			        			    		            	$(this).dialog("close");
			        			    		            }
			        			    		        }
			        			    		    };
	    		        			            GetDialog("add-edit-form-canceled", 300, "auto", buttons, 'center top');
	    		        			        }
	    		        				}
	    		        	    	}
	    		        	    });
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
	            GetDialog(fName, 1262, "auto", buttons, 'left+43 top');
	            
	            if($("#canceled_status").val()==1){
	            	$("#calculation-dialog").button("disable");
	            	$("#cancel-loan").button("disable");
		        }else{
		        	$("#calculation-dialog").button("enable");
	            	$("#cancel-loan").button("enable");
			    }
	            $(".add-edit-form-class").css('position','fixed');
	            LoadTable('letter', 16, 'get_list1', "<'F'Cpl>", aJaxURL, '');
	            
	        }
	    }

		$(document).on("click", "#check_calculation", function () {
			param 	  = new Object();
			param.act = "check_calculation";

			param.local_id	= $("#id").val();
			param.pay_datee	= $('#pay_datee').val();
			
			if(param.pay_datee == ''){
				alert('შეავსე თარიღი');
			}else{
    			$.ajax({
    		        url: aJaxURL,
    			    data: param,
    		        success: function(data) {       
    					if(typeof(data.error) != "undefined"){
    						if(data.error != ""){
    							alert(data.error);
    						}else{
    							$("#full_fee2").val(data.pay_amount);
    							$("#root_fee2").val(data.root);
    							$("#percent_fee2").val(data.percent);
    							$("#penalty_fee2").val(data.penalty);
    							$("#full_pay2").val(data.pay_amount1);
    						}
    					}
    		    	}
    		   });
		    }
		});
		
		
		$(document).on("click", "#check_calculation_out", function () {
			param 	  = new Object();
			param.act = "check_calculation_out";

			param.local_id	 = $("#id").val();
			param.pay_datee1 = $('#pay_datee1').val();

			if(param.pay_datee1 == ''){
				alert('შეავსე თარიღი');
			}else{
    			$.ajax({
    		        url: aJaxURL,
    			    data: param,
    		        success: function(data) {       
    					if(typeof(data.error) != "undefined"){
    						if(data.error != ""){
    							alert(data.error);
    						}else{
    							$("#full_fee3").val(data.pay_amount);
    							$("#root_fee3").val(data.root);
    							$("#percent_fee3").val(data.percent);
    							$("#penalty_fee3").val(data.penalty);
    							$("#full_pay3").val(data.pay_amount1);
    							$("#nasargeblebi").val(data.nasargeblebebi);
    							$("#sakomiso").val(data.sakomisio);
    						}
    					}
    		    	}
    		   });
    		}
		});
		
		$(document).on("click", "#canceled_client_loan", function () {
	    	param 	       = new Object();
			param.act      = "cancel_loan";
			param.hidde_id = $("#id").val();
			
			$.ajax({
		        url: aJaxURL,
			    data: param,
		        success: function(data) {       
					if(typeof(data.error) != "undefined"){
						if(data.error != ""){
							alert(data.error);
						}else{
							alert('ხელშეკრულება წარმატებით დაიხურა');
							$("#add-edit-form").dialog("close");
							LoadTable('index',colum_number,main_act,change_colum_main,aJaxURL,'','');
						}
					}
		    	}
		    });
		});

		$(document).on("click", "#save-dialog", function () {
		    param 			= new Object();

		    param.act	 = "save_cource";
	    	param.id	 = $("#id").val();
	    	param.cource = $("#cource").val();
	    	
			if(param.cource == ""){
				alert("შეავსეთ კურსი!");
			}else {
			    $.ajax({
			        url: aJaxURL1,
				    data: param,
			        success: function(data) {			        
						if(typeof(data.error) != 'undefined'){
							if(data.error != ''){
								alert(data.error);
							}else{
								CloseDialog('add-edit-form-cource');
							}
						}
				    }
			    });
			}
		});

		$(document).on("click", "#difference_cource", function () {
		    param 	  = new Object();
			param.act = "get_difference";
			
		    $.ajax({
		        url: aJaxURL,
			    data: param,
		        success: function(data) {			        
					if(typeof(data.error) != 'undefined'){
						if(data.error != ''){
							alert(data.error);
						}else{
							alert('აქტიური სესხების კურსთა შორის სხვაობა წარმატებით დაანგარიშდა');
						}
					}
			    }
		    });
		});
		$(document).on("change", "#filt_year", function () {
			LoadTable("example",colum_number,'get_list',change_colum_main,aJaxURL);	
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
        #dialog-form fieldset select{
        	height: 19px;
        	width: 70px;
        }
    </style>
</head>

<body>
<div id="tabs" style="width: 1500px">
<div class="callapp_head">მთავარი<hr class="callapp_head_hr"></div>
<div id="button_area">
	<table>
		<tr>
			<td style="width: 280px;">
        		<button id="difference_cource">კურსთა შორის სხვაობის დაანგარიშება</button>
        	</td>
        	<td>
                <select id="filt_year" style="width:  100px;">
            		<?php 
            		
                		mysql_connect('212.72.155.176','root','Gl-1114');
                		mysql_select_db('tgmobile');
                		mysql_set_charset ( 'utf8');
                		
                        $req = mysql_fetch_assoc(mysql_query("SELECT YEAR(CURDATE())+1 AS `year`,
                                                                     YEAR(CURDATE()) AS `cur_year`"));
                        $year = $req['year'];
                        for ($i=0; $i<=10; $i++){
                            $year --; 
                            if($req['cur_year'] == $year){
                                $data1 .= '<option value="' . $year . '" selected="selected">' . $year . '</option>';
                            } else {
                                $data1 .= '<option value="' . $year . '">' . $year . '</option>';
                            }
                        }
                        
                        echo $data1;
            		 ?>
            	</select>
        	</td>
    	</tr>
	</table>
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
    <table class="display" id="table_example" style="width: 100%;">
        <thead>
            <tr id="datatable_header">
                <th>ID</th>
                <th style="width: 6%;">თარიღი</th>
                <th style="width: 16%;">მარკა</th>
                <th style="width: 6%;">კოდი</th>
                <th style="width: 8%;">ს/ხ</th>
                <th style="width: 7%;">პროცენტი</th>
                <th style="width: 5%;">ფასი<br>$</th>
                <th style="width: 5%;">კურსი</th>
                <th style="width: 5%;">ფასი<br>ლ</th>
                <th style="width: 5%;">დარიცხ.%<br>$</th>
                <th style="width: 5%;">დარიცხ.%<br>ლ</th>
                <th style="width: 5%;">დარჩე-<br>ნილი<br>ვალი<br>$</th>
                <th style="width: 5%;">დარჩე-<br>ნილი<br>ძირი<br>$</th>
                <th style="width: 5%;">დარჩე-<br>ნილი<br>ვალი<br>ლ</th>
                <th style="width: 5%;">დარჩე-<br>ნილი<br>ძირი<br>ლ</th>
                <th style="width: 6%;">გაყვანა</th>
                <th style="width: 5%;">ნაშთი<br>ლარში</th>
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
</body>
</html>
<div  id="add-edit-form" class="form-dialog" title="ბარათები"></div>
<div id="add-edit-form-calculation" class="form-dialog" title="წინასწარი კალკულაცია"></div>
<div id="add-edit-form-canceled" class="form-dialog" title="დაანგარიშებული თანხა"></div>
<div id="add-edit-form-cource" class="form-dialog" title="შეიყვანეთ დღევანდელი კურსი"></div>
<div id="add-edit-form-loan" class="form-dialog" title="ხელშეკრულების პირობები"></div>

