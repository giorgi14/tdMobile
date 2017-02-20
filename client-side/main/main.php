<html>
<head>
	<script type="text/javascript">
		var aJaxURL	          = "server-side/main.action.php";		//server side folder url
		var tName	          = "table_";													//table name
		var dialog	          = "add-edit-form";												//form name
		var colum_number      = 17;
	    var main_act          = "get_list";
	    var change_colum_main = "<'dataTable_buttons'T><'F'Cfipl>";   	
		$(document).ready(function () {        	
			LoadTable("example",colum_number,'get_list',change_colum_main,aJaxURL);	
			SetEvents("", "", "", tName+'example', dialog, aJaxURL,'','example',colum_number,main_act,change_colum_main,aJaxURL,'');
 		});
        
		function LoadTable(tbl,num,act,change_colum_main,aJaxURL){
			var dLength = [[50, -1], [50, "ყველა"]];
			
			if(tbl == 'letter'){
				var total =	[4,5,6,7,8,9,10,11,12,13];
				GetDataTable1(tName+tbl, aJaxURL, act, num, "&id="+$("#id").val()+"&loan_currency_id="+$("#loan_currency_id").val(), 0, dLength, 1, "asc", total, change_colum_main);
				
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
    							$("#insurance_fee").html(data.insurance_fee);
    							
    							delta_cource = parseFloat($("#dziri_lari").html()) + parseFloat($("#procenti_lari").html())-parseFloat($("#daricxva_lari").html())-parseFloat($("#gacema_lari").html());
                            	$("#delta_cource").html(delta_cource);
                                
    						}
    					}
    			    }
    		    });
			}else{
				GetDataTable1(tName+tbl, aJaxURL, act, num, "&id="+$("#id").val()+"&loan_currency_id="+$("#loan_currency_id").val(), 0, dLength, 1, "asc", "", change_colum_main);
			}
			$("#table_letter_length").css('top', '2px');
			setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
		}

		function LoadDialog(fName){
	        if(fName == 'add-edit-form'){
	        	var buttons = {
	        			"cancel": {
	    		            text: "დახურვა",
	    		            id: "cancel-dialog",
	    		            click: function () {
	    		            	$(this).dialog("close");
	    		            }
	    		        }
	    		    };
	            GetDialog(fName, 1262, "auto", buttons, 'left+43 top');
	            LoadTable('letter', 17, 'get_list1', "<'F'Cpl>", aJaxURL, '');
	            
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
        #dialog-form fieldset select{
        	height: 19px;
        	width: 70px;
        }
    </style>
</head>

<body>
<div id="tabs" style="width: 95%">
<div class="callapp_head">მთავარი<hr class="callapp_head_hr"></div>
<div id="button_area">
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
    <table class="display" id="table_example" >
        <thead>
            <tr id="datatable_header">
                <th>ID</th>
                <th style="width: 6%;">თარიღი</th>
                <th style="width: 8%;">მარკა</th>
                <th style="width: 5%;">კოდი</th>
                <th style="width: 6%;">ს/ხ</th>
                <th style="width: 7%;">პროცენტი</th>
                <th style="width: 6%;">ფასი<br>$</th>
                <th style="width: 5%;">კურსი</th>
                <th style="width: 6%;">ფასი<br>ლ</th>
                <th style="width: 7%;">დარიცხ.%<br>$</th>
                <th style="width: 7%;">დარიცხ.%<br>ლ</th>
                <th style="width: 6%;">დარჩე-<br>ნილი<br>ვალი<br>$</th>
                <th style="width: 6%;">დარჩე-<br>ნილი<br>ვალი<br>ლ</th>
                <th style="width: 6%;">დარჩე-<br>ნილი<br>ძირი<br>$</th>
                <th style="width: 6%;">დარჩე-<br>ნილი<br>ძირი<br>ლ</th>
                <th style="width: 6%;">გაყვანა</th>
                <th style="width: 6%;">ნაშთი<br>ლარში</th>
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

