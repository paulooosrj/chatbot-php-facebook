<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Logger Views</title>
	<link href='https://fonts.googleapis.com/css?family=Source+Code+Pro:200' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="./assets/css/style.css">
	<style>
		.terminal>div{
			width: 100%;
			display: flex;
			justify-content: center;
			align-items: center;
		}
		.terminal>div>img{
			border-radius: 100%;
			margin-left: 15px;
			border: 2px solid #f4f4f4;
		}
	</style>
</head>
<body>

	<div class="container">
		<div class="window">
			<div class="handle">
				<div class="buttons">
					<button class="close"></button>
					<button class="minimize"></button>
					<button class="maximize"></button>
				</div>
				<span class="title"></span>
			</div>
			<div class="terminal">
				<p>{name: Paulo,Idade: 15}</p>
			</div>
		</div>
	</div>
	
	<script type="text/javascript" src="https://js.pusher.com/4.0/pusher.min.js"></script>
	<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	<script type="text/javascript">

		var pusher = new Pusher('444056f9ccb4f4654664', {
      		cluster: 'us2',
      		encrypted: true,
      		pong_timeout: 6000, //default = 30000
   		 	unavailable_timeout: 2000 //default = 10000
    	});

    	var channel = pusher.subscribe('chatbotphp');
    	channel.bind('logger', function(data) {

    		var scope = JSON.parse(JSON.parse('"'+data.message+'"'));
    		let PayloadFB = "https://graph.facebook.com/v2.6/";
    		let Token = "access_token="+ scope["token_access"];
    		let fields = `?fields=id,name,picture`;
    		var time = new Date(scope["time"]);

  			// GET PAGE
  			$.get(PayloadFB + scope["page_id"] + fields + "&" + Token, function(res){
  				// GET USER
  				$.get(PayloadFB + scope["user_id"] + "?" + Token, function(response){
  					$(".terminal").html(`
  						<div>
  							<img src="${res["picture"]["data"]["url"]}" width="70"/>
							<img src="${response.profile_pic}" width="90"/>
							<h3 style="color:#fff;margin-left: 15px;">${response.first_name} ${response.last_name} :</h3>
							<p style="margin-left: 15px;">${scope["message"]}</p><code style="margin-left: 15px;color:#f4f4f4 !important;background:red">${time.getHours()}:${time.getMinutes()}</code>
  						</div>
  					`);
  				});
  			});/**/

    	});

	</script>
</body>
</html>