<?php if (!is_object($this)) die; ?>
<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>CHURCHIS.US</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.3/css/bootstrapValidator.min.css">
		<style>
			body {
				background: url('/assets/tile.png') repeat white;
			}
			#wrap {
				max-width: 430px;
			}
			#body {
				padding: 75px 0;
			}
			#footer { color: #999; }
				.footer-em { font-style: italic; }
				.footer-sm { 
					font-size: 0.85em; 
					opacity: 0.6; 
					transition: opacity .25s ease-in-out;
					-moz-transition: opacity .25s ease-in-out;
					-webkit-transition: opacity .25s ease-in-out;
				}
				#footer a { color: #999; }
				.footer-sm:hover { font-size: 0.85em; opacity: 0.8; }
			#bottom {
				margin-top: 150px;
			}
			button {
				margin-top: 10px;
				border: none;
				background: none;
			}
			#google {
				width: 160px;
				border: none;
			}
		</style>

		<script src="//code.jquery.com/jquery-2.1.1.min.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.3/js/bootstrapValidator.min.js"></script>
	</head>
	<body>
		<div id="wrap" class="container text-center">
			<div id="body">
				<div id="content">

					<h1><a href="http://frontlinegr.com">
						<img src="assets/fcclogo.png" class="img-responsive" alt="Frontline Community Church">
					</a></h1>

					<div id="bottom">
						<?php

						if ($loggedIn) {

							?>
							You're logged in, <?=htmlentities($loggedInAs); ?>!<br>
							<a href="/logout">Log Out</a>
							<?php

						} else {

							?>
							<form method="post" action="/login">
								<?php

								if (!empty($loginError))
									echo '<p class="text-danger">'. $loginError .'</p>';

								?>
								<button type="submit"><img id="google" alt="Login with Google" src="/assets/google.png"></button>
								<input type="hidden" name="go" value="/manage/">
							</form>
							<?php

						}

						?>
					</div>
				</div> <!-- /#content -->
			</div> <!-- /#body -->
			<div id="footer">
				<p class="footer-em">And let us consider how we may spur one another on toward love and good deeds, not giving up meeting together, as some are in the habit of doing, but encouraging one another &ndash; and all the more as you see the Day approaching.</p>
				<p class="footer-sm">Built with <i class="fa fa-heart"></i> by <a href="https://twitter.com/bigoness">Biggie</a></p>
			</div>
		</div>
	</body>
</html>
