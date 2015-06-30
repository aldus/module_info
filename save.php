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

error_reporting(-1);
ini_set('display_errors', 1);

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
 *	Tells script to update when this page was last updated
 */
$update_when_modified = true; 
require(WB_PATH.'/modules/admin.php');

/**
echo "<pre>";
print_r($_POST);
die("</pre>");
*/

/**
 *	Some string-cleanings
 *
 */
function clean_up_str ($aStr) {
	
	$chars = array(
		"<br />"		=> "",
		"\n"			=> "<br />",
#		"\r"			=> "",
		"</li><br />"	=> "</li>",
		 "<ul><br />"	=> "<ul>",
		 "<br /><li>"	=> "<li>",
#		 "</li><br /></ul>" => "</li></ul>",
		 "<?php"		=> "&lt;?php", 
		 "?>"			=> "?&gt;"
	);
	return addslashes( str_replace(
		array_keys($chars),
		array_values($chars),
		$aStr
	));
}

$table = TABLE_PREFIX ."mod_module_info";

/**	*****************
 *	AMASP File-Upload
 *
 */
if (array_key_exists("amasp_upload", $_FILES) ) {
	
	if ( $_FILES['amasp_upload']['name'] != "") {
		/**
		 *	The AMASP upload folder
		 */
		$amasp_dir = WB_PATH."/media/lepton_uploads/";
		
		if (!is_dir($amasp_dir) ) mkdir($amasp_dir, 0775);
		
		/**
		 *	no spaces inside the filename
		 *
		 */
		$now_file_name = str_replace( array("%20", " "), array("_", "_"), $_FILES['amasp_upload']['name']);
		
		/**
		 *	If the file already exists we are not tor try to unlink it.
		 *	
		 *
		 */
		if (file_exists($amasp_dir.$now_file_name) ) unlink($amasp_dir.$now_file_name);
		
		/**
		 *	Try to move the file from the temp folder to the destination in amasp
		 *
		 */
		$result = move_uploaded_file($_FILES["amasp_upload"]['tmp_name'], $amasp_dir.$now_file_name);
		
		/**
		 *	If this failed we have to do something ...
		 *
		 */
		if ( $_FILES['amasp_upload']['error'] > 0 ) {
			
			switch ($_FILES['amasp_upload']['error'] ) 
			{  
			 	case 1:
					$detail = 'Error: <p> The file is bigger than this PHP installation allows.</p>';
           			break;
				case 2:
					$detail = 'Error: <p> The file is bigger than this form allows.</p>';
					break;
				case 3:
					$detail = 'Error: <p> Only part of the file was uploaded.</p>';
					break;
				case 4:
					$detail = 'Error: <p> No file was uploaded.</p>';
					break;
				default:
					$detail = "Error: <b>unknown</b>!";
		 	}
		 	
		 	$more_details = "\n<br />";
		 	foreach( $_FILES['amasp_upload'] as $k => $v) $more_details .= "<font color='#990000'>".$k."</font> = ".$v."<br />\n";
		 	$more_details .= "\n<br /><b>_SERVER:</b><br />\n";
		 	foreach($_SERVER as $k => $v) $more_details .= "<font color='#990000'>".$k."</font> = ".$v."<br />\n";
		 	
			die ("Failed to upload file: ".$_FILES['amasp_upload']['name']."<br />".$detail.$more_details);
		}
	
		/**
		 *	Setting the file-permissions
		 *	
		 *	M.f.i.	2008-10-20
		 *
		 */
		chmod($amasp_dir.$now_file_name, 0775);
		 
		/**
		 *	We have to rebuild the complete upload/download path
		 *	and store it inside the 'download' field
		 *
		 */
		$path = WB_URL."/media/lepton_uploads/".$now_file_name;
		$_POST['download'] = $path;
		
		/**
		 *	E-Mail notification to the admin(-s)
		 */
		$from	 = "drp@cms-lab.com";
		$to		 = "rp@cms-lab.com";
		$subject = "LEPTON fileupload";
		$message = $_POST['author']." (".$_POST['wb_name'].") has upload the file: ".$now_file_name."\n for the modul '".$_POST['modul']."' to the LEPTON server \nat ".date("Y-m-d H:i:s", TIME()).".";
		
		/**
		 *	Getting the pagelink ...
		 */
		$page_info = $database->query("SELECT link from ".TABLE_PREFIX."pages where page_id='".$_POST['page_id']."'");
		
		if ( $database->is_error() ) $admin->print_error($database->get_error()." [2]", $js_back);
		
		$page_data = $page_info->fetchRow();
		$page_link = WB_URL.PAGES_DIRECTORY.$page_data['link'].".php";		
		$message .= "\nPage: ".$page_link."\n";
		
		mail("drp@cms-lab.com", "LEPTON Server", $message,"-f drp@cms-lab.com"); /** Kopie an Dietrich **/
		mail("rp@cms-lab.com", $subject, $message,"-f drp@cms-lab.com"); /** Kopie an RP **/
		
	}
}

/**	**********************
 *	Update the image order
 */
if (isset($_POST['image_order_list'])) {
	if ($_POST['image_order_list'] != "") {
		
		$result_array = array();
    	preg_match_all( '/src="([^"]*)"/i', $_POST['image_order_list'], $result_array ) ;
    	// alle ergebnisse - links - in [1] als lineare liste!
    	// die(print_r( $result_array[1] ) );
    	$c = 1;
    	foreach($result_array[1] as &$link) {
    		$fields = array(
    			'position' => ($c++ * 10)
    		);
    		$database->build_and_execute(
    			'update',
    			$table."_images",
    			$fields,
    			"`src`='".$link."'"
    		);
    	}
	}
}
//	End of image order

/**	*******************
 *	AMASP Screen-Upload
 *
 */
if (array_key_exists("screen", $_FILES) ) {
	
	
	$n = count($_FILES['screen']['name']);
	
	//	#1
	$offset = $database->get_one("SELECT `position` FROM `".$table."_images` WHERE `section_id`='".$_POST['section_id']."' ORDER BY `position` DESC");
	$offset++;
	
	for($i=0;$i<$n;$i++) {
	
	if ( $_FILES['screen']['name'][$i] != "") {
		/**
		 *	The LEPTON screen upload folder
		 */
		$amasp_dir = WB_PATH."/media/lepton_screen_uploads/".$_POST['section_id']."/";
		
		if (!is_dir(WB_PATH."/media/lepton_screen_uploads/")) mkdir( WB_PATH."/media/lepton_screen_uploads/", 0775);
		if (!is_dir($amasp_dir) ) mkdir($amasp_dir, 0775);
		
		/**
		 *	no spaces inside the filename
		 *
		 */
		$now_file_name = str_replace( array("%20", " "), array("_", "_"), $_FILES['screen']['name'][$i]);
		
		/**
		 *	if the file already exists we try to unlink it ...
		 *
		 */
		if (file_exists($amasp_dir.$now_file_name) ) unlink($amasp_dir.$now_file_name);
		
		/**
		 *	Try to move the file from the temp folder to the destination in amasp
		 *
		 */
		$result = move_uploaded_file($_FILES["screen"]['tmp_name'][$i], $amasp_dir.$now_file_name);
		
		/**
		 *	If this failed we have to do something ...
		 *
		 */
		if ( $_FILES['screen']['error'][$i] > 0 ) {
			
			switch ($_FILES['screen']['error'][$i] ) 
			{  
			 	case 1:
					$detail = 'Error: <p> The file is bigger than this PHP installation allows.</p>';
           			break;
				case 2:
					$detail = 'Error: <p> The file is bigger than this form allows.</p>';
					break;
				case 3:
					$detail = 'Error: <p> Only part of the file was uploaded.</p>';
					break;
				case 4:
					$detail = 'Error: <p> No file was uploaded.</p>';
					break;
				default:
					$detail = "Error: <b>unknown</b>!";
		 	}
		 	
		 	$more_details = "\n<br />";
		 	foreach( $_FILES['screen'][$i] as $k => $v) $more_details .= "<font color='#990000'>".$k."</font> = ".$v."<br />\n";
		 	$more_details .= "\n<br /><b>_SERVER:</b><br />\n";
		 	foreach($_SERVER as $k => $v) $more_details .= "<font color='#990000'>".$k."</font> = ".$v."<br />\n";
		 	
			die ("Failed to upload file: ".$_FILES['screen']['name'][$i]."<br />".$detail.$more_details);
		}
		
		/**
		 *	Setting the permissions ... 
		 *	M.f.i.
		 */
		chmod( $amasp_dir.$now_file_name, 0775 );
		
		/**
		 *	We have to rebuild the complete upload/download path
		 *	and store it inside the 'download' field
		 *
		 */
		$path = WB_URL."/media/lepton_screen_uploads/".$_POST['section_id']."/".$now_file_name;
		$_POST['screen'] = ""; #$path;
/**
 *	Direkt die DB aktualisieren
 */
$a_temp = explode(".", $now_file_name);
array_pop($a_temp);
$alt_name = implode(".", $a_temp);
	$fields = array(
		'src'	=> $path,
		'section_id'	=> $_POST['section_id'],
		'page_id'	=> $_POST['page_id'],
		'title'	=> $alt_name,
		'alt'	=> $now_file_name,
		'active'	=> 1,
		'position'	=> $offset + ($i+1)*10 ## Notice: see #1 (offset)
	);
	$database->build_and_execute(
		'insert',
		$table."_images",
		$fields
	);
		
		/**
		 *	E-Mail notification to the admin(-s)
		 */
		$from	 = "drp@cms-lab.com";
		$to		 = "rp@cms-lab.com";
		$subject = "LEPTON fileupload";
		$message = $_POST['author']." (".$_POST['wb_name'].") has upload the screenfile: ".$now_file_name."\n for the modul '".$_POST['modul']."' to the LEPTON server \nat ".date("Y-m-d H:i:s", TIME()).".";
		
		/**
		 *	Getting the pagelink ...
		 */
		$page_info = $database->query("SELECT link from ".TABLE_PREFIX."pages where page_id='".$_POST['page_id']."'");
		
		if ( $database->is_error() ) $admin->print_error($database->get_error()." [2]", $js_back);
		
		$page_data = $page_info->fetchRow();
		$page_link = WB_URL.PAGES_DIRECTORY.$page_data['link'].".php";
		$message .= "\nPage: ".$page_link."\n";
		
		mail("drp@cms-lab.com", "LEPTON Server", $message,"-f drp@cms-lab.com"); /** Kopie an Dietrich **/
		mail("rp@cms-lab.com", $subject, $message,"-f drp@cms-lab.com"); /** Kopie an RP **/
	}
	}
}

/**
 *	Delete image(-s)
 *
 */
if (array_key_exists ("delete", $_POST) ) {
	foreach($_POST['delete'] as &$id) {
		$filename = $database->get_one(
			"SELECT `src` FROM `".$table."_images` WHERE `id`=".$id
		);
		
		$filename = str_replace(
			LEPTON_URL,
			LEPTON_PATH,
			$filename
		);
		
		if (file_exists($filename)) unlink($filename);
		
		$database->execute_query(
			"DELETE FROM `".$table."_images` WHERE `id`=".$id
		);
	}
}

/**
 *	Update images title, alt and active
 *
 */
if (isset($_POST['title'])) {
	foreach($_POST['title'] as $id=>$value) {
		$temp_fields = array(
			'title' => $value,
			'alt'	=> $_POST['alt'][$id],
			'active' => $_POST['active'][$id]
		);
		
		$database->build_and_execute(
			"update",
			$table."_images",
			$temp_fields,
			"`id`='".$id."'"
		);
	}
}

if (!isset($_POST['screen'])) $_POST['screen'] = "";
if (!isset($_POST['download'])) $_POST['download'] = "";
if (!isset($_POST['guid'])) $_POST['guid'] = "";

$request = array ('modul', 'type', 'author', 'wb_name', 'contact', 'version', 'state', 'license', 'see_also', 'download', 'screen', 'wb_thread', 'web_link', 'description', 'info', 'guid', 'platform', 'requires');

$query = "update ".$table." set ";
foreach ($request as $term) $query .= $term."='". clean_up_str($_POST[$term] ) ."', ";

$query = substr ($query, 0, -2).", last_info='".date("Y-m-d", TIME() )."' where section_id=".$_POST['section_id'];

$database->query ( $query );

/**
*	Check if there is a database error, otherwise say successful
*/
if ( $database->is_error() ) {
	$admin->print_error($database->get_error(), $js_back);
} else {
	$admin->print_success($MESSAGE['PAGES']['SAVED'], ADMIN_URL.'/pages/modify.php?page_id='.$page_id);
}

/**
*	Print admin footer
*/
$admin->print_footer();

?>