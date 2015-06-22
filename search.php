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
function modul_info_search($func_vars) {
	extract($func_vars, EXTR_PREFIX_ALL, 'func');

	// how many lines of excerpt we want to have at most
	$max_excerpt_num = $func_default_max_excerpt;
	$divider = ".";
	$result = false;

	$table = TABLE_PREFIX ."mod_module_info";
	$query = $func_database->query("SELECT * FROM $table WHERE section_id='$func_section_id'") ;
	if($query->numRows() > 0) {
		while($res = $query->fetchRow()) {
			$text = $res['modul'].$divider.$res['wb_name'].$divider.$res['author'].$divider.$res['contact']
					.$divider.$res['description'].$divider.$res['info'].$divider.$divider;
			$mod_vars = array(
				'page_link' => $func_page_link,
				'page_link_target' => "",
				'page_title' => $func_page_title,
				'page_description' => $func_page_description,
				'page_modified_when' => $func_page_modified_when,
				'page_modified_by' => $func_page_modified_by,
				'text' => $text,
				'max_excerpt_num' => $max_excerpt_num
			);
			if(print_excerpt2($mod_vars, $func_vars)) {
				$result = true;
			}
		}
	}
	return $result;
}
?>
