<?php

class Kong_Helpdesk_Ticket_Post_Type extends Kong_Helpdesk
{
    protected $plugin_name;
    protected $version;

    /**
     * Construct Custom Ticket Post Type Class
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
    }

    /**
     * Init Ticket post type class
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    public function init()
    {
        global $kong_helpdesk_options;

        $this->options = $kong_helpdesk_options;

        $this->register_ticket_post_type();
        $this->register_ticket_taxonomy();
        $this->add_custom_meta_fields();

        add_action('restrict_manage_posts', array($this, 'filter_post_type_by_taxonomy' ));
        add_filter('parse_query', array($this, 'convert_id_to_term_in_query' ));

    }

    /**
     * Make filtering for custom Taxonomies possible
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    public function filter_post_type_by_taxonomy()
    {
        global $typenow;
        $post_type = 'ticket'; // change to your post type
        
        $taxonomies = array('ticket_status',  'ticket_system'); // change to your taxonomy
        foreach ($taxonomies as $taxonomy) {
            if ($typenow == $post_type) {
                $selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
                $info_taxonomy = get_taxonomy($taxonomy);
                wp_dropdown_categories(array(
                    'show_option_all' => __("Show All {$info_taxonomy->label}"),
                    'taxonomy'        => $taxonomy,
                    'name'            => $taxonomy,
                    'orderby'         => 'name',
                    'selected'        => $selected,
                    'show_count'      => true,
                    'hide_empty'      => true,
                ));
            };
        }
    }

    /**
     * Build the query for custom tax filtering
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $query [description]
     * @return  [type]                              [description]
     */
    public function convert_id_to_term_in_query($query)
    {
        global $pagenow;
        $post_type = 'ticket'; // change to your post type

        $taxonomies = array('ticket_status', 'ticket_type', 'ticket_system'); // change to your taxonomy
        foreach ($taxonomies as $taxonomy) {
            $q_vars    = &$query->query_vars;
            if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0) {
                $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
                $q_vars[$taxonomy] = $term->slug;
            }
        }
    }

    /**
     * Register Ticket Post Type
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    public function register_ticket_post_type()
    {
        $redirect_base = "";
        $supportMyTicketsPage = $this->get_option('supportMyTicketsPage');
        if (!empty($supportMyTicketsPage)) {
            $redirect_base = get_post_field('post_name', $supportMyTicketsPage) . '/';
        }

        $newTicketsCount = get_option('helpdesk_new_tickets_count');

        $singular = __('Ticket', 'kong-helpdesk');
        $plural = __('Tickets', 'kong-helpdesk');

        if(!$newTicketsCount || $newTicketsCount == 0) {
            $all_tickets = sprintf(__('All %s', 'kong-helpdesk'), $plural);
        } else {
            $all_tickets = sprintf(__('All %s', 'kong-helpdesk'), $plural) . 
                        ' <span class="update-plugins count-' . $newTicketsCount . '">
                            <span class="plugin-count" aria-hidden="true">' . $newTicketsCount . '</span><span class="screen-reader-text">' . $newTicketsCount . ' notifications</span></span>';
        }

        $labels = array(
            'name' => __('TICKETS', 'kong-helpdesk'),
            'all_items' => $all_tickets,
            'singular_name' => $singular,
            'add_new' => sprintf(__('New %s', 'kong-helpdesk'), $singular),
            'add_new_item' => sprintf(__('Add New %s', 'kong-helpdesk'), $singular),
            'edit_item' => sprintf(__('Edit %s', 'kong-helpdesk'), $singular),
            'new_item' => sprintf(__('New %s', 'kong-helpdesk'), $singular),
            'view_item' => sprintf(__('View %s', 'kong-helpdesk'), $singular),
            'search_items' => sprintf(__('Search %s', 'kong-helpdesk'), $plural),
            'not_found' => sprintf(__('No %s found', 'kong-helpdesk'), $plural),
            'not_found_in_trash' => sprintf(__('No %s found in trash', 'kong-helpdesk'), $plural),
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'exclude_from_search' => true,
            'show_ui' => true,
            'menu_position' => 70,
            'rewrite' => array(
                'slug' => $redirect_base . 'ticket',
                'with_front' => false
            ),
            'query_var' => 'tickets',
            'supports' => array('title', 'editor', 'author', 'revisions', 'comments', 'page-attributes'),
            'menu_icon' => 'dashicons-sos',
            'capability_type'     => array('ticket','tickets'),
            'capabilities' => array(
                  'create_posts' => 'do_not_allow',
                'publish_posts' => 'publish_tickets',
                'edit_posts' => 'edit_tickets',
                'edit_others_posts' => 'edit_others_tickets',
                'delete_posts' => 'delete_tickets',
                'delete_others_posts' => 'delete_others_tickets',
                'delete_published_posts' => 'delete_published_tickets',
                'read_private_posts' => 'read_private_tickets',
                'edit_post' => 'edit_ticket',
                'delete_post' => 'delete_ticket',
                'read_post' => 'read_ticket',
                'edit_published_posts' => 'edit_published_tickets'
            ),
            'map_meta_cap' => true,
            'taxonomies' => array('ticket_status', 'ticket_type', 'ticket_system'),
        );

        register_post_type('ticket', $args);
    }

    /**
     * Register custom Ticket Taxonomies
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    

    public function register_ticket_taxonomy()
    {
        global $post;
        // Ticket Category
        $singular = __('Tag', 'kong-helpdesk');
        $plural = __('Tags', 'kong-helpdesk');

        $labels = array(
            'name' => $plural,
            'singular_name' => $singular,
            'search_items' => sprintf(__('Search %s', 'kong-helpdesk'), $plural),
            'all_items' => sprintf(__('All %s', 'kong-helpdesk'), $plural),
            'parent_item' => sprintf(__('Parent %s', 'kong-helpdesk'), $singular),
            'parent_item_colon' => sprintf(__('Parent %s:', 'kong-helpdesk'), $singular),
            'edit_item' => sprintf(__('Edit %s', 'kong-helpdesk'), $singular),
            'update_item' => sprintf(__('Update %s', 'kong-helpdesk'), $singular),
            'add_new_item' => sprintf(__('Add New %s', 'kong-helpdesk'), $singular),
            'new_item_name' => sprintf(__('New %s Name', 'kong-helpdesk'), $singular),
            'menu_name' => $plural,
        );

        $args = array(
                'labels' => $labels,
                //'meta_box_cb' => array($this,'ticket_status_meta_box_callback'),
                "meta_box_cb" => [$this,'ticket_status_meta_box_callback'],
                'public' => false,
                'hierarchical' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'update_count_callback' => '_update_post_term_count',
                'query_var' => true,
                'rewrite' => array('slug' => 'ticket-status'),
                'capabilities' => array(
                    'manage_terms' => 'manage_ticket_status',
                    'edit_terms' => 'edit_ticket_status',
                    'delete_terms' => 'delete_ticket_status',
                    'assign_terms' => 'assign_ticket_status',
                ),
        );

        register_taxonomy('ticket_status', 'ticket', $args);

        $default_status_array = array('open','closed','pending','spam');
        $this->add_ticket_default_terms($default_status_array,'ticket_status');

        // Type
        $singular = __('Type', 'kong-helpdesk');
        $plural = __('Types', 'kong-helpdesk');

        $labels = array(
            'name' => $plural,
            'singular_name' => $singular,
            'search_items' => sprintf(__('Search %s', 'kong-helpdesk'), $plural),
            'all_items' => sprintf(__('All %s', 'kong-helpdesk'), $plural),
            'parent_item' => sprintf(__('Parent %s', 'kong-helpdesk'), $singular),
            'parent_item_colon' => sprintf(__('Parent %s:', 'kong-helpdesk'), $singular),
            'edit_item' => sprintf(__('Edit %s', 'kong-helpdesk'), $singular),
            'update_item' => sprintf(__('Update %s', 'kong-helpdesk'), $singular),
            'add_new_item' => sprintf(__('Add New %s', 'kong-helpdesk'), $singular),
            'new_item_name' => sprintf(__('New %s Name', 'kong-helpdesk'), $singular),
            'menu_name' => $plural,
        );

        $args = array(
                'labels' => $labels,
                'public' => false,
                'hierarchical' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'update_count_callback' => '_update_post_term_count',
                'query_var' => true,
                'rewrite' => array('slug' => 'ticket-types'),
                'capabilities' => array(
                    'manage_terms' => 'manage_ticket_type',
                    'edit_terms' => 'edit_ticket_type',
                    'delete_terms' => 'delete_ticket_type',
                    'assign_terms' => 'assign_ticket_type',
                ),
        );

        register_taxonomy('ticket_type', 'ticket', $args);
        $default_type_array = array('bug','feature','question');
        $this->add_ticket_default_terms($default_type_array,'ticket_type');

        
        

        // Ticket System / Project
        $singular = __('Ticket Category', 'kong-helpdesk');
        $plural = __('Ticket Categories', 'kong-helpdesk');

        $labels = array(
            'name' => $plural,
            'singular_name' => $singular,
            'search_items' => sprintf(__('Search %s', 'kong-helpdesk'), $plural),
            'all_items' => sprintf(__('All %s', 'kong-helpdesk'), $plural),
            'parent_item' => sprintf(__('Parent %s', 'kong-helpdesk'), $singular),
            'parent_item_colon' => sprintf(__('Parent %s:', 'kong-helpdesk'), $singular),
            'edit_item' => sprintf(__('Edit %s', 'kong-helpdesk'), $singular),
            'update_item' => sprintf(__('Update %s', 'kong-helpdesk'), $singular),
            'add_new_item' => sprintf(__('Add New %s', 'kong-helpdesk'), $singular),
            'new_item_name' => sprintf(__('New %s Name', 'kong-helpdesk'), $singular),
            'menu_name' => $plural,
        );

        $args = array(
                'labels' => $labels,
                'public' => false,
                'hierarchical' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'update_count_callback' => '_update_post_term_count',
                'query_var' => true,
                'rewrite' => array('slug' => 'ticket-system'),
                'capabilities' => array(
                    'manage_terms' => 'manage_ticket_system',
                    'edit_terms' => 'edit_ticket_system',
                    'delete_terms' => 'delete_ticket_system',
                    'assign_terms' => 'assign_ticket_system',
                ),
        );

        register_taxonomy('ticket_system', 'ticket', $args);

        $default_system_array = array('customization','troubleshooting');
        $this->add_ticket_default_terms($default_system_array,'ticket_system');



        // Ticket Priority
        $singular = __('Priority', 'kong-helpdesk');
        $plural = __('Priorities', 'kong-helpdesk');

        $labels = array(
            'name' => $plural,
            'singular_name' => $singular,
            'search_items' => sprintf(__('Search %s', 'kong-helpdesk'), $plural),
            'all_items' => sprintf(__('All %s', 'kong-helpdesk'), $plural),
            'parent_item' => sprintf(__('Parent %s', 'kong-helpdesk'), $singular),
            'parent_item_colon' => sprintf(__('Parent %s:', 'kong-helpdesk'), $singular),
            'edit_item' => sprintf(__('Edit %s', 'kong-helpdesk'), $singular),
            'update_item' => sprintf(__('Update %s', 'kong-helpdesk'), $singular),
            'add_new_item' => sprintf(__('Add New %s', 'kong-helpdesk'), $singular),
            'new_item_name' => sprintf(__('New %s Name', 'kong-helpdesk'), $singular),
            'menu_name' => $plural,
        );

        $args = array(
                'labels' => $labels,
                'public' => false,
                'hierarchical' => true,
                'show_ui' => true,
                'show_admin_column' => true,
                'update_count_callback' => '_update_post_term_count',
                'query_var' => true,
                'rewrite' => array('slug' => 'ticket-priority'),
                'capabilities' => array(
                    'manage_terms' => 'manage_ticket_priority',
                    'edit_terms' => 'edit_ticket_priority',
                    'delete_terms' => 'delete_ticket_priority',
                    'assign_terms' => 'assign_ticket_priority',
                ),
        );

        register_taxonomy('ticket_priority', 'ticket', $args);
        $default_priority_array = array('low','medium','high');
        $this->add_ticket_default_terms($default_priority_array,'ticket_priority');


        

    }

    // add default ticket status & categories
    public function add_ticket_default_terms($terms_array , $ticket_taxonomy){

       if(get_option( 'default_terms_'.$ticket_taxonomy )!='1') {
            if(taxonomy_exists( $ticket_taxonomy )){
                
                foreach ($terms_array as $term_name) {
                    $term = term_exists( $term_name , $ticket_taxonomy);
                        if ( $term == 0 && $term == null ) {
                            $insert_data = wp_insert_term(ucfirst($term_name), $ticket_taxonomy,    array('slug' => $term_name) );
                            if( ! is_wp_error($insert_data) ){
                                $term_id = $insert_data['term_id'];
                            }
                            if($term_name == 'open'){
                                $this->set_option_for_status('defaultStatus',$term_id);
                            }else if($term_name == 'closed'){
                                $this->set_option_for_status('defaultSolvedStatus',$term_id);
                            }else if($term_name == 'low') {
                                $this->set_option_for_status('defaultPriority',$term_id);
                            }else if($term_name == 'troubleshooting') {
                                $this->set_option_for_status('defaultSystem',$term_id);
                            } else{
                                //do nothing
                            }
                            
                        }
                    
                 }
            }
            update_option('default_terms_'.$ticket_taxonomy, 1);

       }
        
    }

    //set default status as an option
    public function set_option_for_status($option_name,$option_value) {
        global $kong_helpdesk_options;
        if($option_name){
          Redux::setOption('kong_helpdesk_options',$option_name,$option_value);
        }
    }

    // return termid 



    // dropdown html for ticket status taxonomy in  admin area
    public function ticket_status_meta_box_callback( $post, $box ){

  
        $defaults = array( 'taxonomy' => 'category' );
        
        if ( ! isset( $box['args'] ) || ! is_array( $box['args'] ) ) {
            $args = array();
        }
        else {
            $args = $box['args'];
        }

        extract( wp_parse_args($args, $defaults), EXTR_SKIP );
        $tax = get_taxonomy( $taxonomy );
        ?>
        
       
        <div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
            <?php 
              $name = ($taxonomy == 'category' ) ? 'post_category' : 'tax_input[' . $taxonomy . ']';
                echo "<input type='hidden' name='{$name}[]' value='0' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.
                
                $term_obj = wp_get_object_terms( $post->ID, $taxonomy ); //_log($term_obj[0]->term_id)
                if ( ! empty( $term_obj ) ) {
                    wp_dropdown_categories( array(
                        'taxonomy'      => $taxonomy,
                        'hide_empty'        => 0,
                        'name'          => "{$name}[]",
                        'selected'      => $term_obj[0]->term_id,
                        'orderby'       => 'term_id',
                        'hierarchical'      => 0,
                        'show_option_none'  => 'Select Tag',
                        'class'         => 'widefat'
                    ) );
                }?>
        </div>
    <?php } 
    

    /**
     * Add custom ticket metaboxes
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $post_type [description]
     * @param   [type]                       $post      [description]
     */
    public function add_custom_metaboxes($post_type, $post)
    {
        add_meta_box('kong-helpdesk-agent', __('Ticket', 'kong-helpdesk') . ' ' . $post->ID, array($this, 'short_information'), 'ticket', 'side', 'high');
        add_meta_box('kong-helpdesk-merge', __('Merge Ticket', 'kong-helpdesk'), array($this, 'merge_ticket_metabox'), 'ticket', 'side', 'default');
        add_meta_box('kong-helpdesk-attachments', __('Attachments', 'kong-helpdesk'), array($this, 'attachments'), 'ticket', 'normal', 'default');

        if($this->get_option('enableSupportRating')) {
            add_meta_box('kong-helpdesk-feedback', __('Feedback:', 'kong-helpdesk'), array($this, 'feedback_metabox'), 'ticket', 'normal', 'low');
        }
    }

    /**
     * Display Metabox Short Information
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    public function short_information()
    {
        global $post;

        wp_nonce_field(basename(__FILE__), 'kong_helpdesk_meta_nonce');

        $this->get_meta_taxonomies($post);
        echo '<div class="kong-helpdesk-container">';
            $this->get_created($post);
            $this->get_assigned($post);
        echo '</div>';
        echo '<label for="website_url"><small>' . __('Website:', 'kong-helpdesk') . '</small></label>';
        $website_url = get_post_meta($post->ID, 'website_url', true);
        echo '<input name="website_url" type="text" value="' . $website_url . '" style="width: 100%;">';

        if( (get_post_meta($post->ID, 'source', true) == "Envato")) {
            $this->get_envato($post);
        }

        if (class_exists('WooCommerce') && (get_post_meta($post->ID, 'source', true) == "WooCommerce")) {
            $this->get_woocommerce($post);
        }
    }

    /**
     * Display Metabox Merge Ticket
     * @author CN
     * @version 1.0.0
     * @since   1.4.3
     * 
     * @return  [type]  
     */
    public function merge_ticket_metabox()
    {
        global $post;

        $query_args = array(
            'post_type' => 'ticket',
            'orderby' => 'date',
            'order' => 'DESC',
            'hierarchical' => false,
            'posts_per_page' => -1,
            'post__not_in' => array( $post->ID )
        );
        $tickets = get_posts($query_args);
        
        $mergeURL = admin_url("edit.php");

        if(!empty($tickets)) {
            echo '<select name="merge_ticket_destination">';
            echo '<option value="">' . __('Select Ticket', 'kong-helpdesk') . '</option>';
            foreach ($tickets as $ticket) {
                echo '<option value="' . $ticket->ID . '">' . $ticket->post_title . ' (ID: ' . $ticket->ID . ')</option>';
            }
            echo '</select>';
        }
        ?>
        <button class="button button-primary button-large" href="<?php echo esc_url($mergeURL); ?>"><?php _e('Merge Now', 'kong-helpdesk'); ?></button>
        <?php
    }

    /**
     * Copy a ticket content to an FAQ
     * @author CN
     * @version 1.0.0
     * @since   1.4.3
     * 
     * @return  [type]  
     */
    public function merge_ticket($sourceID, $destinationID)
    {

        if (empty($sourceID) || empty($destinationID)) {
            wp_die(__('No ticket to duplicate has been supplied!', 'kong-helpdesk'));
        }

        $sourcePost = get_post($sourceID);

        if (!empty($sourcePost)) {

            $attachment_ids = get_posts(array(
                'post_type' => 'attachment',
                'numberposts' => -1,
                'post_parent' => $sourcePost->ID,
            ));

            if(!empty($attachment_ids)) {
                foreach ($attachment_ids as $attachment_id) {
                    $attachment_id->post_parent = $destinationID;
                    wp_update_post($attachment_id);
                }
            }

            wp_insert_comment(array(
                'comment_content' => $sourcePost->post_content,
                'comment_post_ID' => $destinationID,
                'user_id' => $sourcePost->post_author

            ));

            $comments = get_comments('post_id=' . $sourceID);
            if(!empty($comments)) {
                foreach ($comments as $comment) {
                    $comment = (array) $comment;
                    $comment['comment_post_ID'] = $destinationID;
                    wp_insert_comment($comment);
                }
            }

            wp_delete_post($sourceID);
            wp_redirect(admin_url('post.php?action=edit&post=' . $destinationID));
            exit();
        } else {
            wp_die(__('Could not Merge Ticket.', 'kong-helpdesk'));
        }
    }

    /**
     * Get Meta Taxonomies
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $post [description]
     * @return  [type]                             [description]
     */
    private function get_meta_taxonomies($post)
    {
        $status = get_the_terms($post->ID, 'ticket_status');
        if (!empty($status)) {
            $status_color = get_term_meta($status[0]->term_id, 'kong_helpdesk_color');
            if (isset($status_color[0]) && !empty($status_color[0])) {
                $status_color = $status_color[0];
            } else {
                $status_color = '#000000';
            }
            if (!empty($status)) {
                echo '<span class="kong-helpdesk-label kong-helpdesk-status-' . $status[0]->slug . '" style="background-color: ' . $status_color . '">' . $status[0]->name .'</span> ';
            }
        }

        $system = get_the_terms($post->ID, 'ticket_system');
        if (!empty($system)) {
            $system_color = get_term_meta($system[0]->term_id, 'kong_helpdesk_color');
            if (isset($system_color[0]) && !empty($system_color[0])) {
                $system_color = $system_color[0];
            } else {
                $system_color = '#000000';
            }
            if (!empty($system)) {
                echo '<span class="kong-helpdesk-label kong-helpdesk-system-' . $system[0]->slug . '" style="background-color: ' . $system_color . '">' . $system[0]->name .'</span> ';
            }
        }

        $type = get_the_terms($post->ID, 'ticket_type');
        if (!empty($type)) {
            $type_color = get_term_meta($type[0]->term_id, 'kong_helpdesk_color');
            if (isset($type_color[0]) && !empty($type_color[0])) {
                $type_color = $type_color[0];
            } else {
                $type_color = '#000000';
            }
            if (!empty($type)) {
                echo '<span class="kong-helpdesk-label kong-helpdesk-type-' . $type[0]->slug . '" style="background-color: ' . $type_color . '">' . $type[0]->name .'</span> ';
            }
        }

        $priority = get_the_terms($post->ID, 'ticket_priority');
        if (!empty($priority)) {
            $priority_color = get_term_meta($priority[0]->term_id, 'kong_helpdesk_color');
            if (isset($priority_color[0]) && !empty($priority_color[0])) {
                $priority_color = $priority_color[0];
            } else {
                $priority_color = '#000000';
            }
            if (!empty($priority)) {
                echo '<span class="kong-helpdesk-label kong-helpdesk-priority-' . $priority[0]->slug . '" style="background-color: ' . $priority_color . '">' . $priority[0]->name .'</span> ';
            }
        }
    }

    /**
     * Get the created information
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $post [description]
     * @return  [type]                             [description]
     */
    private function get_created($post)
    {
        echo '<hr>';
        $author = get_userdata($post->post_author)->data;
        echo '<div class="kong-helpdesk-row">';
            echo '<div class="kong-helpdesk-col-sm-8">';
                echo '<small>' . __('Created on: ', 'kong-helpdesk') . date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($post->post_date)) . '</small><br/>';
                echo '<small>' . __('Created by: ', 'kong-helpdesk') . $author->display_name . '</small><br/>';
                echo '<small> ' . $author->user_email . '</small><br/>';
            echo '</div>';
            echo '<div class="kong-helpdesk-col-sm-4">';
                echo get_avatar($post->post_author, 50);
            echo '</div>';
        echo '</div>';
        echo '<hr>';
    }

    /**
     * Get the assigned Information
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $post [description]
     * @return  [type]                             [description]
     */
    private function get_assigned($post)
    {
        $args = array(
            'role__in' => array('agent', 'administrator', 'shop_manager')
        );
        $agents = get_users($args);
        $current_agent_id = get_post_meta($post->ID, 'agent', true);
        if (!empty($current_agent_id)) {
            $current_agent = get_userdata($current_agent_id)->data;

            echo '<div class="kong-helpdesk-row">';
                echo '<div class="kong-helpdesk-col-sm-8">';
                    echo '<small>' . __('Assigned to: ', 'kong-helpdesk') . $current_agent->display_name . '</small><br/>';
                    echo '<small> ' . $current_agent->user_email . '</small><br/>';
                echo '</div>';
                echo '<div class="kong-helpdesk-col-sm-4">';
                    echo get_avatar($current_agent_id, 50);
                echo '</div>';
            echo '</div>';
        }
        echo '<div class="kong-helpdesk-row">';
            echo '<div class="kong-helpdesk-col-sm-12">';
                echo '<select name="agent" id="agent" class="widefat">';
                echo '<option value="">' . __('Unassigned', 'kong-helpdesk') . '</option>';
                foreach ($agents as $agent) {
                    $agent_id = $agent->data->ID;
                    $agent_name = $agent->data->display_name;

                    $selected = '';
                    if ($agent_id == $current_agent_id) {
                        $selected = 'selected="selected"';
                    }

                    echo '<option value="' . $agent_id . '" ' . $selected . '>' . $agent_name . '</option>';
                }
                echo '</select>';
                echo '<hr>';
            echo '</div>';
        echo '</div>';
    }

    /**
     * Get the Envato Information
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $post [description]
     * @return  [type]                             [description]
     */
    private function get_envato($post)
    {
        $purchase_code = get_post_meta($post->ID, 'purchase_code', true);
        echo '<hr>Envato<br/>';
        if (!empty($purchase_code)) {
            $purchase_data = $this->verifyPurchaseCode($purchase_code);

            if (empty($purchase_data)) {
                echo '<small style="color: red;">' . __('Purchase Code could not be verified!', 'kong-helpdesk') . '</small>';
            } else {
                echo '<a href="' . $purchase_data->item->url . '" target="_blank"><img src="' . $purchase_data->item->previews->landscape_preview->landscape_url . '" style="width: 100%; max-width: 100%;"></a><br/>';
                echo '<small>' . __('Item: ', 'kong-helpdesk') . '<a href="' . $purchase_data->item->url . '" target="_blank">' . $purchase_data->item->name . '</a></small><br/>';
                echo '<small>' . __('License: ', 'kong-helpdesk') . $purchase_data->license . '</small><br/>';
                echo '<small>' . __('Support Until:', 'kong-helpdesk') . $purchase_data->supported_until . '</a></small>';
            }
        }
        echo '<br/>';
        echo '<label for="purchase_code"><small>Purchase Code:</small></label>';
        $purchase_code = get_post_meta($post->ID, 'purchase_code', true);
        echo '<input name="purchase_code" type="text" value="' . $purchase_code . '" style="width: 100%;">';
    }

    /**
     * Verify Envato purchase Code
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $code [description]
     * @return  [type]                             [description]
     */
    private function verifyPurchaseCode($code)
    {
        if (!empty($this->get_option('integrationsEnvatoAPIKey')) && (!empty($this->get_option('integrationsEnvatoUsername')))) {
            $token = $this->get_option('integrationsEnvatoAPIKey');
            $username = $this->get_option('integrationsEnvatoUsername');
        } else {
            return false;
        }

        $envato = new DB_Envato($token);

        $purchase_data = $envato->call('/market/author/sale?code=' . $code);
        
        if (isset($purchase_data->error)) {
            return false;
        }

        return $purchase_data;
    }

    /**
     * Get WooCommerce Pages
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $post [description]
     * @return  [type]                             [description]
     */
    private function get_woocommerce($post)
    {
        $this->get_orders($post);
        $this->get_products($post);
    }

    /**
     * Get WooCommerce Order Pages
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $post [description]
     * @return  [type]                             [description]
     */
    public function get_orders($post)
    {
        $orders = get_posts(array(
            'posts_per_page' => -1,
            'post_type'   => wc_get_order_types(),
            'post_status' => array_keys(wc_get_order_statuses()),
        ));

        $current_order = get_post_meta($post->ID, 'order', true);

        if (!empty($current_order)) {
            $order = wc_get_order($current_order);

            echo '<a href="'. admin_url('post.php?post=' . absint($current_order) . '&action=edit') .'" ><b>' . $order->post->post_title . '</b></a><br/>';
            echo 'ID: #' . $order->ID . '<br>';
            echo 'Order Status: ' . $order->post->post_status . '<br><br>';

            echo '<b>Products:</b><br>';
            foreach ($order-> get_items() as $item_key => $item_values) :
                $item_data = $item_values->get_data();

                $product_name = $item_data['name'];
                $quantity = $item_data['quantity'];

                echo $product_name . ' (Quantity: ' . $quantity . ')';
            endforeach;
        }
    }

    /**
     * Get WooCommerce Products
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $post [description]
     * @return  [type]                             [description]
     */
    public function get_products($post)
    {
        $products = get_posts(array(
            'posts_per_page' => -1,
            'post_type'   => 'product',
            'orderby'          => 'title',
            'order'            => 'ASC',
        ));

        $current_product = get_post_meta($post->ID, 'product', true);

        if (!empty($products)) {
            echo '<select name="product" class="form-control">';
                echo '<option value="">' . __('Select Product', 'kong-helpdesk') . '</option>';
            foreach ($products as $product) {
                $sku = get_post_meta($product->ID, '_sku', true);
                $selected = '';
                if ($current_product == $product->ID) {
                    $selected = 'selected="selected"';
                }

                if (empty($sku)) {
                    echo '<option value="' . $product->ID . '" ' . $selected . '>' . $product->post_title . '</option>';
                } else {
                    echo '<option value="' . $product->ID . '" ' . $selected . '>' . $product->post_title . ' (' . $sku . ')</option>';
                }
            }
            echo '</select>';
        }
    }

    /**
     * Get Attachments Metabox
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    public function attachments()
    {
        global $post;

        $attachments = get_posts(array(
            'post_type' => 'attachment',
            'numberposts' => -1,
            'post_parent' => $post->ID,
        ));
        
        if ($attachments) {
            echo '<ul>';

            foreach ($attachments as $attachment) {
                $attachment_id = $attachment->ID;
                $full_url = wp_get_attachment_url($attachment_id);
                $thumb_url = wp_get_attachment_thumb_url($attachment_id);

                $image_mime_types = array(
                    'image/png',
                    'image/jpeg',
                    'image/jpeg',
                    'image/jpeg',
                    'image/gif',
                    'image/bmp',
                    'image/vnd.microsoft.icon',
                    'image/tiff',
                    'image/tiff',
                    'image/svg+xml',
                    'image/svg+xml',
                );

                $isImage = false;
                if(in_array($attachment->post_mime_type, $image_mime_types)) {
                    $isImage = true;
                }

                $link = "";
                if($isImage) {
                    $link .= '<a class="is-image" href="' . $full_url . '" target="_blank">';
                        $link .= '<img src="' . $thumb_url . '" alt="">';
                    $link .= '</a>';
                } else {
                    $link .= '<a href="' . $full_url . '" target="_blank">';
                        $link .= '<i class="fa fa-download"></i> ' . $attachment->post_title;
                    $link .= '</a>';
                }
                
                echo '<li>' . $link . '</li>';
            }
            echo '</ul>';
        }
        
    }

    /**
     * Content of the Feedback metabox
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    public function feedback_metabox()
    {
        global $post;

        $feedback = get_post_meta($post->ID, 'feedback', true);

        if (empty($feedback)) {
            echo __("No feedback available", 'kong-helpdesk');
            return false;
        }
        echo $feedback;
    }

    /**
     * Save Custom Metaboxes
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $post_id [description]
     * @param   [type]                       $post    [description]
     * @return  [type]                                [description]
     */
    public function save_custom_metaboxes($post_id, $post)
    {
        global $post;
        
        if (!is_object($post)) {
            return;
        }

        // Is the user allowed to edit the post or page?
        if (!current_user_can('edit_post', $post->ID)) {
            return $post->ID;
        }

        if (!isset($_POST['kong_helpdesk_meta_nonce']) || !wp_verify_nonce($_POST['kong_helpdesk_meta_nonce'], basename(__FILE__))) {
            return;
        }

        if(isset($_POST['merge_ticket_destination']) && !empty($_POST['merge_ticket_destination'])) {
            $this->merge_ticket($post->ID, $_POST['merge_ticket_destination']);
        }

        $ticket_meta['agent'] = isset($_POST['agent']) ? $_POST['agent'] : '';
        $ticket_meta['website_url'] = isset($_POST['website_url']) ? $_POST['website_url'] : '';
        $ticket_meta['purchase_code'] = isset($_POST['purchase_code']) ? $_POST['purchase_code'] : '';
        $ticket_meta['order'] = isset($_POST['order']) ? $_POST['order'] : '';
        $ticket_meta['product'] = isset($_POST['product']) ? $_POST['product'] : '';

        
        // Add values of $ticket_meta as custom fields
        foreach ($ticket_meta as $key => $value) { // Cycle through the $ticket_meta array!
            if ($post->post_type == 'revision') {
                return; // Don't store custom data twice
            }
            
            $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
            update_post_meta($post->ID, $key, $value);
        }
    }

    /**
     * Add Custom Meta Field Color to System, Type, Status
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * 
     */
    public function add_custom_meta_fields()
    {
        $prefix = 'kong_helpdesk_';
        $custom_taxonomy_meta_config = array(
            'id' => 'ticket_meta_box',
            'title' => 'Ticket Meta Box',
            'pages' => array('ticket_system', 'ticket_type', 'ticket_status', 'ticket_priority'),
            'context' => 'side',
            'fields' => array(),
            'local_images' => false,
            'use_with_theme' => false,
        );

        $custom_taxonomy_meta_fields = new Tax_Meta_Class($custom_taxonomy_meta_config);
        $custom_taxonomy_meta_fields->addText($prefix.'color', array('name' => __('Color', 'kong-helpdesk')));
        $custom_taxonomy_meta_fields->Finish();
    }

    /**
     * Access check for Tickets
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    public function access()
    {
        global $post;

        if (empty($post)) {
            if (isset($_GET['post']) && !empty($_GET['post'])) {
                $post = get_post($_GET['post']);
            }
        }

        if (!is_object($post)) {
            return true;
        }

        if ($post->post_type == 'ticket') {
            if (!is_user_logged_in()) {
                wp_die(
                    sprintf(__('Please <a href="%s" title="Login">login to view your tickets</a>', 'kong-helpdesk'), wp_login_url(get_permalink())),
                    '', 404);
                return false;
            }

            $current_user = wp_get_current_user();
            $roles = $current_user->roles;
            $role = array_shift($roles);
            $notAllowedRoles = array('subscriber', 'subscriber', 'customer');

            if (intval($post->post_author) === intval($current_user->ID)) {
                return true;
            }

            if ($role == "agent") {
                $assignedAgent = get_post_meta($post->ID, 'agent', true);
                if (intval($assignedAgent) !== intval($current_user->ID)) {
                    wp_die('You are not assigned as an agent.', '', 404);
                }
            }

            if (in_array($role, $notAllowedRoles)) {
                wp_die('Not your ticket', '', 404);
            }
        }
    }

    /**
     * Filter not authore ones 
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $query [description]
     * @return  [type]                              [description]
     */
    public function filter_not_author_ones($query)
    {

        $current_user = wp_get_current_user();
        $roles = $current_user->roles;
        $role = array_shift($roles);

        if (isset($query->query['post_type']) && ($query->query['post_type'] == "ticket")) {
            if ($role == "subscriber") {
                $query->set('author', $current_user->ID);
            }
            if ($role == "agent") {
                $query->set('meta_query', array(
                    array(
                        'key' => 'agent',
                        'value' => get_current_user_id(),
                        'compare' => '='
                    ),
                ));
            }
        }
    }

    /**
     * Modify the Title of Tickets to always have the ID in Front
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $title [description]
     * @param   [type]                       $id    [description]
     * @return  [type]                              [description]
     */
    public function modify_title($title, $id)
    {
        if (empty($title)) {
            return $title;
        }

        if (get_post_type($id) !== "ticket") {
            return $title;
        }

        return sprintf( __('[Ticket: %s] %s', 'kong-helpdesk'), $id, $title);
    }

    /**
     * Show all Authors
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $query_args [description]
     * @param   [type]                       $r          [description]
     */
    public function add_subscribers_to_dropdown( $query_args, $r ) {
        $query_args['who'] = '';
        return $query_args;
    }

    /**
     * Load custom FAQ Topics Template
     * Override this via a file in your theme called archive-faq_topic.php
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $template [description]
     * @return  [type]                                 [description]
     */
    public function ticket_template( $template ) 
    {
        global $post;

        if($this->get_option('useThemesTemplate')) {
            return $template;
        }

        if(is_single()) {
            if($post->post_type == "ticket") {
                $theme_files = array('single-ticket.php', 'kong-helpdesk/single-ticket.php');
                $exists_in_theme = locate_template($theme_files, false);
                if ( $exists_in_theme != '' ) {
                    return $exists_in_theme;
                } else {
                    return plugin_dir_path(__FILE__) . 'views/single-ticket.php';
                }
            }
        }
        return $template;
    }

    public function ticket_columns( $columns ) 
    {

        $columns = array();
        $columns["cb"] = '<input type="checkbox" />';
        $columns["title"] = __('Ticket', 'kong-helpdesk');
        $columns["from"] = __('From', 'kong-helpdesk');
        $columns["assigned"] = __('Assigned To', 'kong-helpdesk');
        $columns["taxonomy-ticket_status"] = __('Status', 'kong-helpdesk');
        //$columns["satisfied"] = __('Satisfied', 'kong-helpdesk');
        //$columns["taxonomy-ticket_type"] = __('Type', 'kong-helpdesk');
        $columns["taxonomy-ticket_system"] = __('Department', 'kong-helpdesk');
        //$columns["taxonomy-ticket_priority"] = __('Priority', 'kong-helpdesk');
        //$columns["comments"] = __('<span class="vers comment-grey-bubble" title="Comments"><span class="screen-reader-text">Comments</span></span>', 'kong-helpdesk');
        $columns["date"] = __('Date', 'kong-helpdesk');

        return $columns;
    }

    public function ticket_columns_content( $column, $post_id )
     {
        global $post;

        switch( $column ) {
            case 'assigned' :

                $agentID = get_post_meta( $post_id, 'agent', true );
                if ( empty( $agentID ) ) {
                    echo __( 'Unassigned' );
                } else {
                    $author = get_userdata($agentID);
                    $url = admin_url('edit.php?post_type=ticket&author=' . $author->ID);
                    echo '<div class="kong-helpdesk-row">';
                        echo '<div class="kong-helpdesk-col-sm-3">';
                            echo get_avatar($agentID, 50, '', '', array('class' => 'helpdesk-avatar'));
                        echo '</div>';
                        echo '<div class="kong-helpdesk-col-sm-8">';
                            printf( '<a href="%s">%s</a>', $url, $author->display_name );
                            printf( '<br/><small>%s</small>',  $author->user_email);
                        echo '</div>';
                    echo '</div>';
                }
                break;
            case 'from' :

                if ( empty( $post->post_author ) ) {
                    echo __( 'No Author', 'kong-helpdesk' );
                } else {
                    $author = get_userdata($post->post_author)->data;
                    $url = admin_url('edit.php?post_type=ticket&author=' . $author->ID);
                    echo '<div class="kong-helpdesk-row">';
                        echo '<div class="kong-helpdesk-col-sm-3">';
                            echo get_avatar($post->post_author, 50, '', '', array('class' => 'helpdesk-avatar'));
                        echo '</div>';
                        echo '<div class="kong-helpdesk-col-sm-8">';
                            printf( '<a href="%s">%s</a>', $url, $author->display_name );
                            printf( '<br/><small>%s</small>',  $author->user_email);
                        echo '</div>';
                    echo '</div>';
                }
                break;
            case 'satisfied' :

                $satisfied = get_post_meta($post->ID, 'satisfied', true);

                if($satisfied == "yes") {
                    echo __('<i class="fa fa-smile-o fa-2x" style="color: #4CAF50;"></i>');
                } elseif($satisfied == "no") {
                    echo __('<i class="fa fa-frown-o fa-2x" style="color: #F44336;"></i>');
                } else {
                    echo __('<i class="fa fa-pause fa-2x" style="color: #aeaeae;"></i>');
                }
                break;
            default :
                break;
        }
    }

    public function update_new_tickets_count($post_ID)
    {
        if (get_post_type($post_ID) !== "ticket") {
            return false;
        }

        $defaultStatus = $this->get_option('defaultStatus');
        if(!$defaultStatus) {
            return false;
        }

        $newTicketsArgs = array(
            'post_type' => 'ticket',
            'posts_per_page' => -1, 
            'tax_query' => array(
                array(
                    'taxonomy'      => 'ticket_status',
                    'field'         => 'term_id', //This is optional, as it defaults to 'term_id'
                    'terms'         => $defaultStatus,
                    // 'operator'      => 'IN', // Possible values are 'IN', 'NOT IN', 'AND'.
                )
            )
        );
        $newTickets = get_posts($newTicketsArgs);

        update_option('helpdesk_new_tickets_count', count($newTickets));
    }

    public function close_old_tickets()
    {
        if(!$this->get_option('supportCloseTicketsAutomatically')) {
            return false;
        }

        $supportCloseTicketsAutomaticallyDays = $this->get_option('supportCloseTicketsAutomaticallyDays');
        if($supportCloseTicketsAutomaticallyDays < 1) {
            return false;
        }

        $defaultSolvedStatus = $this->get_option('defaultSolvedStatus');
        if(!$defaultSolvedStatus) {
            return false;
        }

        $allTicketsQuery = array(
            'post_type' => 'ticket',
            'posts_per_page' => -1,
        );
        $allTickets = get_posts($allTicketsQuery);

        if(empty($allTickets)) {
            return false;
        }

        foreach ($allTickets as $ticket) {

            $args = array(
                'number' => '1',
                'post_id' => $ticket->ID
            );
            $comments = get_comments($args);
            if(isset($comments[0]) && !empty($comments[0]) && is_object($comments[0])) {
                $lastUpdate = $comments[0]->comment_date;
            } else {
                $lastUpdate = $ticket->post_modified;
            }

            if(empty($lastUpdate)) {
                continue;
            }

            $from = strtotime($lastUpdate);
            $today = time();
            $difference = $today - $from;
            $daysOpen = floor($difference / 86400);  // (60 * 60 * 24)

            if($daysOpen > $supportCloseTicketsAutomaticallyDays) {
                wp_set_object_terms($ticket->ID, intval($defaultSolvedStatus), 'ticket_status');
            }
        }
    }


    public function ticket_solved_btn()
    {
        if(!isset($_POST['helpdesk_ticket_solved'])) {
            return false;
        }

        if(!isset($_POST['helpdesk_ticket']) || empty($_POST['helpdesk_ticket'])) {
            return false;
        }

        $ticket_id = absint($_POST['helpdesk_ticket']);
        $ticket = get_post($ticket_id);

        $current_user = wp_get_current_user();
        if (intval($ticket->post_author) !== intval($current_user->ID)) {
            return false;
        }

        $defaultSolvedStatus = $this->get_option('defaultSolvedStatus');
        if(!$defaultSolvedStatus) {
            return false;
        }

        wp_set_object_terms($ticket->ID, intval($defaultSolvedStatus), 'ticket_status');
    }


}