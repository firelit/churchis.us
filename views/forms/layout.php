<?php if (!is_object($this)) die; ?>
<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title><?=$title; ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="robots" content="noindex, nofollow">

		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
		<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
		<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.3/css/bootstrapValidator.min.css">
		<link rel="stylesheet" href="/assets/forms.css">

		<script src="//code.jquery.com/jquery-2.1.1.min.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.3/js/bootstrapValidator.min.js"></script>
	</head>
	<body>
		<div id="wrap" class="container">
			<div id="header">
				<h1>Frontline Small Groups</h1>
			</div>
			<div id="body">
				<div id="content">

					<?php  $this->yieldNow(); ?>

				</div> <!-- /#content -->
			</div> <!-- /#body -->
			<div id="footer">
				And let us consider how we may spur one another on toward love and good deeds, not giving up meeting together, as some are in the habit of doing, but encouraging one another &ndash; and all the more as you see the Day approaching.
			</div>
		</div>
	</body>
</html>
