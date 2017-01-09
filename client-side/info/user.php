<html>
<head>
	<script type="text/javascript">
		var aJaxURL	= "server-side/info/user.action.php";		//server side folder url
		var upJaxURL= "server-side/upload/file.action.php";				//server side folder url
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
			GetDataTable(tName, aJaxURL, "get_list", 5, "", 0, "", 1, "asc", "", change_colum_main);
			setTimeout(function(){
		    	$('.ColVis, .dataTable_buttons').css('display','none');
		    }, 10);
		}

		function LoadDialog(){
			var id		= $("#pers_id").val();
			if(id != ""){
				$("#lname_fname").attr("disabled", "disabled");
			}

			GetButtons("choose_button");
			GetButtons("choose_buttondisabled");
			
			/* Dialog Form Selector Name, Buttons Array */
			GetDialog(fName, 450, "auto", "");

			if( $("#position").val() == 13 ){
					$("#passwordTR").removeClass('hidden');
			}
			$( "#accordion" ).accordion({
				active: false,
				collapsible: true,
				heightStyle: "content",
				activate: function(event, ui) {
					$("#is_user").val();
				}
			});
			$('#position').chosen({ search_contains: true });
		}

	    // Add - Save
		$(document).on("click", "#save-dialog", function () {
			param = new Object();

            //Action
	    	param.act	  = "save_pers";

		    param.id	  = $("#pers_id").val();

		    param.n		  = $("#name").val();
		    param.t		  = $("#tin").val();
		    param.p		  = $("#position").val();
		    param.dep_id  = $("#dep_id").val();
		    param.a		  = $("#address").val();
		    param.pas	  = $("#password").val();
		    param.h_n	  = $("#home_number").val();
		    param.m_n	  = $("#mobile_number").val();
		    param.comm	  = $("#comment").val();

		    param.user	  = $("#user").val();
		    param.userp	  = $("#user_password").val();
		    param.gp	  = $("#group_permission").val();
		    param.ext	  = $("#ext").val();
		    param.file_id = $("#file_id").val();

			if(param.n == ""){
				alert("შეავსეთ სახელი და გვარი!");
			}else if(param.p == 0){
				alert("შეავსეთ თანამდებობა!");
			}else if(param.user && !param.userp){
				alert("შეავსეთ პაროლი")
			}else{
			    $.ajax({
			        url: aJaxURL,
				    data: param,
			        success: function(data) {
						if(typeof(data.error) != "undefined"){
							if(data.error != ""){
								alert(data.error);
							}else{
								LoadTable();
				        		CloseDialog(fName);
							}
						}
				    }
			    });
			}

		});

	    $(document).on("click", "#choose_button", function () {
		    $("#choose_file").click();
		});

	    $(document).on("click", "#choose_buttondisabled", function () {
		    alert('თუ გსურთ ახალი სურათის ატვირთვა, წაშალეთ მიმდინარე შურათი!');
		});

	    
	    $(document).on("change", "#choose_file", function () {
	    	
		    
	        var file_url  = $(this).val();
	        var file_name = this.files[0].name;
	        var file_size = this.files[0].size;
	        var file_type = file_url.split('.').pop().toLowerCase();
	        var path	  = "../../media/uploads/file/";

	        if($.inArray(file_type, ['png','jpg']) == -1){
	            alert("დაშვებულია მხოლოდ 'png', 'jpg'  გაფართოება");
	        }else if(file_size > '15728639'){
	            alert("ფაილის ზომა 15MB-ზე მეტია");
	        }else{
	            if($("#pers_id").val() == ''){
		            users_id = $("#is_user").val();
	            }else{
	            	users_id = $("#pers_id").val()
	            }
	        	$.ajaxFileUpload({
			        url: "server-side/upload/file.action.php",
			        secureuri: false,
	     			fileElementId: "choose_file",
	     			dataType: 'json',
				    data: {
						act: "file_upload",
						button_id: "choose_file",
						file_name: Math.ceil(Math.random()*99999999999),
						file_name_original: file_name,
						table_name: '',
						file_type: file_type,
						file_size: file_size,
						path: path,
						table_id: "",

					},
			        success: function(data) {			        
				        if(typeof(data.error) != 'undefined'){
							if(data.error != ''){
								alert(data.error);
							}else{
								$("#upload_img").attr('src','media/uploads/file/'+data.page[0].rand_name);
								$('#choose_button').attr('id','choose_buttondisabled');
								$("#delete_image").attr('image_id',data.page[0].id);
								$(".complate").attr('onclick','view_image('+ data.page[0].id + ')');
								$("#file_id").val(data.page[0].id);
								
								if($("#pers_id").val() != ''){
									param = new Object();

						            param.act	= "update_file_id";

								    param.pers_id = $("#pers_id").val();
								    param.file_id = $("#file_id").val();
								    
    								$.ajax({
    							        url: aJaxURL,
    								    data: param,
    							        success: function(data) {
    										if(typeof(data.error) != "undefined"){
    											if(data.error != ""){
    												alert(data.error);
    											}else{
    											}
    										}
    								    }
    							    });
    							}
							}						
						}					
				    }
			    });
	        }
	    });

	    $(document).on("click", "#delete_image", function () {
		    $.ajax({
	            url: "server-side/upload/file.action.php",
	            data: "act=delete_file&file_id="+$(this).attr('image_id'),
	            success: function(data) {
	               $('#upload_img').attr('src','media/uploads/file/0.jpg');               
	               $("#choose_button").button();
	               $('#choose_buttondisabled').attr('id','choose_button');
	               $("#file_id").val('');
	               if($("#pers_id").val !=''){
                	    param         = new Object();
    					param.act	  = "delete_file_id";
    					param.pers_id = $("#pers_id").val();
    					
    				    $.ajax({
    				        url: aJaxURL,
    					    data: param,
    				        success: function(data) {
    							if(typeof(data.error) != "undefined"){
    								if(data.error != ""){
    									alert(data.error);
    								}else{
    								}
    							}
    					    }
    				    });
		           }
	            }
	        });
		});

		function view_image(id){
			param = new Object();

	        //Action
	    	param.act	= "view_img";
	    	param.id    = id;
	    	
			$.ajax({
		        url: aJaxURL,
			    data: param,
		        success: function(data) {
					if(typeof(data.error) != "undefined"){
						if(data.error != ""){
							alert(data.error);
						}else{
							var buttons = {
						        	"cancel": {
							            text: "დახურვა",
							            id: "cancel-dialog",
							            click: function () {
							            	$(this).dialog("close");
							            }
							        }
							    };
							GetDialog("add-edit-form-img", 401, "auto", buttons, 'center top');
							$("#add-edit-form-img").html(data.page);
						}
					}
			    }
		    });
		}



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
</head>

<body>
<div id="tabs" style="width: 90%">
<div class="callapp_head">თანამშრომლები<hr class="callapp_head_hr"></div>
    
    <div style="margin-top: 15px;">
        <button id="add_button">დამატება</button>
        <button id="delete_button">წაშლა</button>
    </div>
    
<div class="callapp_filter_show">
<table id="table_right_menu" style="top: 24px;">
    <tr>
        <td>
        	<img alt="table" src="media/images/icons/table_w.png" height="14" width="14">
        </td>
        <td>
        	<img alt="log" src="media/images/icons/log.png" height="14" width="14">
        </td>
        <td id="show_copy_prit_exel" myvar="0">
        	<img alt="link" src="media/images/icons/select.png" height="14" width="14">
        </td>
    </tr>
</table>
<table class="display" id="example" style="width: 100%">
    <thead>
        <tr id="datatable_header">
            <th>ID</th>
            <th style="width: 20%">ვინაობა</th>
            <th style="width: 20%">პირადი ნომერი</th>
            <th style="width: 20%">თანამდებობა</th>
            <th style="width: 37%">მისამართი</th>
            <th style="width: 3%">#</th>
        </tr>
    </thead>
    <thead>
        <tr class="search_header">
            <th class="colum_hidden">
            	<input type="text" name="search_id" value="ფილტრი" class="search_init" style="width: 80%"/>
            </th>
            <th>
                <input type="text" name="search_name" value="ფილტრი" class="search_init" style="width: 80%"/>
            </th>
            <th>
                <input type="text" name="search_tin" value="ფილტრი" class="search_init" style="width: 80%"/>
            </th>
            <th>
                <input type="text" name="search_tin" value="ფილტრი" class="search_init" style="width: 80%"/>
            </th>
            <th>
                <input type="text" name="search_position" value="ფილტრი" class="search_init" style="width: 80%"/>
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
    <div id="image-form" class="form-dialog" title="თანამშრომლის სურათი">
    	<img id="view_img" src="media/uploads/images/worker/0.jpg">
	</div>
	 <!-- jQuery Dialog -->
    <div id="add-group-form" class="form-dialog" title="ჯგუფი">
	</div>
	<div id="add-edit-form-img" class="form-dialog" title="თანამშრომლის სურათი">
	</div>
	
</body>
</html>