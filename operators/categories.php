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
     * @return \tacitus89\homepage\entity\category[] Array of category data entities
     * @access public
     */
    public function get_categories()
    {
        /** @var \tacitus89\homepage\entity\category[] $entities */
        $entities = array();

        // Load all page data from the database
        $sql = 'SELECT forum_id, parent_id, forum_name, hp_name, hp_desc
			FROM ' . FORUMS_TABLE . '
			WHERE hp_show = 1
			ORDER BY left_id ASC';
        $result = $this->db->sql_query($sql);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $category = $this->container->get('tacitus89.homepage.category')->import($row);
            if($row['parent_id'] > 0)
            {
                $entities[$row['parent_id']]->add_sub_category($category);
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