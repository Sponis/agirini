<?php
/**
 * @copyright	Copyright (C) 2005 - 2009 RocketTheme, LLC - All Rights Reserved.
 * @license		GNU/GPL, see LICENSE.php
**/
defined('_JEXEC') or die;
define( 'YOURBASEPATH', dirname(__FILE__) );
require(YOURBASEPATH . DS . "rt_styleswitcher.php");

$color_style			= $this->params->get("colorStyle", "dark");
$template_width 		= $this->params->get("templateWidth", "962");
$leftcolumn_width		= $this->params->get("leftcolumnWidth", "210");
$rightcolumn_width		= $this->params->get("rightcolumnWidth", "210");
$leftcolumn_color		= $this->params->get("leftcolumnColor", "color2");
$rightcolumn_color		= $this->params->get("rightcolumnColor", "color1");
$mootools_enabled       = ($this->params->get("mootools_enabled", 1)  == 0)?"false":"true";
$caption_enabled        = ($this->params->get("caption_enabled", 1)  == 0)?"false":"true";
$rockettheme_logo       = ($this->params->get("rocketthemeLogo", 1)  == 0)?"false":"true";

require(YOURBASEPATH . DS . "rt_styleloader.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Agia Irini Hotel at Santorini</title>
    <meta name="description" content="Agia Irini Hotel at Santorini">
    <meta name="author" content="Vassilis Sponis">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

    <link rel="stylesheet" href="/templates/rt_afterburner/css/normalize.css">
    <script src="/templates/rt_afterburner/js/modernizr-2.6.2.min.js"></script>
   	<jdoc:include type="head" />
	<?php
	require(YOURBASEPATH . DS . "rt_utils.php");

	?> 
	<link rel="stylesheet" href="/templates/rt_afterburner/css/extra.css">
	<link rel="stylesheet" href="/templates/rt_afterburner/css/supersized.core.css" type="text/css" media="screen"/>
	<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/rt_afterburner/css/<?php echo $tstyle ?>.css" type="text/css" />
<!--[if lte IE 6]>
<script type="text/javascript" src="<?php echo $this->baseurl ?>/templates/rt_afterburner/js/ie_suckerfish.js"></script>
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/rt_afterburner/css/styles.ie.css" type="text/css" />
<![endif]-->
<!--[if lte IE 7]>
<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/rt_afterburner/css/styles.ie7.css" type="text/css" />
<![endif]-->
</head>
<body>
	
	<div id="main">
		<div id="wrapper" class="foreground">
			<header>
			<div id="header">
				<jdoc:include type="modules" name="top" style="afterburner" />		
				<a href="<?php echo $this->baseurl ?>" id="logo"></a>
			</div>
			<div id="nav">
				<jdoc:include type="modules" name="nav" style="none" />
			</div>
			</header>
			<div id="content">
			<div id="message">
				<jdoc:include type="message" />
			</div>
			<?php if ($this->countModules('showcase')) : ?>
			<div id="showcase" class="dp100">
				<div class="background"></div>
				<div class="foreground">
					<jdoc:include type="modules" name="showcase" style="none" />
				</div>
			</div>
		<?php endif; ?>
		<jdoc:include type="modules" name="advertisement" style="afterburner" />
		<div id="main-content" class="<?php echo $col_mode; ?>">
			<div id="colmask" class="ckl-<?php echo $leftcolumn_color; ?>">
				<div id="colmid" class="cdr-<?php echo $rightcolumn_color; ?>">
					<div id="colright" class="ctr-<?php echo $rightcolumn_color; ?>">
						<div id="col1wrap">
							<div id="col1pad">
								<div id="col1">
									<?php if ($this->countModules('breadcrumb')) : ?>
									<div class="breadcrumbs-pad">
										<jdoc:include type="modules" name="breadcrumb" />
									</div>
								<?php endif; ?>
								<?php if ($this->countModules('user1 or user2 or user3')) : ?>
								<div id="mainmods" class="spacer<?php echo $mainmod_width; ?>">
									<jdoc:include type="modules" name="user1" style="afterburner" />
									<jdoc:include type="modules" name="user2" style="afterburner" />
									<jdoc:include type="modules" name="user3" style="afterburner" />
								</div>
							<?php endif; ?>
							<div class="component-pad">
								<jdoc:include type="component" />
							</div>
							<?php if ($this->countModules('user4 or user5 or user6')) : ?>
							<div id="mainmods2" class="spacer<?php echo $mainmod2_width; ?>">
								<jdoc:include type="modules" name="user4" style="afterburner" />
								<jdoc:include type="modules" name="user5" style="afterburner" />
								<jdoc:include type="modules" name="user6" style="afterburner" />
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php if ($leftcolumn_width != 0) : ?>
			<div id="col2" class="<?php echo $leftcolumn_color; ?>">
				<jdoc:include type="modules" name="left" style="afterburner" />
			</div>
		<?php endif; ?>
		<?php if ($rightcolumn_width != 0) : ?>
		<div id="col3" class="<?php echo $rightcolumn_color; ?>">
			<jdoc:include type="modules" name="right" style="afterburner" />
		</div>
	<?php endif; ?>
</div>
</div>
</div>
</div>
</div>
<footer>
<!--<?php if ($this->countModules('user7 or user8 or user9')) : ?>
	<div id="mainmods3" class="spacer<?php echo $mainmod3_width; ?>">
		<jdoc:include type="modules" name="user7" style="afterburner" />
		<jdoc:include type="modules" name="user8" style="afterburner" />
		<jdoc:include type="modules" name="user9" style="afterburner" />
	</div>
<?php endif; ?>
<?php if ($this->countModules('bottom')) : ?>
	<div id="footer">
		<div class="footer-pad">
			<jdoc:include type="modules" name="bottom" style="none" />
		</div>
	</div>
<?php endif; ?>
<?php if ($rockettheme_logo=="true") : ?>
	<a href="http://www.rockettheme.com"><span id="logo2"></span></a>
<?php endif; ?>-->
<jdoc:include type="modules" name="footer" style="afterburner" />
<!--<jdoc:include type="modules" name="debug" style="none" />
</div>
</div>-->
</footer>
<script src="/templates/rt_afterburner/js/jquery-1.10.2.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script src="/templates/rt_afterburner/js/main.js"></script>
<script src="/templates/rt_afterburner/js/supersized.core.3.2.1.min.js"></script>
<!--<div class="background">-->
		<jdoc:include type="modules" name="backg" style="none" />
	<!--</div>-->
</body>
</html>