<?php
/**
 * @package Homepage for phpBB3.1
 * @copyright (c) 2015 Marco Candian (tacitus@strategie-zone.de)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 */

namespace tacitus89\homepage\entity;


class topic
{
    /**
     * Data for this entity
     *
     * @var array
     *      topic_id
     *      forum_id
     *      topic_time
     *      topic_view
     *      topic_posts
     *      post_subject
     *      post_text
     *      bbcode_uid
     *      bbcode_bitfield
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
    public function load($id)
    {
        $sql = 'SELECT t.topic_id, t.forum_id, t.topic_time, t.topic_views, t.topic_posts_approved as topic_posts,
                p.post_subject, p.post_text, p.bbcode_uid, p.bbcode_bitfield
			FROM '. TOPICS_TABLE .' t
			LEFT JOIN '. POSTS_TABLE .' p ON p.post_id = t.topic_first_post_id
			WHERE t.id = '. (int) $id;
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
            'topic_id'                => 'integer',
            'forum_id'                => 'integer',
            'topic_time'              => 'integer',
            'topic_views'             => 'integer',
            'topic_posts'             => 'integer',
            'post_subject'            => 'string',
            'post_text'               => 'string',
            'bbcode_uid'              => 'string',
            'bbcode_bitfield'         => 'string',
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
            'topic_id',
            'forum_id',
            'topic_time',
            'topic_views',
            'topic_posts',
            'bbcode_uid',
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

    public function get_topic_time()
    {
        return (isset($this->data['topic_time'])) ? (int) $this->data['topic_time'] : 0;
    }

    public function get_topic_views()
    {
        return (isset($this->data['topic_views'])) ? (int) $this->data['topic_views'] : 0;
    }

    public function get_answers()
    {
        return (isset($this->data['topic_posts'])) ? (int) $this->data['topic_posts'] - 1 : 0;
    }

    /**
     * Get title
     *
     * @return string Title
     * @access public
     */
    public function get_title()
    {
        return (isset($this->data['post_subject'])) ? (string) $this->data['post_subject'] : '';
    }

    /**
     * Get message
     *
     * @return string Message
     * @access public
     */
    public function get_message()
    {
        // If these haven't been set yet; use defaults
        $text = (isset($this->data['post_text'])) ? $this->data['post_text'] : '';
        $uid = (isset($this->data['bbcode_uid'])) ? $this->data['bbcode_uid'] : '';
        $bitfield = (isset($this->data['bbcode_bitfield'])) ? $this->data['bbcode_bitfield'] : '';
        $parse_flags = ($bitfield ? OPTION_FLAG_BBCODE : 0) | OPTION_FLAG_SMILIES;

        $text = generate_text_for_display($text, $uid, $bitfield, $parse_flags, true);

        return $text;
    }

    /**
     * @return mixed
     */
    public function get_url($phpbb_root_path, $phpEx)
    {
        return append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $this->data['forum_id'] . '&amp;t=' . $this->data['topic_id']);
    }
}