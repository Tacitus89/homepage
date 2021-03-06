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
        $sql = 'SELECT p.forum_id, p.topic_id, p.post_text, p.bbcode_uid, p.bbcode_bitfield,
				f.forum_name, f.hp_desc, f.hp_gallery_id, f.hp_game_id
			FROM '. FORUMS_TABLE .' f
			LEFT JOIN '. POSTS_TABLE .' p ON p.post_id = f.hp_post_id
			WHERE hp_url = "' . $this->db->sql_escape($name) .'"
			    AND hp_show = 1';
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
        return (isset($this->data['forum_name'])) ? (string) $this->data['forum_name'] : '';
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
     * Get Meta Description
     *
     * @return string Meta-Description
     * @access public
     */
    public function get_desc()
    {
        return '<meta name="description" content="'.$this->data['hp_desc'] .'" />';
    }

    /**
     * Get Gallery ID
     *
     * @return int Gallery ID
     * @access public
     */
    public function get_gallery_id()
    {
        return (int) $this->data['hp_gallery_id'];
    }

    /**
     * Get Game ID
     *
     * @return int Game ID
     * @access public
     */
    public function get_game_id()
    {
        return (int) $this->data['hp_game_id'];
    }
	
	/**
     * @return mixed
     */
    public function get_url($phpbb_root_path, $phpEx)
    {
        return append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $this->data['forum_id'] . '&amp;t=' . $this->data['topic_id']);
    }
}