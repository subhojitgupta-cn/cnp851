<?php

class Kong_Helpdesk_Admin extends Kong_Helpdesk
{
    protected $plugin_name;
    protected $version;

    /**
     * Construct Helpdesk Admin Class
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
        add_action('admin_menu', array($this,'custom_menu_links'));
    }

    /**
     * Enqueue Admin Styles
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  boolean
     */
    public function enqueue_styles()
    {
        wp_enqueue_style($this->plugin_name.'-admin', plugin_dir_url(__FILE__).'css/kong-helpdesk-admin.css', array(), $this->version, 'all');
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.7.0', 'all');
        wp_enqueue_style('morris', 'https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css', array(), '0.5.1', 'all');
        wp_enqueue_style('Luminous', 'https://cdnjs.cloudflare.com/ajax/libs/luminous-lightbox/1.0.1/luminous-basic.min.css', array(), '1.0.1', 'all');
    }
    
    /**
     * Enqueue Admin Scripts
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  boolean
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script('Luminous', 'https://cdnjs.cloudflare.com/ajax/libs/luminous-lightbox/1.0.1/Luminous.min.js', array('jquery'), '1.0.1', true);
        wp_enqueue_script($this->plugin_name.'-admin', plugin_dir_url(__FILE__).'js/kong-helpdesk-admin.js', array('jquery', 'Luminous'), $this->version, false);
        wp_enqueue_script($this->plugin_name.'-livechat', plugin_dir_url(__FILE__).'js/kong-helpdesk-livechat.js', array('jquery'), $this->version, false);
        wp_enqueue_script('raphael', 'https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js', array('jquery'), '2.1.0', false);
        wp_enqueue_script('morris', 'https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js', array('jquery', 'raphael'), '0.5.1', false);
        
    }

    /**
     * Add admin JS vars
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  boolean
     */
    public function add_admin_js_vars()
    {
    ?>
    <script type='text/javascript'>
        var kong_helpdesk_settings =<?php echo json_encode(array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'liveChatAJAXInterval' => $this->get_option('liveChatAJAXInterval') ?  $this->get_option('liveChatAJAXInterval') : 2000,
        )); ?>
    </script>
    <?php
    }

    /**
     * Load Extensions
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  boolean
     */
    public function load_extensions()
    {
        if(!is_admin() || !current_user_can('administrator') || 
            (defined('DOING_AJAX') && DOING_AJAX && 
            (isset($_POST['action']) && $_POST['action'] != "kong_helpdesk_options_ajax_save") )){
            return false;
        }

        // Load the theme/plugin options
        if (file_exists(plugin_dir_path(dirname(__FILE__)).'admin/options-init.php')) {
            require_once plugin_dir_path(dirname(__FILE__)).'admin/options-init.php';
        }
        return true;
    }

    /**
     * Init
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  boolean
     */
    public function init()
    {
        global $kong_helpdesk_options;

        if(!is_admin() || !current_user_can('administrator') || (defined('DOING_AJAX') && DOING_AJAX)){
            $kong_helpdesk_options = get_option('kong_helpdesk_options');
        }

        $this->options = $kong_helpdesk_options;


        // Knowledge Base Page
        $this->create_page_if_null('Knowledge Base','knowledge-base','[knowledge_base]','FAQKnowledgeBasePage');
        // My Tickets Page
        $this->create_page_if_null('My Tickets','my-tickets','[my_tickets]','supportMyTicketsPage');
        // New Ticket Page
        $this->create_page_if_null('New Ticket','new-ticket','[new_ticket]','supportNewTicketPage');



    }


    public function create_page_if_null($pageTitle,$pageSlug,$content,$option_value) {
        global $wp_rewrite;
        $wp_rewrite->flush_rules();
        if( get_page_by_path( $pageSlug , OBJECT ) == NULL ) {
            $this->create_pages_fly($pageTitle,$pageSlug,$content,$option_value);
        }
    }

    public function create_pages_fly($pageTitle,$pageSlug,$content,$option_value) {
         global $kong_helpdesk_options;
         $this->options = $kong_helpdesk_options;
        $createPage = array(
          'post_title'    => $pageTitle,
          'post_content'  => $content,
          'post_status'   => 'publish',
          'post_author'   => 1,
          'post_type'     => 'page',
          'post_name'     => $pageSlug
        );

        // Insert the post into the database
        $page_id = wp_insert_post( $createPage );


        Redux::setOption('kong_helpdesk_options',$option_value,$page_id);

    }

    /**
     * Maybe redirect reporters
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  redirect_to
     */
    public function maybe_login_redirect($redirect_to, $request, $user)
    {
        if (!$this->get_option('supportRedirectAfterLogin')) {
            return $redirect_to;
        }

        $supportMyTicketsPage = $this->get_option('supportMyTicketsPage');
        if (empty($supportMyTicketsPage)) {
            return $redirect_to;
        }

        if (isset($user->roles) && is_array($user->roles)) {
            if (in_array('subscriber', $user->roles)) {
                return get_permalink($supportMyTicketsPage);
            } else {
                return $redirect_to;
            }
        } else {
            return $redirect_to;
        }
    }  

    /**
     * Remove Menus for Agents & Reporter
     * @author CN
     * @version 1.0.0
     * @since   1.0.3
     * 
     * @return  [type]  
     */
    public function remove_menus()
    {
        $user = wp_get_current_user();

        if (isset($user->roles) && is_array($user->roles)) {
            if (in_array('subscriber', $user->roles) || in_array('agent', $user->roles)) {
                remove_menu_page( 'index.php' );
                remove_submenu_page( 'index.php', 'my-sites.php' );
                remove_menu_page( 'index.php' );                  //Dashboard
                
            }
        }

        // remove menu item for all users
        remove_menu_page( 'index.php' );                   // dashboard
        remove_menu_page( 'jetpack' );                    //Jetpack* 
        remove_menu_page( 'edit.php' );                   //Posts
        //remove_menu_page( 'upload.php' );                 //Media
        //remove_menu_page( 'edit.php?post_type=page' );    //Pages
        remove_menu_page( 'edit-comments.php' );          //Comments
        remove_menu_page( 'themes.php' );                 //Appearance
        remove_menu_page( 'plugins.php' );                //Plugins
        //remove_menu_page( 'users.php' );                  //Users
        remove_menu_page( 'tools.php' );                  //Tools
        remove_menu_page( 'options-general.php' );        //Settings
    }

    // created custom menu
    public function custom_menu_links() {


        /*add_menu_page(
            'Tickets',
            'Tickets',
            'manage_options',
            'edit.php?post_type=ticket',
            '',
            '',
            81
        );*/
        
    }

    /**
     * Remove Admin bar nodes
     * @author CN
     * @version 1.0.0
     * @since   1.0.3
     * 
     * @param   [type]                       $wp_admin_bar [description]
     * @return  [type]                                     [description]
     */
    public function remove_admin_bar_nodes( $wp_admin_bar ) 
    {
        $user = wp_get_current_user();

        if (isset($user->roles) && is_array($user->roles)) {
            if (in_array('subscriber', $user->roles) || in_array('agent', $user->roles)) {
                $wp_admin_bar->remove_node( 'site-name-default' );
                $wp_admin_bar->remove_node( 'site-name' );
                $wp_admin_bar->remove_node( 'dashboard' );
            }
        }

    } 

    /**
     * Redirect Reporter to My Profile
     * Redirect Agents to All Tickets
     * @author CN
     * @version 1.0.0
     * @since   1.0.3
     * 
     * @return  [type]  
     */
    public function redirect_dashboard()
    {


        $actual_link = (explode('/', $_SERVER['REQUEST_URI']));
        $screen = get_current_screen();

        if($screen->id === "dashboard") {

            $user = wp_get_current_user();

            if (in_array('subscriber', $user->roles)) {
                $newLink = admin_url('profile.php');
                header('Location: ' . $newLink);
            }

            if (in_array('agent', $user->roles)) {
                echo "test2";
                $newLink = admin_url('edit.php?post_type=ticket');
                header('Location: ' . $newLink);
            }
        }
        
        return false;
    }

    /**
     * Maybe modify login url
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @link    http://plugins.db-dzine.com
     * @return  redirect_to
     */
    public function maybe_modify_login_url($login_url, $redirect, $force_reauth ) 
    {
        $supportLoginPage = $this->get_option('supportLoginPage');
        if (empty($supportLoginPage)) {
            return $login_url;
        }

        $login_page = get_permalink($supportLoginPage);
        $login_url = add_query_arg( 'redirect_to', $redirect, $login_page );
        return $login_url;
    }
}