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

		//it show all categories
        if($category == ''){
            $sql = 'SELECT f1.forum_id, f1.parent_id, f1.forum_name, f1.forum_desc, f1.forum_image, f1.hp_url, f1.hp_desc, f1.hp_special
			FROM ' . FORUMS_TABLE . ' f1, '. FORUMS_TABLE .' f2
			WHERE (f2.hp_show = 1 AND f2.parent_id = 0
			    AND (f1.hp_special = 0 AND f1.hp_show = 1 AND (f1.parent_id = 0 OR f1.parent_id = f2.forum_id)) )
				OR (f1.forum_id = 543 AND f2.forum_id = 543)
				OR (f2.hp_show = 1 AND f1.hp_special = 1 AND (f1.parent_id = 0 OR f1.parent_id = f2.forum_id))
			ORDER BY f1.hp_special, f1.left_id ASC';
        }
		//Special url
		//display all modding forums
        elseif ($category == 'modding') {
            $sql = 'SELECT f1.forum_id, f1.parent_id, f1.forum_name, f1.forum_desc, f1.forum_image, f1.hp_url, f1.hp_desc, f1.hp_special
			FROM ' . FORUMS_TABLE . ' f1
			WHERE f1.hp_show = 1 AND f1.hp_special = 1
			ORDER BY f1.left_id ASC';
        }
		//display all forums of category
		else {
			$sql = 'SELECT f1.forum_id, f1.parent_id, f1.forum_name, f1.forum_desc, f1.forum_image, f1.hp_url, f1.hp_desc, f1.hp_special
			FROM ' . FORUMS_TABLE . ' f1
			RIGHT JOIN '. FORUMS_TABLE .' f2 ON (f2.left_id < f1.left_id AND f2.right_id > f1.right_id)
			WHERE f1.hp_show = 1 AND f2.hp_show = 1 
				AND f1.hp_special = 0 AND f2.hp_special = 0
			    AND f2.hp_url = "'. $category .'"
			ORDER BY f1.left_id ASC';
		}
        // Load all page data from the database
        $result = $this->db->sql_query($sql);

        while ($row = $this->db->sql_fetchrow($result))
        {
			//adding to pseudo-Category
			if($row['hp_special'] == 1)
			{
				$row['parent_id'] = 543;
			}
			//pseudo-Category
			if($row['forum_id'] == 543)
			{
				$row['forum_name'] = 'Modding';
				$row['hp_url'] = 'modding';
			}
			
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