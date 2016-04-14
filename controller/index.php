<?php
/**
*
* @package Homepage for phpBB3.1
* @copyright (c) 2015 Marco Candian (tacitus@strategie-zone.de)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace tacitus89\homepage\controller;


/**
* Index controller
*/
class index
{
    /** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\config\db_text */
	protected $config_text;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

    /** @var \phpbb\cache\driver\driver_interface */
    protected $cache;

    /** @var string phpBB root path */
	protected $root_path;

	/** @var string phpEx */
	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\config\config                  	$config          Config object
	* @param \phpbb\config\db_text                 	$config_text     DB text object
	* @param \phpbb\template\template              	$template        Template object
	* @param \phpbb\user                           	$user            User object
    * @param \phpbb\cache\driver\driver_interface	$cache           Cache object
    * @param string									$root_path       phpBB root path
	* @param string									$php_ext         phpEx
	* @return \tacitus89\homepage\controller\index
	* @access public
	*/
	public function __construct(\phpbb\config\config $config,
                                \phpbb\config\db_text $config_text,
                                \phpbb\controller\helper $helper,
                                \phpbb\template\template $template,
                                \phpbb\user $user,
                                \phpbb\cache\driver\driver_interface $cache,
                                $root_path,
                                $php_ext)
	{
		$this->config = $config;
		$this->config_text = $config_text;
		$this->helper = $helper;
		$this->template = $template;
		$this->user = $user;
        $this->cache = $cache;
        $this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	 * Display the games
	 *
	 * @param string $category
	 * @return null
	 * @access public
	 */
	public function display($category = '')
	{
       echo $category;
	   
	   return $this->helper->render('hp_body.html', $this->user->lang('HOMEPAGE'));
	}
}