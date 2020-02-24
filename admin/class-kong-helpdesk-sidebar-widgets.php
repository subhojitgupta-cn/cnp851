<?php

class Kong_Helpdesk_Sidebar_Widgets extends Kong_Helpdesk
{
    protected $plugin_name;
    protected $version;

    /**
     * Construct Sidebar
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @param   [type]                       $plugin_name [description]
     * @param   [type]                       $version     [description]
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Init Sidebar Widgetes
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
    }

    /**
     * Register Helpdesk Sidebar
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function register_sidebar()
    {
        $args = array(
            'name' => __('Helpdesk Sidebar', 'kong-helpdesk'),
            'id' => 'helpdesk-sidebar',
            'description' => __('Widgets in this area will be shown on all posts and pages.', 'kong-helpdesk'),
            'before_widget' => '<li id="%1$s" class="widget %2$s">',
            'after_widget'  => '</li>',
            'before_title'  => '<h2 class="widgettitle">',
            'after_title'   => '</h2>',
        );

        register_sidebar($args);
    }

    /**
     * Register Helpdesk Widgets
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @link    https://plugins.db-dzine.com
     * @return  [type]                       [description]
     */
    public function register_widgets()
    {
        register_widget( 'FAQ_Posts' );
        register_widget( 'FAQ_Dynamic_Posts' );
        register_widget( 'FAQ_Live_Search' );
        register_widget( 'FAQ_Topics' );
    }
}
