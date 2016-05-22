<?php
/**
 *
 * @package Homepage for phpBB3.1
 *
 * @copyright (c) 2015 Marco Candian (tacitus@strategie-zone.de)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace tacitus89\homepage\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class listener implements EventSubscriberInterface
{

    /** @var \phpbb\request\request */
    protected $request;

    /**
     * Constructor
     *
     * @param \phpbb\request\request               $request          Request object
     * @return \tacitus89\homepage\event\listener
     * @access public
     */
    public function __construct(\phpbb\request\request $request)
    {
        $this->request = $request;
    }

    /**
     * Assign functions defined in this class to event listeners in the core
     *
     * @return array
     * @static
     * @access public
     */
    static public function getSubscribedEvents()
    {
        return array(
            'core.acp_manage_forums_display_form'	=> 'show_hp_options',
            'core.acp_manage_forums_request_data'   => 'set_hp_options',
        );
    }

    /**
     * Show the current values to template
     *
     * @param object $event The event object
     * @return null
     * @access public
     */
    public function show_hp_options($event)
    {
        $template_data = $event['template_data'];
        $forum_data = $event['forum_data'];
        $template_data['S_HP_SHOW'] = ($forum_data['hp_show'])? true : false;
        $template_data['HP_URL'] = $forum_data['hp_url'];

        if($forum_data['forum_type'] == 1)
        {
            $template_data['HP_POST_ID'] = $forum_data['hp_post_id'];
            $template_data['HP_GALLERY_ID'] = $forum_data['hp_gallery_id'];
            $template_data['HP_GAME_ID'] = $forum_data['hp_game_id'];
        }
        $template_data['HP_DESC'] = $forum_data['hp_desc'];
        $template_data['HP_META'] = $forum_data['hp_meta'];
        $event['template_data'] = $template_data;
    }

    /**
     * Adding the request data to variable
     *
     * @param object $event The event object
     * @return null
     * @access public
     */
    public function set_hp_options($event)
    {
        $forum_data = $event['forum_data'];
        $forum_data['hp_show'] = $this->request->variable('homepage_show', 0);
        $forum_data['hp_url'] = $this->request->variable('hp_url', '');
        if($forum_data['forum_type'] == 1)
        {
            $forum_data['hp_post_id'] = $this->request->variable('hp_post_id', 0);
            $forum_data['hp_gallery_id'] = $this->request->variable('hp_gallery_id', 0);
            $forum_data['hp_game_id'] = $this->request->variable('hp_game_id', 0);
        }
        else{
            $forum_data['hp_post_id'] = 0;
            $forum_data['hp_gallery_id'] = 0;
            $forum_data['hp_game_id'] = 0;
        }
        $forum_data['hp_desc'] = $this->request->variable('hp_desc', '');
        $forum_data['hp_meta'] = $this->request->variable('hp_meta', '');
        $event['forum_data'] = $forum_data;
    }
}