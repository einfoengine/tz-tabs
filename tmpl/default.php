<?php
/**
 * @package Tz Tabs
 * @version 2.1
 * @author ThemeZart http://www.themezart.com
 * @copyright Copyright (C) 2009 - 2011 ThemeZart
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */
defined( '_JEXEC' ) or die('Restricted access');

if($content_source !== 'joomla'){
	echo '<div class="alert alert-warning">'.JText::_('TZ_TABS_WARNING_WRONG_LAYOUT').'</div>';
	return;
}

$count = count($list);

$tabs = $params->get('count',5);
$tabs_position = $params->get('tabs_position','top');
$intro_count = $params->get('intro_limit',100);
$intro_text_limit = $params->get('intro_text_limit',1);
$show_readmore = $params->get('show_readmore',1);
$readmore_text = $params->get('readmore_text','Read more...');
$link_window = $params->get('link_window',1);
$show_title_innerside = $params->get('show_title_innerside',1);
$readmore_class = $params->get('readmore_class','btn');

if(intval($tabs) > $count) $tabs = $count;
elseif(intval($tabs) == 0) $tabs = $count;

$tabs_title = modTzTabsHelper::generateTabs($tabs,$list,$params);
?>
<div id="<?php echo $module_id;?>" class="tz-tabs tz-wrapper <?php echo $params->get('theme','style1');?>">
	<?php if($tabs_position == 'top') echo $tabs_title;?>

	<div id="<?php echo $module_id;?>-pans" class="tz-pans">
		<?php
			if ($tabs == 0) $tabs = count($list);
			for($i=0; $i<$tabs; $i++){
				if($list[$i]->introtext != NULL){ 
					//prepare the module title
					if (strpos($list[$i]->title,'::') !== false) {
						$moduleTitle = explode('::',$list[$i]->title);
						$moduleTitle = "<span>".$moduleTitle[0]."</span>".$moduleTitle[1];
					}else{
						$moduleTitle = $list[$i]->title;
					}
				?>
					<div class="tz-pane clearfix">
						<?php if($show_title_innerside){ ?>
							<h3 class="title">
								<?php echo $moduleTitle; ?>
							</h3>
						<?php } ?>
						<div class="content">
							<?php echo modTzTabsHelper::cText($list[$i]->introtext,$intro_count,$intro_text_limit) ?>
						</div>

						<?php if($show_readmore){ ?>
							<a href="<?php echo $list[$i]->link; ?>" class="<?php echo $readmore_class; ?>" <?php echo ($link_window =='2' ? 'target="_blank"' : ''); ?>>
								<?php echo $readmore_text; ?>
							</a>
						<?php } ?>
						
					</div>
				<?php 	
				}
			}
			?>
	</div>

	<?php if($tabs_position == 'bottom') echo $tabs_title;?>


</div>