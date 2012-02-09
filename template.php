<?php
// $Id: template.php

//	@param $vars
//	  A sequential array of variables to pass to the theme template.
//	@param $hook
//	  The name of the theme function being called ("page" in this case.)

function oho_responsive_preprocess_page(&$vars, $hook) {
  global $theme;

  // Adding Viewport Metatag to $vars['head']
  drupal_set_html_head("<meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1'>");
  $vars['head'] = drupal_get_html_head();

  
  // Basic Grid 12 Column Grid
  if (!empty($vars['left']) && empty($vars['right'])) {
    $vars['sb_first_grid'] = 'four';
    $vars['content_grid'] = 'eight';
  }
  if (empty($vars['left']) && !empty($vars['right'])) {
    $vars['content_grid'] =   'eight';
    $vars['sb_last_grid'] = 'four';
  } 
  if (!empty($vars['left']) && !empty($vars['right'])) {
    $vars['sb_first_grid'] = 'three';
    $vars['content_grid'] = 'six';
    $vars['sb_last_grid'] = 'three';
  }
  if (empty($vars['left']) && empty($vars['right'])) {
    $vars['content_grid'] = 'twelve';
  }

  /*
   * Solve 31 CSS files limit in Internet Explorer
   * by converting link tags into @imports surrounded by style tags.
   * Only 15 @imports are included in each style tag.
   * This function will only run when there are over 29 stylesheets loaded.
   *
   * Notes: This problem can be solved by available modules but modules have the risk
   * of being overriden by other modules later in the load process. The theme layer
   * will one of the last things to have access to and alter $vars['styles']
   * 
   *
   * Original code - http://drupal.org/node/228818#comment-2609368
   * More info on IE limitation - http://support.microsoft.com/kb/262161
   */
   
   //Only run if Optimize CSS files is not on in /admin/settings/performance
   $preprocess_css = variable_get('preprocess_css', 0, 0);
   if (!$preprocess_css) {
  	 
  	 //Get the total number of stylesheets, including those included with Conditional Styles Module
  	 $stylesheet_count = substr_count($vars['styles'], '<link');
  	 if (module_exists('conditional_styles')) {
  	 	$stylesheet_count += substr_count($vars['conditional_styles'], 0);
  	 }
  	 
  	 //Only run this function if there are over 29
  	 if ($stylesheet_count > 29) { 
  	   $styles = '';
  	   foreach ($vars['css'] as $media => $types) {
  	     $import = '';
  	     $counter = 0;
  	     foreach ($types as $files) {
  	       foreach ($files as $css => $preprocess) {
  	         $import .= '@import "'. base_path() . $css .'";'."\n";
  	         $counter++;
  	         if ($counter == 15) {
  	           $styles .= "\n".'<style type="text/css" media="'. $media .'">'."\n". $import .'</style>';
  	           $import = '';
  	           $counter = 0;
  	         }
  	       }
  	     }
  	     if ($import) {
  	       $styles .= "\n".'<style type="text/css" media="'. $media .'">'."\n". $import .'</style>' . "\n";
  	     }
  	   }
  	   if ($styles) {
  	     //Adding styles configured with the Conditional Styles module
  	     $vars['styles'] = $styles . $vars['conditional_styles'];
  	   }
     }
   }
   
   //Template suggestions based on path alias
   if (module_exists('path')) {
     $alias = drupal_get_path_alias(str_replace('/edit','',$_GET['q']));
     if ($alias != $_GET['q']) {
       $template_filename = 'page';
       foreach (explode('/', $alias) as $path_part) {
         $template_filename = $template_filename . '-' . $path_part;
         $vars['template_files'][] = $template_filename;
       }
     }
   }

  // Don't display empty help from node_help().
  if ($vars['help'] == "<div class=\"help\"><p></p>\n</div>") {
    $vars['help'] = '';
  }

  // from ZEN // Override or insert PHPTemplate variables into the page templates.
  //	
  //	This function creates the body classes that are relative to each page
  // Classes for body element. Allows advanced theming based on context
  // (home page, node of certain type, etc.)
  
  $body_classes = array($vars['body_classes']);
  if (user_access('administer blocks')) {
    $body_classes[] = 'admin';
  }
  if (!$vars['is_front']) {
    // Add unique classes for each page and website section
    $path = drupal_get_path_alias($_GET['q']);
    list($section, ) = explode('/', $path, 2);
    $body_classes[] = oho_responsive_id_safe('page-'. $path);
    $body_classes[] = oho_responsive_id_safe('section-'. $section);

    //OHO - Giving the page access to the current section and path
    $vars['section'] = $section;
    $vars['path'] = $path;

    if (arg(0) == 'node') {
      if (arg(1) == 'add') {
        if ($section == 'node') {
          array_pop($body_classes); // Remove 'section-node'
        }
        $body_classes[] = 'section-node-add'; // Add 'section-node-add'
      }
      elseif (is_numeric(arg(1)) && (arg(2) == 'edit' || arg(2) == 'delete')) {
        if ($section == 'node') {
          array_pop($body_classes); // Remove 'section-node'
        }
        $body_classes[] = 'section-node-'. arg(2); // Add 'section-node-edit' or 'section-node-delete'
      }
    }
  }
  $vars['body_classes'] = implode(' ', $body_classes); // Concatenate with spaces
}

//
//	from ZEN // Override or insert PHPTemplate variables into the node templates.
//	
//	 This function creates the NODES classes, like 'node-unpublished' for nodes
//	 that are not published, or 'node-mine' for node posted by the connected user...
//	
//	@param $vars
//	  A sequential array of variables to pass to the theme template.
//	@param $hook
//	  The name of the theme function being called ("node" in this case.)
//

function oho_responsive_preprocess_node(&$vars, $hook) {
  global $user;

  // Special classes for nodes
  $node_classes = array();
  if ($vars['sticky']) {
    $node_classes[] = 'sticky';
  }
  if (!$vars['node']->status) {
    $node_classes[] = 'node-unpublished';
    $vars['unpublished'] = TRUE;
  }
  else {
    $vars['unpublished'] = FALSE;
  }
  if ($vars['node']->uid && $vars['node']->uid == $user->uid) {
    // Node is authored by current user
    $node_classes[] = 'node-mine';
  }
  if ($vars['teaser']) {
    // Node is displayed as teaser
    $node_classes[] = 'node-teaser';
  }
  // Class for node type: "node-type-page", "node-type-story", "node-type-my-custom-type", etc.
  $node_classes[] = 'node-type-'. $vars['node']->type;
  $vars['node_classes'] = implode(' ', $node_classes); // Concatenate with spaces
}

//
// from ZEN // Override or insert PHPTemplate variables into the block templates.
//
//	This function create the EDIT LINKS for blocks and menus blocks.
//	When overing a block (except in IE6), some links appear to edit
//	or configure the block. You can then edit the block, and once you are
//	done, brought back to the first page.
//
// @param $vars
//   A sequential array of variables to pass to the theme template.
// @param $hook
//   The name of the theme function being called ("block" in this case.)
// 

function oho_responsive_preprocess_block(&$vars, $hook) {
  $block = $vars['block'];
  //If the block class module is active, generate it's classes
  if(function_exists('block_class')) {
    $vars['bc_classes'] = block_class($block);
  }
  //Manually placed blocks have no region
  if(!isset($block->region)) {
    $block->region = '';
  }
}


//
//  Create some custom classes for comments
//

function comment_classes($comment) {
  $node = node_load($comment->nid);
  global $user;
 
  $output .= ($comment->new) ? ' comment-new' : ''; 
  $output .=  ' '. $status .' '; 
  if ($node->name == $comment->name) {	
    $output .= 'node-author';
  }
  if ($user->name == $comment->name) {	
    $output .=  ' mine';
  }
  return $output;
}


// 	
// 	Customize the PRIMARY and SECONDARY LINKS, to allow the admin tabs to work on all browsers
// 	An implementation of theme_menu_item_link()
// 	
// 	@param $link
// 	  array The menu item to render.
// 	@return
// 	  string The rendered menu item.
// 	

function oho_responsive_menu_item_link($link) {
  if (empty($link['options'])) {
    $link['options'] = array();
  }
   
  //Unique IDs for menu items
  if (array_key_exists('mlid', $link)) {
   $css_id = 'menu-' . $link['mlid'];
  }
  
  //Add html ID's as one of the attributes to be added to links
  if (isset($css_id)) {
    $link['options']['attributes']['id'] = $css_id;
  }
 
 
  // If an item is a LOCAL TASK, render it as a tab
  if ($link['type'] & MENU_IS_LOCAL_TASK) {
    $link['title'] = '<span class="tab">'. check_plain($link['title']) .'</span>';
    $link['options']['html'] = TRUE;
  }

  if (empty($link['type'])) {
    $true = TRUE;
  }

  /////////////////////////////////////////
  //  This snippet allows you to make a menu item not have a link by linking it to node/3 (a dummy page)
  //  The node number can vary as long as it's a dummy page and all items that you do not want to be links go to it.
  //  Remove the comments below to enable this feature.
  
  //  if ($link['type'] && $link['href'] == 'node/2') {
  //    return '<span id=". $css_id ." class="nolink">'.check_plain($link['title']).'</span>';
  //  }

  //Return the individual link item using l()
  return l($link['title'], $link['href'], $link['options']);
}

/**
 * Duplicate of theme_menu_local_tasks() but adds clear-block to tabs.
 */
function oho_responsive_menu_local_tasks() {
  $output = '';

  if ($primary = menu_primary_local_tasks()) {
    $output .= "<ul class=\"tabs primary clear-block\">\n". $primary ."</ul>\n";
  }
  if ($secondary = menu_secondary_local_tasks()) {
    $output .= "<ul class=\"tabs secondary clear-block\">\n". $secondary ."</ul>\n";
  }

  return $output;
}

//	
//	Add custom classes to menu item
//	
	
function oho_responsive_menu_item($link, $has_children, $menu = '', $in_active_trail = FALSE, $extra_class = NULL) {
  $class = ($menu ? 'expanded' : ($has_children ? 'collapsed' : 'leaf'));
  if (!empty($extra_class)) {
    $class .= ' '. $extra_class;
  }
  if ($in_active_trail) {
    $class .= ' active-trail';
  }
  #New line added to get unique classes for each menu item
  $css_class = 'menu-item-' . oho_responsive_id_safe(str_replace(' ', '_', strip_tags($link)));
  return '<li class="'. $class . ' ' . $css_class . '">' . $link . $menu ."</li>\n";
}


//	
//	Converts a string to a suitable html ID attribute.
//	
//	 http://www.w3.org/TR/html4/struct/global.html#h-7.5.2 specifies what makes a
//	 valid ID attribute in HTML. This function:
//	
//	- Ensure an ID starts with an alpha character by optionally adding an 'n'.
//	- Replaces any character except A-Z, numbers, and underscores with dashes.
//	- Converts entire string to lowercase.
//	
//	@param $string
//	  The string
//	@return
//	  The converted string
//	


function oho_responsive_id_safe($string) {
  // Replace with dashes anything that isn't A-Z, numbers, dashes, or underscores.
  $string = strtolower(preg_replace('/[^a-zA-Z0-9_-]+/', '-', $string));
  // If the first character is not a-z, add 'n' in front.
  if (!ctype_lower($string{0})) { // Don't use ctype_alpha since its locale aware.
    $string = 'id'. $string;
  }
  return $string;
}

//
// Return a themed breadcrumb trail.
//	Alow you to customize the breadcrumb markup
//

function oho_responsive_breadcrumb($breadcrumb) {
  if (!empty($breadcrumb)) {
    return '<div class="breadcrumb">'. implode(' » ', $breadcrumb) .'</div>';
  }
}

// Get node terms from a specific vocabulary (http://drupal.org/node/133223#comment-1353692)
// OHO has added the ability to separate terms by comma
function oho_responsive_taxonomy_links($node, $vid, $style = 'default') {
  if (count($node->taxonomy)){
    $tags = array();
    foreach ($node->taxonomy as $term) {
	   if ($term->vid == $vid){
      $tags[] = array('title' => $term->name, 'href' => taxonomy_term_path($term), 'attributes' => array('rel' => 'tag'));
	   }
		}
		
    if ($tags){
			//Supports two styles for taxonomy term output. Default is the default list and comma is comma delimited 
			if ($style == 'default') {
				return theme_links($tags, array('class'=>'links inline'));
			}
			elseif ($style == 'comma') {
				foreach ($tags as $link) {
					$output[] = l($link['title'], $link['href'], $link);
				}
				return implode(', ' , $output);
			}
    }
  }
}
