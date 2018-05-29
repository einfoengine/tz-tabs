<?php
/**
 * @package Tz Tabs
 * @version 2.1
 * @author ThemeZart http://www.themezart.com
 * @copyright Copyright (C) 2009 - 2011 ThemeZart
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 *
 */

// no direct access
defined( '_JEXEC' ) or die('Restricted access');

require_once JPATH_SITE . '/components/com_content/helpers/route.php';
JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel');

abstract class modTzTabsHelper{

    public static function getLists(&$params)
	{
        //joomla specific
        if((string)$params->get('content_source') == 'joomla'){
            // Get the dbo
            $db = JFactory::getDbo();

           // Get an instance of the generic articles model
			$model = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

            // Set application parameters in model
            $app = JFactory::getApplication();
            $appParams = $app->getParams();
            $model->setState('params', $appParams);

            // Set the filters based on the module params
            $model->setState('list.start', 0);
            $model->setState('list.limit', (int) $params->get('count', 5));
            $model->setState('filter.published', 1);

            // Access filter
            $access = !JComponentHelper::getParams('com_content')->get('show_noauth');
            $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
            $model->setState('filter.access', $access);

            // Category filter
            $model->setState('filter.category_id', $params->get('catid', array()));

            // User filter
            $userId = JFactory::getUser()->get('id');
            switch ($params->get('user_id'))
            {
                    case 'by_me':
                            $model->setState('filter.author_id', (int) $userId);
                            break;
                    case 'not_me':
                            $model->setState('filter.author_id', $userId);
                            $model->setState('filter.author_id.include', false);
                            break;

                    case '0':
                            break;

                    default:
                            $model->setState('filter.author_id', (int) $params->get('user_id'));
                            break;
            }
            // Filter by language
            $model->setState('filter.language',$app->getLanguageFilter());

            //  Featured switch
            switch ($params->get('show_featured'))
            {
                    case '1':
                            $model->setState('filter.featured', 'only');
                            break;
                    case '0':
                            $model->setState('filter.featured', 'hide');
                            break;
                    default:
                            $model->setState('filter.featured', 'show');
                            break;
            }

            // Set ordering
            $order_map = array(
                    'm_dsc' => 'a.modified DESC, a.created',
                    'mc_dsc' => 'CASE WHEN (a.modified = '.$db->quote($db->getNullDate()).') THEN a.created ELSE a.modified END',
                    'c_dsc' => 'a.created',
                    'p_dsc' => 'a.publish_up',
            );
            $ordering = JArrayHelper::getValue($order_map, $params->get('ordering'), 'a.publish_up');
            $dir = 'DESC';

            $model->setState('list.ordering', $ordering);
            $model->setState('list.direction', $dir);
            $items = $model->getItems();

           foreach ($items as &$item) {
                //prepare the module title
				if (strpos($item->title,'::') !== false) {
					$itemTitle = explode('::',$item->title);
					$itemTitle = "<span>".$itemTitle[0]."</span>".$itemTitle[1];
				}else{
					$itemTitle = $item->title;
				}
				
				$item->title	=	$itemTitle;
				$item->slug		=	$item->id.':'.$item->alias;
                $item->catslug	=	$item->catid.':'.$item->category_alias;

                if ($access || in_array($item->access, $authorised))
                {
                        // We know that user has the privilege to view the article
                        $item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
                }
                else {
                        $item->link = JRoute::_('index.php?option=com_user&view=login');
                }

                $item->introtext = JHtml::_('content.prepare', $item->introtext);

            }

            return $items;

        }else{
            //module specific
            $mods = $params->get('modules');
            $options 	= array('style' => 'none');
            $items = array();

            for ($i=0;$i<count($mods);$i++) {
				$modules = modTzTabsHelper::getModule($mods[$i]);
				$items[$i] = new stdClass();
                $items[$i]->order 	= $modules->ordering;
				
				//prepare the module title
				if (strpos($modules->title,'::') !== false) {
					$moduleTitle = explode('::',$modules->title);
					$moduleTitle = "<span>".$moduleTitle[0]."</span>".$moduleTitle[1];
				}else{
					$moduleTitle = $modules->title;
				}
                
				$items[$i]->title 	= $moduleTitle;
                $items[$i]->content = $items[$i]->introtext = JModuleHelper::renderModule( $modules, $options);
                $items[$i]->params = $modules->params;
		    }

		    return $items;

        }


    }
    
	//fetch module by id
    public static function getModule( $id )
	{

		$db		= JFactory::getDBO();
		$where = ' AND ( m.id='.$id.' ) ';

		$query = 'SELECT *'.
			' FROM #__modules AS m'.
			' WHERE m.client_id = 0'.
			$where.
			' ORDER BY ordering'.
			' LIMIT 1';

		$db->setQuery( $query );
		$module = $db->loadObject();

		if (!$module) return null;

		$file				= $module->module;
		$custom				= substr($file, 0, 4) == 'mod_' ?  0 : 1;
		$module->user		= $custom;
		$module->name		= $custom ? $module->title : substr($file, 4);
		$module->style		= null;
		$module->position	= strtolower($module->position);
		$clean[$module->id]	= $module;

		return $module;
	}


    public static function loadScripts($params, $module_id)
	{
        $doc = JFactory::getDocument();
        //load jquery first
        modTzTabsHelper::loadJquery($params);
        
        $effect         = "'". $params->get('transition_type','default'). "'";
        $fadein_speed   = (int)$params->get('fadein_speed',500);
        $fadeout_speed  = (int)$params->get('fadeout_speed',0);
        $auto_play      = ( (int)$params->get('auto_play',0) ) ? 'true' : 'false';
        $auto_pause     = ( (int)$params->get('auto_pause',1) ) ? 'true' : 'false';
        $event          = "'". $params->get('tabs_interaction','click'). "'";

        //scrollable js settings.
        if($params->get('tabs_scrollable')){
            $scroll = ".slideshow({autoplay: {$auto_play},autopause: {$auto_pause}})";
        }else{
            $scroll = '';
        }
        if((int)$auto_play){
            $rotate = 'rotate: true,';
        }else{
            $rotate = '';
        }

        $js = "
            jQuery.noConflict();
            jQuery(document).ready(function(){
                jQuery('#{$module_id} .tz-nav ul').tabs('#{$module_id}-pans > .tz-pane',{
                    effect: {$effect},
                    fadeInSpeed: {$fadein_speed},
                    fadeOutSpeed: {$fadeout_speed},
                    {$rotate}
                    event: {$event}
                }){$scroll};
            });
        ";
        $doc->addScriptDeclaration($js);

        if(!defined('TZ_TABS')){
            //add tab engine js file
            $doc->addScript(JURI::root(true).'/modules/mod_tz_tabs/assets/js/tz_tabs.js');
            define('TZ_TABS',1);
        }
    }


    public static function loadJquery($params)
	{
        $doc = JFactory::getDocument();    //document object
        $app = JFactory::getApplication(); //application object
		
		if ( version_compare( JVERSION, '3.0', '<' ) == 1) {  
		
			static $jqLoaded;

			if ($jqLoaded) {
				return;
			}

			if($params->get('load_jquery') AND !$app->get('jQuery')){
				//get the cdn
				$cdn = $params->get('jquery_source');
				switch ($cdn){
					case 'google_cdn':
						$file = 'https://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js';
						break;
					case 'local':
						$file = JURI::root(true).'/modules/mod_tz_tabs/assets/js/jquery-2.0.2.min.js';
						break;
				}
				$app->set('jQuery','2.0.2');
				$doc->addScript($file);
				//$doc->addScriptDeclaration("jQuery.noConflict();");
				$jqLoaded = TRUE;
			}
		}else{
			JHTML::_('jquery.framework');
			//JHtml::_('bootstrap.framework')
		}
    }


    public static function loadStyles($params)
	{
        $app        = JApplication::getInstance('site', array(), 'J');
        $template   = $app->getTemplate();
        $doc        = JFactory::getDocument();
		//echo $params->get('theme');die;
        //Load stylesheets
        if($params->get('theme') !== 'custom' )
        {
            $style_path = JURI::root(true).'/modules/mod_tz_tabs/assets/styles/';
            $style_selected = $params->get('theme','style1');
            $style_file_name = $style_selected . '.css';
			//echo $style_path . $style_selected . '/' . $style_file_name;die;
            $doc->addStyleSheet($style_path . $style_selected . '/' . $style_file_name);
        }else{
            if (file_exists(JPATH_SITE.DS.'templates'.DS.$template.'/css/tz_tabs.css'))
            {
               $doc->addStyleSheet(JURI::root(true).'/templates/'.$template.'/css/tz_tabs.css');
            }
        }
    }


    public static function generateTabs($tabs, $list, $params)
	{
        $title_type = $params->get('tabs_title_type');
        $tab_scrollable = $params->get('tabs_scrollable');
        $position = $params->get('tabs_position','top');
		
        if($title_type == 'custom'){
            $titles = explode(",",$params->get('tabs_title_custom'));
        }

        if($tabs == 0 OR $tabs>count($list)) $tabs = count($list);

        $html  = "<div class='tz-nav $position'>";
        if($params->get('tabs_scrollable')) $html .= "<a class='backward'>backward</a>\n";
        $html .= "<ul>";

        for($i=0; $i<$tabs; $i++){
            $class = '';
            if($list[$i]->introtext != NULL){
                if(!$i) $class= 'first';
                if($i == $tabs - 1) $class= 'last';
				//params
                if($title_type == 'custom'){
					$title = (isset($titles[$i])) ? $titles[$i] : '';
				}else{
					$title = $list[$i]->title;
				}
				
				if((string)$params->get('content_source') == 'mods'){
					$moduleParams = json_decode($list[$i]->params);
					$header_class = $moduleParams->header_class;
				}else{
					$header_class = false;
				}
				//prepare the module title
				if (strpos($title,'::') !== false) {
					$moduleTitle = explode('::',$title);
					$moduleTitle = "<span>".$moduleTitle[0]."</span>".$moduleTitle[1];
				}else{
					$moduleTitle = $title;
				}
                $html .= "<li class='$class' ><a href=\"javascript::\">".($header_class ? '<i class="'.$header_class.'"></i> ' : '')."$moduleTitle</a></li>\n";

            }

        }
        $html .= "</ul>\n";
        if($params->get('tabs_scrollable')) $html .= "<a class='forward'>forward</a>\n";
        $html .= "<div class='clear'></div>";
        $html .= "</div> <!--tz-nav end-->\n";

        return $html;
        
    }
	
	public static function cText($text, $limit, $type=0) {//function to cut text
		//$text 					= preg_replace('/<img[^>]+\>/i', "", $text);
		if ($limit==0) {//no limit
			$allowed_tags 		= '<b><i><a><small><h1><h2><h3><h4><h5><h6><sup><sub><em><strong><u><br>';
			$text 				= strip_tags( $text, $allowed_tags );
			$text 				= $text;	
		} else {
			if ($type==1) {//character lmit
				//$text 			= JFilterOutput::cleanText($text);
				$sep  			= (strlen($text)>$limit) ? '...' : '';
				$text 			= utf8_substr($text,0,$limit) . $sep;		
			} else {//word limit
				//$text 			= JFilterOutput::cleanText($text);
				$text 			= explode(' ',$text);
				$sep 			= (count($text)>$limit) ? '...' : '';
				$text			= implode(' ', array_slice($text,0,$limit)) . $sep;		
			}		
		}
		return $text;
	}

}