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

/**
 *	Drop tables.
 */
$table = TABLE_PREFIX ."mod_module_info";
$database->query("DROP TABLE ".$table );
$database->query("DROP TABLE ".$table."_images" );

/**
 *	Remove folders and images inside the media-directory.
 */
if (!function_exists("rm_full_dir")) {
	require_once( LEPTON_PATH."/framework/functions/function.rm_full_dir.php");
}

$filename = LEPTON_PATH.MEDIA_DIRECTORY."/lepton_uploads/";
if (file_exists($filename)) rm_full_dir( $filename );

$filename = LEPTON_PATH.MEDIA_DIRECTORY."/lepton_screen_uploads/";
if (file_exists($filename)) rm_full_dir( $filename );

?>