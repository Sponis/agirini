<?php
/**
 * @version     $Id$
 * @package     JLVExtension
 * @subpackage  JLV ImageGallery
 * @author      JLV Extension Team <admin@jlvextension.com>
 * @copyright   Copyright (C) 2013 JLV Extension. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 **/

// No direct access to this file
defined('_JEXEC') or die;

JHtml::stylesheet('modules/mod_jlv_imagegallery/assets/css/gallery.css');
JHtml::stylesheet('modules/mod_jlv_imagegallery/assets/css/jquery.fancybox-1.3.4.css');
if( !defined('SMART_JQUERY') && $params->get('include_jquery', 0) == "1" ){
	JHtml::script('modules/mod_jlv_imagegallery/assets/js/jquery-1.8.2.min.js');
	JHtml::script('modules/mod_jlv_imagegallery/assets/js/jquery-noconflict.js');
	define('SMART_JQUERY', 1);
}
JHtml::script('modules/mod_jlv_imagegallery/assets/js/jsmart.easing.1.3.js');
JHtml::script('modules/mod_jlv_imagegallery/assets/js/jquery.fancybox-1.3.4.pack.js');
JHtml::script('modules/mod_jlv_imagegallery/assets/js/jcarousel.js');

ImageHelper::setDefault($params);
if (count($items)>0){
	$instance	= rand().time();
	$titleposition			= $params->get("titleposition", 'over');
	$transition				= $params->get("transition", 'none');
	$show_nextprev					= $params->get('show_nextprev', 1);
	$count = count($items);
	$total = (int)$params->get('numberImage',27);
	if($total > $count){
		$total = $count;
	}
	$total_image_pag = $params->get('items_page', 9);
	$pags = (int)ceil($total/$total_image_pag);
	$play = $params->get('play', 1);
	if (!$play){
		$interval = 0;
	} else {
		$interval = $params->get('interval', 5000);
	}?>


<div id="jlv_imagegallery_<?php echo $instance;?>" class="jlv-gallery <?php if( $params->get("effect") == 'slide' ){ echo $params->get("effect");}?> <?php echo $params->get("deviceclass_sfx");?>" data-interval="<?php echo $interval;?>" data-pause="hover">
	<div class="pre-text">
		<?php echo $params->get('pretext'); ?>
	</div>	
	<div class="jlv-content">
		<div class="jlv-navigation clearfix">
			<div class="jlv-buttons">
				<ul>
					<?php if($show_nextprev==1){ ?>
					<li class="jlv-prev" href="#jlv_imagegallery_<?php echo $instance;?>" data-jslide="prev"></li>
					<?php } ?>
					<?php for($j=0; $j<$pags; $j++): ?>
						<li class="jlv-nav <?php if( $j == 0 ){echo ' sel';}?>"  href="#jlv_imagegallery_<?php echo $instance;?>" data-jslide="<?php echo $j;?>"></li>
					<?php endfor; ?>						
					<?php if($show_nextprev==1){ ?>
					<li class="jlv-next"  href="#jlv_imagegallery_<?php echo $instance;?>" data-jslide="next"></li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<div class="jlv-items">
			<?php
			for($i=0; $i<$pags; $i++){ ?>
			<div class="jlv-item <?php if($i==0){echo "active";}?> item">
				<?php $start = $i * $total_image_pag;
					$end   = ($i + 1) * $total_image_pag;
					$qu = 0;
					foreach ($items as $key => $item){ 
						if($qu == $total_image_pag){
							$qu = 0;
						}
						$qu++;
						
 						if ($key >= $start && $key < $end) {?>
					<div class="item-info">
						<a class="item-info-image" rel="jlv_imagegallery_image_<?php echo $instance;?>" href="<?php echo $item['url']; ?>" title="<?php echo $item['title'] ;?>">
							<?php echo JLVImageGallery::imageTag($item['image']);?>
							<span class="bg-hover"></span>
							<span class="icon-hover"></span>
						</a>
						
					</div>
			    	<?php
			    		$clear = 'clr1';
			    		if ($qu % 2 == 0) $clear .= ' clr2';
			    		if ($qu % 3 == 0) $clear .= ' clr3';
			    		if ($qu % 4 == 0) $clear .= ' clr4';
			    		if ($qu % 5 == 0) $clear .= ' clr5';
			    		if ($qu % 6 == 0) $clear .= ' clr6';
			    	?>
	    			<div class="<?php echo $clear; ?>"></div> 					
				<?php
					}} ?>
			</div>
		<?php
			}
			?>
		</div>
	</div>
	<div class="post-text">
		<?php echo $params->get('posttext'); ?>
	</div>
	<div class="copyright"><a href="http://jlvextension.com/joomla-extension/jlv-imagegallery.html" target="_blank" title="Image Gallery">Image Gallery</a> by <a href="http://jlvextension.com" target="_blank" title="Joomla! Extension">Joomla! Extension</a></div>
</div>


<?php } else{ echo JText::_('There are no image inside folder: ') . $params->get('folder');}?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#jlv_imagegallery_<?php echo $instance;?>').each(function(){
		var $this = $(this), options = options = !$this.data('modal') && $.extend({}, $this.data());
		$this.jcarousel(options);
		$this.bind('jslide', function(e){
			var index = $(this).find(e.relatedTarget).index();
			$('[data-jslide]').each(function(){
				var $nav = $(this), $navData = $nav.data(), href, $target = $($nav.attr('data-target') || (href = $nav.attr('href')) && href.replace(/.*(?=#[^\s]+$)/, ''));
				if ( !$target.is($this) ) return;
				if (typeof $navData.jslide == 'number' && $navData.jslide==index){
					$nav.addClass('sel');
				} else {
					$nav.removeClass('sel');
				}
			});

		});
	});
	
	$("a[rel=jlv_imagegallery_image_<?php echo $instance;?>]").fancybox({
		'transitionIn'	: '<?php echo $transition; ?>',
		'transitionOut'	: '<?php echo $transition; ?>',
		'titlePosition' : '<?php echo $titleposition; ?>',
		'titleFormat'	: function(title, currentArray, currentIndex, currentOpts) {
			return 'Image ' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? '  - ' + title : '') + '';
		},
		easingIn: 'easeInOutQuad',
		easingOut: 'easeInOutQuad',
		onStart: function(){
			var $btn = $('#jlv_imagegallery_<?php echo $instance; ?>');
			$btn.jcarousel('pause');
		},
		onClosed: function(){
			var $btn = $('#jlv_imagegallery_<?php echo $instance; ?>');
			$btn.jcarousel('cycle');
		}
	});
	
});
</script>

