<?php if (!is_object($this)) die; ?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Error <?=$error; ?></title>
	<meta name="robots" content="noindex, nofollow">
	<style type="text/css">
		html { height: 100%; }
		body { height: 100%; padding: 0; margin: 0; font-family: helvetica, sans-serif; font-size: 18pt; }	
		a { outline: none; }
		#wrapper { width: 100%; margin: 0 auto; padding: 0; height: 100%; }
		#wrapper td { vertical-align: middle; text-align: center; }
		.smaller { margin: 1.12em 0; opacity: 0.3; }
	</style>
</head>
<body>
	<table id="wrapper">
		<tr><td>
			<?=$message; ?>
			<div class="smaller">[Error Type <?=$error; ?>]</div>
		</td></tr>
	</table>
</body>
</html>