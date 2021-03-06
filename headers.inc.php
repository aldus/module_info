<?php

/**
 *
 *  @module         Module Information
 *  @version        see info.php of this module
 *  @author         Dietrich Roland Pehlke
 *  @copyright      2009-2014 Dietrich Roland Pehlke
 *  @license        http://www.gnu.org/licenses/gpl.html
 *  @license terms  see info.php of this module
 *  @platform       see info.php of this module
 *
 *
 */

if (defined('LEPTON_PATH')) {	
	include(LEPTON_PATH.'/framework/class.secure.php'); 
} else {
	$oneback = "../";
	$root = $oneback;
	$level = 1;
	while (($level < 10) && (!file_exists($root.'/framework/class.secure.php'))) {
		$root .= $oneback;
		$level += 1;
	}
	if (file_exists($root.'/framework/class.secure.php')) { 
		include($root.'/framework/class.secure.php'); 
	} else {
		trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
	}
}

$mod_headers = array();

/**	**********************************************************************************
 *	Ok - here we go.
 *	This is one of the reasons to use LEPTON 2.x now!
 *	As we're using the SLIMBOX2 for the frontend we are in the _need_ to tell the core
 *	to use a) jQuery Core and b) some js and css files for it.
 *	Fairly simple here:
 *
 */

if ( file_exists( LEPTON_PATH.'/modules/lib_jquery/plugins/Slimbox2' ) ) {
	// prevent to load the header(-s) twice a time if more than one section is used.
	if (!defined("mod_module_info_headers")) {
		define("mod_module_info_headers", true);
		$mod_headers = array(
			'frontend' => array(
				'css' => array(
					array(
						'media'	=> 'screen',
						'file'	=> '/modules/lib_jquery/plugins/Slimbox2/slimbox2.css',
					),
					array(
						'media'	=> 'screen',
						'file'	=> '/modules/module_info/rating/rating.css'
					)
				),
				'jquery' => array(
					array(
						'core'	=> true
					)
				),	
				'js' => array(
					'/modules/lib_jquery/plugins/Slimbox2/slimbox2.js'
				)
			),
			'backend' => array(
				'css' => array(
					array(
						'media'	=> 'screen',
						'file'	=> '/modules/lib_jquery/plugins/Slimbox2/slimbox2.css',
					)
				),	
				'js' => array(
					'/modules/lib_jquery/jquery-ui/external/jquery.idTabs.min.js',
					'/modules/lib_jquery/jquery-ui/external/jquery-insert.js',
					'/modules/lib_jquery/plugins/Slimbox2/slimbox2.js',
					'/modules/module_info/js/jquery.dragsort-0.5.2.min.js',
					'/modules/lib_jquery/jquery-ui/external/jquery.MultiFile.pack.js',
					'/modules/lib_jquery/jquery-ui/external/jquery.MetaData.js'
				)
			)
		);
	}
}

/**
 *	That's all!
 */
?>