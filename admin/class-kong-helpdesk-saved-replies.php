<?php

class Kong_Helpdesk_Saved_Replies_Post_Type extends Kong_Helpdesk
{
    protected $plugin_name;
    protected $version;

    /**
     * Construct Saved Replies Class
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @param   [type]                       $plugin_name [description]
     * @param   [type]                       $version     [description]
     * @param   [type]                       $stop_words  [description]
     */
    public function __construct($plugin_name, $version, $stop_words)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->stop_words = $stop_words;

        add_filter('manage_saved_replies_posts_columns', array($this, 'columns_head'));
        add_action('manage_saved_replies_posts_custom_column', array($this, 'columns_content'), 10, 1);
    }

    /**
     * Init Saved Replies
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function init()
    {
        global $kong_helpdesk_options;
        $this->options = $kong_helpdesk_options;

        if (!$this->get_option('enableSavedReplies')) {
            return false;
        }

        $this->register_saved_reply_post_type();
        $this->register_saved_reply_taxonomy();
    }

    /**
     * Get saved Replies
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function get_saved_replies()
    {
        $reponse = array(
            'status' => 'false',
            'suggessted_replies' => array(),
            'all_replies' => array(),
        );

        $content = strip_tags( $_POST['content'] );
        if (empty($content)) {
            die(json_encode($reponse));
        }

        $words = array_count_values(str_word_count(strtolower($content), 1));
        $words = array_diff($words, $this->stop_words, array(1));
        $words = array_keys($words);

        $args = array(
            'post_type' => 'saved_reply',
            'numberposts' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'saved_reply_tags',
                    'field' => 'slug',
                    'terms' => $words,
                    'operator' => 'IN'
                )
            )
        );

        $suggessted_replies = get_posts($args);

        $args = array(
            'post_type' => 'saved_reply',
            'numberposts' => -1,
            'fields' => 'ids,post_title',
        );
        $all_replies = get_posts($args);

        $reponse = array(
            'status' => 'true',
            'suggessted_replies' => $suggessted_replies,
            'all_replies' => $all_replies,
        );

        die(json_encode($reponse));
    }

    /**
     * Get single save reply to load this into comment form
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function get_saved_reply()
    {
        $reponse = array(
            'status' => 'false',
            'reply' => array(),
        );

        $id = $_POST['id'];
        if (empty($id)) {
            die(json_encode($reponse));
        }

        $saved_reply = get_post($id);
        $reponse = array(
            'status' => 'true',
            'reply' => $saved_reply,
        );

        die(json_encode($reponse));
    }

    /**
     * Register Custom Post type "saved_reply"
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function register_saved_reply_post_type()
    {
        $singular = __('Canned Reply', 'kong-helpdesk');
        $plural = __('Canned Replies', 'kong-helpdesk');

        $labels = array(
            'name' => __('Canned Replies', 'kong-helpdesk'),
            'all_items' => sprintf(__('All %s', 'kong-helpdesk'), $plural),
            'singular_name' => $singular,
            'add_new' => sprintf(__('New %s', 'kong-helpdesk'), $singular),
            'add_new_item' => sprintf(__('Add New %s', 'kong-helpdesk'), $singular),
            'edit_item' => sprintf(__('Edit %s', 'kong-helpdesk'), $singular),
            'new_item' => sprintf(__('New %s', 'kong-helpdesk'), $singular),
            'view_item' => sprintf(__('View %s', 'kong-helpdesk'), $plural),
            'search_items' => sprintf(__('Search %s', 'kong-helpdesk'), $plural),
            'not_found' => sprintf(__('No %s found', 'kong-helpdesk'), $plural),
            'not_found_in_trash' => sprintf(__('No %s found in trash', 'kong-helpdesk'), $plural),
        );

        $args = array(
            'labels' => $labels,
            'public' => false,
            'exclude_from_search' => true,
            'show_ui' => true,
            'menu_position' => 70,
            'rewrite' => array(
                'slug' => 'saved_reply',
                'with_front' => false
            ),
            'query_var' => 'saved_replies',
            'supports' => array('title', 'editor', 'author', 'revisions', 'post-tags'),
            'menu_icon' => 'dashicons-format-chat',
            'capability_type'     => array('saved_reply','saved_replies'),
            'capabilities' => array(
                'publish_posts' => 'publish_saved_replies',
                'edit_posts' => 'edit_saved_replies',
                'edit_others_posts' => 'edit_others_saved_replies',
                'delete_posts' => 'delete_saved_replies',
                'delete_others_posts' => 'delete_others_saved_replies',
                'delete_published_posts' => 'delete_published_saved_replies',
                'read_private_posts' => 'read_private_saved_replies',
                'edit_post' => 'edit_saved_reply',
                'delete_post' => 'delete_saved_reply',
                'read_post' => 'read_saved_reply',
                'edit_published_posts' => 'edit_published_saved_replies'
            ),
            'map_meta_cap' => true
        );

        register_post_type('saved_reply', $args);
    }

    /**
     * Register Saved Reply Categories and Saved Reply Filter Taxonomies.
    *  Saved reply categories => For better internal categorization
    *  Saved reply tags => For suggested reply
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function register_saved_reply_taxonomy()
    {
        // Saved Reply Category Taxonomy
        $singular = __('Category', 'kong-helpdesk');
        $plural = __('Categories', 'kong-helpdesk');

        $labels = array(
            'name' => sprintf(__('%s', 'kong-helpdesk'), $plural),
            'singular_name' => sprintf(__('%s', 'kong-helpdesk'), $singular),
            'search_items' => sprintf(__('Search %s', 'kong-helpdesk'), $plural),
            'all_items' => sprintf(__('All %s', 'kong-helpdesk'), $plural),
            'parent_item' => sprintf(__('Parent %s', 'kong-helpdesk'), $singular),
            'parent_item_colon' => sprintf(__('Parent %s:', 'kong-helpdesk'), $singular),
            'edit_item' => sprintf(__('Edit %s', 'kong-helpdesk'), $singular),
            'update_item' => sprintf(__('Update %s', 'kong-helpdesk'), $singular),
            'add_new_item' => sprintf(__('Add New %s', 'kong-helpdesk'), $singular),
            'new_item_name' => sprintf(__('New %s Name', 'kong-helpdesk'), $singular),
            'menu_name' => sprintf(__('%s', 'kong-helpdesk'), $plural),
        );

        $args = array(
                'labels' => $labels,
                'public' => true,
                'hierarchical' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'update_count_callback' => '_update_post_term_count',
                'query_var' => true,
                'rewrite' => array('slug' => 'saved_reply-topics', 'with_front' => false),
                'capabilities' => array(
                    'manage_terms' => 'manage_saved_reply_topics',
                    'edit_terms' => 'edit_saved_reply_topics',
                    'delete_terms' => 'delete_saved_reply_topics',
                    'assign_terms' => 'assign_saved_reply_topics',
                ),
        );
        register_taxonomy('saved_reply_categories', 'saved_reply', $args);

        // Saved Reply Tag Taxonomy
        $singular = __('Tag', 'kong-helpdesk');
        $plural = __('Tags', 'kong-helpdesk');

        $labels = array(
            'name' => sprintf(__('%s', 'kong-helpdesk'), $plural),
            'singular_name' => sprintf(__('%s', 'kong-helpdesk'), $singular),
            'search_items' => sprintf(__('Search %s', 'kong-helpdesk'), $plural),
            'all_items' => sprintf(__('All %s', 'kong-helpdesk'), $plural),
            'parent_item' => sprintf(__('Parent %s', 'kong-helpdesk'), $singular),
            'parent_item_colon' => sprintf(__('Parent %s:', 'kong-helpdesk'), $singular),
            'edit_item' => sprintf(__('Edit %s', 'kong-helpdesk'), $singular),
            'update_item' => sprintf(__('Update %s', 'kong-helpdesk'), $singular),
            'add_new_item' => sprintf(__('Add New %s', 'kong-helpdesk'), $singular),
            'new_item_name' => sprintf(__('New %s Name', 'kong-helpdesk'), $singular),
            'menu_name' => sprintf(__('%s', 'kong-helpdesk'), $singular),
            'separate_items_with_commas' => __('Separate tags with commas'),
            'add_or_remove_items' => __('Add or remove tags'),
            'choose_from_most_used' => __('Choose from the most used tags'),
        );

        $args = array(
            'hierarchical' => false,
            'labels' => $labels,
            'show_ui' => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var' => true,
            'rewrite' => array( 'slug' => 'saved-reply-tag' ),
            'capabilities' => array(
                'manage_terms' => 'manage_saved_reply_tags',
                'edit_terms' => 'edit_saved_reply_tags',
                'delete_terms' => 'delete_saved_reply_tags',
                'assign_terms' => 'assign_saved_reply_tags',
            ),
        );

        register_taxonomy('saved_reply_tags', 'saved_reply', $args);
    }

    /**
     * Show Copy link on comments
     * Allows users to copy comment_content to saved_reply post_content
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @param   [type]                       $array   [description]
     * @param   [type]                       $comment [description]
     * @return  [type]                                [description]
     */
    public function show_copy_link($array, $comment)
    {
        if (!$this->get_option('enableSavedReplies')) {
            return $array;
        }

        $post = get_post($comment->comment_post_ID);
        if ($post->post_type != 'ticket') {
            return $array;
        }

        $temp = $array;
        $array = array();

        $notifyUrl = wp_nonce_url(admin_url("edit.php?action=copy_comment_to_saved_reply&comment=" . absint($comment->comment_ID)), 'kong_helpdesk_copy_' . $comment->comment_ID);

        $array['copy-comment-to-saved-reply'] = '<a class="button button-primary button-small" href="' . esc_url($notifyUrl) . '">' . __('Create Saved Reply', 'kong-helpdesk') .'</a>';

        $array = array_merge($array, $temp);

        return $array;
    }

    /**
     * Created the new Saved Reply based on Comment
     * @author Daniel Barenkamp
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function copy_comment_to_saved_reply()
    {
        if (empty($_REQUEST['comment'])) {
            wp_die(__('No comment to duplicate has been supplied!', 'kong-helpdesk'));
        }

        // Get the original page
        $id = isset($_REQUEST['comment']) ? absint($_REQUEST['comment']) : '';

        check_admin_referer('kong_helpdesk_copy_' . $id);

        $comment = get_comment($id);

        if (! empty($comment)) {
            $post = new stdClass();
            $post->post_type = 'saved_reply';
            $post->post_author = wp_get_current_user()->ID;
            $post->post_content = $comment->comment_content;

            $new_post_id = wp_insert_post($post);

            wp_redirect(admin_url('post.php?action=edit&post=' . $new_post_id));
            exit();
        } else {
            wp_die(__('Saved Reply creation failed, could not find original comment: ', 'kong-helpdesk') . ' ' . $id);
        }
    }

   public function check($ticket_id, $message, $comment_parent = 0)
    {
        $tags = get_terms('saved_reply_tags');

        if(empty($tags)) {
            return false;
        }

        $tmp = array();
        foreach ($tags as $tag) {
            $tmp[$tag->name] = $tag->term_id;
        }
        $tags = $tmp;

        $message = filter_var($message, FILTER_SANITIZE_STRING);

        $words = array_count_values(str_word_count(strtolower($message), 1));
        $matches = array_intersect_key($tags, $words);
        if(empty($matches)) {
            return false;
        }

        $args = array(
            'numberposts' => 1,
            'post_type' => 'saved_reply',
            'tax_query' => array(
                array(
                  'taxonomy' => 'saved_reply_tags',
                  'field' => 'id',
                  'terms' => $matches, // Where term_id of Term 1 is "1".
                  'include_children' => false
                )
            )
        );
        $saved_replies = get_posts( $args );
        if(empty($saved_replies)) {
            return false;
        }
        $saved_reply = $saved_replies[0];
        $default_author = $this->get_option('savedRepliesAutomaticUser');
        if(empty($default_author)) {
            return false;
        }


        $author = get_userdata($default_author)->data;
        $commentdata = array(
            'comment_post_ID' => $ticket_id,
            'comment_author' => $author->display_name,
            'comment_author_email' => $author->user_email,
            'comment_author_url' => '',
            'comment_content' =>  $saved_reply->post_content,
            'comment_type' => '',
            'comment_parent' => $comment_parent,
            'user_id' => $author->ID,
        );
        
        //Insert new comment and get the comment ID
        //wp_new_comment not working because of preprocess_comment
        $comment_id = wp_insert_comment($commentdata, true);

        if (is_int($comment_id)) {
            return true;
        } else {
            return false;
        }
    }
}
