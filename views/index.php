<?php if (!is_object($this)) die; ?>
<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>CHURCHIS.US</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.3/css/bootstrapValidator.min.css">
		<style>
			body {
				background: url('/assets/retina_wood.png') repeat white;
			}
			#wrap {
				max-width: 430px;
			}
			#body {
				padding: 50px 0 20px;
			}
			#footer { color: #b59871; }
				.footer-em { font-style: italic; }
				.footer-sm { 
					font-size: 0.85em; 
					opacity: 0.6; 
					transition: opacity .25s ease-in-out;
					-moz-transition: opacity .25s ease-in-out;
					-webkit-transition: opacity .25s ease-in-out;
				}
				#footer a { color: #b59871; }
				.footer-sm:hover { font-size: 0.85em; opacity: 0.8; }
			label {
				color: #917248;
			}
			input[type="text"], input[type="password"], .btn-default {
				border: 1px solid #d5c1a5;
			}
			#bottom {
				margin-top: 80px;
			}
			button {
				margin-top: 10px;
				border: none;
				background: none;
			}
			#staff-login {
				margin-top: 60px;
				opacity: 0.5;
			}
			#staff-login:hover {
				opacity: 1.0;
			}
			#staff-login .btn-default:hover {
				background-color: white;
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
							<a href="/manage/">You're logged in, <?=htmlentities($loggedInAs); ?>!</a><br>
							<a href="/logout">Log Out</a>
							<?php

						} else {

							?>
							<form method="post" action="/login" class="text-left clearfix center-block" style="width:300px;">
								<?php

								if (!empty($loginError))
									echo '<p class="alert alert-danger">'. $loginError .'</p>';

								?>
								<div class="form-group">
									<label for="email">Email Address</label>
									<input type="text" name="email" placeholder="Email" class="form-control input-lg">
								</div>
								<div class="form-group">
									<label for="password">Password</label>
									<input type="password" name="password" placeholder="Password" class="form-control input-lg">
								</div>
								<button class="btn btn-primary btn-lg pull-right" type="submit">Login</button>
								<input type="hidden" name="type" value="local">
							</form>
							<form method="post" action="/login" id="staff-login">
								<button class="btn btn-default btn-sm" type="submit">Frontline Staff Login</button>
								<input type="hidden" name="type" value="google">
							</form>
							<?php

						}

						?>
					</div>
				</div> <!-- /#content -->
			</div> <!-- /#body -->
			<div id="footer">
				<!-- <p class="footer-em">And let us consider how we may spur one another on toward love and good deeds, not giving up meeting together, as some are in the habit of doing, but encouraging one another &ndash; and all the more as you see the Day approaching.</p> -->
				<p class="footer-sm">Built with <i class="fa fa-heart"></i> by <a href="https://twitter.com/bigoness">Biggie</a></p>
			</div>
		</div>
	</body>
</html>
