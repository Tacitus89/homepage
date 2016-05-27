<?php
/**
 *
 * @package Homepage for phpBB3.1
 *
 * @copyright (c) 2015 Marco Candian (tacitus@strategie-zone.de)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace tacitus89\homepage\operators;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Operator for a set of categories
 */
class widgets
{
    /** @var \phpbb\config\config */
    protected $config;

    /** @var ContainerInterface */
    protected $container;

    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

    /** @var \phpbb\template\template */
    protected $template;

    /** @var \phpbb\user */
    protected $user;

    /** @var string phpBB root path */
    protected $root_path;

    /** @var string phpEx */
    protected $php_ext;

    /**
     * Constructor
     *
     * @param \phpbb\config\config                  $config     Config object
     * @param ContainerInterface                    $container  Service container interface
     * @param \phpbb\db\driver\driver_interface     $db         Database connection
     * @param \phpbb\template\template              $template   Template object
     * @param \phpbb\user                           $user       User object
     * @param string								$root_path  phpBB root path
     * @param string								$php_ext    phpEx
     * @return \tacitus89\homepage\operators\widgets
     * @access public
     */
    public function __construct(\phpbb\config\config $config,
                                ContainerInterface $container,
                                \phpbb\db\driver\driver_interface $db,
                                \phpbb\template\template $template,
                                \phpbb\user $user,
                                $root_path,
                                $php_ext)
    {
        $this->config = $config;
        $this->container = $container;
        $this->db = $db;
        $this->template = $template;
        $this->user = $user;
        $this->root_path = $root_path;
        $this->php_ext = $php_ext;
    }

    /**
     * Set news to template
     *
     * @param integer $forum_id
     * @param string $name
     */
    public function getNews($forum_id, $name)
    {
        $topics = $this->container->get('tacitus89.homepage.topics')->get_all_topics($forum_id, 10);

        foreach ($topics as $topic)
        {
            $this->template->assign_block_vars('news_' . $name, array(
                'TITLE'	    => $topic->get_title(),
                'DATE'      => date('d.m.Y H:i', $topic->get_topic_time()),
                'URL'       => $topic->get_url($this->root_path, $this->php_ext),
                'COMMENTS'  => $topic->get_answers(),
            ));
        }
    }

    /**
     * Set Announcements to template
     *
     */
    public function getAnnouncements()
    {
        $announcements = $this->container->get('tacitus89.homepage.topics')->get_announcements();

        foreach ($announcements as $element)
        {
            $this->template->assign_block_vars('announcements', array(
                'TITLE'	    => $element->get_title(),
                'TEXT'      => $element->get_message(),
                'COMMENTS'  => $element->get_answers(),
                'DATE'      => date('d.m.Y H:i', $element->get_topic_time()),
                'URL'       => $element->get_url($this->root_path, $this->php_ext),
            ));
        }
    }

    public function getTeam()
    {
        $sql = 'SELECT u.user_id, u.username, u.user_colour, g.group_id, g.group_name, g.group_colour
                FROM '. USERS_TABLE .' u
                LEFT JOIN '. GROUPS_TABLE .' g ON g.group_id = u.group_id
                WHERE u.group_id = 5 OR  u.group_id = 8
                ORDER BY u.username DESC';

        $result = $this->db->sql_query($sql);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $this->template->assign_block_vars('team', array(
                'USERNAME_FULL'	    => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
                'GROUP_COLOUR'      => $row['group_colour'],
                'GROUP_NAME'        => $this->user->lang['G_' . $row['group_name']],
                'U_GROUPS'          => append_sid("{$this->root_path}memberlist.$this->php_ext", 'mode=group&amp;g=' . $row['group_id']),
            ));
        }
    }

    public function getActiveUser()
    {
        $sql = 'SELECT u.user_id, u.username, u.user_colour, u.user_posts
                FROM '. USERS_TABLE .' u
                WHERE u.user_posts > 0
                ORDER BY u.user_posts DESC
                LIMIT 8';

        $result = $this->db->sql_query($sql);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $this->template->assign_block_vars('active_user', array(
                'USERNAME_FULL'	    => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
                'USER_POSTS'        => $row['user_posts'],
            ));
        }
    }

    /**
     * Get Images from Gallery
     *
     * @param int $gallery_id
     * @access public
     */
    public function getImagesFromGallery($gallery_id)
    {
        if(!isset($this->config['phpbb_gallery_version']) || $gallery_id == 0)
        {
            return;
        }

        $sql = 'SELECT gi.image_id
                FROM phpbb_gallery_images gi
                WHERE gi.image_album_id = '. $gallery_id .'
                ORDER BY gi.image_id DESC
                LIMIT 8';

        $result = $this->db->sql_query($sql);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $this->template->assign_block_vars('gallery_images', array(
                'MINI'	    => append_sid("{$this->root_path}app.php/gallery/image/". $row['image_id'] ."/mini"),
                'SOURCE'    => append_sid("{$this->root_path}app.php/gallery/image/". $row['image_id'] ."/source"),
            ));
        }
    }

    /**
     * Get Images from Gallery
     *
     * @param int $gallery_id
     * @access public
     */
    public function getInfoFromGameCollection($game_id)
    {
        if(!isset($this->config['games_active']) || $this->config['games_active'] == 0 || $game_id == 0)
        {
            return;
        }

        $sql = 'SELECT g.name, g.developer, g.publisher, g.genre, g.game_release
                FROM phpbb_games g
                WHERE g.id = '. $game_id;

        $result = $this->db->sql_query($sql);
        $row = $this->db->sql_fetchrow($result);
        $this->template->assign_vars(array(
            'S_GAME_INFO'       => true,
            'GAME_NAME'         => $row['name'],
            'GAME_DEVELOPER'    => $row['developer'],
            'GAME_PUBLISHER'    => $row['publisher'],
            'GAME_GENRE'        => $row['genre'],
            'GAME_RELEASE'      => date('d.m.Y',$row['game_release']),

        ));
    }
}