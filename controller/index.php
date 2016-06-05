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
		
		$this->template->assign_vars(array(
			'U_HOME'		=> $this->getDomain(false),
			'U_MODDING'		=> $this->getDomain() . 'modding',
        ));
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

		$widget->getActiveTopics(5);
        $widget->getNews(8, 'sz');
		$widget->getUserOnline();
        $widget->getActiveUser();
		$widget->getNewUser();
        $widget->getTeam();
		
		$this->template->assign_vars(array(
			'META'			=> '<meta name="description" content="Das größte deutsche Forum für Strategiespiele aller Art. Egal ob es sich um die Total War-Serie, Europa Universalis, Hearts of Iron, Crusader Kings, oder Stellaris handelt, hier tauscht man sich aus, spielt miteinander und genießt das Leben." />',
        ));

        return $this->helper->render('hp_index.html', 'Startseite');
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
		try
		{
			$page = $this->container->get('tacitus89.homepage.page')->load($forum);
		}
		catch (\tacitus89\homepage\exception\base $e)
		{
			redirect(append_sid($this->getDomain() . '404.html'));
		}
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
			'U_FORUM'		=> $page->get_url($this->root_path, $this->php_ext),
            'HP_TITLE'	    => $page->get_title(),
            'HP_CONTENT'	=> $page->get_message(),
			'META'			=> $page->get_desc(),
        ));

        $widget = $this->container->get('tacitus89.homepage.widgets');
        $widget->getImagesFromGallery($page->get_gallery_id());
        $widget->getInfoFromGameCollection($page->get_game_id());

        return $this->helper->render('hp_forum.html', $page->get_title());
    }

    /**
     * Display all forum of a category
     *
     * @param $category
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayCategory($category)
    {
		try
		{
			if($category == 'modding')
			{
				$category = $this->container->get('tacitus89.homepage.category')->load_by_id(543);
			}
			else
			{
				$category = $this->container->get('tacitus89.homepage.category')->load($category);
			}
		}
		catch (\tacitus89\homepage\exception\base $e)
		{
			redirect(append_sid($this->getDomain() . '404.html'));
		}
		
        $forums = $this->container->get('tacitus89.homepage.categories')->get_categories($category->get_hp_url());

        foreach ($forums as $forum)
        {
            $this->template->assign_block_vars('forums', array(
                'NAME'	    => $forum->get_name(),
                'URL'		=> $this->getDomain() . $category->get_hp_url() . '/' . $forum->get_hp_url(),
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
		
		$this->template->assign_vars(array(
			'META'			=> $category->get_desc(),
        ));
		
		$page_title = $category->get_name();
		if($category->get_id() == 543)
		{
			$page_title = 'Modding';
		}

        return $this->helper->render('hp_category.html', $page_title);
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
        
		return $this->config['server_protocol'] . $this->config['server_name'];
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