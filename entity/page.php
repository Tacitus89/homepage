<?php
/**
 * @package Homepage for phpBB3.1
 * @copyright (c) 2015 Marco Candian (tacitus@strategie-zone.de)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace tacitus89\homepage\entity;


class page
{
    /**
     * Data for this entity
     *
     * @var array
     * @access protected
     */
    protected $data;

    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

    /**
     * Constructor
     *
     * @param \phpbb\db\driver\driver_interface    $db                  Database object
     * @return \tacitus89\homepage\entity\page
     * @access public
     */
    public function __construct(\phpbb\db\driver\driver_interface $db)
    {
        $this->db = $db;
    }

    /**
     * Load the data from the database for this page
     *
     * @param string $name Name of page
     * @return page $this object for chaining calls; load()->set()->save()
     * @access public
     * @throws \tacitus89\homepage\exception\out_of_bounds
     */
    public function load($name)
    {
        $sql = 'SELECT *
			FROM ' . FORUMS_TABLE . '
			WHERE hp_name = "' . $this->db->sql_escape($name) .'"';
        $result = $this->db->sql_query($sql);
        $this->data = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        if ($this->data === false)
        {
            // A page does not exist
            throw new \tacitus89\homepage\exception\out_of_bounds('name');
        }

        return $this;
    }
}