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

if($content_source !== 'mods'){
	echo '<div class="alert alert-warning">'.JText::_('TZ_TABS_WARNING_WRONG_LAYOUT').'</div>';
	return;
}

$tabs = $params->get('count',3);
$tabs_position = $params->get('tabs_position','top');
$show_title_innerside = $params->get('show_title_innerside',1);

$count = count($list);

if(intval($tabs) > $count) $tabs = $count;
elseif(intval($tabs) == 0) $tabs = $count;

$tabs_title = modTzTabsHelper::generateTabs($tabs,$list,$params);
?>

<div id="<?php echo $module_id;?>" class="tz-wrapper <?php echo $params->get('theme','style1');?>">
	<?php if($tabs_position == 'top') echo $tabs_title;?>

	<div id="<?php echo $module_id;?>-pans"  class="tz-pans">
		<?php
			if ($tabs == 0) $tabs = count($list);
			for($i=0; $i<$tabs; $i++){
				
				if($list[$i]->content != NULL){ 
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
						<?php echo $list[$i]->content; ?>
					</div>
				<?php
				}
			}

			?>
	</div>

	<?php if($tabs_position == 'bottom') echo $tabs_title;?>


</div>
