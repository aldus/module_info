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

$ignore = TABLE_PREFIX;
$all_tables = $database->list_tables( $ignore );

if (in_array("mod_modul_info", $all_tables)) {
	$database->execute_query(
		"RENAME TABLE `".TABLE_PREFIX."mod_modul_info` TO `".TABLE_PREFIX."mod_module_info`;"
	);
	$all_tables[] = "mod_module_info";
}

if (in_array("mod_modul_info_images", $all_tables)) {
	$database->execute_query(
		"RENAME TABLE `".TABLE_PREFIX."mod_modul_info_images` TO `".TABLE_PREFIX."mod_module_info_images`;"
	);
	$all_tables[] = "mod_module_info_images";
}

$database->execute_query(
	"UPDATE `".TABLE_PREFIX."sections` SET `module`='module_info' WHERE `module`='modul_info'"
);

$database->execute_query(
	"UPDATE `".TABLE_PREFIX."addons` SET `directory`='module_info' WHERE `directory`='modul_info'"
);

if (!in_array("mod_module_info_images", $all_tables)) {
	$query = "CREATE TABLE `".TABLE_PREFIX."mod_module_info_images` (
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

	$database->execute_query( $query );
}

/**
 *	Check the entries. Copy the field "screen" into "src" of the image-table and leave the _old_ one blank.
 */
$all = array();
$database->execute_query(
	"SELECT `page_id`,`section_id`,`screen` FROM `".TABLE_PREFIX."mod_module_info` WHERE `screen` <> ''",
	true,
	$all
);

foreach($all as &$ref) {
	$fields = array(
		'page_id'	=> $ref['page_id'],
		'section_id' => $ref['section_id'],
		'position'	=> 10,
		'src'	=> $ref['screen'],
		'active'	=> 1,
		'title'		=> "",
		'alt'		=> ""
	);
	
	$database->build_and_execute(
		'insert',
		TABLE_PREFIX."mod_module_info_images",
		$fields
	);
	
	$fields = array(
		'screen'	=> ""
	);
	
	$database->build_and_execute(
		'update',
		TABLE_PREFIX."mod_module_info",
		$fields,
		"`section_id`=".$ref['section_id']
	);
}

/**
 *	Check for all "old" paths/folders
 *
 */

$all = array();
$database->execute_query(
	"SELECT `src`, `section_id`,`id` FROM `".TABLE_PREFIX."mod_module_info_images`",
	true,
	$all
);

$basename = LEPTON_PATH.MEDIA_DIRECTORY."/lepton_screen_uploads/";

foreach( $all as &$item) {
	$link = $basename.$item['section_id']."/";
	if(!is_dir($link)) {
		mkdir($link, 0775);
	}
	$temp = str_replace (LEPTON_URL, LEPTON_PATH, $item['src']);
	$a = explode("/", $temp);
	$filename = array_pop($a);
	if (!file_exists( $link.$filename)) {
		if (file_exists( $basename.$filename )) {
			copy( $basename.$filename, $link.$filename);
			$database->execute_query(
				"UPDATE `".TABLE_PREFIX."mod_module_info_images` set `src`='".(str_replace(LEPTON_PATH, LEPTON_URL, $link.$filename))."' WHERE `id`='".$item['id']."'"
			);
		}
	}
}

//	Do we have to alter the field "rating" to varchar(255)?
$table_info = array();
$database->describe_table(
	TABLE_PREFIX."mod_module_info",
	$table_info
);
foreach($table_info as &$info) {
	if ($info['Field'] == "rating") {
		if ($info['Type'] != "varchar(255)") {
			$database->execute_query(
				"ALTER TABLE `".TABLE_PREFIX."mod_module_info` CHANGE `rating` `rating` VARCHAR(255)  NOT NULL  DEFAULT ''"
			);
		}
	}
}
?>