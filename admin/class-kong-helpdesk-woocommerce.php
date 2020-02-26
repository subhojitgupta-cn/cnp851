<?php

class Kong_Helpdesk_WooCommerce extends Kong_Helpdesk
{
    protected $plugin_name;
    protected $version;

    /**
     * Construct WooCommerce Integration
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @param   string                         $plugin_name
     * @param   string                         $version    
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Init WooCommerce Integration
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
     * Add My Tickets & Submit Ticket to WooCommerce My Account page
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $items [description]
     * @return  [type]                              [description]
     */
    public function my_account_ticket_menu( $items ) 
    {
        if(!$this->get_option('integrationsWooCommerce') || !class_exists('WooCommerce')) {
            return $items;
        }
        $logout = $items['customer-logout']; 
        unset( $items['customer-logout'] );    

        $items['my-tickets'] = __( 'My Tickets', 'kong-helpdesk' );
        $items['new-ticket'] = __( 'Submit Ticket', 'kong-helpdesk' );

        $items['customer-logout'] = $logout;
     
        return $items;
    }

    /**
     * My Account -> My Tickets Endpoint
     * My Account -> New Ticket Endpoint
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    public function my_account_tickets_endpoint() 
    {
        if(!$this->get_option('integrationsWooCommerce') || !class_exists('WooCommerce')) {
            return false;
        }

        add_rewrite_endpoint( 'my-tickets', EP_PAGES );
        add_rewrite_endpoint( 'new-ticket', EP_PAGES );
    }

    /**
     * My Tickets Callback
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    public function my_account_my_tickets_endpoint_content() 
    {
        if(!$this->get_option('integrationsWooCommerce') || !class_exists('WooCommerce')) {
            return false;
        }
        echo do_shortcode('[my_tickets]');
    }

    /**
     * New Ticket Callback
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    public function my_account_new_ticket_endpoint_content() 
    {
        if(!$this->get_option('integrationsWooCommerce') || !class_exists('WooCommerce')) {
            return false;
        }
        echo '<h2>' . __('Submit a new Ticket', 'kong-helpdesk') . '</h2>';
        echo do_shortcode('[new_ticket type="WooCommerce"]');
    }

    /**
     * Custom User Redidrect
     * @deprecated 0.9
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    public function custom_user_redirect( $redirect, $user ) 
    {
        if(!$this->get_option('integrationsWooCommerce') || !class_exists('WooCommerce')) {
            return false;
        }
        // Get the first of all the roles assigned to the user
        $role = $user->roles[0];

        $dashboard = admin_url();
        $myaccount = get_permalink( wc_get_page_id( 'myaccount' ) );
        if( $role == 'administrator' ) {
            //Redirect administrators to the dashboard
            $redirect = $dashboard;
        } elseif ( $role == 'shop-manager' ) {
            //Redirect shop managers to the dashboard
            $redirect = $dashboard;
        } elseif ( $role == 'editor' ) {
            //Redirect editors to the dashboard
            $redirect = $dashboard;
        } elseif ( $role == 'author' ) {
            //Redirect authors to the dashboard
            $redirect = $dashboard;
        } elseif ( $role == 'customer' || $role == 'subscriber' ) {
            //Redirect customers and subscribers to the "My Account" page
            $redirect = $myaccount;
        } else {
            //Redirect any other role to the previous visited page or, if not available, to the home
            $redirect = wp_get_referer() ? wp_get_referer() : home_url();
        }
        return $redirect;
    }

    /**
     * Show admin bar for customers
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    public function show_admin_bar() 
    {

        if(!$this->get_option('integrationsWooCommerce') || !$this->get_option('integrationsWooCommercePreventAdminBar') || !class_exists('WooCommerce')) {
            return false;
        }
        $current_user = wp_get_current_user();
        $roles = $current_user->roles;
        $role = array_shift( $roles );

        $notAllowedRoles = array('subscriber', 'customer', 'subscriber');
        if( in_array($role, $notAllowedRoles) ){
            return true;
        }

        return false;
    }

    /**
     * Prevent Admin access
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $array [description]
     * @return  [type]                              [description]
     */
    public function prevent_admin_access($array)
    {
        if(!$this->get_option('integrationsWooCommerce') || !$this->get_option('integrationsWooCommercePreventAdminAccess') || !class_exists('WooCommerce')) {
            return $array;
        }
        $current_user = wp_get_current_user();
        $roles = $current_user->roles;
        $role = array_shift( $roles );

        $notAllowedRoles = array('subscriber', 'customer', 'subscriber');
        if( in_array($role, $notAllowedRoles) ){
            return false;
        }

        return $array;
    }

    /**
     * [maybe_show_faqs description]
     * @author CN
     * @version 1.0.0
     * @since   1.1.3
     * 
     * @return  [type]                       [description]
     */
    public function maybe_show_faqs($tabs) 
    {
        if(!$this->get_option('integrationsWooCommerce') || !$this->get_option('integrationsWooCommerceFAQ')|| !class_exists('WooCommerce')) {
            return $tabs;
        }

        $tabs[] = array(
            'title' => __('FAQs', 'kong-helpdesk'),
            'priority' => 40,
            'callback' => array($this, 'show_faqs')
        );

        return $tabs;
    }


    public function show_faqs($key, $tab) 
    {
        global $post;

        $term_objs = get_the_terms( $post->ID , 'product_cat' );
        if(empty($term_objs)) {
            return false;
        }

        $term_ids = array();
        foreach ($term_objs as $term_obj) {
            $term_ids[] = $term_obj->term_id;
        }

        $args = array( 
            'post_type' => 'faq', 
            'posts_per_page' => -1, 
            'tax_query' => array(
                array(
                    'taxonomy'      => 'product_cat',
                    'field'         => 'term_id', //This is optional, as it defaults to 'term_id'
                    'terms'         => $term_ids,
                    'operator'      => 'IN', // Possible values are 'IN', 'NOT IN', 'AND'.
                    'include_children' => false
                )
            )
        );
        $faqs = get_posts($args);

        if(empty($faqs)) {
            echo '<p class="woocommerce-helpdesk-product-faq-no-faqs">' . 
                    __('Currently we do not have any frequently asked questions.', 'kong-helpdesk') . 
                '</p>';
            return false;
        }

        echo '<h2 class="woocommerce-helpdesk-product-faq-header">' . __('Frequently Asked Questions', 'kong-helpdesk') . '</h2>';

        foreach ($faqs as $faq) {
            echo '<h3 class="woocommerce-helpdesk-product-faq-title">' . $faq->post_title . '</h3>';
            echo '<div class="woocommerce-helpdesk-product-faq-content">' . wpautop( do_shortcode($faq->post_content) ) . '</div>';
            echo '<hr class="woocommerce-helpdesk-product-faq-divider">';
        }
        return true;
    }
}