<?php
/*------------------------------------------------------------------------
# mod_responsivegallery - Responsive Photo Gallery for Joomla 2.5 v2.7.0
# ------------------------------------------------------------------------
# author    GraphicAholic
# copyright Copyright (C) 2011 GraphicAholic.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.graphicaholic.com
-------------------------------------------------------------------------*/
// No direct access
defined('_JEXEC') or die('Restricted access');
$document =& JFactory::getDocument();
$path			= $params->get('path');
$containerMargin	= trim($params->get('containerMargin', '0px'));
$imageFeed		= $params->get('imageFeed', 0);
$thumbratio		= $params->get('thumbratio', 1) ? true : false;
$thumbwidth		= trim($params->get('thumbwidth', 64));
$thumbheight		= trim($params->get('thumbheight', 44));
$galleryNumber		= ($params->get('galleryNumber', 1));
$flickrAPI		= $params->get('flickrAPI');
$flickrSecret		= $params->get('flickrSecret', '');
$flickrToken		= $params->get('flickrToken', '');
$flickrPrivate		= $params->get('flickrPrivate', 0);
$flickrSet		= $params->get('flickrSet');
$flickrNumber		= $params->get('flickrNumber', '');
$flickrThumb		= $params->get('flickrThumb');
if($flickrThumb == "1") $flickrThumb = "square";
if($flickrThumb == "2") $flickrThumb = "largesquare";
if($flickrThumb == "3") $flickrThumb = "thumbnail";
$flickrCaption		= $params->get('flickrCaption', 2);
$autoPlay		= $params->get('autoPlay');
$infiniteLoop		= $params->get('infiniteLoop');
$showJtitle		= $params->get('showJtitle', '');
?>
<?php if ($autoPlay == "2") : ?>
<script type="text/javascript">
jQuery(function(){
         doTimer()
        })
var t;
var timer_is_on=0;

function timedCount()
{
jQuery('.rg-image-nav-next').click()
t=setTimeout("timedCount()",<?php echo $infiniteLoop; ?>);
}

function doTimer()
{
if (!timer_is_on)
{
timer_is_on=1;
timedCount(<?php echo $infiniteLoop; ?>);
}
}

function stopCount()
{
clearTimeout(t);
timer_is_on=0;
}
</script>
<?php endif ; ?>
<?php if ($imageFeed == "0"): ?>
			<style type="text/css">
				.rg-image img{
					max-height:<?php echo $params->get('maxHeight') ?> !important;
					max-width:<?php echo $params->get('maxWidth') ?> !important;
				} 
			</style>
		<noscript>
			<style type="text/css">
				.es-carousel<?php echo $moduleID; ?> ul{
					display:block;
				} 
			</style>
		</noscript>
		<script id="img-wrapper-tmpl" type="text/x-jquery-tmpl">	
			<div class="rg-image-wrapper">
				{{if itemsCount > 1}}
					<div class="rg-image-nav">
						<a href="#" class="rg-image-nav-prev">Previous Image</a>
						<a href="#" class="rg-image-nav-next">Next Image</a>
					</div>
				{{/if}}
				<div class="rg-image"></div>
				<div class="rg-loading"></div>
				<div class="rg-caption-wrapper">
					<div class="rg-caption" style="display:none;">
						<p></p>
					</div>
				</div>
			</div>
		</script>
			<div class="contentText" style="margin-top:<?php echo $containerMargin; ?>">
				<div id="rg-gallery<?php echo $galleryNumber; ?>" class="rg-gallery">
				<?php if ($autoPlay == "2") : ?>
					<div id="buttons">
					<div class="playbutton"><a href="javascript:doTimer();"><img src="modules/mod_responsivegallery/images/play.png" alt="Play" style="margin-bottom:-4px" width="18" height="18" alt="Play"></a></div>
					<div class="pausebutton"><a href="javascript:stopCount();"><img src="modules/mod_responsivegallery/images/pause.png" alt="Pause" style="margin-bottom:-4px;margin-left:-1px" width="18" height="18" alt="Pause"></a></div>
						</div>
						<?php endif ; ?>
				<?php if ($autoPlay == "1") : ?>
					<div id="buttons">
					<div class="noplaybutton"></div>
					<div class="nopausebutton"></div>
						</div>
						<?php endif ; ?>
					<div class="rg-thumbs">
						<!-- Elastislide Carousel Thumbnail Viewer -->
						<div class="es-carousel-wrapper">
							<div class="es-nav">
								<span class="es-nav-prev">Previous</span>
								<span class="es-nav-next">Next</span>
							</div>
							<div class="es-carousel">
		<style>.es-carousel<?php echo $moduleID; ?> ul li a img{
			width: <?php echo $thumbwidth; ?>;
			height: <?php echo $thumbheight; ?>;
			}
		</style>
		<?php if ($showJtitle == "1") : ?>
		<ul>
			<?php foreach($list as $item) { ?>
					<li>
						<a href="#"><img src="<?php echo $item->thumb ?>" data-large="<?php echo $LiveSite ?><?php echo $item->image ?>" alt="<?php echo $item->title ?>" data-description="" data-href="<?php echo $item->image ?>" />
					</a>
					</li>
			<?php } ?>
		</ul>
		<?php endif ; ?>
		<?php if ($showJtitle == "2") : ?>
		<ul>
			<?php foreach($list as $item) { ?>
					<li>
						<a href="#"><img src="<?php echo $item->thumb ?>" data-large="<?php echo $LiveSite ?><?php echo $item->image ?>" alt="<?php echo $item->title ?>" data-description="<?php echo $item->title ?>" data-href="<?php echo $item->image ?>" />
					</a>
					</li>
			<?php } ?>
		</ul>
		<?php endif ; ?>
							</div>
						</div><!-- End Elastislide Carousel Thumbnail Viewer -->
					</div><!-- rg-thumbs -->
				</div><!-- rg-gallery -->
			</div>
		<?php endif ; ?>
<?php if ($imageFeed == "1") : ?>
			<style type="text/css">
				.rg-image img{
					max-height:<?php echo $params->get('maxHeight') ?> !important;
					max-width:<?php echo $params->get('maxWidth') ?> !important;
				} 
			</style>
		<noscript>
			<style type="text/css">
				.es-carousel<?php echo $moduleID; ?> ul{
					display:block;
				} 
			</style>
		</noscript>
		<script id="img-wrapper-tmpl" type="text/x-jquery-tmpl">	
			<div class="rg-image-wrapper">
				{{if itemsCount > 1}}
					<div class="rg-image-nav">
						<a href="#" class="rg-image-nav-prev">Previous Image</a>
						<a href="#" class="rg-image-nav-next">Next Image</a>
					</div>
				{{/if}}
				<div class="rg-image"></div>
				<div class="rg-loading"></div>
				<div class="rg-caption-wrapper">
					<div class="rg-caption" style="display:none;">
						<p></p>
					</div>
				</div>
			</div>
		</script>
			<div class="contentText" style="margin-top:<?php echo $containerMargin; ?>">
				<div id="rg-gallery<?php echo $galleryNumber; ?>" class="rg-gallery">
				<?php if ($autoPlay == "2") : ?>
					<div id="buttons">
					<div class="playbutton"><a href="javascript:doTimer();"><img src="modules/mod_responsivegallery/images/play.png" alt="Play" style="margin-bottom:-4px" width="18" height="18" alt="Play"></a></div>
					<div class="pausebutton"><a href="javascript:stopCount();"><img src="modules/mod_responsivegallery/images/pause.png" alt="Pause" style="margin-bottom:-4px;margin-left:-1px" width="18" height="18" alt="Pause"></a></div>
						</div>
						<?php endif ; ?>
				<?php if ($autoPlay == "1") : ?>
					<div id="buttons">
					<div class="noplaybutton"></div>
					<div class="nopausebutton"></div>
						</div>
						<?php endif ; ?>
					<div class="rg-thumbs">
						<!-- Elastislide Carousel Thumbnail Viewer -->
						<div class="es-carousel-wrapper">
							<div class="es-nav">
								<span class="es-nav-prev">Previous</span>
								<span class="es-nav-next">Next</span>
							</div>
							<div class="es-carousel">
		<?php
 			require_once("flickr/phpFlickr.php");
		if($flickrPrivate =="1") {			
 			$f = new phpFlickr("$flickrAPI", "$flickrSecret");
 			$f->setToken("$flickrToken");				
			}
		if($flickrPrivate =="0") {
 			$f = new phpFlickr("$flickrAPI");
			}
 			$ph_sets = $f->photosets_getList($user);
		?>
		<?php if ($flickrCaption == "1") : ?>
			<div id="gallery">
				<div class="photosets">
					<?php $photos = $f->photosets_getPhotos($flickrSet, NULL, NULL, $flickrNumber); ?>
					<?php foreach ($photos['photoset']['photo'] as $photo): $d = $f->photos_getInfo($photo['id']); ?>
						<div class="photos">
						<ul>
				<li>
					<a href="#"><img src="<?= $f->buildPhotoURL($photo, $flickrThumb) ?>" data-large="<?= $f->buildPhotoURL($photo, 'large') ?>" alt="<?= $photo['title'] ?>" data-description= "<?= $photo['title'] ?>" data-href="<?= $f->buildPhotoURL($photo, 'large') ?>" />
					</a>
  						</div>
  					<?php endforeach ; ?>
  				</div>
				</li>
						</ul>
			</div>
							</div><!-- End Elastislide Carousel Thumbnail Viewer -->
						</div><!-- rg-thumbs -->
					</div><!-- rg-gallery -->
				</div>
			</div>
		<?php elseif ($flickrCaption == "2") : ?>
			<div id="gallery">
				<div class="photosets">
					<?php $photos = $f->photosets_getPhotos($flickrSet, NULL, NULL, $flickrNumber); ?>
					<?php foreach ($photos['photoset']['photo'] as $photo): $d = $f->photos_getInfo($photo['id']); ?>
						<div class="photos">
						<ul>
				<li>
					<a href="#"><img src="<?= $f->buildPhotoURL($photo, $flickrThumb) ?>" data-large="<?= $f->buildPhotoURL($photo, 'large') ?>" alt="<?= $photo['title'] ?>" data-description= "<?= $d['photo']['description'] ?>" data-href="<?= $f->buildPhotoURL($photo, 'large') ?>" />
					</a>
  						</div>
  					<?php endforeach; ?>
  				</div>
				</li>
						</ul>
			</div>
							</div><!-- End Elastislide Carousel Thumbnail Viewer -->
						</div><!-- rg-thumbs -->
					</div><!-- rg-gallery -->
				</div>
			</div>
		<?php elseif ($flickrCaption == "3") : ?>
			<div id="gallery">
				<div class="photosets">
					<?php $photos = $f->photosets_getPhotos($flickrSet, NULL, NULL, $flickrNumber); ?>
					<?php foreach ($photos['photoset']['photo'] as $photo): $d = $f->photos_getInfo($photo['id']); ?>
						<div class="photos">
						<ul>
				<li>
					<a href="#"><img src="<?= $f->buildPhotoURL($photo, $flickrThumb) ?>" data-large="<?= $f->buildPhotoURL($photo, 'large') ?>" alt="<?= $photo['title'] ?>" data-description= "<?= $photo['title'] ?> - <?= $d['photo']['description'] ?>" data-href="<?= $f->buildPhotoURL($photo, 'large') ?>" />
					</a>
  						</div>
  					<?php endforeach; ?>
  				</div>
				</li>
						</ul>
			</div>
							</div><!-- End Elastislide Carousel Thumbnail Viewer -->
						</div><!-- rg-thumbs -->
					</div><!-- rg-gallery -->
				</div>
			</div>
		<?php elseif ($flickrCaption == "4") : ?>
			<div id="gallery">
				<div class="photosets">
					<?php $photos = $f->photosets_getPhotos($flickrSet, NULL, NULL, $flickrNumber); ?>
					<?php foreach ($photos['photoset']['photo'] as $photo): $d = $f->photos_getInfo($photo['id']); ?>
						<div class="photos">
						<ul>
				<li>
					<a href="#"><img src="<?= $f->buildPhotoURL($photo, $flickrThumb) ?>" data-large="<?= $f->buildPhotoURL($photo, 'large') ?>" alt="" data-description= "" data-href="<?= $f->buildPhotoURL($photo, 'large') ?>" />
					</a>
  						</div>
  					<?php endforeach ; ?>
  				</div>
				</li>
						</ul>
			</div>
							</div><!-- End Elastislide Carousel Thumbnail Viewer -->
						</div><!-- rg-thumbs -->
					</div><!-- rg-gallery -->
				</div>
			</div>
		<?php endif ; ?>
<?php endif ; ?>