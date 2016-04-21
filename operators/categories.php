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
class categories
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
     * Get all rightful categories
     *
     * @param string $category
     * @return \tacitus89\homepage\entity\category[] Array of category data entities
     * @throws \tacitus89\homepage\exception\invalid_argument
     * @throws \tacitus89\homepage\exception\out_of_bounds
     * @access public
     */
    public function get_categories($category = '')
    {
        /** @var \tacitus89\homepage\entity\category[] $entities */
        $entities = array();

        if($category == ''){
            $sql = 'SELECT forum_id, parent_id, forum_name, forum_image, hp_name, hp_desc
			FROM ' . FORUMS_TABLE . '
			WHERE hp_show = 1
			ORDER BY left_id ASC';
        }
        else {
            $sql = 'SELECT f1.forum_id, f1.parent_id, f1.forum_name, f1.forum_image, f1.hp_name, f1.hp_desc
			FROM ' . FORUMS_TABLE . ' f1
			RIGHT JOIN '. FORUMS_TABLE .' f2 ON (f2.left_id < f1.left_id AND f2.right_id > f1.right_id)
			WHERE f1.hp_show = 1 AND f2.hp_show = 1
			    AND f2.hp_name = "'. $category .'"
			ORDER BY f1.left_id ASC';
        }
        // Load all page data from the database
        $result = $this->db->sql_query($sql);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $category = $this->container->get('tacitus89.homepage.category')->import($row);
            if(isset($entities[$row['parent_id']]) && $row['parent_id'] > 0)
            {
                $entities[$row['parent_id']]->add_forum($category);
            }
            else
            {
                // Import each page row into an entity
                $entities[$row['forum_id']] = $category;
            }
        }
        $this->db->sql_freeresult($result);

        // Return all page entities
        return $entities;
    }
}