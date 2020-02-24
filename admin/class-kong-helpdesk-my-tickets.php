<?php

class Kong_Helpdesk_My_Tickets extends Kong_Helpdesk
{
    protected $plugin_name;
    protected $version;

    /**
     * Construct My Tickets Class
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @param   [type]                       $plugin_name      [description]
     * @param   [type]                       $version          [description]
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

    }

    /**
     * Init My Tickets Class
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function init()
    {
        global $kong_helpdesk_options;
        $this->options = $kong_helpdesk_options;

        add_shortcode('my_tickets', array( $this, 'my_tickets' ));
        add_action('admin_menu', array( $this, 'inbox_category_menu' ));
    }

    /**
     * Render my tickets shortcode [my_tickets orderby="date" order="DESC"]
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @param   [type]                       $atts [description]
     * @return  [type]                             [description]
     */
    public function my_tickets($atts)
    {
        if (!is_user_logged_in()) {
            return sprintf(__('Please <a href="%s" title="Login">login to view your tickets</a>', 'kong-helpdesk'), wp_login_url(get_permalink()));
        }

        $args = shortcode_atts(array(
            'orderby' => 'date',
            'order' => 'DESC',
        ), $atts);

        $orderby = $args['orderby'];
        $order = $args['order'];

        $query_args = array(
            'post_type' => 'ticket',
            'orderby' => $orderby,
            'order' => $order,
            'hierarchical' => false,
            'posts_per_page' => -1,
            'suppress_filters' => false,
        );
        
        $current_user = wp_get_current_user();
        $roles = $current_user->roles;
        $role = array_shift($roles);

        if (!current_user_can('administrator')) {
            $query_args['author'] = get_current_user_id();
        }

        $agentRoles = array('agent');
        if (in_array($role, $agentRoles)) {
            unset($query_args['author']);
            $query_args['meta_query'] =  array(
                    array(
                        'key' => 'agent',
                        'value' => get_current_user_id(),
                        'compare' => '='
                    ),
                );
        }

        ob_start();

        echo '<div class="kong-helpdesk kong-helpdesk-my-tickets">';

        $checks = array('both', 'only_ticket');

        if(in_array($this->get_option('supportSidebarDisplay'), $checks) && $this->get_option('supportSidebarPosition') == "left" && !(function_exists('is_account_page') && is_account_page()) ) {
        ?>
        <div class="kong-helpdesk-col-sm-4 kong-helpdesk-sidebar">
            <?php dynamic_sidebar('helpdesk-sidebar'); ?>
        </div>
        <?php
        }

        $checks = array('none', 'only_faq');
        if(in_array($this->get_option('supportSidebarDisplay'), $checks) || (function_exists('is_account_page') && is_account_page()) ) {
            echo '<div class="kong-helpdesk-col-sm-12">';
        } else {
            echo '<div class="kong-helpdesk-col-sm-8">';
        }
        ?>
            <table class="kong-helpdesk-my-tickets-table responsive display nowrap">
                <thead class="kong-helpdesk-my-tickets-header">

                    <?php if($this->get_option('myTicketsShowName')) { ?>
                    <th>
                        <span class="kong-helpdesk-my-tickets-title"><?php echo __('Name', 'kong-helpdesk') ?></span>
                    </th>
                    <?php } ?>

                    <?php if($this->get_option('myTicketsShowDate')) { ?>
                    <th>
                        <span class="kong-helpdesk-my-tickets-date"><?php echo __('Date', 'kong-helpdesk') ?></span>
                    </th>
                    <?php } ?>

                    <?php if($this->get_option('myTicketsShowStatus')) { ?>
                    <th>
                        <span class="kong-helpdesk-my-tickets-status"><?php echo __('Status', 'kong-helpdesk') ?></span>
                    </th>
                    <?php } ?>

                    <?php if($this->get_option('myTicketsShowSystem')) { ?>
                    <th>
                        <span class="kong-helpdesk-my-tickets-system"><?php echo __('Department', 'kong-helpdesk') ?></span>
                    </th>
                    <?php } ?>

                    <?php if($this->get_option('myTicketsShowType')) { ?>
                    <th>
                        <span class="kong-helpdesk-my-tickets-type"><?php echo __('Type', 'kong-helpdesk') ?></span>
                    </th>
                    <?php } ?>

                    <th>
                        <?php echo __('Actions', 'kong-helpdesk') ?></span>
                    </th>
                </thead>
                <?php
                $tickets = get_posts($query_args);

                if (empty($tickets)) {
                    echo __('No tickets submitted yet.', 'kong-helpdesk');
                } else {
                    foreach ($tickets as $ticket) {
                        ?>
                        <tr>
                            <?php if($this->get_option('myTicketsShowName')) { ?>
                            <td>
                                <a href="<?php echo get_permalink($ticket->ID) ?>">
                                    <span class="kong-helpdesk-my-tickets-title"><?php echo sprintf(__('[Ticket: %s]', 'kong-helpdesk'), $ticket->ID) . ' ' . $ticket->post_title ?></span>
                                </a>
                            </td>
                            <?php } ?>

                            <?php if($this->get_option('myTicketsShowDate')) { ?>
                            <td>
                                <span class="kong-helpdesk-my-tickets-date"><?php echo date_i18n( get_option( 'date_format' ), strtotime($ticket->post_date) ) ?></span>
                            </td>
                            <?php } ?>

                            <?php if($this->get_option('myTicketsShowStatus')) { ?>
                            <td>
                            <?php
                                $status = get_the_terms($ticket->ID, 'ticket_status');
                                if (!empty($status)) {
                                    $status_color = get_term_meta($status[0]->term_id, 'kong_helpdesk_color');
                                    if (isset($status_color[0]) && !empty($status_color[0])) {
                                        $status_color = $status_color[0];
                                    } else {
                                        $status_color = '#000000';
                                    }
                                    echo '<span class="kong-helpdesk-my-tickets-status label kong-helpdesk-status-' . $status[0]->slug . '" style="background-color: ' . $status_color . '">' . $status[0]->name . '</span>';
                                }
                                ?>
                            </td>
                            <?php } ?>

                            <?php if($this->get_option('myTicketsShowSystem')) { ?>
                            <td>
                            <?php
                                $system = get_the_terms($ticket->ID, 'ticket_system');
                                if (!empty($system)) {
                                    $system_color = get_term_meta($system[0]->term_id, 'kong_helpdesk_color');
                                    if (isset($system_color[0]) && !empty($system_color[0])) {
                                        $system_color = $system_color[0];
                                    } else {
                                        $system_color = '#000000';
                                    }
                                    echo '<span class="kong-helpdesk-my-tickets-system label kong-helpdesk-system-' . $system[0]->slug . '" style="background-color: ' . $system_color . '">' . $system[0]->name . '</span>';
                                }
                                ?>
                            </td>
                            <?php } ?>

                            <?php if($this->get_option('myTicketsShowType')) { ?>
                            <td>
                            <?php
                                $type = get_the_terms($ticket->ID, 'ticket_type');
                                if (!empty($type)) {
                                    $type_color = get_term_meta($type[0]->term_id, 'kong_helpdesk_color');
                                    if (isset($type_color[0]) && !empty($type_color[0])) {
                                        $type_color = $type_color[0];
                                    } else {
                                        $type_color = '#000000';
                                    }
                                    echo '<span class="kong-helpdesk-my-tickets-type label kong-helpdesk-type-' . $type[0]->slug . '" style="background-color: ' . $type_color . '">' . $type[0]->name . '</span>';
                                }
                                ?>
                            </td>
                            <?php } ?>
                            
                            <td>
                                <a href="<?php echo get_permalink($ticket->ID) ?>"><span class="kong-helpdesk-my-tickets-type"><?php echo __('View', 'kong-helpdesk') ?></span></a>
                            </td>
                        </tr>
                        <?php
                    }
                }

            echo '</table>';
        echo '</div>';


        $checks = array('both', 'only_ticket');
        if(in_array($this->get_option('supportSidebarDisplay'), $checks) && $this->get_option('supportSidebarPosition') == "right" && !(function_exists('is_account_page') && is_account_page()) ) {
        ?>
        <div class="kong-helpdesk-col-sm-4 kong-helpdesk-sidebar">
            <?php dynamic_sidebar('helpdesk-sidebar'); ?>
        </div>
        <?php
        }
        
        $output_string = ob_get_contents();
        ob_end_clean();
        return $output_string;
    }

    public function inbox_category_menu() {

        $parent_menu_slug ='';
        // add inbox menuitems 

        $_taxonomy_array = array(
            array(
                'id'       => 'ticket_status',
                'menu_name'  => 'INBOX',
                'slug'       =>'konginbox',
                'icon'       =>'dashicons-email',
                'position'   => 2,
            ),
            array(
                'id'       => 'ticket_system',
                'menu_name'  => 'FOLDERS',
                'slug'       =>'kongfolder',
                'icon'       =>'dashicons-portfolio',
                'position'   => 3,
            )
            
        );


        foreach ($_taxonomy_array as $taxonomy) {
            $menu_taxonomy_terms = get_terms( array(
                'taxonomy' => $taxonomy['id'],
                'orderby' => 'term_order',
                'order' => 'ASC',
                'hide_empty' => false,
            ) );

            if($menu_taxonomy_terms) {
                $parent_menu_slug = $menu_taxonomy_terms[0]->slug;

                add_menu_page(
                    $taxonomy['menu_name'], 
                    $taxonomy['menu_name'], 
                    'edit_tickets', 
                    $taxonomy['slug'].'-'.$parent_menu_slug,
                    '',
                    $taxonomy['icon'],
                    $taxonomy['position']
                );
                $this->kong_get_menu_items_terms($menu_taxonomy_terms ,$taxonomy,$parent_menu_slug, 'kong_inbox_callback');   
            }
            
        }

             
    }

    // get all terms based on taxonomy name
    public function kong_get_menu_items_terms($menu_taxonomy_terms ,$taxonomy ,$parent_menu_slug, $callbackname) {
        foreach ($menu_taxonomy_terms as $terms) {
            //$term_slug = $terms->slug;
             add_submenu_page( $taxonomy['slug'].'-'.$parent_menu_slug, $terms->name, $terms->name.' <span>'.$terms->count.'</span>',
                'edit_tickets', $taxonomy['slug'].'-'.$terms->slug,array($this,$callbackname));
        }

    }

    // kong inbox callback function for wp-list-table
    public function kong_inbox_callback() { 
 
       $kong_inbox_list_table = new Kong_Inbox_List_Table();

        // Fetch, prepare, sort, and filter our data.
        $kong_inbox_list_table->prepare_items();

        ?>
        <div class="wrap">
        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="inbox-filter" method="get" class="inbox-filter">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            <!-- Now we can render the completed list table -->
            <?php $kong_inbox_list_table->display() ?>
        </form>

</div>
    <?php 
        
     }
}

