<?php
/**
*
* @package Homepage for phpBB3.1
* @copyright (c) 2015 Marco Candian (tacitus@strategie-zone.de)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace tacitus89\homepage\controller;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
* Index controller
*/
class index
{
    /** @var \phpbb\config\config */
    protected $config;

	/** @var ContainerInterface */
	protected $container;

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
    * @param \phpbb\config\config                   $config          Config object
	* @param ContainerInterface          			$container       Service container interface
	* @param \phpbb\template\template              	$template        Template object
	* @param \phpbb\user                           	$user            User object
    * @param \phpbb\cache\driver\driver_interface	$cache           Cache object
    * @param string									$root_path       phpBB root path
	* @param string									$php_ext         phpEx
	* @return \tacitus89\homepage\controller\index
	* @access public
	*/
	public function __construct(\phpbb\config\config $config,
                                ContainerInterface $container,
                                \phpbb\controller\helper $helper,
                                \phpbb\template\template $template,
                                \phpbb\user $user,
                                \phpbb\cache\driver\driver_interface $cache,
								$root_path,
                                $php_ext)
	{
        $this->config = $config;
		$this->container = $container;
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
		$this->showCategories();

		$page = $this->container->get('tacitus89.homepage.page')->load($category);

        $this->template->assign_vars(array(
            'HP_TITLE'	    => $page->get_title(),
            'HP_CONTENT'	=> $page->get_message(),
        ));

		
		return $this->helper->render('hp_body.html', $this->user->lang('HOMEPAGE'));
	}

	/**
	 * Display all rightful categories on Homepage
	 */
	private function showCategories()
	{
        $categories = $this->container->get('tacitus89.homepage.categories')->get_categories();

        foreach ($categories as $category)
        {
            $this->template->assign_block_vars('categories', array(
                'NAME'	    => $category->get_name(),
            ));

            foreach ($category->get_forums() as $forum)
            {
                $this->template->assign_block_vars('categories.forums', array(
                    'NAME'	=> $forum->get_name(),
                    'DESC'  => $forum->get_hp_desc(),
                    'URL'   => $this->getDomain() . $forum->get_hp_name(),
                ));
            }
        }
	}

    /**
     * Get domain
     */
    private function getDomain()
    {
        return $this->config['server_protocol'] . $this->config['server_name'] . '/';
    }
}