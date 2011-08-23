<?php
/**
*
* @package Board3 Portal v2 - Gallery Block
* @copyright (c) Board3 Group ( www.board3.de )
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package Gallery Block
*/
class portal_gallery_side_module
{
	/**
	* Allowed columns: Just sum up your options (Exp: left + right = 10)
	* top		1
	* left		2
	* center	4
	* right		8
	* bottom	16
	*/
	var $columns = 10;

	/**
	* Default modulename
	*/
	var $name = 'PORTAL_GALLERY';

	/**
	* Default module-image:
	* file must be in "{T_THEME_PATH}/images/portal/"
	*/
	var $image_src = 'portal_gallery.png';

	/**
	* module-language file
	* file must be in "language/{$user->lang}/mods/portal/"
	*/
	var $language = 'portal_gallery_module';
	
	/**
	* custom acp template
	* file must be in "adm/style/portal/"
	*/
	var $custom_acp_tpl = '';

	/**
	* Not available on this module
	function get_template_center($module_id)
	{
		global $config;

		$block = new phpbb_gallery_block();
		$block->set_mode((int) $config['board3_gallery_center_mode_' . $module_id]);
		$block->set_display((int) $config['board3_gallery_center_display_' . $module_id]);
		$block->set_nums(array(
			'rows'		=> (int) $config['board3_gallery_center_rows_' . $module_id],
			'columns'	=> (int) $config['board3_gallery_center_columns_' . $module_id],
			'comments'	=> (int) $config['board3_gallery_center_crows_' . $module_id],
			'contests'	=> (int) $config['board3_gallery_center_contests_' . $module_id],
		));
		$block->set_toggle((bool) $config['board3_gallery_center_comments_' . $module_id]);
		$block->set_pegas((bool) $config['board3_gallery_center_pgalleries_' . $module_id]);
		$block->display();

		return 'gallery_center.html';
	}
	*/

	function get_template_side($module_id)
	{
		global $config;

		$block = new phpbb_gallery_block();
		$block->set_mode((int) $config['board3_gallery_side_mode_' . $module_id]);
		$block->set_display((int) $config['board3_gallery_side_display_' . $module_id]);
		$block->set_nums(array(
			'rows'		=> (int) $config['board3_gallery_side_rows_' . $module_id],
			'columns'	=> 1,
			'comments'	=> 0,
			'contests'	=> 0,
		));
		$block->set_pegas((bool) $config['board3_gallery_side_pgalleries_' . $module_id]);
		$block->set_template_block_name('small_imageblock');
		$block->display();

		return 'gallery_side.html';
	}

	function get_template_acp($module_id)
	{
		global $phpbb_root_path, $phpEx, $user, $db;

		$user->add_lang('mods/gallery_acp');
		$user->add_lang('mods/gallery');

		include($phpbb_root_path . 'includes/acp/acp_gallery_config.' . $phpEx);

		$portal_modules = obtain_portal_modules();
		$side_column = false;

		foreach ($portal_modules as $cur_module)
		{
			if ($cur_module['module_id'] == $module_id)
			{
				$cur_column = column_num_string($cur_module['module_column']);

				if (in_array($cur_column, array('left', 'right')))
				{
					$side_column = true;
				}
			}
		}

		return array(
			'title'	=> 'ACP_PORTAL_GALLERY_SETTINGS',
			'vars'	=> array(
				'legend1'						=> 'ACP_PORTAL_GALLERY_SETTINGS_RIGHT',
				'board3_gallery_side_mode_' . $module_id			=> array('lang' => 'RRC_GINDEX_MODE',		'validate' => 'int',	'type' => 'custom',			'explain' => true,	'method' => 'rrc_modes', 'submit' => 'store_rrc'),
				'board3_gallery_side_rows_' . $module_id			=> array('lang' => 'RRC_GINDEX_ROWS',		'validate' => 'int',	'type' => 'text:3:3',		'explain' => false),
				'board3_gallery_side_display_' . $module_id			=> array('lang' => 'RRC_DISPLAY_OPTIONS',	'validate' => 'int',	'type' => 'custom',			'explain' => false,	'method' => 'rrc_display', 'submit' => 'store_rrc'),
				'board3_gallery_side_pgalleries_' . $module_id		=> array('lang' => 'RRC_GINDEX_PGALLERIES',	'validate' => 'bool',	'type' => 'radio:yes_no',	'explain' => false),
			),
		);
	}

	/**
	* API functions
	*/
	function install($module_id)
	{
		set_config('board3_gallery_side_mode_' . $module_id, 1);
		set_config('board3_gallery_side_rows_' . $module_id, 1);
		set_config('board3_gallery_side_display_' . $module_id, 5);
		set_config('board3_gallery_side_pgalleries_' . $module_id, 0);
		set_config('board3_gallery_side_version', '2.1.1');

		return true;
	}

	function uninstall($module_id)
	{
		global $db;

		$del_config = array(
			'board3_gallery_side_mode_' . $module_id,
			'board3_gallery_side_rows_' . $module_id,
			'board3_gallery_side_display_' . $module_id,
			'board3_gallery_side_pgalleries_' . $module_id,
			'board3_gallery_side_version',
		);

		$sql = 'DELETE FROM ' . CONFIG_TABLE . '
			WHERE ' . $db->sql_in_set('config_name', $del_config);
		return $db->sql_query($sql);
	}

	/**
	* Wrapp the functions, so they're always up to date with the gallery functions.
	*/
	function rrc_modes($value, $key, $module_id)
	{
		return acp_gallery_config::rrc_modes($value, $key, $module_id);
	}

	function rrc_display($value, $key, $module_id)
	{
		return acp_gallery_config::rrc_display($value, $key, $module_id);
	}
	
	function store_rrc($key, $module_id)
	{
		// Changing the value, casted by int to not mess up anything
		$config_value = (int) array_sum(request_var($key, array(0)));
		set_config($key, $config_value);
	}
}

?>