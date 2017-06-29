<html>
<head>
	<script type="text/javascript">
		var aJaxURL	          = "server-side/report/transaction_book.action.php";		//server side folder url
		var tName	          = "example";	//table name
		var fName	          = "add-edit-form"; //form name
		var change_colum_main = "<'dataTable_buttons'T><'F'Cfipl>";
		    	
		$(document).ready(function () {
			$("#filt_day").chosen();        	
			LoadTable(tName,10,change_colum_main,aJaxURL);
		});
        
		function LoadTable(tName,num,change_colum_main,aJaxURL){
			var total =	[6,7,8];
			GetDataTable(tName, aJaxURL, 'get_list', num, "&filt_day="+$("#filt_day").val(), 0, "", 1, "desc", total, change_colum_main);
			setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
		}

		$(document).on("change", "#filt_day", function () {
			LoadTable(tName,10,change_colum_main,aJaxURL);	 
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
            top: 24px;
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
<div id="tabs" style="width: 95%;">
<div class="callapp_head">დარიცხვების ჟურნალი<hr class="callapp_head_hr"></div>
    <div id="button_area">
    	<select id="filt_day" style="width:  100px;">
    	    <option value="0">ყველა</option>
    		<option value="1">01</option>
    		<option value="2">02</option>
    		<option value="3">03</option>
    		<option value="4">04</option>
    		<option value="5">05</option>
    		<option value="6">06</option>
    		<option value="7">07</option>
    		<option value="8">08</option>
    		<option value="9">09</option>
    		<option value="10">10</option>
    		<option value="11">11</option>
    		<option value="12">12</option>
    		<option value="13">13</option>
    		<option value="14">14</option>
    		<option value="15">15</option>
    		<option value="16">16</option>
    		<option value="17">17</option>
    		<option value="18">18</option>
    		<option value="19">19</option>
    		<option value="20">20</option>
    		<option value="21">21</option>
    		<option value="22">22</option>
    		<option value="23">23</option>
    		<option value="24">24</option>
    		<option value="25">25</option>
    		<option value="26">26</option>
    		<option value="27">27</option>
    		<option value="28">28</option>
    		<option value="29">29</option>
    		<option value="30">30</option>
    		<option value="31">31</option>
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
    <table class="display" id="example" >
        <thead>
            <tr id="datatable_header">
                <th>ID</th>
                <th style="width: 9%;">თარიღი</th>
                <th style="width: 28%;">კლიენტი</th>
                <th style="width: 9%;">ორისის კოდი</th>
                <th style="width: 9%;">ს/ხ</th>
                <th style="width: 9%;">ვალუტა</th>
                <th style="width: 9%;">დარიცხვა%<br>დოლარი</th>
                <th style="width: 9%;">დარიცხვა%<br>ლარი</th>
            	<th style="width: 9%;">ზედმეტობა</th>
            	<th style="width: 9%;">სტატუსი</th>
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
        <tfoot>
            <tr>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th style="text-align: left; font-weight: bold;"><p align="right">სულ ჯამი</p></th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
                <th>&nbsp;</th>
            </tr>
        </tfoot>
    </table>
</body>
</html>


