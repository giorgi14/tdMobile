<?php include('classes/core.php');?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" /> 
   
    <title>Menu</title>
	<link rel="stylesheet" href="media/css/menu/text.css" />
	<link rel="stylesheet" href="media/css/menu/960_fluid.css" />
	<link rel="stylesheet" href="media/css/menu/main.css" />
	<link rel="stylesheet" href="media/css/menu/bar_nav.css" />
	<link rel="stylesheet" href="media/css/menu/side_nav.css" />
	<link rel="stylesheet" href="media/css/menu/skins/theme_blue.css" /> <?php require_once('includes/functions.php');?>
    <style type="text/css">
    html,body{
    	min-height: 100% !important;
    }
    #page-container{
    	float: left !important;
    	margin-left: 85px !important;
    	width: 90%;
    	background: #fff;
    }
    /* Define the body style */

#menuwrapper ul, #menuwrapper ul li{
    margin:0;
    padding:0;
    list-style:none;
	font-size: 11px;
}
#menuwrapper ul li ul{
	display: none;
}
#menuwrapper{
	z-index: 11;
    position:fixed;
    left:0;
    top:0;
    bottom:0;
}
#sidemenu{
	background:#272727;
    width:85px;
    height:100%;
}
#sidemenu li{
	padding: 5px 0 !important;
}
#menuwrapper ul li{
    background-color:#272727;
    width:85px;
    cursor:pointer;
    text-align:center;
}

#menuwrapper ul li:hover{
    /*position:relative;*/
    
}

/* We apply the link style */
#menuwrapper ul li a{
    padding-top:10px;
    padding-bottom:10px;
    color:#2681DC;
    display:inline-block;
    text-decoration:none;
	font-family: pvn;
	font-weight: bolder;
	width: 80px;
}

#menuwrapper ul li a.selected{
 	color:#ddd;   
}
#change_a{
	color:#2681DC;
    transition:color 0.3s ease;
    -o-transition:color 0.3s ease;
    -webkit-transition:color 0.3s ease;
    -moz-transition:color 0.3s ease;
}

.isari {
    content: "  ";
    display: inline-block;
    border-bottom: 10px solid transparent;
    border-left: 6px solid transparent;
    border-right: 10px solid #2681DC;
    border-top: 10px solid transparent;
    height: 0px;
    margin-top: -2px;
    padding: 2px !important;
	padding-bottom: 11px !important;
    width: 69px;
    transition:border-right 1s ease;
    -o-transition:border-right 1s ease;
    -webkit-transition:border-right 1s ease;
    -moz-transition:border-right 1s ease;
}

.after_class {
	border-right: 0.8em solid transparent;
    transition:none;
    -o-transition:none;
    -webkit-transition:none;
    -moz-transition:none;
}

#show_ul{
    position:absolute;
    display:block;
    visibility:hidden;
    height:100%;
    background-color:transaprent;
    width:0px;
    color:#fff;
    
}

#show_ul li{
    width: 100% !important;
}
#show_ul li a{
    width: 90% !important;
    text-align: left;
    padding-left: 15px;
	padding-top: 7px !important;
	padding-bottom: 7px !important;
}
#menuwrapper ul li ul li{
    color:#fff;
    background:transparent;

}

#show_ul{
    left:85px;
    top:0px;
    display:block !important;
    visibility:visible;
    width:400px;
    background-color:#2681DC;
    transition:background-color 0.5s ease;
    -o-transition:background-color 0.5s ease;
    -webkit-transition:background-color 0.5s ease;
    -moz-transition:background-color 0.5s ease;
    
}
.hide_my_ass{
	display: none;
}
.show_my_ass{
	display: block;
}
#show_ul a{
    color: #FFF !important;
}
    </style>
    <script type="text/javascript">

    var menu = '';
    $(function(){
        var windowH = $(window).height();
        var wrapperH = $('#menuwrapper').height();
        if(windowH > wrapperH) {                            
            $('#menuwrapper').css({'height':($(window).height())+'px'});
        }                                                                               
        $(window).resize(function(){
            var windowH = $(window).height();
            var wrapperH = $('#menuwrapper').height();
            var differenceH = windowH - wrapperH;
            var newH = wrapperH + differenceH;
            var truecontentH = $('body').height();
            if(windowH > truecontentH) {
                $('#menuwrapper').css('height', (newH)+'px');
            }

        })          
    });    
    var sub_menu = '';
	var AjaxURL = "includes/menu.server.php";	
	$.ajax({
        url: AjaxURL,
        data: "act=get_product_info",
        dataType: "json",
        success: function(data) {
        	var i = 0;        	
        	var page_id = '';
        	var page_name = '';
        	$( data.nav ).each(function( index ) {
      		    page_id = data.nav[i].id;
      		    page_name = data.nav[i].title;
      		    url = data.nav[i].url;
        		sub_menu = '';
      		    if(url == "#"){
          		    var sub_i = 0;
            		sub_menu += '<ul>';            		
      		    	$( data.nav[i].sub ).each(function( index ) {
      		    		if(data.nav[i].sub[sub_i].url == "#"){
      		    		    sub_menu += '<li class="click_me" page_id="'+data.nav[i].sub[sub_i].id+'" style="border-bottom: 1px solid #3C8EE0;"><a style="font-weight: bold; font-size: 13px;" href="'+ data.nav[i].sub[sub_i].url +'">' + data.nav[i].sub[sub_i].title + '</a><span class="span_'+data.nav[i].sub[sub_i].id+'" style="display: block;font-family: BPG arial;padding-top: 7px;width: 10px;float: right;padding-right: 10px;">+</span></li>';
        		    		  deep_sub_i = 0;
        		    		  $( data.nav[i].sub[sub_i].sub ).each(function( index ) {
        		    			  sub_menu += '<li class="hide_my_ass sub_'+data.nav[i].sub[sub_i].id+'"  style="border-bottom: 1px solid #3C8EE0;"><img style="margin-top: 4px;" src="media/images/icons/'+data.nav[i].sub[sub_i].sub[deep_sub_i].sub_icon+'" alt="16 ICON" height="16" width="16"><a  style="font-weight: normal !important; padding-left: 6px; width: 85% !important; font-family: bpg;" href="index.php?pg='+ data.nav[i].sub[sub_i].sub[deep_sub_i].page_id +'">' + data.nav[i].sub[sub_i].sub[deep_sub_i].title + '</a></li>';
        		    			  deep_sub_i++;
        		    		  });
      		    		}else{
      		    			sub_menu += '<li style="border-bottom: 1px solid #3C8EE0;"><a style="font-weight: bold; font-size: 13px; color: #FFF !important;" href="index.php?pg=' + data.nav[i].sub[sub_i].page_id +'">' + data.nav[i].sub[sub_i].title + '</a><span style="display: block;font-family: BPG arial;padding-top: 7px;width: 10px;float: right;padding-right: 10px;"></span></li>';
      		    		}
            		    sub_i++;
      		    		
       		    	});
      		    	  sub_menu += '</ul>';
      		    	  if(page_id == 1){
        		    	    menu += '<li logout="logout" onclick="parent.location=\'' + url +'\'"><img id="img_'+ page_id +'" src="media/images/icons/'+data.nav[i].icon+'" alt="24 ICON" height="24" width="24"><a class="link_'+ page_id +'" href="'+ url +'">' + page_name + '</a>'+ sub_menu +'</li>';
      		    	  }else{
        		    		menu += '<li class="give_me_blue" onclick="parent.location=\'' + url +'\'"><img id="img_'+ page_id +'" src="media/images/icons/'+data.nav[i].icon+'" alt="24 ICON" height="24" width="24"><a class="link_'+ page_id +'" href="'+ url +'">' + page_name + '</a>'+ sub_menu +'</li>';
      		    	  }
      		    }else{
        		      menu += '<li onclick="parent.location=\'index.php?pg=' + page_id +'\'"><img id="img_'+ page_id +'" src="media/images/icons/'+data.nav[i].icon+'" alt="24 ICON" height="24" width="24"><a id="link_'+ page_id +'" href="index.php?pg=' + page_id +'">' + page_name + '</a></li>';
      		    }
        		i++;
      		});
      		
        	$("#sidemenu").html(menu);       
      		if(location.search){
      			var words = location.search;
      			var n = words.split("=");
      			var str = $('#img_'+n[n.length - 1]).attr('src');
      			if(str == undefined){}else{
      			str = str.substr(0, (str.length - 9));
      			}
      			$('#img_'+n[n.length - 1]).attr("src", ""+str+".png");
      			$('#link_'+n[n.length - 1]).css('color','#FFF');
      			$($('#img_'+n[n.length - 1]).parent()[0]).css('background-color','#2681DC');
      		}
        }
    });		
	$(document).on("click", "#menuwrapper ul li", function () {
		if($(this).attr('logout')=='logout'){
			var buttons = {
					"save": {
			            text: "შენახვა",
			            id: "save_logout",
			            click: function () {
			            	param 			        = new Object();
    						param.act		        = "add_user_log";
    						param.logout_actions    = $('#logout_actions').val();
    						param.logout_comment    = $('#logout_comment').val();
    				        $.ajax({
    				            url: 'logout.php',
    				            data: param,
    				            success: function(data) {
    				            	window.location = 'http://192.168.11.96:8080/callapp_main/index.php?pg=1';
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
	        GetDialog('logout', 357, "auto", buttons, 'center top');
	        
	        param 			= new Object();
			param.act		= "get_logout";
			param.cat_id    = $('#incomming_cat_1').val();
	        $.ajax({
	            url: 'logout.php',
	            data: param,
	            success: function(data) {
	                $("#logout").html(data.page);
	            }
	        });
		}else{
        	$("#menuwrapper ul li").children('ul').attr('id', '');
        	$("#menuwrapper ul li").children('a').attr('id', '');
        	$("#menuwrapper ul li").children('a').attr('class', '');
            $(this).children('ul').attr('id', 'show_ul');
            $(this).children('a').attr('id', 'change_a');
            if($(this).children('ul').text() != ''){
            $(this).children('a').attr('class', 'isari');
            }
		}
    });
	$(document).on("click", "#page-container", function () {
		$("#menuwrapper ul li").children('ul').attr('id', '');
    	$("#menuwrapper ul li").children('a').attr('id', '');
    	$("#menuwrapper ul li").children('a').attr('class', '');
	});
	$(document).on("click", ".click_me", function () {
		$( ".span_"+$(this).attr('page_id') ).text('-');
		$( this ).addClass( "click_me_again" );
		$( this ).removeClass( "click_me" );
		$( ".sub_"+$(this).attr('page_id') ).fadeIn( "slow" );
	});
	$(document).on("click", ".click_me_again", function () {
		$( ".span_"+$(this).attr('page_id') ).text('+');
		$( this ).addClass( "click_me" );
		$( this ).removeClass( "click_me_again" );
		$( ".sub_"+$(this).attr('page_id') ).fadeOut( "slow" );
	});
	$(document).on("click", ".give_me_blue", function () {
		$("#img_7").attr("src","media/images/icons/home_blue.png");
		$("#img_5").attr("src","media/images/icons/out_blue.png");
		$("#img_2").attr("src","media/images/icons/news_blue.png");
		$("#img_6").attr("src","media/images/icons/task_blue.png");
		$("#img_4").attr("src","media/images/icons/inc_blue.png");
		$("#img_3").attr("src","media/images/icons/flesh_panel_blue.png");
		$("#img_25").attr("src","media/images/icons/report_blue.png");
		$("#img_12").attr("src","media/images/icons/cnobari_blue.png");
		$("#img_8").attr("src","media/images/icons/about_us_blue.png");
		$("#img_1").attr("src","media/images/icons/sing_out_blue.png");
		$("#img_44").attr("src","media/images/icons/call_center_menu_blue.png");
		$("#img_45").attr("src","media/images/icons/documents_blue.png");
		$("#img_56").attr("src","media/images/icons/client_menu_blue.png");
		
		$("#img_65").attr("src","media/images/icons/visit_blue.png");
		$("#img_66").attr("src","media/images/icons/documents_blue.png");
		
		$("#menuwrapper a").css('color','#2681DC');
		var str = $(this).children('img').attr('src');
		str = str.substr(0, (str.length - 9));
	    $(this).children('img').attr("src", ""+str+".png");
	    $(this).children('a').css('color','#FFF');
	    $('#sidemenu li').css('background-color','#272727');
	    $('#sidemenu ul li').css('background-color','#2681DC');
	    $(this).css('background-color','#2681DC');
	});
	</script>

</head>

<body>
	
		<div id="menuwrapper">
        <ul id="sidemenu">
            
    	</ul>
        </div>
			
</body>
</html>