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
		<link rel="stylesheet" href="/assets/manage.css">

		<script src="//code.jquery.com/jquery-2.1.1.min.js"></script>
		<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>

		<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.4/angular.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.4/angular-route.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.4/angular-resource.js"></script>

		<script src="/assets/manage.js"></script>
		<script>window.is_admin = <?=json_encode($isAdmin); ?>;</script>
	</head>
	<body ng-app="churchis">
		<nav class="navbar navbar-inverse navbar-fixed-top">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="/manage/"><img alt="Frontline" height="20" src="/assets/fcclogo-small.png"></a>
				</div>
				<div id="navbar" class="navbar-collapse collapse">
					<ul class="nav navbar-nav" ng-controller="HeaderCtl">
						<li ng-class="{ active: isActive('/groups(/\d+)?') }"><a href="#/groups">Groups</a></li>
						<li ng-class="{ active: isActive('/members(/\d+)?') }"><a href="#/members">Members</a></li>
						<?php if ($isAdmin) { ?>
						<li ng-class="{ active: isActive('/users(/\d+)?') }"><a href="#/users">User Accounts</a></li>
						<?php } ?>
					</ul>
					<ul class="nav navbar-nav navbar-right">
						<li class="hidden-xs"><a><?=htmlentities($loggedInAs); ?></a></li>
						<li><a href="/logout">Log Out</a></li>
					</ul>
				</div>
			</div>
		</nav>
		<div id="wrap" class="container">
			<div id="body" ng-view></div>
		</div>
		<div id="footer">
			<p class="footer-sm">Frontline Community Church Small Groups<br>Need help? <a href="mailto:office@frontlinegr.com">office@frontlinegr.com</a></p>
			<p class="footer-sm">Built with <i class="fa fa-heart"></i> by <a href="https://twitter.com/bigoness">Biggie</a></p>
		</div>
	</body>
</html>
