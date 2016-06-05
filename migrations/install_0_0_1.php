<?php

/**
*
* @package Homepage Control for phpBB3.1
* @copyright (c) 2015 Marco Candian (tacitus@strategie-zone.de)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace tacitus89\homepage\migrations;

class install_0_0_1 extends \phpbb\db\migration\migration
{
	var $homepage_version = '0.0.1';

	public function effectively_installed()
	{
		return isset($this->config['homepage_version']) && version_compare($this->config['homepage_version'], $this->homepage_version, '>=');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_data()
	{
		return array(
			
		);
	}
	
	/**
	* Add columns
	*
	* @return array Array of table schema
	* @access public
	*/
	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'forums'		=> array(
					'hp_show'			=> array('BOOL', 0),
					'hp_special'		=> array('BOOL', 0),
					'hp_url'			=> array('VCHAR:255', ''),
					'hp_post_id'		=> array('UINT:8', 0),
					'hp_meta'			=> array('VCHAR:255', ''),
					'hp_desc'			=> array('VCHAR:255', ''),
					'hp_gallery_id'		=> array('UINT:8', 0),
					'hp_game_id'		=> array('UINT:8', 0),
				),
			),
		);
	}
	
	/**
	* Drop columns
	*
	* @return array Array of table schema
	* @access public
	*/
	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'forums' => array('hp_show', 'hp_special', 'hp_name', 'hp_post_id','hp_meta', 'hp_desc', 'hp_gallery_id', 'hp_game_id'),
			),
		);
	}
}
