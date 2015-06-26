<?php

/**
 *
 * @category        page
 * @package         LEPTON-CMS - Modules: Module Information
 * @author          Dietrich Roland Pehlke
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        LEPTON-CMS 2.0.0
 * @requirements    PHP 5.4 and higher
 * @version         0.7.7
 * @lastmodified    Jun 2015 
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

$table = TABLE_PREFIX ."mod_module_info";

$database->execute_query("DELETE FROM `".$table."` WHERE `section_id` =".$section_id);

$database->execute_query("DELETE FROM `".$table."_images` WHERE `section_id` =".$section_id);

/**
 *	Remove folders and images inside the media-directory.
 */
if (!function_exists("rm_full_dir")) {
	require_once( LEPTON_PATH."/framework/functions/function.rm_full_dir.php");
}

$filename = LEPTON_PATH.MEDIA_DIRECTORY."/lepton_screen_uploads/".$section_id."/";
if (file_exists($filename)) rm_full_dir( $filename );

?>