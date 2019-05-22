<!doctype html>
<html>
<head>
<title>Author Page</title>
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
		    transform: translate(-50%, 0%);
		}

	</style>

</head>

<body>
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-xs-12 col-sm-8 panel panel-default centered">
	<h1>Author</h1>
	<?php
		$author_id = $_GET["author_id"];
		$link = mysqli_connect("db.lifanz.cn:3306", 'ee101_user', 'ee1012019', 'ee101');
		$result = mysqli_query($link, "SELECT AuthorName from authors where AuthorID='$author_id'");
		if ($result) {
			$author_name = mysqli_fetch_array($result)['AuthorName'];
			echo "Name: $author_name<br>";
		} else {
			echo "Name not found";
		}
		$result = mysqli_query($link, "SELECT affiliations.AffiliationID, affiliations.AffiliationName from (select AffiliationID, count(*) as cnt from paper_author_affiliation where AuthorID='$author_id' and AffiliationID is not null group by AffiliationID order by cnt desc) as tmp inner join affiliations on tmp.AffiliationID = affiliations.AffiliationID");


		if ($result->num_rows>0) {
			$affiliation_name = mysqli_fetch_array($result)['AffiliationName'];
			echo "Affiliation: $affiliation_name";
		} else {
			echo "Affiliation not found";
		}

		$result = mysqli_query($link, "SELECT PaperID from paper_author_affiliation where AuthorID='$author_id'");

		if ($result->num_rows>0) {
	?>
			<center><table class="table table-hover component tablesorter tablesorter-default tablesortere5cb36a9e7829"><thead><tr><th>Title</th><th>Authors</th><th>Conference</th></tr></thead><tbody>
	<?php
			
			while ($row = mysqli_fetch_array($result)) {
				echo "<tr>";
				$paper_id = $row['PaperID'];
				$paper_info = mysqli_fetch_array(mysqli_query($link, "SELECT Title, ConferenceID from papers where PaperID='$paper_id'"));
				$paper_title = $paper_info['Title'];
				$conf_id = $paper_info['ConferenceID'];
				
				echo "<td>$paper_title</td>";

				$auresult = mysqli_query($link, "SELECT AuthorName,AuthorID FROM `authors` WHERE `AuthorID` IN (SELECT AuthorID FROM `paper_author_affiliation` WHERE `PaperID`= '$paper_id' ORDER BY `AuthorSequence` ASC)");

				echo "<td>";
				while ($rows = mysqli_fetch_array($auresult)) {
					$paperauthor_name=$rows['AuthorName'];
					$paperauthor_id=$rows['AuthorID'];
					echo "<a href=\"/author.php?author_id=$paperauthor_id\">$paperauthor_name; </a>";
				}
				echo "</td>";

				$conf_info = mysqli_fetch_array(mysqli_query($link, "SELECT ConferenceName FROM `conferences` WHERE `ConferenceID` = '$conf_id'"));
				$conf_name = $conf_info['ConferenceName'];
				echo "<td>$conf_name</td>";

				echo "</tr>";
			}
			echo "</tbody></table></center>";

		}
		else{
			echo "<h1> No paper of this author is found.</h1>";
		}

	?>
	<br></br>


				</div>
		</div>
</div>
</body>

</html>