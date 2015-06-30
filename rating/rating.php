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

if(!isset($_SESSION['miv_'])) die();
$h = $_SESSION['miv_'];
$n = substr($h, 0, 16);
$v = substr($h, -16);
if (!isset($_GET[ $n ])) die();
if ($_GET[ $n ] != $v) die();

unset( $_SESSION['miv_'] );

if (!isset($_GET['sec'])) die();
if (!isset($_GET['score'])) die();

$sec = intval($_GET['sec']);
$score = intval($_GET['score']);

$info = array();
$database->execute_query(
	"SELECT `rating`, `votes` FROM `".TABLE_PREFIX."mod_module_info` WHERE `section_id`='".$sec."'",
	true,
	$info,
	false
);

$info['votes']++;

if ($info['rating'] == '') {
	$temp = array(0,0,0,0,0,0);
} else {
	$temp = explode(",", $info['rating']);
}
if (count($temp) < 6) {
	for($i = count($temp); $i<6; $i++) $temp[] = 0;
}
$temp[ $score ]++;

$info['rating'] = implode(",", $temp);

$database->build_and_execute(
	"update",
	TABLE_PREFIX."mod_module_info",
	$info,
	"`section_id`='".$sec."'"
);

echo "Thank you for voting";

?>