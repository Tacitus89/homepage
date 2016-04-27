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
class topics
{
    /** @var ContainerInterface */
    protected $container;

    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

    /**
     * Constructor
     *
     * @param ContainerInterface $container Service container interface
     * @param \phpbb\db\driver\driver_interface $db Database connection
     * @return \tacitus89\homepage\operators\categories
     * @access public
     */
    public function __construct(ContainerInterface $container, \phpbb\db\driver\driver_interface $db)
    {
        $this->container = $container;
        $this->db = $db;
    }

    /**
     * Get all announcements
     *
     * @return \tacitus89\homepage\entity\topic[] Array of category data entities
     * @throws \tacitus89\homepage\exception\invalid_argument
     * @throws \tacitus89\homepage\exception\out_of_bounds
     * @access public
     */
    public function get_announcements()
    {
        /** @var \tacitus89\homepage\entity\topic[] $entities */
        $entities = array();

        $sql = 'SELECT t.topic_time, t.topic_views, t.topic_posts_approved as topic_posts, p.post_subject, p.post_text, p.bbcode_uid, p.bbcode_bitfield
			FROM '. TOPICS_TABLE .' t
			LEFT JOIN '. POSTS_TABLE .' p ON p.post_id = t.topic_first_post_id
			WHERE t.topic_type = 3';
        // Load all page data from the database
        $result = $this->db->sql_query($sql);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $entities[] = $this->container->get('tacitus89.homepage.topic')->import($row);
        }
        $this->db->sql_freeresult($result);

        // Return all topic entities
        return $entities;
    }

    public function get_all_topics($forum_id, $max_topics)
    {
        /** @var \tacitus89\homepage\entity\topic[] $entities */
        $entities = array();

        $sql = 'SELECT t.topic_time, t.topic_views, t.topic_posts_approved as topic_posts, p.post_subject, p.post_text, p.bbcode_uid, p.bbcode_bitfield
			FROM '. TOPICS_TABLE .' t
			LEFT JOIN '. POSTS_TABLE .' p ON p.post_id = t.topic_first_post_id
			WHERE t.forum_id = ' . $forum_id .'
			ORDER BY t.topic_time ASC
			LIMIT ' . $max_topics;
        // Load all page data from the database
        $result = $this->db->sql_query($sql);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $entities[] = $this->container->get('tacitus89.homepage.topic')->import($row);
        }
        $this->db->sql_freeresult($result);

        // Return all topic entities
        return $entities;
    }
}