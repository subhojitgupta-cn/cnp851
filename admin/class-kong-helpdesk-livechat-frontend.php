<?php

class Kong_Helpdesk_Livechat_Frontend extends Kong_Helpdesk
{
    protected $plugin_name;
    protected $version;

    /**
     * Construct Livechat Class
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       
     * @param   [type]                       
     * @param   [type]                       $ticket_processor   [description]
     * @param   [type]                       $comments_processor [description]
     */
    public function __construct($plugin_name, $version, $ticket_processor, $comments_processor, $attachments)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->ticket_processor = $ticket_processor;
        $this->comments_processor = $comments_processor;
        $this->attachments = $attachments;
    }

    /**
     * Init Livechat
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    public function init()
    {
        global $kong_helpdesk_options;
        $this->options = $kong_helpdesk_options;
    }

    /**
     * Check if any user has been online in the last 1 minutes
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    public function get_agents_online()
    {
        $logged_in_users = get_transient('agents_online'); //Get the active users from the transient.
        $count = 0;

        if($logged_in_users) {
            foreach ($logged_in_users as $logged_in_user) {
                if (isset($logged_in_user['last']) && ($logged_in_user['last'] > (time()-60) )) {
                    $count++;
                }
            }
        }
        
        return $count;
    }

    /**
     * Check when a user was last online.
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $id [description]
     * @return  [type]                           [description]
     */
    public function user_last_online($id)
    {
        $logged_in_users = get_transient('agents_online'); //Get the active users from the transient.
        
        //Determine if the user has ever been logged in (and return their last active date if so).
        if (isset($logged_in_users[$id]['last'])) {
            return $logged_in_users[$id]['last'];
        } else {
            return false;
        }
    }

    /**
     * Create Ticket from Livechat
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    public function create_ticket()
    {
        $type = 'Chat';

        $response = array(
            'status' => 'false',
            'message' => __('Ticket could not be created. Please contact support email directly.', 'kong-helpdesk')
        );
        if (isset($_POST['helpdeskTicket'])) {
            ob_start();

            $status = $this->ticket_processor->form_sanitation($_POST, $type);
            $return = ob_get_clean();

            if ($status !== false) {
                $response = array(
                    'status' => 'true',
                    'message' => sprintf( __('A seperate Chat-Ticket with the ID %s has been created.', 'kong-helpdesk'), $this->ticket_processor->post_id),
                    'ticket' => $this->ticket_processor->post_id,
                );
            } else {
                $response = array(
                    'status' => 'false',
                    'message' => $this->ticket_processor->errors[0]
                );
            }
        }
        die(json_encode($response));
    }

    /**
     * Post comment to ticket
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    public function comment_ticket()
    {
        $response = array(
            'status' => 'true',
            'message' => ''
        );

        $status = $this->comments_processor->form_sanitation($_POST, 'Chat');

        if (!$status) {
            $response['status'] = 'false';
        }

        die(json_encode($response));
    }

    /**
     * Post comment to ticket
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    public function upload_file()
    {
        if (!isset($_POST['ticket']) || empty($_POST['ticket'])) {
            $response['error'] = __('Ticket not set.', 'kong-helpdesk');
            die(json_encode($response));
        }

        $id = intval($_POST['ticket']);
        $author = get_userdata(get_current_user_id());

        $commentdata = array(
            'comment_post_ID' => $id,
            'comment_author' => $author->display_name,
            'comment_author_email' => $author->user_email,
            'comment_author_url' => '',
            'comment_content' => sanitize_text_field($_FILES['helpdesk-attachments']['name'][0]),
            'comment_type' => '',
            'comment_parent' => 0,
            'user_id' => $author->ID,
        );

        //Insert new comment and get the comment ID
        $comment_id = wp_new_comment($commentdata, true);

        if (!is_int($comment_id)) {
            die('Error while inserting comment');
        }

        $response = array(
            'status' => 'true',
            'message' => ''
        );

        $status = $this->attachments->save_comment_attachments(null, get_comment($comment_id) );

        if (!$status) {
            $response['status'] = 'false';
        }

        die(json_encode($response));
    }

    /**
     * Get ticket details
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    public function get_ticket()
    {
        $response = array(
            'status' => 'false',
            'chat' => '',
            'error' => '',
        );

        if (!isset($_POST['ticket']) || empty($_POST['ticket'])) {
            $response['error'] = __('Ticket not set.', 'kong-helpdesk');
            die(json_encode($response));
        }

        $id = intval($_POST['ticket']);
        $ticket = get_post($id);

        if (empty($ticket)) {
            $response['error'] = __('Ticket does not exists.', 'kong-helpdesk');
            die(json_encode($response));
        }

        $current_user = wp_get_current_user();
        $roles = $current_user->roles;
        $role = array_shift($roles);
        $notAllowedRoles = array('administrator', 'agent', 'shop_manager', 'author', 'editor', 'contributor');
        if (!in_array($role, $notAllowedRoles) && (intval($ticket->post_author) !== get_current_user_id())) {
        // if (intval($ticket->post_author) !== get_current_user_id()) {
            $response['error'] = __('This is not your Ticket!', 'kong-helpdesk');
            die(json_encode($response));
        }

        $data = array();

        // Get Ticket Post Content for First View
        if ($_POST['all'] === "true") {
            $data[] = array(
                'author_name' => get_userdata($ticket->post_author)->data->display_name,
                'author_img' => get_avatar_url($ticket->post_author),
                'time' => date('H:i', strtotime($ticket->post_date)),
                'content' => $ticket->post_content,
                'agent' => false,
                'attachment_url' => '',
                'attachment_thumb' => '',
            );
        }

        // Process Ticket Comments into Chat
        $args = array(
            'order' => 'ASC',
            'orderby' => 'date',
            'post_id' => $id,
        );

        $comments = get_comments($args);

        $comment_ids = array();
        foreach ($comments as $comment) {
            if (isset($_POST['comment_ids']) && is_array($_POST['comment_ids']) && in_array($comment->comment_ID, $_POST['comment_ids'])) {
                continue;
            }
            $comment_ids[] = $comment->comment_ID;

            $agent = false;
            if($comment->user_id !== $ticket->post_author) {
                $agent = true;
            }

            $full_url = '';
            $thumb_url = '';
            $attachment_ids = get_comment_meta($comment->comment_ID, 'kong_helpdesk_attachments');
            if (isset($attachment_ids[0]) && !empty($attachment_ids[0])) {
                $html = '<div class="kong-helpdesk-comment-attachments">';

                $attachment_ids = $attachment_ids[0];
                foreach ($attachment_ids as $attachment_id) {
                    $full_url = wp_get_attachment_url($attachment_id);
                    $thumb_url = wp_get_attachment_thumb_url($attachment_id);
                }
            }

            $data[] = array(
                'author_name' => $comment->comment_author,
                'author_img' => get_avatar_url($comment->user_id),
                'time' => date('H:i', strtotime($comment->comment_date)),
                'content' => $comment->comment_content,
                'agent' => $agent,
                'attachment_url' => $full_url,
                'attachment_thumb' => $thumb_url,
            );
        }

        $response = array(
            'status' => 'true',
            'title' => __('Ticket', 'kong-helpdesk') . ' :' . $ticket->ID,
            'comment_ids' => $comment_ids,
            'chat' => $data,
        );

        die(json_encode($response));
    }

    /**
     * Get ticket details
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    public function get_comments()
    {
        $this->get_ticket();
    }

    /**
     * HTML rendering for the reporter chat
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    public function render_chat()
    {
        if (!$this->get_option('enableLiveChat')) {
            return false;
        }

        $liveChatColor = $this->get_option('liveChatAccentColor');
        $liveChatDefaultIcon = $this->get_option('liveChatDefaultIcon');
        $liveChatTitle = $this->get_option('liveChatTitle');

        $liveChatWelcomeOnline = $this->get_option('liveChatWelcomeOnline');
        $liveChatStatusOnline = $this->get_option('liveChatStatusOnline');

        $icon = "https://secure.gravatar.com/avatar/49273d102e375f8791647c5a5dce1837?s=64&d=mm&r=g";
        if(isset($liveChatDefaultIcon['url']) && !empty($liveChatDefaultIcon['url'])) {
            $icon = $liveChatDefaultIcon['url'];
        }
        ?>  

        <div id="kong-helpdesk-livechat-trigger" class="kong-helpdesk-livechat-trigger" style="background-color: <?php echo $liveChatColor ?>; display: none;">
            <i class="fa fa-commenting-o"></i>
        </div>
    
        <div id="kong-helpdesk-livechat-content" class="kong-helpdesk-livechat-content" style="display: none;">
            <div id="kong-helpdesk-livechat-header" class="kong-helpdesk-livechat-header">
                <?php if(!empty($icon)) { ?>
                <div id="kong-helpdesk-livechat-header-icon" class="kong-helpdesk-livechat-header-icon">
                    <img src="<?php echo $icon ?>">
                </div>
                <?php } ?>
                <div id="kong-helpdesk-livechat-header-title-container" class="kong-helpdesk-livechat-header-title-container">
                    <span id="kong-helpdesk-livechat-header-title" class="kong-helpdesk-livechat-header-title">
                        <?php echo $liveChatTitle ?>
                    </span>
                    <br>
                    <span id="kong-helpdesk-livechat-header-status" class="kong-helpdesk-livechat-header-status">
                        <?php echo $liveChatWelcomeOnline ?>
                    </span>
                </div>
                <a id="kong-helpdesk-livechat-close" class="kong-helpdesk-livechat-close"><i class="fa fa-close"></i></a>
            </div>
            <div id="kong-helpdesk-livechat-messages" class="kong-helpdesk-livechat-messages">
                <!-- Welcome Message -->
                <div class="kong-helpdesk-livechat-message-container kong-helpdesk-livechat-message-agent kong-helpdesk-clearfix">
                    <div class="kong-helpdesk-livechat-author">
                        <img src="<?php echo $icon ?>" class="kong-helpdesk-livechat-author-image">
                        <span class="kong-helpdesk-livechat-author-name">Helpdesk</span>
                    </div>
                    <div id="kong-helpdesk-livechat-welcome" class="kong-helpdesk-livechat-message kong-helpdesk-livechat-welcome">
                        <?php echo $liveChatWelcomeOnline ?>
                    </div>
                </div>

                <!-- Possible Error -->
                <div id="kong-helpdesk-livechat-enter-chat-form-error" class="kong-helpdesk-livechat-enter-chat-form-error"></div>
    
                <!-- Success -->
                <div id="kong-helpdesk-livechat-enter-chat-form-success" class="kong-helpdesk-livechat-enter-chat-form-success"></div>

                <hr>
                <div class="kong-helpdesk-livechat-enter-chat">
                    <form id="kong-helpdesk-livechat-enter-chat-form" action="<?php echo esc_url($_SERVER['REQUEST_URI']) ?>" method="post">

                    </form>
                </div>
                <!-- Chat messages appear here -->
                <div id="kong-helpdesk-livechat-chat-messages" class="kong-helpdesk-livechat-chat-messages">
                    
                </div>
                <div class="kong-helpdesk-clearfix"></div>
            </div>
            <div id="kong-helpdesk-livechat-footer" class="kong-helpdesk-livechat-footer">
                <form id="kong-helpdesk-livechat-comment-form" class="kong-helpdesk-livechat-comment-form" 
                action="<?php echo esc_url($_SERVER['REQUEST_URI']) ?>" method="post" enctype="multipart/form-data">
                    <input class="kong-helpdesk-livechat-comment-form-message" type="text" name="helpdesk_message" placeholder="<?php echo __('Type your message…', 'kong-helpdesk') ?>">
                    <?php if($this->get_option('liveChatAllowAttachments')) { ?>
                    <div class="kong-helpdesk-livechat-comment-form-attachment">
                        <label for="helpdesk_attachment">
                            <i class="fa fa-paperclip"></i>
                        </label>
                        <input id="helpdesk_attachment" name="helpdesk_attachment" type="file"/>
                    </div>
                    <?php } ?>
                </form>
            </div>
        </div>

        <?php
    }

    public function check_allowed()
    {
        $allowed = true;
        if (!$this->get_option('enableLiveChat')) {
            $allowed = false;
        }

        $logged_in = is_user_logged_in();

        if(!$logged_in && !$this->get_option('liveChatAllowGuest')) {
            $allowed = false;
        }

        if($logged_in) {

            $current_user = wp_get_current_user();

            $roles = $current_user->roles;
            $role = array_shift($roles);
            $notAllowedRoles = apply_filters('kong_helpdesk_livechat_not_allowed_roles', array() );

            if (is_user_logged_in() && (in_array($role, $notAllowedRoles) )) {
                $allowed = false;
            }
        }

        if($this->get_option('liveChatHideAgentsOffline')) {
            $agentsOnline = $this->get_agents_online();
            if($agentsOnline == 0) {
                $allowed = false;
            }
        }

        $allowed = apply_filters('kong_helpdesk_livechat_allowed', $allowed);

        echo json_encode($allowed);
        wp_die();
    }

    public function check_status()
    {
        $return = array(
            'allowed' => true,
            'status' => '',
            'welcome' => '',
            'logged_in' => is_user_logged_in(),
            'enter_chat_fields' => '',
            'online' => 0,
        );

        $username = __('Visitor', 'kong-helpdesk');
        $current_user = wp_get_current_user();
        if(isset($current_user->display_name) && !empty($current_user->display_name)) {
            $username = $current_user->display_name;
        }

        $agentsOnline = $this->get_agents_online();
        $return['online'] = $agentsOnline;

        if($agentsOnline > 0) {
            $return['status'] = sprintf($this->get_option('liveChatStatusOnline'), $username);
            $return['welcome'] = wpautop( sprintf($this->get_option('liveChatWelcomeOnline'), $username));
        } else {
            $return['status'] = sprintf($this->get_option('liveChatStatusOffline'), $username);
            $return['welcome'] = wpautop( sprintf($this->get_option('liveChatWelcomeOffline'), $username));
        }

        $return['enter_chat_fields'] = $this->get_enter_chat_fields();

        echo json_encode($return);
        wp_die();
    }

    public function get_enter_chat_fields()
    {
        $html = "";
        $liveChatButtonText = $this->get_option('liveChatButtonText');

        if(!is_user_logged_in()) {

            $html .= 
            '<fieldset>
                <input type="text" name="helpdesk_username" placeholder="' . __('Your Name', 'kong-helpdesk') . '">
            </fieldset>
            <fieldset>
                <input type="text" name="helpdesk_email" placeholder="' . __('Your Email', 'kong-helpdesk') . '">
            </fieldset>';
        }

        $html .= 
        '<fieldset>
            <input type="text" name="helpdesk_subject" placeholder="' . __('Subject', 'kong-helpdesk') . '">
        </fieldset>
        <fieldset>
            <input type="text" name="helpdesk_message" placeholder="' . __('Type your message…', 'kong-helpdesk') . '">
        </fieldset>

        <div id="kong-helpdesk-livechat-enter-chat-form-ticket-id" class="kong-helpdesk-livechat-enter-chat-form-ticket-id">
            <div>' . __('Or enter your Ticket ID:', 'kong-helpdesk') . '</div>
            <fieldset>
                <input type="text" name="ticket" placeholder="' . __('Ticket ID', 'kong-helpdesk') . '">
            </fieldset>
        </div>
        <input type="submit" value="' . $liveChatButtonText . '">';

        return $html;
    }

    public function allow_duplicate_messages($dupe_id, $commentdata)
    {
        if($dupe_id === NULL) {
            return $dupe_id;
        }

        $post_id = $commentdata['comment_post_ID'];
        $ticket_type = get_post_meta($post_id, 'source', true);
        if($ticket_type == "Chat") {
            return false;
        }

        return $dupe_id;
    }

    public function disable_flood_filter()
    {
        return false;
    }
}