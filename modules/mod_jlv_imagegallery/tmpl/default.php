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

//JHtml::stylesheet('modules/mod_jlv_imagegallery/assets/css/gallery.css');
//JHtml::stylesheet('modules/mod_jlv_imagegallery/assets/css/jquery.fancybox-1.3.4.css');
JHtml::stylesheet('modules/mod_jlv_imagegallery/assets/js/jquery.fancybox.css');
if( !defined('SMART_JQUERY') && $params->get('include_jquery', 0) == "1" ){
	//JHtml::script('modules/mod_jlv_imagegallery/assets/js/jquery-1.8.2.min.js');
	JHtml::script('modules/mod_jlv_imagegallery/assets/js/jquery-1.10.1.min.js');
	JHtml::script('modules/mod_jlv_imagegallery/assets/js/jquery-noconflict.js');
	define('SMART_JQUERY', 1);
}
//JHtml::script('modules/mod_jlv_imagegallery/assets/js/jsmart.easing.1.3.js');
//JHtml::script('modules/mod_jlv_imagegallery/assets/js/jquery.fancybox-1.3.4.pack.js');
//JHtml::script('modules/mod_jlv_imagegallery/assets/js/jcarousel.js');
JHtml::script('modules/mod_jlv_imagegallery/assets/js/jquery.fancybox.pack.js');

ImageHelper::setDefault($params);
if (count($items)>0){
	$instance	= rand().time();
	$titleposition			= $params->get("titleposition", 'over');
	$transition				= $params->get("transition", 'none');
	$show_nextprev			= $params->get('show_nextprev', 1);
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
	
		
			<?php
			for($i=0; $i<$pags; $i++){ ?>
			
				<?php $start = $i * $total_image_pag;
					$end   = ($i + 1) * $total_image_pag;
					$qu = 0;
					foreach ($items as $key => $item){ 
						if($qu == $total_image_pag){
							$qu = 0;
						}
						$qu++;
						
 						if ($key >= $start && $key < $end) {?>
					
						<a class="item-info-image fancybox" rel="jlv_imagegallery_image_<?php echo $instance;?>" href="<?php echo $item['url']; ?>" title="<?php echo $item['title'] ;?>">
							<?php echo JLVImageGallery::imageTag($item['image']);?>
							<span class="bg-hover"></span>
							<span class="icon-hover"></span>
						</a>
	    								
				<?php
					}} ?>			
		<?php
			}
			?>
		

</div>


<?php } else{ echo JText::_('There are no image inside folder: ') . $params->get('folder');}?>

<script type="text/javascript">
	$(document).ready(function() {
		$(".fancybox").fancybox();
	});
</script>
