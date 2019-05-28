<!doctype html>
<html>
<head>
<title>Author Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Bootstrap -->
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">
	<link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet" />
	<script src="//libs.baidu.com/jquery/1.10.2/jquery.min.js"></script>
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
		    transform: translate(-50%, 0%);
		}

	</style>

</head>

<body>
	<div class="container">
		<div class="row">
		<div class="col-md-12 col-xs-12 col-sm-12 panel panel-default" style="height:40px;font-size:25px;">
			Author Page
		</div>
		<div class="col-md-6 col-xs-6 col-sm-6 panel panel-default " style="padding:8px;font-size:18px;">
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
				<span class="badge" align="right"><?php echo $row["cnt"]; ?></span>
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
				$result = mysqli_query($link, "SELECT PaperID from paper_author_affiliation where AuthorID='$author_id'");
				while($row = mysqli_fetch_array($result)){
					$paper_id = $row["PaperID"];
					$paper_info = mysqli_fetch_array(mysqli_query($link, "SELECT Title from papers where PaperID='$paper_id'"));
					$paper_title = $paper_info["Title"];
					$paperlist[$paper_title] = array("ref" => 0);
					echo "<li class='list-group-item' align='left' style='padding:8px;'>";
					echo "<a target='_blank' href='./search.php?field=PaperName&value=".$paper_title."'>".$paper_title."</a>";
					$tmp_search = mysqli_query($link, "SELECT count(*) as cnt from paper_reference where ReferenceID = '$paper_id'");
					$tmp_search = mysqli_fetch_array($tmp_search);
					$ref_num = (int)$tmp_search["cnt"];
					$paperlist[$paper_title]["ref"] = $ref_num;
					if($ref_num){
						echo "<span class='badge pull-right' data-toggle='tooltip' data-placement='auto' data-html='true' title='Numbers of references'>$ref_num</span>";
					}
					echo "</li>";
					$year = mysqli_fetch_array(mysqli_query($link, "SELECT PaperPublishYear as year, ConferenceID from papers where papers.PaperID = '$paper_id'"));
					$paperlist[$paper_title]["year"] = (int)$year["year"];
					$confid = $year["ConferenceID"];
					$paperlist[$paper_title]["conf"] = mysqli_fetch_array(mysqli_query($link, "SELECT ConferenceName from conferences where ConferenceID = '$confid'"))["ConferenceName"];
				}
			?>
			</ul>
		</div>
		</div>
		<div class="col-md-6 col-xs-6 col-sm-6 panel panel-default" style="color:rgba(255, 255, 255, 0.75);">
		<div class="col-md-12 col-xs-12 col-sm-12 panel panel-default" style="color:rgba(255, 255, 255, 0);height:500px;" id="image1">
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
					console.log(msg);
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
							data: confdata,
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
		</div>
	</div>
</body>

</html>