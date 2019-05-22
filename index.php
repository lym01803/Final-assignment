<!doctype html>
<html>

<head>

<title>Acemap by Lifan Zhang</title>
<script src="https://ssl.captcha.qq.com/TCaptcha.js"></script>
	<script type="text/javascript">

		window.callback = function(res){
			var authorval = document.getElementById("author").value;
			var titleval = document.getElementById("paper_title").value;
			var confval = document.getElementById("Conference").value;
			if(authorval||confval||titleval){
			    console.log(res)
			    // res（用户主动关闭验证码）= {ret: 2, ticket: null}
			    // res（验证成功） = {ret: 0, ticket: "String", randstr: "String"}
			    if(res.ret === 0){
			    	var form = document.getElementById('queryForm');
			        document.getElementById("captchaticket").setAttribute('value',res.ticket);
			        document.getElementById("randstr").setAttribute('value',res.randstr);
			        form.submit();
			    }
			}
			else{
				alert("No data input!");
			}
		}
    </script>
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
		    top: 50%;
		    left: 50%;
		    transform: translate(-50%, -50%);
		}

	</style>

</head>

<body>
	<script type="text/javascript">
		document.getElementById("captchaticket").setAttribute('value',0)
	</script>
	<div class="container" >
		<div class="row">
			<div class="col-md-4 col-xs-10 col-sm-8 panel panel-default centered">
				<h1 class="margin-big-vertical"><center>Acemap Search</center></h1>
				<form action="/search.php" id="queryForm">
					<div class="margin-base-vertical">
			    		<label>Paper Title:</label>
			    		<input type="text" class="form-control margin-base-vertical" name="paper_title" id="paper_title" " placeholder="Enter Paper Title">
			    		<label>Author Name:</label>
			    		<input type="text" class="form-control margin-base-vertical" name="author" id="author" placeholder="Enter Author Name">    
			    		<label>Conference Name:</label>
			    		<input type="text" class="form-control margin-base-vertical" name="Conference" id="Conference" placeholder="Enter Conference Name">    
			    		<input type="text" name="ticket" id="captchaticket" style="visibility: hidden;">    
			    		<input type="text" name="randstr" id="randstr" style="visibility: hidden;">    
			    		<center>
							<button type="button" class="btn btn-primary margin-big-vertical" id="TencentCaptcha" data-appid="2094801839" data-cbfn="callback">Submit</button>
						</center>
					</div>

				</form>
				<center>

			</div>
		</div>
	</div>


</body>

</html>
