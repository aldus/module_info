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

if(!isset($_SESSION['mih_'])) die();
$h = $_SESSION['mih_'];
$n = substr($h, 0, 16);
$v = substr($h, -16);
if (!isset($_POST[ $n ])) die();
if ($_POST[ $n ] != $v) die();

unset( $_SESSION['mih_'] );

if(isset($_POST['job'])) {
	switch($_POST['job']) {
		case 'dl':
			$id = intval($_POST['id']);
			$database->execute_query(
				"UPDATE `".TABLE_PREFIX."mod_module_info` set `counter`=`counter`+1 WHERE `section_id`='".$id."'"
			);
			break;
	}
}
?>