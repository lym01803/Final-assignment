<!DOCTYPE html> 
<html>
<head>
<title>Search Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Bootstrap -->
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet" />
	<style>

		/* http://css-tricks.com/perfect-full-page-background-image/ */
		html {
			background: url(DJI_0017.jpg) no-repeat center center fixed;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			background-size: cover;
		}

		body {
			padding-top: 20px;
			font-size: 16px;
			font-family: sans-serif;
			background: transparent;
			text-align: center;
		}

		h1 {
			font-family: Arial, sans-serif;
			font-weight: 400;
			font-size: 30px;
		}

		/* Override B3 .panel adding a subtly transparent background */
		.panel {
			background-color: rgba(255, 255, 255, 0.75);
		}

		.margin-base-vertical {
			margin: 10px 0;
		}

		.margin-big-vertical {
			margin: 20px 0;
		}
		

		.centered {
		    position: absolute;
		    top: 10%;
		    left: 50%;
		    transform: translate(-50%, 0);
		}
		.side_area{
			border: 1px solid #eeeeee;
        	background-color: #eeeeee;
        	width: 3%;
        	height: 100%;
        	position: absolute;
        	top: 0%;
		}
	</style>
</head>
<script src="//libs.baidu.com/jquery/1.10.2/jquery.min.js"></script>
<script>
	var global_start = 0;
	var global_rows = 10;
	var global_is_finalpage = 0;
	var global_field;
	var global_value;
	function show_table(){
		$.ajax({
			type: "POST",
            async: "false",
            url: "./search_solr.php",
            dataType: "json",
            data: {
				"field":global_field,
				"value":global_value,
				"start":global_start,
				"rows":global_rows,
            },
            success: function(msg) {
                to_show(msg);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log("Error!" + " " + XMLHttpRequest.status + " " + XMLHttpRequest.readyState + " " + textStatus);
            }
		});
	}//ajax,显示表格部分。向search_solr.php发送json，并从该php接收返回的json对象。
	//to_show函数根据返回的json对象msg显示表格。
	function to_show(msg){
		//console.log("toshow");
		var htmlstr = "";
		htmlstr += "<h1>Search Results</h1>\
		<center><table class=\"table table-hover component tablesorter tablesorter-default tablesortere5cb36a9e7829\">\
		<thead><tr><th>Title</th><th>Authors</th><th>Conference</th></tr></thead><tbody>";
		if(!msg.hasOwnProperty("response") || !msg["response"].hasOwnProperty("docs")){
			global_is_finalpage = 1;
			if(global_start >= global_rows){
				global_start -= global_rows;
			}
			return false;
		}
		global_is_finalpage = msg["response"]["docs"].length < global_rows;
		for(var idx in msg["response"]["docs"]){
			var paper = msg["response"]["docs"][idx];
			htmlstr += "<tr><td>" + paper["PaperName"] + "</td><td>";
			var authorname = paper["AuthorsName"];
			for(var i = 0; i < authorname.length; i++){
				var authorid = paper["AuthorsID"][i];
				htmlstr += "<a href=\"/author.php?author_id=" + authorid + "\">" + authorname[i] + ";</a>";
			}
			htmlstr += "</td><td>" + paper["ConferenceName"] + "</td></tr>";
		}
		htmlstr += "</tbody></table></center><br/><br/>";
		htmlstr += turn_page_button(msg);
		//console.log(htmlstr);
		document.getElementById("table_div").innerHTML = htmlstr;
	}
	$(document).ready(function(){
		//实现第一种翻页方式
		$("#table_div").mousemove(function(event){
			var mousex = event.clientX;
			var mousey = event.clientY;//鼠标坐标
			var item = document.getElementById("table_div");
			var top = item.getBoundingClientRect().top;
			var bottom = item.getBoundingClientRect().bottom;
			var left = item.getBoundingClientRect().left;
			var right = item.getBoundingClientRect().right;
			var width = right - left;//表格div边界
			var type = "default";
			$("#table_div").unbind("click");
			if(mousey >= top && mousey <= bottom){
				if(mousex >= left && mousex <= left + 0.1*width){
					if(global_start >= global_rows){
						type = "url(./image/leftarrow.png),auto";
						$("#table_div").click(function(){
							if(global_start < global_rows){
								return false;
							}
							global_start -= global_rows;
							show_table();
						});
					}
				}else if(mousex <= right && mousex >= right - 0.1*width){
					type = "url(./image/rightarrow.png),auto";
					if(!global_is_finalpage){
						$("#table_div").click(function(){
							if(global_is_finalpage){
								return false;
							}
							global_start += global_rows;
							show_table();
						});
					}
				}
				$(this).css({cursor:type});
			}
		});
	});
	function turn_page(id){
		var num = parseInt(id);
		global_start = (num - 1) * 10;
		show_table();
	}
	function turn_page_button(msg){
		var str = "";
		str += "Found " + msg["response"]["numFound"] + " results<br/>";
		var this_page = global_start/global_rows + 1;
		var lower_bound = this_page - 5;
		if(lower_bound <= 0){lower_bound = 1;}
		var upper_bound = lower_bound + 9;
		while(upper_bound * global_rows >= msg["response"]["numFound"] + 10){upper_bound--;}
		for(var i = lower_bound; i <= upper_bound; i++){
			if(i == this_page){
				str += "<a href=\"javascript:turn_page("+String(i)+")\" style=\"color:#aa00cc;\">"+String(i)+"</a>&nbsp&nbsp";
			}else{
				str += "<a href=\"javascript:turn_page("+String(i)+")\">"+String(i)+"</a>&nbsp&nbsp";
			}
		}
		return str; 
	}
</script>
<body>
	<div class="container" id="out_container">
	<?php

	//----------------------------------
	// 腾讯验证码后台接入demo
	//----------------------------------
	header('Content-type:text/html;charset=utf-8');
	$verified = 0;
	$AppSecretKey = "0DCUvhU_IXU1P2lYfz4EYQQ**"; //$_GET["AppSecretKey"]
	$appid = "2094801839"; //$_GET["appid"]
	$Ticket = $_GET["ticket"]; //$_GET["Ticket"]
	$Randstr = $_GET["randstr"]; //$_GET["Randstr"]
	$UserIP = "106.15.90.39"; //$_GET["UserIP"]

	/**
	 * 请求接口返回内容
	 * @param  string $url [请求的URL地址]
	 * @param  string $params [请求的参数]
	 * @param  int $ipost [是否采用POST形式]
	 * @return  string
	*/
	function txcurl($url,$params=false,$ispost=0){
	    $httpInfo = array();
	    $ch = curl_init();

	    curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
	    curl_setopt( $ch, CURLOPT_USERAGENT , 'JuheData' );
	    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 60 );
	    curl_setopt( $ch, CURLOPT_TIMEOUT , 60);
	    curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	    if( $ispost )
	    {
	        curl_setopt( $ch , CURLOPT_POST , true );
	        curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
	        curl_setopt( $ch , CURLOPT_URL , $url );
	    }
	    else
	    {
	        if($params){
	            curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
	        }else{
	            curl_setopt( $ch , CURLOPT_URL , $url);
	        }
	    }
	    $response = curl_exec( $ch );
	    if ($response === FALSE) {
	        //echo "cURL Error: " . curl_error($ch);
	        return false;
	    }
	    $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
	    $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
	    curl_close( $ch );
	    return $response;
	}
/*
	$url = "https://ssl.captcha.qq.com/ticket/verify";
	$params = array(
	    "aid" => $appid,
	    "AppSecretKey" => $AppSecretKey,
	    "Ticket" => $Ticket,
	    "Randstr" => $Randstr,
	    "UserIP" => $UserIP
	);
	$paramstring = http_build_query($params);
	$content = txcurl($url,$paramstring);
	$result = json_decode($content,true);
	if($result){
	    if($result['response'] == 1){
	        $verified=1;
	        
	    }else{
	        //echo $result['response'].":".$result['err_msg'];
	        echo "<h1>Illegal Operation</h1>";
	        echo "Redirect to home after 3 seconds...";
	        header("refresh:3;url=//acemap.lifanz.cn");
	    }
	}else{
	    echo "请求失败";
	}
*/
	?>
	<script>
		var verified = 1;<?php //echo $verified; ?>;
		global_field = "<?php if(array_key_exists("field", $_GET)){echo $_GET["field"];}else{echo "PaperName";} ?>";
		global_value = "<?php if(array_key_exists("value", $_GET)){echo $_GET["value"];}else{echo "";}?>";
		if(verified){
			show_table();
		}
	</script>
			<div class="row">
			<div class="col-md-8 col-xs-12 col-sm-8 panel panel-default centered" id="table_div">
			</div></div>
</div>

</body>

</html>