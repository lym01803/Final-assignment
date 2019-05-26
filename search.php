<!DOCTYPE html> 
<html>
<head>
<title>Search Page</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<script src="//libs.baidu.com/jquery/1.10.2/jquery.min.js"></script>
    <!-- Bootstrap -->
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet"/>
	<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet" />
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
	<script src="./js/echarts.min.js"></script>
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
<script>
	var global_start = 0;
	var global_rows = 10;
	var global_is_finalpage = 0;
	var global_max_page = 0;
	var global_field;
	var global_value;
	var global_usingquickturnpage = 1;
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
				$("[data-toggle='popover']").popover();//此句勿删,勿改动位置;html重写后,需要重新激活popover.
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log("Error!" + " " + XMLHttpRequest.status + " " + XMLHttpRequest.readyState + " " + textStatus);
            }
		});
	}//ajax,显示表格部分。向search_solr.php发送json，并从该php接收返回的json对象。
	//to_show函数根据返回的json对象msg显示表格。
	function to_show(msg){
		$("[data-toggle='popover']").popover("hide");
		//console.log("toshow");
		var htmlstr = "";
		htmlstr += "<h1>Search Results</h1>\
		<center><table class=\"table table-hover component tablesorter tablesorter-default tablesortere5cb36a9e7829\">\
		<thead><tr><th>Title</th><th>Authors</th><th>Conference</th></tr></thead><tbody>";
		if(msg.hasOwnProperty("response")){
			global_max_page = parseInt((parseInt(msg["response"]["numFound"])-1)/global_rows) + 1;
		}
		else{
			global_max_page = 1;
		}
		if(!msg.hasOwnProperty("response") || !msg["response"].hasOwnProperty("docs")){
			global_is_finalpage = 1;
			if(global_start >= global_rows){
				global_start -= global_rows;
			}
			return false;
		}
		global_is_finalpage = parseInt(msg["response"]["numFound"]) <= (global_start + global_rows);
		//console.log(global_start);
		//console.log(global_rows);
		//console.log(msg["response"]["numFound"]);
		for(var idx in msg["response"]["docs"]){
			var paper = msg["response"]["docs"][idx];
			htmlstr += "<tr><td>" + paper["PaperName"] + "</td><td>";
			var authorname = paper["AuthorsName"];
			for(var i = 0; i < authorname.length; i++){
				var authorid = paper["AuthorsID"][i];
				htmlstr += "<a href=\"/author.php?author_id=" + authorid + "\">" + authorname[i] + ";&nbsp;</a>";
			}
			htmlstr += "</td><td>" + paper["ConferenceName"] + "</td></tr>";
		}
		htmlstr += "</tbody></table></center><br/><br/>";
		htmlstr += setting_button();
		htmlstr += turn_page_button(msg);
		document.getElementById("table_div").innerHTML = htmlstr;
	}
	$(document).ready(function(){
		//实现第一种翻页方式
		$("#table_div").mousemove(function(event){
			//console.log(global_usingquickturnpage);
			if(!global_usingquickturnpage){
				$(this).css({cursor:'default'});
				return ;
			}
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
					if(!global_is_finalpage){
						type = "url(./image/rightarrow.png),auto";
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
		//$("[data-toggle='popover']").popover();//此句可删
	});
	function gotopageclick(){
		var page = parseInt(document.getElementById("to_page").value);
		if(isNaN(page)){
			page = parseInt(global_start/global_rows) + 1;
		}else if(page < 1){
			page = 1;
		}else if(page > global_max_page){
			page = global_max_page;
		}
		turn_page(page);
	}
	function turn_page(id){
		if(isNaN(id)){
			id = parseInt(id);
		}
		global_start = (id - 1) * global_rows;
		show_table();
	}
	function turn_page_button(msg){
		var str = "";
		str += "Found " + msg["response"]["numFound"] + " results<br/>";
		var this_page = parseInt(global_start/global_rows) + 1;
		var max_page = parseInt((parseInt(msg["response"]["numFound"])-1)/global_rows) + 1;
		var lower_bound = this_page - 5;
		if(lower_bound <= 0){lower_bound = 1;}
		var upper_bound = lower_bound + 9;
		while(upper_bound * global_rows >= msg["response"]["numFound"] + global_rows){upper_bound--;}
		str += "<ul class=\"pagination center-block\">";
		str += "<li><a href=\"javascript:turn_page(1)\">&lt;&lt;</a></li>";
		for(var i = lower_bound; i <= upper_bound; i++){
			if(i == this_page){
				str += "<li class=\"active\"><a href=\"javascript:turn_page("+String(i)+")\">"+String(i)+"</a></li>";
			}else{
				str += "<li><a href=\"javascript:turn_page("+String(i)+")\">"+String(i)+"</a></li>";
			}
		}
		str += "<li><a href=\"javascript:turn_page("+max_page+")\">&gt;&gt;</a></li></ul>";
		return str; 
	}
	function pageitemschange(){
		var sele = document.getElementById("pageitems");
		global_rows = parseInt(sele.options[sele.selectedIndex].value);
		global_start = parseInt(global_start / global_rows) * global_rows; 
		show_table();
	}
	function usingQuickTurnPageChange(){
		global_usingquickturnpage ^= 1;
		show_table();
	}
	function setting_button(){
		var selectstr = "";
		var items=Array(10,15,20,25,30,50,75,100);
		for(var i = 0; i < items.length; i++){
			if(items[i] != parseInt(global_rows)){
				selectstr += "<option value='"+String(items[i])+"'>"+String(items[i])+"</option>";
			}else{
				selectstr += "<option value='"+String(items[i])+"' selected>"+String(items[i])+"</option>";
			}
		}
		var now_page = parseInt(global_start/global_rows) + 1;
		var checked = global_usingquickturnpage ? "checked" : "";
		return "<img src=\"./image/setting.svg\" alt='Set' width=\"30px\" class=\"btn popover-toggle\" style=\"padding:1px 1px 1px 1px;\" id=\"settingbutton\" title=\"\" data-container=\"body\" data-toggle=\"popover\" data-placement=\"auto\" data-original-title=\"Settings\"\
		data-html=\"true\" data-content=\"<select id='pageitems' onchange='pageitemschange();'>"+selectstr+"</select>&nbsp;items per page<hr/><button class='btn btn-default' id='gotopage' style='padding:3px 5px 3px 5px' onclick='gotopageclick();'>Go to</button>&nbsp;page&nbsp<input id='to_page' style='width:25%;' value='"+now_page+"'></input><hr/>\
		<input id='quickturnpage' type='checkbox' onchange='usingQuickTurnPageChange()' style='zoom:150%;position:absolute;top:117px;'"+checked+"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Using quick page-turning<hr/>\"/>&nbsp&nbsp";
	}
	window.onbeforeunload = function(){
		sessionStorage.setItem("start", String(global_start));
		sessionStorage.setItem("rows", String(global_rows));
	}//事实证明,当想要用cookie的时候,可以先考虑一下用session,这玩意儿明显好用得多
	function deal_with_session(){
		var str = sessionStorage.getItem("start");
		if(str != null && str != ""){
			global_start = parseInt(str);
		}
		str = sessionStorage.getItem("rows");
		if(str != null && str != ""){
			global_rows = parseInt(str);
		}
	}
</script>
<body>
	<div class="container" id="out_container">
	<?php

	//----------------------------------
	// 腾讯验证码后台接入demo
	//----------------------------------
	/*header('Content-type:text/html;charset=utf-8');
	$verified = 0;
	$AppSecretKey = "0DCUvhU_IXU1P2lYfz4EYQQ**"; //$_GET["AppSecretKey"]
	$appid = "2094801839"; //$_GET["appid"]
	$Ticket = $_GET["ticket"]; //$_GET["Ticket"]
	$Randstr = $_GET["randstr"]; //$_GET["Randstr"]
	$UserIP = "106.15.90.39"; //$_GET["UserIP"]*/

	/**
	 * 请求接口返回内容
	 * @param  string $url [请求的URL地址]
	 * @param  string $params [请求的参数]
	 * @param  int $ipost [是否采用POST形式]
	 * @return  string
	*/
	/*
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
	}*/
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
		deal_with_session();
		var verified = 1;<?php //echo $verified; ?>;
		global_field = "<?php if(array_key_exists("field", $_GET)){echo $_GET["field"];}else{echo "Paper_Author_Conference";} ?>";
		global_value = "<?php if(array_key_exists("value", $_GET)){echo $_GET["value"];}else{echo "";}?>";
		if(verified){
			show_table();
		}
	</script>
		<div class="row">
			<div class="col-md-12 col-xs-12 col-sm-12 panel panel-default" id="table_div">
			</div>
			<div class="col-md-6 col-xs-6 col-sm-6 panel panel-default" id="image_div" style="background-color:rgba(255,255,255,0.8);height:400px;">
			<script type="text/javascript">
				$.ajax({
					type: "POST",
					async: "false",
					url: "search_stat.php",
					dataType: "json",
					data: {
						"field":global_field,
						"value":global_value,
						"partial":"Year",
					},
					success: function(msg) {
						show_img(msg);
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						alert("Error!" + XMLHttpRequest.status + XMLHttpRequest.readyState + textStatus);
					}
				});
				function show_img(msg) {
					var myChart = echarts.init(document.getElementById('image_div'));
					var xdata=Array(), ydata=Array();
					var idx = 0;
					for(var year in msg){
						if(year != "numFound" && parseInt(year) >= 1970){
							ydata[idx] = year;
							xdata[idx] = parseInt(msg[year]);
							idx++;
						}
					}
        			var option = {
    					legend: {
        					data:['Number of papers ---- Year']
    					},
    					toolbox: {
        					show : true,
        					feature : {
            					mark : {show: true},
            					dataView : {show: true, readOnly: false},
            					magicType : {show: true, type: ['line', 'bar']},
            					restore : {show: true},
            					saveAsImage : {show: true}
        					}
    					},
    					calculable : true,
   	 					tooltip : {
        					trigger: 'axis',
        					formatter: "Year : {b}<br/>Number : {c}",
    					},
    					xAxis : [
							{
            					type : 'value',
            					axisLabel : {
                					formatter: function(value){
										return value;
									},
								},
							},
    					],
    					yAxis : [
        					{
            					type : 'category',
            					axisLine : {onZero: false},
            					axisLabel : {
                					formatter: function(value){
										return value;
									},
            					},
            					boundaryGap : false,
            					data : ydata,
        					}
    					],
    					series : [
        					{
            					name:'Number of papers ---- Year',
            					type:'bar',
            					smooth:true,
            					itemStyle: {
                					normal: {
										color: 'rgba(160,120,180,1)',
                    					lineStyle: {
											color: 'rgba(160,120,180,1)',
                        					shadowColor : 'rgba(160,120,180,1)'
										}
                					}
            					},
            					data: xdata
        					}
    					]
					};
					// 使用刚指定的配置项和数据显示图表。
					myChart.setOption(option);
				}
			</script>
			</div>
			<div class="col-md-6 col-xs-6 col-sm-6 panel panel-default" id="image_div2" style="background-color:rgba(255,255,255,0.8);height:400px;">
			<script>
				$.ajax({
					type:"POST",
					async:"false",
					url:"search_stat.php",
					dataType:"html",
					data:{
						"field":global_field,
						"value":global_value,
						"partial":"ConferenceName",
					},
					success: function(msg) {
						show_img2(msg);
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						alert("Error!" + XMLHttpRequest.status + XMLHttpRequest.readyState + textStatus);
					}
				});
				function show_img2(msg){
					var myChart = echarts.init(document.getElementById('image_div2'));
					var xdata=Array(), ydata=Array();
					var idx = 0;
					for(var p in msg){
						if(p != "numFound"){
							xdata[idx] = p;
							ydata[idx] = msg[p];
							idx++;
						}
					}
					option = {
    					title: {
        					text: '浏览器占比变化',
        					subtext: '纯属虚构',
        					top: 10,
        					left: 10
    					},
    					tooltip: {
        					trigger: 'item',
        					backgroundColor : 'rgba(0,0,250,1.0)'
    					},
    					legend: {
        					type: 'scroll',
        					bottom: 10,
        					data: (function (){
            					var list = [];
            					for (var i = 1; i <=28; i++) {
                					list.push(i + 2000 + '');
            					}
            					return list;
        					})()
    					},
    					visualMap: {
        					top: 'middle',
        					right: 10,
        					color: ['red', 'yellow'],
        					calculable: true
    					},
    					radar: {
       						indicator : [
           						{ text: 'IE8-', max: 400},
           						{ text: 'IE9+', max: 400},
           						{ text: 'Safari', max: 400},
           						{ text: 'Firefox', max: 400},
           						{ text: 'Chrome', max: 400}
        					]
    					},
    					series : (function (){
        					var series = [];
        					for (var i = 1; i <= 28; i++) {
            				series.push({
                				name:'浏览器（数据纯属虚构）',
                				type: 'radar',
                				symbol: 'none',
                				lineStyle: {
                   					width: 1
                				},
                				emphasis: {
                    				areaStyle: {
                        				color: 'rgba(0,250,0,0.3)'
                    				}
                				},
                				data:[
                  					{
                    					value:[
                        					(40 - i) * 10,
                        					(38 - i) * 4 + 60,
                        					i * 5 + 10,
                        					i * 9,
                        					i * i /2
                    					],
                    					name: i + 2000 + ''
                  					}
                				]
            				});
        				}
        				return series;
    				})()
				};
				myChart.setOption(option);
			}
			</script>
			</div>
		</div>
</div>

</body>

</html>