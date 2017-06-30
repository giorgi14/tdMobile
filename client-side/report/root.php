<html>
<head>
	<script type="text/javascript">
		var aJaxURL	          = "server-side/report/root.action.php";		//server side folder url
		var tName	          = "table_";													//table name
		var dialog	          = "add-edit-form";												//form name
		var colum_number      = 10;
	    var main_act          = "get_list";
	    var change_colum_main = "<'dataTable_buttons'T><'F'Cfipl>";   	
		$(document).ready(function () {
			$("#filt_month").chosen();
			$("#filt_year").chosen();
			LoadTable("example",colum_number,'get_list',change_colum_main,aJaxURL);	
		});
		
        function LoadTable(tbl,num,act,change_colum_main,aJaxURL){
			var dLength = [[50, -1], [50, "ყველა"]];
			GetDataTable1(tName+tbl, aJaxURL, act, num, "&filt_month="+$("#filt_month").val()+"&filt_year="+$("#filt_year").val(), 0, dLength, 1, "asc", "", change_colum_main);
			$("#table_letter_length").css('top', '2px');
			setTimeout(function(){$('.ColVis, .dataTable_buttons').css('display','none');}, 90);
		}

        $(document).on("change", "#filt_month", function () {
        	LoadTable("example",colum_number,'get_list',change_colum_main,aJaxURL);	 
	    });

        $(document).on("change", "#filt_year", function () {
        	LoadTable("example",colum_number,'get_list',change_colum_main,aJaxURL);	 
	    });

        $(document).on("click", ".callapp_refresh", function () {
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
        .callapp_refresh{
            padding: 5px;
            border-radius:3px;
            color:#FFF;
            background: #9AAF24;
            float: right;
            font-size: 13px;
            cursor: pointer;
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
    <div class="callapp_head">გადახდილი ძირი თანხა თვეების მიხედვით<span class="callapp_refresh"><img alt="refresh" src="media/images/icons/refresh.png" height="14" width="14">   განახლება</span><hr class="callapp_head_hr"></div>
    <div id="button_area">
    	<select id="filt_month" style="width:  130px;">
    		<?php 
    		
        		mysql_connect('212.72.155.176','root','Gl-1114');
        		mysql_select_db('tgmobile');
        		mysql_set_charset ( 'utf8');
    		
    		    $c_date	= date('m');
                $req = mysql_query("SELECT id,
                                          `name`
                                    FROM   month");
    
                while( $res = mysql_fetch_assoc($req)){
                    if($res['id'] == $c_date){
                        $data .= '<option value="' . $res['id'] . '" selected="selected">' . $res['name'] . '</option>';
                    } else {
                        $data .= '<option value="' . $res['id'] . '">' . $res['name'] . '</option>';
                    }
                }
                
                echo $data;
    		 ?>
    	</select>
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

    <table class="display" id="table_example" >
        <thead>
            <tr id="datatable_header">
                <th>ID</th>
                <th style="width: 7%;">თარიღი</th>
                <th style="width: 40%;">სახელი, გვარი</th>
                <th style="width: 7%;">კოდი</th>
                <th style="width: 7%;">ს/ხ</th>
                <th style="width: 7%;">სესხი</th>
                <th style="width: 9%;">დარიცხვის<br>თარიღი</th>
                <th style="width: 7%;">გადახდის<br>თარიღი</th>
                <th style="width: 9%;">გადახდილი<br>ძირი</th>
                <th style="width: 7%;">დარჩენილი<br>ძირი</th>
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
</body>
</html>
