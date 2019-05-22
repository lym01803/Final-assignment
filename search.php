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

	</style>
</head>

<body>
	<div class="container">
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

	?>

	<?php
		$paper_title = $_GET["paper_title"];
		$authorquery = $_GET["author"];
		if($verified) {
	?>
			<div class="row">
			<div class="col-md-8 col-xs-12 col-sm-8 panel panel-default centered">
				<h1>Search Results</h1>
	<?php
			if ($paper_title) {
				echo "Search for Title: ".$paper_title;
				$ch = curl_init();
				$timeout = 5;
				$query = urlencode(str_replace(' ', '+', $paper_title));
				$url = "http://127.0.0.1:8983/solr/ee101_core_1/select?indent=on&q=PaperName:".$query."&wt=json";

				curl_setopt ($ch, CURLOPT_URL, $url);
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				$result = json_decode(curl_exec($ch), true);
				curl_close($ch);
	?>
			<center><table class="table table-hover component tablesorter tablesorter-default tablesortere5cb36a9e7829"><thead><tr><th>Title</th><th>Authors</th><th>Conference</th></tr></thead><tbody>
	<?php
				foreach ($result['response']['docs'] as $paper) {
					echo "<tr>";
					echo "<td>";
					echo $paper['PaperName'];
					echo "</td>";

					echo "<td>";
					foreach ($paper['AuthorsName'] as $idx => $author) {
						$author_id = $paper['AuthorsID'][$idx];
						echo "<a href=\"/author.php?author_id=$author_id\">$author; </a>";
					}
					echo "</td>";

					# 请补充针对Conference Name的显示
					echo "<td>";
					echo $paper['ConferenceName'];
					echo "</td>";

					echo "</tr>";
				}
				echo "</tbody></table></center><br><br>";
			}
		
		}

		# 请补充针对AuthorName以及ConferenceName的搜索
	?>

	<?php
		$authorquery = $_GET["author"];
		if($verified) {
			if ($authorquery) {
				echo "Search for Author: ".$authorquery;
				$ch = curl_init();
				$timeout = 5;
				$query = urlencode(str_replace(' ', '+', $authorquery));
				$url = "http://127.0.0.1:8983/solr/ee101_core_1/select?indent=on&q=AuthorsName:".$query."&wt=json";

				curl_setopt ($ch, CURLOPT_URL, $url);
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				$result = json_decode(curl_exec($ch), true);
				curl_close($ch);
	?>
			<center><table class="table table-hover component tablesorter tablesorter-default tablesortere5cb36a9e7829"><thead><tr><th>Title</th><th>Authors</th><th>Conference</th></tr></thead><tbody>
	<?php
				foreach ($result['response']['docs'] as $paper) {
					echo "<tr>";
					echo "<td>";
					echo $paper['PaperName'];
					echo "</td>";

					echo "<td>";
					foreach ($paper['AuthorsName'] as $idx => $author) {
						$author_id = $paper['AuthorsID'][$idx];
						echo "<a href=\"/author.php?author_id=$author_id\">$author; </a>";
					}
					echo "</td>";

					# 请补充针对Conference Name的显示
					echo "<td>";
					echo $paper['ConferenceName'];
					echo "</td>";

					echo "</tr>";
				}
				echo "</tbody></table></center><br><br>";
			}
		
		}
		# 请补充针对AuthorName以及ConferenceName的搜索
	?>

	<?php
		$confquery = $_GET["Conference"];
		if($verified) {
			if ($confquery) {
				echo "Search for Conference: ".$confquery;
				$ch = curl_init();
				$timeout = 5;
				$query = urlencode(str_replace(' ', '+', $confquery));
				$url = "http://127.0.0.1:8983/solr/ee101_core_1/select?indent=on&q=ConferenceName:".$query."&wt=json";

				curl_setopt ($ch, CURLOPT_URL, $url);
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				$result = json_decode(curl_exec($ch), true);
				curl_close($ch);
	?>
			<center><table class="table table-hover component tablesorter tablesorter-default tablesortere5cb36a9e7829"><thead><tr><th>Title</th><th>Authors</th><th>Conference</th></tr></thead><tbody>
	<?php
				foreach ($result['response']['docs'] as $paper) {
					echo "<tr>";
					echo "<td>";
					echo $paper['PaperName'];
					echo "</td>";

					echo "<td>";
					foreach ($paper['AuthorsName'] as $idx => $author) {
						$author_id = $paper['AuthorsID'][$idx];
						echo "<a href=\"/author.php?author_id=$author_id\">$author; </a>";
					}
					echo "</td>";

					# 请补充针对Conference Name的显示
					echo "<td>";
					echo $paper['ConferenceName'];
					echo "</td>";

					echo "</tr>";
				}
				echo "</tbody></table></center><br><br>";
			}
		
		}
		# 请补充针对AuthorName以及ConferenceName的搜索
	?>
</div></div>
</div>

</body>

</html>