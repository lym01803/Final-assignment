<!doctype html>
<html>
<head>
<title>Author Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Bootstrap -->
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
	<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet" />
	<script src="//libs.baidu.com/jquery/1.10.2/jquery.min.js"></script>
	<script src="./js/ex/echarts.js"></script>
	<script src="./js/ex/echarts-wordcloud.min.js"></script>
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
			background-color: rgba(255, 255, 255, 0.85);
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
		    transform: translate(-50%, 0%);
		}

	</style>

</head>

<body>
	<div class="container">
		<div class="row">
		<div class="col-md-12 col-xs-12 col-sm-12 panel panel-default" style="height:40px;font-size:25px;margin-bottom:2px;">
			Author Page
		</div>
		<div class="col-md-6 col-xs-6 col-sm-6 panel panel-default " style="padding:8px;font-size:18px;" id="left-col">
		<?php	
			$paperlist = array();
			$author_id=$_GET["authorid"];
			$link=mysqli_connect("db.lifanz.cn:3306", 'ee101_user', 'ee1012019', 'ee101');
			$result=mysqli_query($link, "SELECT AuthorName from authors where AuthorID='$author_id'");
			$author_name=mysqli_fetch_array($result)["AuthorName"];
		?>
			<div class="pull-left" style="padding:8px;">
				Author Name : <?php echo ucwords($author_name);?>
			</div><br/><hr/>
			<div class="pull-left" style="padding:8px;">
			Affiliation Name : 
			</div><br/><br/>
			<ul class="list-group">
		<?php
			$result = mysqli_query($link, "SELECT affiliations.AffiliationID, affiliations.AffiliationName, cnt from (select AffiliationID, count(*) as cnt from paper_author_affiliation where AuthorID='$author_id' and AffiliationID is not null group by AffiliationID order by cnt desc) as tmp inner join affiliations on tmp.AffiliationID = affiliations.AffiliationID");
			while($row = mysqli_fetch_array($result)){
		?>
			<li class="list-group-item" align="left">
		<?php
				echo ucwords($row["AffiliationName"]);
		?>
				<span class="badge" align="right" data-toggle="tooltip" data-placement="auto" title='Number of papers associated with this affiliation'><?php echo $row["cnt"]; ?></span>
			</li>
		<?php
			}	
		?>
			</ul>
			<hr/>
		<div class="pull-left" style="padding:5px;text-align:left;line-height:40px;">
			Paper : <br/>
			<ul class="list-group">
			<?php
				$paper_num = 0;
				$result = mysqli_query($link,"SELECT e.PaperID as PaperID, e.Title as Title, e.PaperPublishYear as Year, e.refcount as cnt, f.ConferenceName as conf from (SELECT c.PaperID , c.Title, c.ConferenceID, c.PaperPublishYear, d.refcount from ((SELECT a.* from (papers a inner join paper_author_affiliation b on a.PaperID = b.PaperID) where b.AuthorID = '$author_id') c inner join paper_count d on c.PaperID = d.PaperID)) e inner join conferences f on e.ConferenceID = f.ConferenceID");
				//$result = mysqli_query($link, "SELECT PaperID from paper_author_affiliation where AuthorID='$author_id'");
				$result_global = mysqli_fetch_all($result, MYSQLI_BOTH);
				foreach($result_global as $row){
					$paper_id = $row["PaperID"];
					$paper_num += 1;
					//$paper_info = mysqli_fetch_array(mysqli_query($link, "SELECT Title from papers where PaperID='$paper_id'"));
					$paper_title = $row["Title"];
					$paperlist[$paper_title] = array("ref" => 0);
					echo "<li class='list-group-item' align='left' style='padding:8px;'>";
					echo "<span><a target='_blank' href='./search.php?field=PaperName&value=".$paper_title."'>".$paper_title."</a>";
					//$tmp_search = mysqli_query($link, "SELECT count(*) as cnt from paper_reference where ReferenceID = '$paper_id'");
					//$tmp_search = mysqli_fetch_array($tmp_search);
					$ref_num = (int)$row["cnt"];
					$paperlist[$paper_title]["ref"] = $ref_num;
					if($ref_num){
						//echo "<span class='badge pull-right' data-toggle='tooltip' data-placement='auto' data-html='true' title='Numbers of references'>$ref_num</span>";
						echo "<label class='pull-right' style='font-size:12px;' title='number of references'>$ref_num</label>";
					}
					echo "</span></li>";
					//$year = mysqli_fetch_array(mysqli_query($link, "SELECT PaperPublishYear as year, ConferenceID from papers where papers.PaperID = '$paper_id'"));
					$paperlist[$paper_title]["year"] = (int)$row["Year"];
					//$confid = $row["ConferenceID"];
					$paperlist[$paper_title]["conf"] = $row["conf"];
				}
			?>
			</ul>
		</div>
		</div>
		<div class="col-md-6 col-xs-6 col-sm-6 panel panel-default" style="height:500px;margin:0px;" id="image1">
				<script>
					var msg = <?php echo json_encode($paperlist); ?>;
					//console.log(msg);
					var min_year = 10000;
					var max_year = 0;
					var yeartox = Array();
					var yeardata = Array();
					var conftoy = Array();
					var confdata = Array();
					var size = Array();
					var order = 0;
					var idx = 0;
					var pos = Array();
					var Number__ = Array();
					var mx = 0;
					for(var title in msg){
						var num = parseInt(msg[title]["ref"]);
						if(num > mx){
							mx = num;
						}
					}
					if(mx == 0){
						mx = 1;
					}
					var AAA = 16;
					var BBB = 64 + Math.log(mx)*8;
					for(var title in msg){
						if(parseInt(msg[title]["year"]) > max_year){
							max_year = parseInt(msg[title]["year"]);
						}
						if(parseInt(msg[title]["year"]) < min_year){
							min_year = parseInt(msg[title]["year"]);
						}
						var tmp = msg[title]["conf"];
						if(!conftoy.hasOwnProperty(tmp)){
							conftoy[tmp] = order;
							confdata[order] = tmp;
							order++;
						}
						var num = parseInt(msg[title]["ref"]);
						Number__[idx] = num;
						size[idx++] = BBB - (BBB - AAA) * (1.0 - num/mx);
					}
					for(var i = min_year - 2; i <= max_year + 1; i++){
						yeartox[i] = i - min_year + 2;
						yeardata[i - min_year + 2] = i;
					}
					idx = 0;
					//console.log(msg);
					var Conf__ = Array();
					var Year__ = Array();
					for(var title in msg){
						var year = parseInt(msg[title]["year"]);
						var conf = msg[title]["conf"];
						Conf__[idx] = conf;
						Year__[idx] = year;
						pos[idx++] = [yeartox[year], conftoy[conf]];
					}
					var option = {
						title:{
							text: "Number of references - Year - Conference",
						},
						tooltip:{
							formatter: function(param){
								var id = parseInt(param["dataIndex"]);
								return "Conferences: "+Conf__[id]+"<br/>Year: "+Year__[id]+"<br/>Number: "+Number__[id];
							}
						},
						xAxis: {
							data: yeardata,
						},
						yAxis: {
							boundaryGap: true,
							data: confdata,
							splitLine: {
        						show: true,
        						lineStyle:{
           							color: ['rgba(60,60,60,0.5)'],
           							width: 1,
           							type: 'solid'
      							}
　　						}
						},
						series: [{
							symbolSize: function(param, idx){
								return parseFloat(size[parseInt(idx["dataIndex"])]);
							},
							data: pos,
							type: "scatter",
						}],
					}
					var myChart = echarts.init(document.getElementById('image1'));
					myChart.setOption(option);
				</script>
		</div>
		<div class="col-md-6 col-xs-6 col-sm-6 panel panel-default" style="margin:0px;" id="image2">
		<script>
			document.getElementById("image2").style="margin:0px;background-color:rgba(255,255,255,0.95);height:<?php echo $paper_num * 35+400; ?>px;";
			$.ajax({
					type: "POST",
					async: "false",
					url: "./author_stat1.php",
					dataType: "json",
					data: {
						"author_id": "<?php echo $author_id; ?>",
						"author_name": "<?php echo $author_name; ?>",
					},
					success: function(msg) {
						image2show(msg);
					},
					error: function(XMLHttpRequest, textStatus, errorThrown) {
						alert("Error!" + XMLHttpRequest.status + XMLHttpRequest.readyState + textStatus);
					}
				});
			function image2show(data){
			var myChart = echarts.init(document.getElementById('image2'));
			var option = {
        		tooltip: {
            		trigger: 'item',
					triggerOn: 'mousemove',
					formatter: function(param){
						return param["data"]["name"];
					}
				},
				title: {
					text: "Author----------------Paper----------------Co-author",
					subtext: "\"No.\" in 'Paper' column shows the paper's appearance order in the left column of this page\n\"No.\" in 'Co-author' column shows the author sequence",
				},
        		series: [
            		{
                		type: 'tree',
                		data: [data],
                		top: '60px',
                		left: '15%',
                		bottom: '15px',
                		right: '25%',
                		symbolSize: 7,
                		label: {
                    		normal: {
                        		position: 'left',
                        		verticalAlign: 'middle',
                        		align: 'right',
                        		fontSize: 10
                    		}
                		},
               			leaves: {
                    		label: {
                       			normal: {
                            		position: 'right',
                            		verticalAlign: 'middle',
                            		align: 'left'
                        		}
                    		}
                		},
						expandAndCollapse: true,
                		animationDuration: 550,
                		animationDurationUpdate: 750
            		}
        		]
    		};
			myChart.setOption(option);
		}
		</script>
		</div>
		<div class="col-md-6 col-xs-6 col-sm-6 panel panel-default" style="height:500px;margin:0px;" id="image3">
		<?php 
				$wdcnt = array();
				foreach($result_global as $row){
					$p_title = $row["Title"];
					$p_array = explode(" ", $p_title);
					foreach($p_array as $word){
						if(array_key_exists($word, $wdcnt)){
							$wdcnt[$word] += 1;
						}else {
							$wdcnt[$word] = (int)1;
						}
					}
				}
				$retval = array();
				foreach($wdcnt as $k=>$v){
					if(strlen($k) < 3){
						$v = (int)($v / 2);
					}
					$retval[] = array("name"=>$k, "value"=>$v);
				}
				$retval = json_encode($retval);
				echo "<script>var word_cloud_data = $retval;</script>";
		?>
		<script>
			var option = {
				series: [
    				{
   						type: 'wordCloud',
        				sizeRange: [10, 60],
        				rotationRange: [-90, 90],
						right: '5%',
        				textStyle: {
            				normal: {
                				color: function () {
									while(1){
										//console.log("try");
										var a = Math.random()*255, b = Math.random()*255, c = Math.random()*255;
										if(a + b + c < 600 && a + b + c > 200 && (a - b) * (a - b) + (b - c) * (b - c) + (c - a)*(c - a) > 20000){
                    						return 'rgb(' + [
                            					Math.round(Math.random() * 255),
                            					Math.round(Math.random() * 255),
                            					Math.round(Math.random() * 255)
												].join(',') + ')';
										}
									}
                				}
            				},
            				emphasis: {
                				shadowBlur: 10,
                				shadowColor: '#333'
            				}
        				},
        				data: word_cloud_data,
    				}
    			]
			};
			var myChart = echarts.init(document.getElementById('image3'));
			myChart.setOption(option);
		</script>
		</div>
	</div>
	<script>
		var ht = 1400 + 35 * <?php echo $paper_num; ?>;
		if($("#left-col").height() < ht){
			$("#left-col").height(ht);
			//console.log($("#left-col").height());
		}
	</script>
</body>

</html>