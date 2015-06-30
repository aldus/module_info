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
global $lepton_filemanager;
if (!is_object($lepton_filemanager)) require_once( "../../framework/class.lepton.filemanager.php" );

$files_to_register = array(
	'/modules/module_info/save.php',
	'/modules/module_info/headers.inc.php'
);

$lepton_filemanager->register( $files_to_register );

?>