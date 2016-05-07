<?php
/**
 * @package Homepage for phpBB3.1
 * @copyright (c) 2015 Marco Candian (tacitus@strategie-zone.de)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace tacitus89\homepage\entity;


class category
{
    /**
     * Data for this entity
     *
     * @var array
     *      forum_id
     *      forum_name
     *      forum_image
     *      hp_url
     *      hp_desc
     * @access protected
     */
    protected $data;

    /**
     * Data for forums as category entity
     *
     * @var \tacitus89\homepage\entity\category[]
     * @access protected
     */
    protected $forums = array();

    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

    /**
     * Constructor
     *
     * @param \phpbb\db\driver\driver_interface    $db                  Database object
     * @return category
     * @access public
     */
    public function __construct(\phpbb\db\driver\driver_interface $db)
    {
        $this->db = $db;
    }

    /**
     * Load the data from the database for this page
     *
     * @param int $id Category identifier
     * @return page $this object for chaining calls; load()->set()->save()
     * @access public
     * @throws \tacitus89\homepage\exception\out_of_bounds
     */
    public function load($id)
    {
        $sql = 'SELECT forum_id, forum_name, forum_desc, forum_image, hp_url, hp_desc
			FROM ' . FORUMS_TABLE . '
			WHERE id = ' . (int) $id .'
			    AND parent_id = 0 AND hp_show = 1';
        $result = $this->db->sql_query($sql);
        $this->data = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        if ($this->data === false)
        {
            // A page does not exist
            throw new \tacitus89\homepage\exception\out_of_bounds('id');
        }

        return $this;
    }

    /**
     * Import data for a category
     *
     * Used when the data is already loaded externally.
     * Any existing data on this page is over-written.
     * All data is validated and an exception is thrown if any data is invalid.
     *
     * @param array $data Data array, typically from the database
     * @return category $this object for chaining calls; load()->set()->save()
     * @access public
     * @throws \tacitus89\homepage\exception\base
     */
    public function import($data)
    {
        // Clear out any saved data
        $this->data = array();

        // All of our fields
        $fields = array(
            // column						=> data type (see settype())
            'forum_id'						=> 'integer',
            'forum_name'					=> 'string',
            'forum_desc'                    => 'string',
            'forum_image'                   => 'string',
            'hp_url'                       => 'string',
            'hp_desc'                       => 'string',
        );

        // Go through the basic fields and set them to our data array
        foreach ($fields as $field => $type)
        {
            // If the data wasn't sent to us, throw an exception
            if (!isset($data[$field]))
            {
                throw new \tacitus89\homepage\exception\invalid_argument(array($field, 'FIELD_MISSING'));
            }

            // If the type is a method on this class, call it
            if (method_exists($this, $type))
            {
                $this->$type($data[$field]);
            }
            else
            {
                // settype passes values by reference
                $value = $data[$field];

                // We're using settype to enforce data types
                settype($value, $type);

                $this->data[$field] = $value;
            }
        }

        // Some fields must be unsigned (>= 0)
        $validate_unsigned = array(
            'forum_id',
        );

        foreach ($validate_unsigned as $field)
        {
            // If the data is less than 0, it's not unsigned and we'll throw an exception
            if ($this->data[$field] < 0)
            {
                throw new \tacitus89\homepage\exception\out_of_bounds($field);
            }
        }

        return $this;
    }

    /**
     * Get id
     *
     * @return int Category identifier
     * @access public
     */
    public function get_id()
    {
        return (isset($this->data['forum_id'])) ? (int) $this->data['forum_id'] : 0;
    }

    /**
     * Get name
     *
     * @return string Name
     * @access public
     */
    public function get_name()
    {
        return (isset($this->data['forum_name'])) ? (string) $this->data['forum_name'] : '';
    }

    /**
     * Get forum description
     *
     * @return string Forum description
     * @access public
     */
    public function  get_forum_desc()
    {
        return (isset($this->data['forum_desc'])) ? (string) $this->data['forum_desc'] : '';
    }
    
    /**
     * Get forum image
     *
     * @return string Forum image
     * @access public
     */
    public function get_forum_image()
    {
        return (isset($this->data['forum_image'])) ? (string) $this->data['forum_image'] : '';
    }

    /**
     * Get hp_url
     *
     * @return string Name
     * @access public
     */
    public function get_hp_url()
    {
        return (isset($this->data['hp_url'])) ? (string) $this->data['hp_url'] : '';
    }

    /**
     * Get hp_desc
     *
     * @return string Name
     * @access public
     */
    public function get_hp_desc()
    {
        return (isset($this->data['hp_desc'])) ? (string) $this->data['hp_desc'] : '';
    }

    /**
     * Adding an sub category in this category
     *
     * @param category $category
     * @access public
     */
    public function add_forum(\tacitus89\homepage\entity\category $category)
    {
        $this->forums[] = $category;
    }

    /**
     * Get all sub categories of this category
     *
     * @return category[]
     * @access public
     */
    public function get_forums()
    {
        return $this->forums;
    }
}
