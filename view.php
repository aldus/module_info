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
if ((__FILE__ != $_SERVER['SCRIPT_FILENAME']) === false) {
	die('<head><title>Access denied</title></head><body><h2 style="color:red;margin:3em auto;text-align:center;">Cannot access this file directly</h2></body></html>');
}

/**
 *	Load Language file
 */
$lang = (dirname(__FILE__))."/languages/". LANGUAGE .".php";
require_once ( !file_exists($lang) ? (dirname(__FILE__))."/languages/EN.php" : $lang );

global $parser;
global $loader;
	
if (!isset($parser))
{
	require_once( LEPTON_PATH."/modules/lib_twig/library.php" );
}
$loader->prependPath( dirname(__FILE__)."/templates/", "modul_info" );

/***
 *	Secure-Hash for the download-counter
 *
 */
$hash = sha1( microtime() );
if(isset($_SESSION['mih_'])) unset ($_SESSION['mih_'] );
$_SESSION['mih_'] = $hash;
$h_name = substr($hash, 0, 16);
$h_value = substr($hash, -16);

echo $parser->render(
	"@modul_info/counter.lte",
	array(
		'LEPTON_URL' => LEPTON_URL,
		'h_name' => $h_name,
		'h_value' => $h_value
	)
);

/** ****************
 *	Getting the data
 */
$mod_info = array();
$database->execute_query(
	"SELECT * FROM `".TABLE_PREFIX."mod_module_info` WHERE `section_id` =".$section_id,
	true,
	$mod_info,
	false
);

/**	***********
 *	Rating code
 *
 */

/**
 *	Secure hash for rating
 */
$hash = sha1( md5(microtime().TIME().$_SERVER['HTTP_USER_AGENT']) );
if(isset($_SESSION['miv_'])) unset ($_SESSION['miv_'] );
$_SESSION['miv_'] = $hash;
$h_name = substr($hash, 0, 16);
$h_value = substr($hash, -16);

$rating_js = file_get_contents( __dir__."/rating/rating.js");
$rating_js = str_replace("{{ LEPTON_URL }}", LEPTON_URL, $rating_js);

/**
 *	Average
 */
if ($mod_info['rating']=="") $mod_info['rating']="0,0,0,0,0,0";
$temp = explode(",", $mod_info['rating']);
$n = 0;
if (count($temp) < 6) {
	for($i = count($temp); $i<6; $i++) $temp[] = 0;
}
for($i=1;$i<=5;$i++) $n += $temp[$i]*$i;

$av = ($mod_info['votes'] == 0) 
	? 0
	: ($n / ($mod_info['votes']*5)) * 100
	;

$rating_html = $parser->render(
	"@modul_info/rating.lte",
	array(
		'LEPTON_URL' => LEPTON_URL,
		'votes'	=> $mod_info['votes']. " votes",
		'user_ip' => "127.0.0.1",
		'average'	=> $av,
		'rating_js'	=> $rating_js,
		'section_id' => $section_id,
		'h_name'	=> $h_name,
		'h_value' 	=> $h_value
	)
);

/**	*************************************************
 *	Start collecting the information for the template
 */
$all_info = array();

//	Name
$all_info[] = array(
	'label'	=> $MOD_MODULINFO['MODUL'],
	'value'	=> $mod_info['modul']
);

//	Author
$all_info[] = array(
	'label'	=> $MOD_MODULINFO['AUTHOR'],
	'value'	=> $mod_info['author']
);

//	Type
$all_info[] = array(
	'label'	=> $MOD_MODULINFO['TYPE'],
	'value'	=> $mod_info['type']
);

//	Version
$all_info[] = array(
	'label'	=> $MOD_MODULINFO['VERSION'],
	'value'	=> $mod_info['version']
);

//	State
$all_info[] = array(
	'label'	=> $MOD_MODULINFO['STATE'],
	'value'	=> $mod_info['state']
);

//	License
$all_info[] = array(
	'label'	=> $MOD_MODULINFO['LICENSE'],
	'value'	=> $mod_info['license']
);

//	Guid
if($mod_info['guid'] != "") {
	$all_info[] = array(
		'label'	=> $MOD_MODULINFO['GUID'],
		'value'	=> $mod_info['guid']
	);
}

//	Platform
$all_info[] = array(
	'label'	=> $MOD_MODULINFO['PLATFORM'],
	'value'	=> $mod_info['platform']
);

//	Last info - last change
$all_info[] = array(
	'label'	=> $MOD_MODULINFO['LAST_INFO'],
	'value'	=> $mod_info['last_info']
);

//	Rating
$all_info[] = array(
	'label'	=> $MOD_MODULINFO['RATING'],
	'value'	=> $rating_html
);


//	See also ...
if ($mod_info['see_also'] != "") {
	$ids = explode(",", $mod_info['see_also']);
	$links = array();
	foreach($ids as &$id) {
		$page_info = array();
		$database->execute_query(
			"SELECT `page_title`,`link` FROM `".TABLE_PREFIX."pages` WHERE `page_id`= ".$id,
			true,
			$page_info,
			false
		);
		
		if (count($page_info) > 0) {
			$link = LEPTON_URL.PAGES_DIRECTORY.$page_info['link'].".php";
			$links[] = "<a href='".$link."' >".$page_info['page_title']."</a>";
		}
	}
	
	$all_info[] = array(
		'label'	=> $MOD_MODULINFO['SEE_ALSO'],
		'value'	=> implode(", ", $links)
	);
}

//	requires
if($mod_info['requires'] != "") {
	$all_info[] = array(
		'label'	=> $MOD_MODULINFO['REQUIRES'],
		'value'	=> $mod_info['requires']
	);
}

//	Download - include counter call
if ($mod_info['download'] != "") {
	$temp = explode("/", $mod_info['download']);
	$filename = array_pop($temp);
	$link = "<a href='".$mod_info['download']."' target='_blank' onclick='do_count(".$mod_info['section_id'].");'>".$filename."</a>";
	$all_info[] = array(
		'label'	=> $MOD_MODULINFO['DOWNLOAD'],
		'value'	=> $link
	);
}

//	Description
if($mod_info['description'] != "") {
	$all_info[] = array(
		'label'	=> $MOD_MODULINFO['DESCRIPTION'],
		'value'	=> $mod_info['description']
	);
}

//	Info
if($mod_info['info'] != "") {
	$all_info[] = array(
		'label'	=> $MOD_MODULINFO['INFO'],
		'value'	=> $mod_info['info']
	);
}

//	Screen-image - Keep in mind that the image-infos are store in another table!
$images = array();
$database->execute_query(
	"SELECT `src`,`title`,`alt` FROM `".TABLE_PREFIX."mod_module_info_images` WHERE `section_id`='".$section_id."' AND `active`='1' ORDER BY `position`",
	true,
	$images
);

$num_of_images = count($images);
if($num_of_images > 0) {
	
	$class_name = $num_of_images > 1 ? "thumb" : "single";
	
	$link = "";
	foreach($images as &$ref) {
		/**
		 *	Build the link - include the lightbox-call
		 */
		$link .= "<a href='".$ref['src']."' rel='lightbox[mod_info]' title='".$ref['title']."' ><img src='".$ref['src']."' class='".$class_name."' alt='".$ref['alt']."' /></a>";
	}
	
	$all_info[] = array(
		'label'	=> $MOD_MODULINFO['SCREEN'],
		'value'	=> $link
	);
}

/** ***********************************
 *	Prepare all values for the template
 */
$output_values = array(
	'all_info' => $all_info
);

/** *****************************
 *	At last we echo the hole page
 */
echo $parser->render(
	"@modul_info/view.lte",
	$output_values
);

?>