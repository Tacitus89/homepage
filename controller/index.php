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

        $this->showMenu();
        $this->user->add_lang_ext('tacitus89/homepage', 'homepage');
	}

	/**
	 * Display the games
	 *
	 * @param string $category
	 * @return null
	 * @access public
	 */
	public function displayIndex()
	{
        $widget = $this->container->get('tacitus89.homepage.widgets');

        $widget->getAnnouncements();

        $widget->getNews(8, 'sz');
        $widget->getActiveUser();
        $widget->getTeam();

        return $this->helper->render('hp_index.html', $this->user->lang('HOMEPAGE'));
	}

    /**
     * Display a forum page
     *
     * @param $forum
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \tacitus89\homepage\exception\out_of_bounds
     */
    public function displayForum($forum)
    {
        $page = $this->container->get('tacitus89.homepage.page')->load($forum);
        $forums = $this->container->get('tacitus89.homepage.categories')->get_categories($forum);

        foreach ($forums as $forum)
        {
            $this->template->assign_block_vars('forums', array(
                'NAME'	    => $forum->get_name(),
                'URL'		=> $forum->get_url($this->root_path, $this->php_ext),
                'IMAGE'     => $this->getForum() . $forum->get_forum_image(),
            ));
        }

        $this->template->assign_vars(array(
            'HP_TITLE'	    => $page->get_title(),
            'HP_CONTENT'	=> $page->get_message(),
        ));

        $widget = $this->container->get('tacitus89.homepage.widgets');
        $widget->getImagesFromGallery($page->get_gallery_id());
        $widget->getInfoFromGameCollection($page->get_game_id());

        return $this->helper->render('hp_forum.html', $this->user->lang('HOMEPAGE'));
    }

    /**
     * Display all forum of a category
     *
     * @param $category
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayCategory($category)
    {
        $forums = $categories = $this->container->get('tacitus89.homepage.categories')->get_categories($category);

        foreach ($forums as $forum)
        {
            $this->template->assign_block_vars('forums', array(
                'NAME'	    => $forum->get_name(),
                'URL'		=> $this->getDomain() . $category . '/' . $forum->get_hp_url(),
                'IMAGE'     => $this->root_path . $forum->get_forum_image(),
            ));

            foreach ($forum->get_forums() as $subforum)
            {
                $this->template->assign_block_vars('forums.subforums', array(
                    'NAME'	=> $subforum->get_name(),
                    'URL'	=> $subforum->get_url($this->root_path, $this->php_ext),
                ));
            }
        }

        return $this->helper->render('hp_category.html', $this->user->lang('HOMEPAGE'));
    }

	/**
	 * Display all rightful categories on Homepage
	 */
	private function showMenu()
	{
        $categories = $this->container->get('tacitus89.homepage.categories')->get_categories();

        foreach ($categories as $category)
        {
            $this->template->assign_block_vars('categories', array(
                'NAME'	    => $category->get_name(),
				'URL'		=> $this->getDomain() . $category->get_hp_url(),
            ));

            foreach ($category->get_forums() as $forum)
            {
                $this->template->assign_block_vars('categories.forums', array(
                    'NAME'	=> $forum->get_name(),
                    'DESC'  => $forum->get_forum_desc(),
                    'URL'   => $this->getDomain() . $category->get_hp_url() . '/' . $forum->get_hp_url(),
                ));
            }
        }
	}

    /**
     * Get domain
     *
     * @param bool $slash
     * @return string
     */
    private function getDomain($slash = true)
    {
        if($slash == true)
        {
            return $this->config['server_protocol'] . $this->config['server_name'] . '/';
        }
        else
        {
            return $this->config['server_protocol'] . $this->config['server_name'];
        }
    }

    /**
     * Get forum path
     *
     * @return string
     */
    private function getForum()
    {
        return $this->getDomain(false) . $this->config['script_path'] . '/';
    }
}