<?php

class Kong_Helpdesk_Notifications extends Kong_Helpdesk
{
    protected $plugin_name;
    protected $version;

    /**
     * Construct Notifications Class
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $plugin_name [description]
     * @param   [type]                       $version     [description]
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        add_action('admin_menu', array($this,'mail_templates_menu'));
    }

    /**
     * Init Notifications (set Mail template)
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    public function init()
    {
        global $kong_helpdesk_options;
        $this->options = $kong_helpdesk_options;

        // copnmtent of mail templates
        $template_array = ['new_ticket_created','tag_new','tag_changed','comment_added','add_agent','assigned_agent_changed'];
        $new_ticket_created = array('subject'=>'New Ticket created','content'=>'<p><b>A Ticket with the ID [ticket_id] has been created:</b></p>[ticket_content]');
        $tag_new = array('subject'=>'[tag_name] changed to: [new_tag]','content'=>'<p>The [tag_name] changed to: [new_tag]</p>');
        $tag_changed = array('subject'=>'[tag_name] changed from [old_tag] to [new_tag]','content'=>'<p>The [tag_name] has changed from [old_tag] to [new_tag]</p>');
        $comment_added = array('subject'=>'Comment added','content'=>'<p><b>New comment added by [comment_author] :</b></p>[comment_content]');
        $add_agent = array('subject'=>'Assigned to [agent]','content'=>'<p>This ticket has been assigned to [agent]</p>');
        $assigned_agent_changed = array('subject'=>'Assigned to [agent]','content'=>'<p>This ticket has been assigned from [agent1] to [agent2]</p>');



        $tpl = $this->get_option('supportMailTemplate');
        $supportName = $this->get_option('supportName');
        $supportMail = $this->get_option('supportMail');
        $supportLogo = $this->get_option('supportLogo');
        $supportFooter = $this->get_option('supportFooter');

        if($supportLogo['url'] !=''){
            $url = $supportLogo['url'];  
        }else {
            $url = site_url().'/wp-content/plugins/kong-general/public/img/kong-logo.png';
        }

        //die;
        $tpl = str_replace('{{support_name}}', $supportName, $tpl);
        $tpl = str_replace('{{support_logo}}', $url, $tpl);
        $tpl = str_replace('{{footer}}', $supportFooter, $tpl);
        $tpl = str_replace('{{ticket_link_text}}', __('View Ticket', 'kong-helpdesk'), $tpl);

        $this->headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'Reply-To:' . $supportName. ' <' . $supportMail . '>',
            'From:' . $supportName. ' <' . $supportMail . '>',
        );

        $this->tpl = $tpl;

        // add option for mail templates
        foreach($template_array as $template) {

            if(!get_option($template)){
                add_option($template, $$template);
            }
        }
        
       
    }

     // created custom menu
     public function mail_templates_menu() {

        add_menu_page( 
            __( 'MAIL TEMPLATES', 'kong-helpdesk' ),
            'MAIL TEMPLATES',
            'manage_options',
            'mail-templates',
            array($this,'helpdesk_mail_templates'),
            '',
            81
        );      
    }

    public function helpdesk_mail_templates() {

        $mail_template_content = $mail_template_data = '';
        $mail_template_select = 'new_ticket_created';

        
        if ( isset( $_POST['mail_template_nonce'] )) {
            if(! wp_verify_nonce( $_POST['mail_template_nonce'], '_mail_template_nonce' ) ){
                echo ( __( 'Security check', 'kong-helpdesk' ) ); 
            } else {
                $mail_template_select = $_POST['mail_template_select'];
                $mail_template_subject = $_POST['mail_template_subject'];
                $mail_template_content = $_POST['mail_template_content'];
                if( $_POST['mail_template_mode'] == 'modify') {
                    if(get_option($mail_template_select)!=''){
                        $mail_template_data = array('subject'=>$mail_template_subject,'content'=>$mail_template_content);
                        update_option($mail_template_select, $mail_template_data);
                    }
                }
                
            }
        }
        ?>

        <div class="kg_container">
            <div class="kg_block">
                <div class="kg_top kg_primary_bg_color">
                    <h2><i class="fa fa-cog"></i>Mail Templates</h2>
                </div>
                <div class="kg_form_block">
                    <div class="mail-_templates">
                        <form action="<?php echo admin_url('edit.php?page=mail-templates') ?>" name="mail_template_frm" id="mail_template_frm" method="POST">
                            <?php wp_nonce_field( '_mail_template_nonce', 'mail_template_nonce' ); ?>
                            <input type="hidden" name="mail_template_mode" id="mail_template_mode" value="modify" />
                            <div class="kong-helpdesk-row">
                                 <div class="input-field">
                                    <label for="mail_template_select" class="active">Selected Template:</label>
                            <select name="mail_template_select" id="mail_template_select">
                                <option value="new_ticket_created" <?php echo $mail_template_select =='new_ticket_created' ? 'selected="true"' : '';?>>New Ticket Created Notification</option>
                                <option value="tag_new" <?php echo $mail_template_select =='tag_new' ? 'selected="true"' : '';?>>New Tag Notification</option>
                                <option value="tag_changed" <?php echo $mail_template_select =='tag_changed' ? 'selected="true"' : '';?>>Tag Change Notification</option>
                                <option value="comment_added" <?php echo $mail_template_select =='comment_added' ? 'selected="true"' : '';?>>Comment Added Notification</option>
                                <option value="add_agent" <?php echo $mail_template_select =='add_agent' ? 'selected="true"' : '';?>>Add Agent</option>
                                <option value="assigned_agent_changed" <?php echo $mail_template_select =='assigned_agent_changed' ? 'selected="true"' : '';?>>Assigned Agent Changed Notification</option>
                            </select>
                            </div>
                            </div>
                            <div class="kong-helpdesk-row">
                              <div class="input-field">
                            <?php $mailtemplate = get_option( $mail_template_select );
                            $mail_subject = $mailtemplate['subject'];
                            ?>
                            <label for="mail_template_subject" class="active">Subject:</label>
                            <input type="text" id="mail_template_subject" name="mail_template_subject" class="regular-text" value="<?php echo $mail_subject;?>" placeholder="">
                                </div>
                            </div>
                            <div class="kong-helpdesk-row">
                            <?php
                            
                            $meta_content = wpautop(stripslashes($mailtemplate['content']));
                            $editor_id = 'mail_template_content';
                            $settings =   array(
                                'wpautop' => true,              // Whether to use wpautop for adding in paragraphs. Note that the paragraphs are added automatically when wpautop is false.
                                'media_buttons' => false,        // Whether to display media insert/upload buttons
                                'textarea_name' => $editor_id,       // The name assigned to the generated textarea and passed parameter when the form is submitted.
                                'textarea_rows' => 20,          // The number of rows to display for the textarea
                                'tabindex' => '',               // The tabindex value used for the form field
                                'editor_css' => '',             // Additional CSS styling applied for both visual and HTML editors buttons, needs to include <style> tags, can use "scoped"
                                'editor_class' => '',           // Any extra CSS Classes to append to the Editor textarea
                                'teeny' => false,               // Whether to output the minimal editor configuration used in PressThis
                                'dfw' => false,                 // Whether to replace the default fullscreen editor with DFW (needs specific DOM elements and CSS)
                                'tinymce' => true,              // Load TinyMCE, can be used to pass settings directly to TinyMCE using an array
                                'quicktags' => true,            // Load Quicktags, can be used to pass settings directly to Quicktags using an array. Set to false to remove your editor's Visual and Text tabs.
                                'drag_drop_upload' => false     // Enable Drag & Drop Upload Support (since WordPress 3.9)
                            );

                            // display the editor
                            wp_editor( $meta_content, $editor_id, $settings );

                            ?>	
                            </div>
                            <div class="kong-helpdesk-row" style="margin-top:15px">
                                <input type="submit" value="update" />
                            </div>
                        </form>
                    </div>
                </div>                
            </div>
        </div>
        

    <?php }
    
    /**
     * Ticket Created Notification
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $new_status [description]
     * @param   [type]                       $old_status [description]
     * @param   [type]                       $post       [description]
     * @return  [type]                                   [description]
     */
    public function ticket_created($new_status, $old_status, $post)
    {
        if ($post->post_status == "auto-draft") {
            return false;
        }

        if ($post->post_type !== "ticket") {
            return false;
        }

        if ($new_status !== "publish") {
            return false;
        }

        if ($new_status === $old_status) {
            return false;
        }

        if (!$this->get_option('notificationsNewTicket')) {
            return false;
        }

        $maildata = get_option( 'new_ticket_created' );

        $this->subject = $maildata['subject'];

        $this->id = $post->ID;
        $this->title = $post->post_title;
        $content = stripslashes($maildata['content']);
        $content = str_replace(array('[ticket_id]','[ticket_content]'), array($post->ID, $post->post_content), $content);
       
        $this->content = $content;
        $this->link = get_permalink($post->ID);

        $to = array();
        if ($this->get_option('notificationsNewTicketReporter')) {
            $author = get_userdata($post->post_author)->data;
            $to[] = $author->display_name . '<' . $author->user_email . '>';
        }

        if ($this->get_option('notificationsNewTicketAgent')) {
            $assignedAgent = get_post_meta($post->ID, 'agent', true);
            if(!empty($assignedAgent)) {
                $author = get_userdata($assignedAgent);
                $to[] = $author->display_name . '<' . $author->user_email . '>';
            }
        }
        
        $additionalUsers = $this->get_option('notificationsNewTicketUsers');
        if (!empty($additionalUsers)) {
            foreach ($additionalUsers as $additionalUser) {
                $author = get_userdata($additionalUser)->data;
                $to[] = $author->display_name . '<' . $author->user_email . '>';
            }
        }

        $this->to = $to;
        
        $this->sendMail();
    }

    /**
     * Terms changes (status changed)
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $object_id [description]
     * @param   [type]                       $tt_id     [description]
     * @param   [type]                       $taxonomy  [description]
     * @return  [type]                                  [description]
     */
    public function terms_changed($object_id, $tt_id, $taxonomy)
    {
        if (get_post_type($object_id) !== "ticket") {
            return false;
        }

        $newTerm = get_term($tt_id);
        $taxonomy = get_taxonomy($newTerm->taxonomy);

        if (!$this->get_option('notifications' . $taxonomy->label . 'Change')) {
            return false;
        // Only show 1 Taxonomy Change!
        } else {
            $oldTerms = get_the_terms($object_id, $taxonomy->name);
            if (is_array($oldTerms)) {
                foreach ($oldTerms as $oldTerm) {
                    if ($oldTerm->term_id == $newTerm->term_id) {
                        continue;
                    }
                    $post = get_post($object_id);
                    $maildata = get_option( 'tag_changed' );

                    $subject = $maildata['subject'];
                    $subject = str_replace(array('[tag_name]','[old_tag]','[new_tag]'), array($taxonomy->label,$oldTerm->name,$newTerm->name), $subject); 

                    $this->subject =  $subject;

                    $this->id = $post->ID;
                    $this->title = $post->post_title;
                    $content = stripslashes($maildata['content']);
                    $content = str_replace(array('[tag_name]','[old_tag]','[new_tag]'), array($taxonomy->label,$oldTerm->name,$newTerm->name), $content);                
                    $this->content = $content;
                    $this->link = get_permalink($post->ID);

                    $to = array();
                    if ($this->get_option('notifications' . $taxonomy->label . 'ChangeReporter')) {
                        $author = get_userdata($post->post_author)->data;
                        $to[] = $author->display_name . '<' . $author->user_email . '>';
                    }

                    if ($this->get_option('notifications' . $taxonomy->label . 'ChangeAgent')) {
                        $assignedAgent = get_post_meta($post->ID, 'agent', true);
                        if(!empty($assignedAgent)) {
                            $author = get_userdata($assignedAgent);
                            $to[] = $author->display_name . '<' . $author->user_email . '>';
                        }
                    }
                    
                    $additionalUsers = $this->get_option('notifications' . $taxonomy->label . 'ChangeUsers');
                    if (!empty($additionalUsers)) {
                        foreach ($additionalUsers as $additionalUser) {
                            $author = get_userdata($additionalUser)->data;
                            $to[] = $author->display_name . '<' . $author->user_email . '>';
                        }
                    }
                    $this->to = $to;
                    
                    $this->sendMail();
                }
            } else {
                $post = get_post($object_id);
                
                $maildata = get_option( 'tag_new' );

                $subject = $maildata['subject'];
                $subject = str_replace(array('[tag_name]','[new_tag]'), array($taxonomy->label,$newTerm->name), $subject); 

                $this->subject =  $subject;

                $this->id = $post->ID;
                $this->title = $post->post_title;
                $content = stripslashes($maildata['content']);
                $content = str_replace(array('[tag_name]','[new_tag]'), array($taxonomy->label,$newTerm->name), $content);                
                $this->content = $content;
                $this->link = get_permalink($post->ID);

                $to = array();
                if ($this->get_option('notifications' . $taxonomy->label . 'ChangeReporter')) {
                    $author = get_userdata($post->post_author)->data;
                    $to[] = $author->display_name . '<' . $author->user_email . '>';
                }

                if ($this->get_option('notifications' . $taxonomy->label . 'ChangeAgent')) {
                    $assignedAgent = get_post_meta($post->ID, 'agent', true);
                    if(!empty($assignedAgent)) {
                        $author = get_userdata($assignedAgent);
                        $to[] = $author->display_name . '<' . $author->user_email . '>';
                    }
                }
                
                $additionalUsers = $this->get_option('notifications' . $taxonomy->label . 'ChangeUsers');
                if (!empty($additionalUsers)) {
                    foreach ($additionalUsers as $additionalUser) {
                        $author = get_userdata($additionalUser)->data;
                        $to[] = $author->display_name . '<' . $author->user_email . '>';
                    }
                }
                $this->to = $to;
                
                $this->sendMail();
            }
        }

        return true;
    }

    /**
     * Comment Created Notification
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $id      [description]
     * @param   [type]                       $comment [description]
     * @return  [type]                                [description]
     */
    public function comment_created($id, $comment)
    {
        if (!isset($comment->comment_post_ID) || empty($comment->comment_post_ID)) {
            return false;
        }

        $postID = $comment->comment_post_ID;
        $post = get_post($postID);

        if ($post->post_type !== "ticket") {
            return false;
        }

        if (!$this->get_option('notificationsCommentAdded')) {
            return false;
        }

        $maildata = get_option( 'comment_added' );
        $this->subject = $maildata['subject'];

        $this->id = $post->ID;
        $this->title = $post->post_title;
        $content = stripslashes($maildata['content']);
        $content = str_replace(array('[comment_author]','[comment_content]'), array($comment->comment_author,$comment->comment_content), $content);                
        $this->content = $content;
        $this->link = get_permalink($post->ID);

        $to = array();
        
        $additionalUsers = $this->get_option('notificationsCommentAddedUsers');
        if (!empty($additionalUsers)) {
            foreach ($additionalUsers as $additionalUser) {
                $author = get_userdata($additionalUser)->data;
                $to[] = $author->display_name . '<' . $author->user_email . '>';
            }
        }

        // Reporter commented
        if ($comment->user_id == $post->post_author) {
            if ($this->get_option('notificationsCommentAddedAgent')) {
                $assignedAgent = get_post_meta($post->ID, 'agent', true);
                if(!empty($assignedAgent)) {
                    $author = get_userdata($assignedAgent);
                    $to[] = $author->display_name . '<' . $author->user_email . '>';
                }
            }
        // Manager commented
        } else {
            if ($this->get_option('notificationsCommentAddedReporter')) {
                $author = get_userdata($post->post_author)->data;
                $to[] = $author->display_name . '<' . $author->user_email . '>';
            }
        }

        $this->to = $to;
        $this->sendMail();

        return true;
    }

    /**
     * Assigned Agent changed Notification
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $meta_id     [description]
     * @param   [type]                       $object_id   [description]
     * @param   [type]                       $meta_key    [description]
     * @param   [type]                       $_meta_value [description]
     * @return  [type]                                    [description]
     */
    public function agent_changed($meta_id, $object_id, $meta_key, $_meta_value)
    {
        if ($meta_key !== "agent") {
            return false;
        }

        if (!$this->get_option('notificationsAgentChanged')) {
            return false;
        }

        $agentBeforeID = get_post_meta($object_id, 'agent', true);
        $agentAfterID = $_meta_value;

        if ($agentBeforeID == $agentAfterID) {
            return false;
        }

        if (empty($agentBeforeID)) {
            $agent_before_name = __('Unassigned', 'kong-helpdesk');
            $agent_before_email = '';
        } else {
            $agentBefore = get_userdata($agentBeforeID)->data;
            $agent_before_name = $agentBefore->display_name;
            $agent_before_email = $agentBefore->user_email;
        }

        if (empty($agentAfterID)) {
            $agent_after_name = __('Unassigned', 'kong-helpdesk');
            $agent_after_email = '';
        } else {
            $agentAfter = get_userdata($agentAfterID)->data;
            $agent_after_name = $agentAfter->display_name;
            $agent_after_email = $agentAfter->user_email;
        }


        $maildata = get_option( 'assigned_agent_changed' );
        $this->subject = str_replace(array('[agent]'), array($agent_after_name), $maildata['subject']);

        $post = get_post($object_id);
        $this->id = $post->ID;
        $this->title = $post->post_title;
        $content = stripslashes($maildata['content']);
        $content = str_replace(array('[agent1]','[agent2]'), array($agent_before_name,$agent_after_name), $content);                
        $this->content = $content;
        $this->link = get_permalink($post->ID);

        $to = array();
        if ($this->get_option('notificationsAgentChangedReporter')) {
            $author = get_userdata($post->post_author)->data;
            $to[] = $author->display_name . '<' . $author->user_email . '>';
        }

        if ($this->get_option('notificationsAgentChangedAgent')) {
            if ($agent_after_email !== "") {
                $author = $agentAfter;
                $to[] = $agent_after_name . '<' . $agent_after_email . '>';
            }
        }
        
        $additionalUsers = $this->get_option('notificationsAgentChangedUsers');
        if (!empty($additionalUsers)) {
            foreach ($additionalUsers as $additionalUser) {
                $author = get_userdata($additionalUser)->data;
                $to[] = $author->display_name . '<' . $author->user_email . '>';
            }
        }
        $this->to = $to;
        $this->sendMail();
    }

    /**
     * Assigned Agent added Notification
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $meta_id    [description]
     * @param   [type]                       $object_id  [description]
     * @param   [type]                       $meta_key   [description]
     * @param   [type]                       $meta_value [description]
     * @return  [type]                                   [description]
     */
    public function agent_added($meta_id, $object_id, $meta_key, $meta_value)
    {
        if ($meta_key !== "agent") {
            return false;
        }

        if (!$this->get_option('notificationsAgentChanged')) {
            return false;
        }

        $agentID = $meta_value;
        $agent = get_userdata($agentID)->data;

        $maildata = get_option( 'add_agent' );


        $this->subject = str_replace(array('[agent]'), array($agent->display_name), $maildata['subject']);

        $post = get_post($object_id);
        $this->id = $post->ID;
        $this->title = $post->post_title;
        $content = stripslashes($maildata['content']);
        $content = str_replace(array('[agent]'), array($agent->display_name), $content);                
        $this->content = $content;
        $this->link = get_permalink($post->ID);

        $to = array();
        if ($this->get_option('notificationsAgentChangedReporter')) {
            $author = get_userdata($post->post_author)->data;
            $to[] = $author->display_name . '<' . $author->user_email . '>';
        }

        if ($this->get_option('notificationsAgentChangedAgent')) {
            $author = $agent;
            $to[] = $author->display_name . '<' . $author->user_email . '>';
        }
        
        $additionalUsers = $this->get_option('notificationsAgentChangedUsers');
        if (!empty($additionalUsers)) {
            foreach ($additionalUsers as $additionalUser) {
                $author = get_userdata($additionalUser)->data;
                $to[] = $author->display_name . '<' . $author->user_email . '>';
            }
        }
        $this->to = $to;
        $this->sendMail();
    }

    /**
     * Send out notification Mail
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    private function sendMail()
    {
        $subject = '[Ticket: ' . $this->id . '] ' . $this->title . ' – ' . $this->subject;

        $search = array('{{content}}' , '{{ticket_link}}', '{{title}}');
        $replace = array(
            $this->content,
            $this->link,
            '[Ticket: ' . $this->id . '] ' . $this->title
        );

        $content = str_replace($search, $replace, $this->tpl);

        if (wp_mail($this->to, $subject, $content, $this->headers)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Turn off the default Comment added on Post / Page WP functionality
     * Settings > Discussions
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $maybe_notify [description]
     * @param   [type]                       $comment_ID   [description]
     * @return  [type]                                     [description]
     */
    public function disable_default_notifications($maybe_notify, $comment_ID)
    {
        $comment = get_comment($comment_ID);
        $post = get_post($comment->comment_post_ID);

        if ($post->post_type == "ticket") {
            $maybe_notify = false;
        }

        return $maybe_notify;
    }
}
