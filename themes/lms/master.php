<!DOCTYPE html>
<?php
$meta_data = $controller ? $controller->_meta_data () : null;
$meta_title = ($meta_data ? ($meta_data ['title'] ? $meta_data ['title'] . ' | ' : '') : '') . SITE_TITLE;
$meta_description = $meta_data ? $meta_data ['description'] : '';
$meta_keywords = $meta_data ? $meta_data ['keywords'] : '';
$css_array = $controller ? $controller->_css_list () : array ();
$js_array = $controller ? $controller->_js_list () : array ();
$currentUser = getCurrentUser ();

?>

<html lang="en">
<head>
<title><?=$meta_title?></title>

<meta name="author" content="Learner Management System" />
<meta name="copyright"
	content="Copyright &copy; 2020 Learner Management System. All Rights Reserved." />
<meta name="robots" content="all" />
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!--[if (gte IE 9) | (!IE)]><!-->
<script src="/public_html/js/jquery-2.1.4.min.js"></script>
<!--<![endif]-->
<link rel="stylesheet" type="text/css"
	href="<?=$routeData->theme_url?>css/bootstrap.min.css">
<link rel="stylesheet" type="text/css"
	href="<?=$routeData->theme_url?>css/bootstrap-theme.min.css">
<link rel="stylesheet" type="text/css"
	href="<?=$routeData->theme_url?>css/modern-business.css">
<link rel="stylesheet" type="text/css"
	href="<?=$routeData->theme_url?>css/style.css">
<link rel="stylesheet" type="text/css"
	href="/public_html/fontawesome/css/font-awesome.css">
<?php foreach($css_array as $href):?>
<link rel="stylesheet" type="text/css" href="<?=$href?>" media="screen" />
<?php endforeach; ?>


<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->

<!-- Include all compiled plugins (below), or include individual files as needed -->
<script
	src="<?=$routeData->theme_url?>js/bootstrap.min.js"></script>


<?php foreach($js_array as $src):?>
<script type="text/javascript" src="<?=$src?>"></script>
<?php endforeach; ?>

</head>
<body>
	<nav class="navbar navbar-inverse navbar-fixed-top ">
		<div class="container">
			<!-- Brand and toggle get grouped for better mobile display -->
			<div class="navbar-header">
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
					<span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span>
				</button>
				<a class="navbar-brand" href="/"> Learner Management System</a>
			
			</div>
			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<ul class="nav navbar-nav" id="navbar-main">
					<?php if(isset($_SESSION['user']['id'])): ?>
					<?php require 'menu-show.inc.php'; ?>
					<?php endif; ?>
 				</ul>
				<ul class="nav navbar-nav navbar-right">
				<?php if(isset($_SESSION['user']['id'])): ?>
    					<li class="dropdown">
        					<a class="dropdown-toggle" data-toggle="dropdown" href="#" id="user">
        					 		<?=$currentUser->name.' '.$currentUser->surname?> 
        					 		<span class="caret"></span>
        					</a>
    						<ul class="dropdown-menu" aria-labelledby="user">
    							<li><a href="/client/logout" /> <i class="fa fa-sign-out"></i> Sign Out </a></li>
    						</ul>
    					</li>
				 <?php else: ?>
					<li class="divider-vertical"></li>
						
					<li class="dropdown">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#" id="signin"> <i class="fa fa-sign-in"></i> Sign In <span class="caret"></span></a>
						<div class="dropdown-menu" style="padding: 15px; min-width: 250px;">
							<form role="form" method="POST" action="<?=WEB_PATH_S?>login/">
								<fieldset>
									<legend>Please sign in</legend>
									<input type="hidden" name="login" value="1" />
									<div class="form-group signin">
										<input name="email" id="login_email" type="email" class="form-control" placeholder="USERNAME">
									</div>
									<div class="form-group signin">
										<input name="password" id="login_password" type="password" class="form-control" placeholder="Password">
									</div>
    								<div class="form-group signin">
										<button class="btn btn-lg bg-blue-grey btn-block" type="submit">Sign in</button>
									</div>
								</fieldset>
							</form>
						</div></li>
					<?php endif; ?>
				</ul>
			</div>
			<!--/.navbar-collapse -->
		</div>
	</nav>


	<?=$page_content?>


</body>
<footer id="footer">

	<section id="footerRights">
		<div class="container">
			<div class="col-md-8 footer-grid">
					<div class="copyrights text-center"></div>
			</div>

			<div class="col-md-4 footer-grid">
				<div class="social-icons footer-social-icons">
					<a class="facebook" href="#"></a> <a class="twitter" href="#"></a>
					<a class="google-plus" href="#"></a>
				</div>
			</div>

			<div class="clearfix"></div>
		</div>
	</section>
</footer>
</html>