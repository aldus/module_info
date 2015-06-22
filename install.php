<?php

/**
 *
 * @category        page
 * @package         LEPTON-CMS - Modules: Module Information
 * @author          Dietrich Roland Pehlke
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        LEPTON-CMS 2.0.0
 * @requirements    PHP 5.4 and higher
 * @version         0.7.6
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

/**
 *	Looking for old installations!
 */
$old_name = "modul_info";
if (file_exists( LEPTON_PATH."/modules/".$old_name)) {
	require_once( __DIR__."/upgrade.php");
	return true; // exit
}

$table = TABLE_PREFIX ."mod_module_info";

$queries = array();

$queries[] = "DROP TABLE IF EXISTS ".$table;

$query  = "CREATE TABLE ".$table." (";
$query .= "section_id	INT(11) NOT NULL PRIMARY KEY,";
$query .= "page_id		INT(11) NOT NULL,";
$query .= "modul		varchar(255) default 'no_name',";
$query .= "type			varchar(255) default 'page',";
$query .= "license		varchar(255) default 'GPL', ";
$query .= "wb_name		varchar(255) default 'no_name',";
$query .= "author		varchar(255) default 'no_name',";
$query .= "contact		varchar(255) default '' ,";
$query .= "version		varchar(255) default '0.1.0', ";
$query .= "state		varchar(255) default 'dev', ";
$query .= "see_also		varchar(255) default '' ,";
$query .= "last_info	date,";
$query .= "download		varchar(255),";
$query .= "wb_thread	varchar(255),";
$query .= "web_link		varchar(255),";
$query .= "screen		varchar(255),";
$query .= "description	text ,";
$query .= "guid			VARCHAR( 36 ) NOT NULL ,";
$query .= "platform		VARCHAR( 64 ) NOT NULL default '2.7',";
$query .= "`group`		VARCHAR(255)  NOT NULL ,";
$query .= "requires		VARCHAR(255)  NOT NULL ,";
$query .= "counter		INT(11) NOT NULL DEFAULT '0',";	// !
$query .= "rating		VARCHAR(255)  NOT NULL  DEFAULT '',";	// !
$query .= "votes		INT(11) NOT NULL DEFAULT '0',"; // !
$query .= "info			text  )";

$queries[] = $query;

$queries[] = "DROP TABLE IF EXISTS ".$table."_images";

$query = "CREATE TABLE `".$table."_images` (
  `id`		int(11)		unsigned NOT NULL AUTO_INCREMENT,
  `active`	int(1)		unsigned DEFAULT '0',
  `page_id`	int(11)		unsigned DEFAULT NULL,
  `section_id` int(11)	unsigned DEFAULT NULL,
  `position` int(10) unsigned NOT NULL DEFAULT '1',
  `src`		tinytext,
  `title`	varchar(255) DEFAULT NULL,
  `alt`		varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
);";

$queries[] = $query;

foreach($queries as &$q) {

	$database->execute_query($q);

}

?>