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
    public function load($name)
    {
        $sql = 'SELECT p.post_subject, p.post_text, p.bbcode_uid, p.bbcode_bitfield
			FROM '. FORUMS_TABLE .' f
			LEFT JOIN '. POSTS_TABLE .' p ON p.post_id = f.hp_post
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
}