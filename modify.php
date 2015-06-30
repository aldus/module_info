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

if (!function_exists("restore_string")) {
	function restore_string ($aStr) {
		$lookup = array (
			"<br />"	=> "\n",
			"\\'"		=> "'",
			"\\\""		=> "\""
		);
		$found = array ();
		$replace = array ();
	
		foreach ($lookup as $k=>$v) { $found[]= $k; $replace[]= $v; }
	
		return str_replace($found, $replace, $aStr);
	}
}

$lang = (dirname(__FILE__))."/languages/". LANGUAGE .".php";
require_once ( !file_exists($lang) ? (dirname(__FILE__))."/languages/EN.php" : $lang );

/**	*******************************
 *	Try to get the template-engine.
 */
global $parser, $loader;
if (!isset($parser))
{
	require_once( LEPTON_PATH."/modules/lib_twig/library.php" );
}

$loader->prependPath( dirname(__FILE__)."/templates/backend/", "module_info" );

/**
 *	Here we go
 */
$table = TABLE_PREFIX."mod_module_info";

$sql_result = $database->query("SELECT * FROM `".$table."` WHERE section_id=".$section_id);
$data = $sql_result->fetchRow( MYSQL_ASSOC );

foreach($data as $k=>$v) $data[$k] = restore_string($v);

$state_options = array ('Alpha', 'Beta', 'RC', 'Stable');
$state_select = "<select name='state' class='mod_info' >\n";
foreach($state_options as $i) $state_select .= "<option value='".$i."' ".($i==$data['state'] ? 'selected="selected"':'').">".$i."</option>";
$state_select .= "</select>";

$page_types = array ("page", "template", "admin tool", "snippet", "code", "core replacement", "BE-Theme", "wysiwyg", "library");
$type_select = "<select name='type' class='mod_info' >\n";
foreach($page_types as $i) $type_select .= "<option value=\"".$i."\" ".($i==$data['type'] ? 'selected="selected"' : '' ).">".$i."</option>\n";
$type_select .= "</select>\n";

/**
 *	Image/Screen preview
 */
$screen_preview = "";
$images = array();
$database->execute_query(
	"SELECT `id`,`active`,`src`,`title`,`alt` FROM `".TABLE_PREFIX."mod_module_info_images` WHERE `section_id`='".$section_id."' ORDER BY `position`",
	true,
	$images
);

if(count($images) > 0) {
	$screen_preview = $parser->render(
		"@module_info/images_details.lte",
		array(
			'images' => $images,
			'THEME_URL'	=> THEME_URL,
			'LEPTON_URL' => LEPTON_URL,
			'section_id' => $section_id
		)
	);
}
/**
 *	Platform
 *
 */
$platform_items = array ('LEPTON 1.x', 'LEPTON 2.x', 'LEPTON 1.x/2.x hybrid');
$platform_select = "<select id='platform_select' name='platform' class='mod_info' >\n";
foreach ($platform_items as $temp) $platform_select .= sprintf("<option value='%s' %s>%s</option>\n", $temp,($data['platform']==$temp?'selected':''), $temp);
$platform_select .= "</select>\n";

/**
 *	Parent, Page_ID
 *
 *
 */
$temp_info = $database->query("SELECT `parent`, `page_id` from `".TABLE_PREFIX."pages` where `page_id`='".$page_id."'");
$temp_data = $temp_info->fetchRow();
if ($temp_data['parent'] != 0) {
	$temp_info = $database->query("SELECT `parent`, `menu_title` from `".TABLE_PREFIX."pages` where `page_id`='".$temp_data['page_id']."'");
	
}	

/**
 *	JS File uploader
 */
$js_source = file_get_contents( __DIR__."/js/backend_upload.js");
$js_source = str_replace(
	array("{LEPTON_URL}", "{THEME_URL}", "{SECTION_ID}"),
	array( LEPTON_URL, THEME_URL, $section_id ),
	$js_source
);

echo $js_source;

/**
 *	Prepare all information
 *
 */
$module_data = array(
	
	'block_a' => array(
		'label' => $MOD_MODUL_INFO['LABEL_BLOCK_A'],
		'items'	=> array()
		),
	
	'block_b' => array(
		'label' => $MOD_MODUL_INFO['LABEL_BLOCK_B'],
		'items' => array()
		),
		
	'block_c' => array(
		'label' => $MOD_MODUL_INFO['LABEL_BLOCK_C'],
		'items' => array()
		),
	
	'block_d' => array(
		'label' => $MOD_MODUL_INFO['LABEL_BLOCK_D'],
		'items' => array()
		)
);

//	Name of the module
$module_data['block_a']['items'][] = array(
	'type'	=> "text",
	'name'	=> "modul",
	'label' => $MOD_MODULINFO['MODUL'],
	'value'	=> $data['modul']
);

//	Type of the module
$module_data['block_a']['items'][] = array(
	'type'	=> 'select',
	'name'	=> 'select',
	'label' => $MOD_MODULINFO['TYPE'],
	'value'	=> $type_select	// #1
);

//	Author of the module
$module_data['block_a']['items'][] = array(
	'type'	=> 'text',
	'name'	=> 'author',
	'label' => $MOD_MODULINFO['AUTHOR'],
	'value'	=> $data['author']
);

//	WB_name - Nick-name
$module_data['block_a']['items'][] = array(
	'type'	=> 'text',
	'name'	=> 'wb_name',
	'label' => $MOD_MODULINFO['WB_NAME'],
	'value'	=> $data['wb_name']
);

//	Contact information
$module_data['block_a']['items'][] = array(
	'type'	=> 'text',
	'name'	=> 'contact',
	'label' => $MOD_MODULINFO['CONTACT'],
	'value'	=> $data['contact']
);

//	Version
$module_data['block_a']['items'][] = array(
	'type'	=> 'text',
	'name'	=> 'version',
	'label' => $MOD_MODULINFO['VERSION'],
	'value'	=> $data['version']
);

//	State
$module_data['block_a']['items'][] = array(
	'type'	=> 'select',
	'name'	=> 'state',
	'label' => $MOD_MODULINFO['STATE'],
	'value'	=> $state_select
);

//	license
$module_data['block_a']['items'][] = array(
	'type'	=> 'text',
	'name'	=> 'license',
	'label' => $MOD_MODULINFO['LICENSE'],
	'value'	=> $data['license']
);

//	GUID
$module_data['block_a']['items'][] = array(
	'type'	=> 'text',
	'name'	=> 'guid',
	'label' => ($data['guid'] == "" ? "<a href='http://createguid.com/' target='blank'>(get guid)</a> " : "").$MOD_MODULINFO['GUID'],
	'value'	=> $data['guid']
);

//	platform
$module_data['block_a']['items'][] = array(
	'type'	=> 'select',
	'name'	=> 'platform',
	'label' => $MOD_MODULINFO['PLATFORM'],
	'value'	=> $platform_select
);

//	download - textfield/url
$module_data['block_a']['items'][] = array(
	'type'	=> 'text',
	'name'	=> 'download',
	'label' => $MOD_MODULINFO['DOWNLOAD'],
	'value'	=> $data['download']
);

//	amasp_upload
$module_data['block_a']['items'][] = array(
	'type'	=> 'file',
	'name'	=> 'amasp_upload',
	'label' => $MOD_MODULINFO['AMASP_UPLOAD'],
	'value'	=> ""
);

//	wb_thread
$module_data['block_a']['items'][] = array(
	'type'	=> 'text',
	'name'	=> 'wb_thread',
	'label' => $MOD_MODULINFO['WB_THREAD'],
	'value'	=> $data['wb_thread']
);

//	web_link
$module_data['block_a']['items'][] = array(
	'type'	=> 'text',
	'name'	=> 'web_link',
	'label' => $MOD_MODULINFO['WEB_LINK'],
	'value'	=> $data['web_link']
);

//	see_also
$module_data['block_a']['items'][] = array(
	'type'	=> 'text',
	'name'	=> 'see_also',
	'label' => $MOD_MODULINFO['SEE_ALSO'],
	'value'	=> $data['see_also']
);

//	requires
$module_data['block_a']['items'][] = array(
	'type'	=> 'text',
	'name'	=> 'requires',
	'label' => $MOD_MODULINFO['REQUIRES'],
	'value'	=> $data['requires']
);

//	######## Block b ######################
//	description
$module_data['block_b']['items'][] = array(
	'type'	=> 'textarea',
	'name'	=> 'description',
	'label' => $MOD_MODULINFO['DESCRIPTION'],
	'value'	=> $data['description']
);

//	info
$module_data['block_b']['items'][] = array(
	'type'	=> 'textarea',
	'name'	=> 'info',
	'label' => $MOD_MODULINFO['INFO'],
	'value'	=> $data['info']
);

//	######### Block c #####################
//	screen
$module_data['block_c']['items'][] = array(
	'type'	=> 'file_screen',
	'name'	=> 'screen',
	'label' => $MOD_MODULINFO['SCREEN'],
	'value'	=> $screen_preview
);

//	######### Block d ######################
//	Download statistics
$module_data['block_d']['items'][] = array(
	'type'	=> 'html',
	'name'	=> 'downloads',
	'label' => $MOD_MODUL_INFO['LABEL_STATS_DOWNLOAD'],
	'value'	=> intval($data['counter'])
);

//	Votes
$module_data['block_d']['items'][] = array(
	'type'	=> 'html',
	'name'	=> 'votes',
	'label' => $MOD_MODUL_INFO['LABEL_STATS_VOTES'],
	'value'	=> intval($data['votes'])
);

//	Average
if ($data['rating']=="") $data['rating']="0,0,0,0,0,0";
$temp = explode(",", $data['rating']);
$n = 0;
if (count($temp) < 6) {
	for($i = count($temp); $i<6; $i++) $temp[] = 0;
}
for($i=1;$i<=5;$i++) $n += $temp[$i]*$i;

$av = ($data['votes'] == 0) 
	? 0
	: ($n / ($data['votes']*5)) * 100
	;
$module_data['block_d']['items'][] = array(
	'type'	=> 'html',
	'name'	=> 'average',
	'label' => $MOD_MODUL_INFO['LABEL_STATS_AVERAGE'],
	'value'	=> $av."%"
);
/** ***********
 *	Form values
 */
$form_values = array(
	'LEPTON_URL'	=> LEPTON_URL,
	'page_id'	=> $page_id,
	'section_id'	=> $section_id,
	'module_data' => $module_data,
	'TEXT_SAVE'	=> $TEXT['SAVE'],
	'TEXT_CANCEL'	=> $TEXT['CANCEL']
);

echo $parser->render(
	"@module_info/modify.lte",
	$form_values
);

