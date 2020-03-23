<?php

    /**
     * For full documentation, please visit: http://docs.reduxframework.com/
     * For a more extensive sample-config file, you may look at:
     * https://github.com/reduxframework/redux-framework/blob/master/sample/sample-config.php
     */

if (! class_exists('Redux')) {
    return;
}

    // This is your option name where all the Redux data is helpdeskd.
    $opt_name = "kong_helpdesk_options";

    /**
     * ---> SET ARGUMENTS
     * All the possible arguments for Redux.
     * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
     * */

    $theme = wp_get_theme(); // For use with some settings. Not necessary.

    $args = array(
        'opt_name' => 'kong_helpdesk_options',
        'use_cdn' => true,
        'dev_mode' => false,
        'display_name' => __('Kong Helpdesk', 'kong-helpdesk'),
        'display_version' => '1.0.0',
        'page_title' => __('Kong Helpdesk', 'kong-helpdesk'),
        'update_notice' => true,
        'intro_text' => '',
        'footer_text' => '&copy; ' . date('Y') . ' weLaunch',
        'admin_bar' => true,
        'menu_type' => 'menu',
        'menu_title' => __('HELPDESK OPTION', 'kong-helpdesk'),
        'allow_sub_menu' => false,
       /* 'page_parent' => 'edit.php?post_type=ticket',
        'page_parent_post_type' => 'ticket',*/
        'customizer' => false,
        'default_mark' => '*',
        'hints' => array(
            'icon_position' => 'right',
            'icon_color' => 'lightgray',
            'icon_size' => 'normal',
            'tip_style' => array(
                'color' => 'light',
            ),
            'tip_position' => array(
                'my' => 'top left',
                'at' => 'bottom right',
            ),
            'tip_effect' => array(
                'show' => array(
                    'duration' => '500',
                    'event' => 'mouseover',
                ),
                'hide' => array(
                    'duration' => '500',
                    'event' => 'mouseleave unfocus',
                ),
            ),
        ),
        'output' => true,
        'output_tag' => true,
        'settings_api' => true,
        'cdn_check_time' => '1440',
        'compiler' => true,
        'page_permissions' => 'manage_options',
        'save_defaults' => true,
        'show_import_export' => true,
        'database' => 'options',
        'transient_time' => '3600',
        'network_sites' => true,
    );

    Redux::setArgs($opt_name, $args);

    /*
     * ---> END ARGUMENTS
     */

    /*
     * ---> START HELP TABS
     */

    $tabs = array(
        array(
            'id'      => 'help-tab',
            'title'   => __('Information', 'kong-helpdesk'),
            'content' => __('<p>Need support? Please use the comment function on codecanyon.</p>', 'kong-helpdesk')
        ),
    );
    Redux::setHelpTab($opt_name, $tabs);

    // Set the help sidebar
    // $content = __( '<p>This is the sidebar content, HTML is allowed.</p>', 'kong-helpdesk' );
    // Redux::setHelpSidebar( $opt_name, $content );


    /*
     * <--- END HELP TABS
     */


    /*
     *
     * ---> START SECTIONS
     *
     */

    Redux::setSection($opt_name, array(
        'title'  => __('Kong Helpdesk', 'kong-helpdesk'),
        'id'     => 'general',
        'desc'   => __('Need support? Please use the comment function on codecanyon.', 'kong-helpdesk'),
        'icon'   => 'el el-home',
    ));

    Redux::setSection($opt_name, array(
        'title'      => __('General', 'kong-helpdesk'),
        'id'         => 'general-settings',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enable',
                'type'     => 'checkbox',
                'title'    => __('Enable', 'kong-helpdesk'),
                'subtitle' => __('Enable Helpdesk.', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'       => 'inbox_post_per_page',
                'type'     => 'spinner',
                'title'    => __('Inbox Pagination Limit', 'kong-helpdesk'),
                'subtitle' => __('Total numbers of tickets per page ', 'kong-helpdesk'),
                'default'  => '10',
                'min'      => '1',
                'step'     => '1',
                'max'      => '100',
            ),
            array(
                'id'       => 'supportLoginPage',
                'type'     => 'select',
                'title'    => __('Login Page', 'kong-helpdesk'),
                'subtitle' => __('This will be the page, where the login button will link to.', 'kong-helpdesk'),
                'data'     => 'pages'
            ),
            array(
                'id'       => 'supportMyTicketsPage',
                'type'     => 'select',
                'title'    => __('My Tickets Page', 'kong-helpdesk'),
                'subtitle' => __('Make sure the [my_tickets] shortcode is placed there. After saving go to settings > permalinks and save.', 'kong-helpdesk'),
                'data'     => 'pages'
            ),
            array(
                'id'       => 'supportNewTicketPage',
                'type'     => 'select',
                'title'    => __('New Ticket Page', 'kong-helpdesk'),
                'subtitle' => __('Make sure the [new_ticket] shortcode is placed there.', 'kong-helpdesk'),
                'data'     => 'pages'
            ),
            array(
                'id'       => 'supportRedirectAfterLogin',
                'type'     => 'checkbox',
                'title'    => __('Redirect reporters to My Tickets page', 'kong-helpdesk'),
                'subtitle' => __('After login all reporters will be redirected to the my tickets page.', 'kong-helpdesk'),
                'default'  => '0',
            ),
            array(
                'id'       => 'supportOnlyLoggedIn',
                'type'     => 'checkbox',
                'title'    => __('Only Logged In', 'kong-helpdesk'),
                'subtitle' => __('Allow Ticket creation via Forms only when User is logged in.', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'       => 'supportSendLoginCredentials',
                'type'     => 'checkbox',
                'title'    => __('Send Login credentials', 'kong-helpdesk'),
                'subtitle' => __('Send out the login credentials when a new account has been created for a new user.', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'       => 'supportSidebarDisplay',
                'type'     => 'select',
                'title'    => __('Sidebar Display', 'kong-helpdesk'),
                'subtitle' => __('Where you want to show the sidebar.', 'kong-helpdesk'),
                'options' => array(
                    'none' => __('None', 'kong-helpdesk'),
                    'only_faq' => __('Only in Knowledge Base', 'kong-helpdesk'),
                    'only_ticket' => __('Only for Ticket pages', 'kong-helpdesk'),
                    'both' => __('Show in Ticket & FAQ pages', 'kong-helpdesk'),
                ),
                'default' => 'both',
            ),
            array(
                'id'       => 'supportSidebarPosition',
                'type'     => 'select',
                'title'    => __('Sidebar Position', 'kong-helpdesk'),
                'subtitle' => __('Left or Right sidebar.', 'kong-helpdesk'),
                'options' => array(
                    'left' => __('Left', 'kong-helpdesk'),
                    'right' => __('Right', 'kong-helpdesk'),
                    ),
                'default' => 'left',
            ),
            array(
                'id'       => 'excel2007',
                'type'     => 'checkbox',
                'title'    => __('Use Excel 2007', 'kong-helpdesk'),
                'subtitle' => __('If you can not work with xlsx (Excel 2007 and higher) files, check this. You then can work with normal .xls files.', 'kong-helpdesk'),
                'default'  => '0',
            ),
            array(
                'id'   => 'export',
                'type' => 'info',
                'desc' => '<div style="text-align:center;">
                    <a href="' . get_admin_url() . 'edit.php?post_type=stores&page=kong_helpdesk_options_options&export-tickets=all" class="btn btn-golden btn-kong">' . __('Export all Tickets', 'kong-helpdesk') . '</a>
                    </div>'
            ),
        )
    ));

    $defaults = array(
        array(
            'id'     =>'defaultStatus',
            'type' => 'select',
            'data' => 'terms',
            'args' => array(
                'taxonomies' => array( 'ticket_status' ),
                'hide_empty' => false,
            ),
            'title' => __('Default New Ticket Status', 'kong-helpdesk'),
            'subtitle' => __('The default status for new tickets.', 'kong-helpdesk'),
        ),
        array(
            'id'     =>'defaultSolvedStatus',
            'type' => 'select',
            'data' => 'terms',
            'args' => array(
                'taxonomies' => array( 'ticket_status' ),
                'hide_empty' => false,
            ),
            'title' => __('Default Solved (closed) Status', 'kong-helpdesk'),
            'subtitle' => __('Set the name of the Solved (closed) ticket status.', 'kong-helpdesk'),
        ),
        array(
            'id'     =>'defaultType',
            'type' => 'select',
            'data' => 'terms',
            'args' => array(
                'taxonomies' => array( 'ticket_type' ),
                'hide_empty' => false,
            ),
            'title' => __('Default Type', 'kong-helpdesk'),
            'subtitle' => __('The default type for new tickets.', 'kong-helpdesk'),
        ),
        array(
            'id'     =>'defaultPriority',
            'type' => 'select',
            'data' => 'terms',
            'args' => array(
                'taxonomies' => array( 'ticket_priority' ),
                'hide_empty' => false,
            ),
            'title' => __('Default priority', 'kong-helpdesk'),
            'subtitle' => __('The default priority for new tickets.', 'kong-helpdesk'),
        ),
        array(
            'id'     =>'defaultSystem',
            'type' => 'select',
            'data' => 'terms',
            'args' => array(
                'taxonomies' => array( 'ticket_system' ),
                'hide_empty' => false,
            ),
            'title' => __('Default Department', 'kong-helpdesk'),
            'subtitle' => __('The default Department for new tickets.', 'kong-helpdesk'),
        ),
        array(
            'id'       => 'defaultAgent',
            'type'     => 'select',
            'title'    => __('Default Agent', 'kong-helpdesk'),
            'subtitle' => __('The default user for new tickets.', 'kong-helpdesk'),
            'data' => 'users',
        )
    );

    $terms = get_terms( array(
        'hide_empty' => false,
    ) );
    $departmentDefaultAgents = array();
    foreach ($terms as $term) {
        if($term->taxonomy == "ticket_system") {
            $departmentDefaultAgents[] = array(
                'id'       => 'defaultAgent' . $term->term_id,
                'type'     => 'select',
                'title'    => sprintf( __('Default Agent for %s', 'kong-helpdesk'), $term->name),
                'subtitle' => sprintf( __('The default user for %s.', 'kong-helpdesk'), $term->name),
                'data' => 'users',
            );
        }
    }
    $defaults = array_merge($defaults, $departmentDefaultAgents);

    Redux::setSection($opt_name, array(
        'title'      => __('Defaults', 'kong-helpdesk'),
        'id'         => 'default-settings',
        'subsection' => true,
        'fields'     => $defaults
    ));


    Redux::setSection($opt_name, array(
        'title'      => __('Desktop Notifications', 'kong-helpdesk'),
        'id'         => 'desktop-notifications',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enableDesktopNotifications',
                'type'     => 'checkbox',
                'title'    => __('Enable Desktop Notifications', 'kong-helpdesk'),
                'default'  => '0',
            ),
            array(                
                'id'       => 'desktopNotificationsAJAXInterval',
                'type'     => 'spinner',
                'title'    => __('AJAX Interval', 'kong-helpdesk'),
                'subtitle' => __('Increase the interval (in miliseconds) to save server performance.', 'kong-helpdesk'),
                'default'  => '2000',
                'min'      => '1',
                'step'     => '10',
                'max'      => '9999999999',
                'required' => array('enableDesktopNotifications','equals','1'),
            ),
            array(
                'id'       => 'desktopNotificationsWelcomeTitle',
                'type'     => 'text',
                'title'    => __('Welcome Title', 'kong-helpdesk'),
                'default'  => __('Welcome to Helpdesk', 'kong-helpdesk'),
                'required' => array('enableDesktopNotifications','equals','1'),
            ),
            array(
                'id'       => 'desktopNotificationsWelcomeText',
                'type'     => 'text',
                'title'    => __('Welcome Text', 'kong-helpdesk'),
                'default'  => __('How can we help you today?', 'kong-helpdesk'),
                'required' => array('enableDesktopNotifications','equals','1'),
            ),
            array(                
                'id'       => 'desktopNotificationsWelcomeTimeout',
                'type'     => 'spinner',
                'title'    => __('Welcome Timout', 'kong-helpdesk'),
                'subtitle' => __('Time in minutes when the welcome message should pop up again.', 'kong-helpdesk'),
                'default'  => '120',
                'min'      => '1',
                'step'     => '10',
                'max'      => '9999999999',
                'required' => array('enableDesktopNotifications','equals','1'),
            ),
            array(
                'id'        =>'desktopNotificationsIcon',
                'type'      => 'media',
                'url'       => true,
                'title'     => __('Set an icon', 'kong-helpdesk'),
                'subtitle'  => __('The icon must be in square format.', 'kong-helpdesk'),
                'args'      => array(
                    'teeny'            => false,
                ),
                'required' => array('enableDesktopNotifications','equals','1'),
            ),
            array(
                'id'        =>'desktopNotificationsTimeout',
                'title'     => __('Timeout', 'kong-helpdesk'),
                'subtitle'  => __('Set the time when the notification automatically hides.', 'kong-helpdesk'),
                'type'     => 'spinner',
                'default'  => '4000',
                'min'      => '100',
                'step'     => '100',
                'max'      => '20000',
                'required' => array('enableDesktopNotifications','equals','1'),
            ),
        )
    ));


    Redux::setSection($opt_name, array(
        'title'      => __('FAQ – Knowledge Base', 'kong-helpdesk'),
        'id'         => 'faq-settings',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enableFAQ',
                'type'     => 'checkbox',
                'title'    => __('Enable the FAQs Knowledge Base', 'kong-helpdesk'),
                'subtitle' => __('Custom Post Type FAQ will be created. Ticket can be copied into a new FAQ.', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'       => 'FAQKnowledgeBasePage',
                'type'     => 'select',
                'title'    => __('Knowledge Base Page', 'kong-helpdesk'),
                'subtitle' => __('Make sure the [knowledge_base] shortcode is placed there. After saving go to settings > permalinks and save.', 'kong-helpdesk'),
                'data'     => 'pages',
                'required' => array('enableFAQ','equals','1'),
            ),
            array(
                'id'       => 'FAQShowSearch',
                'type'     => 'checkbox',
                'title'    => __('Show Search', 'kong-helpdesk'),
                'default'  => '1',
                'required' => array('enableFAQ','equals','1'),
            ),
            array(
                'id'        =>'FAQSearchMaxResults',
                'title'     => __('Maximum Live Search Results', 'kong-helpdesk'),
                'subtitle'  => __('Set maximum results for FAQ live search.', 'kong-helpdesk'),
                'type'     => 'spinner',
                'default'  => '4',
                'min'      => '1',
                'step'     => '1',
                'max'      => '10',
                'required' => array('enableFAQ','equals','1'),
            ),
            array(
                'id'       => 'FAQShowViews',
                'type'     => 'checkbox',
                'title'    => __('Show Views Count', 'kong-helpdesk'),
                'default'  => '1',
                'required' => array('enableFAQ','equals','1'),
            ),
            array(
                'id'       => 'FAQRatingEnable',
                'type'     => 'checkbox',
                'title'    => __('Show Rating', 'kong-helpdesk'),
                'default'  => '1',
                'required' => array('enableFAQ','equals','1'),
            ),
            array(
                'id'       => 'FAQRatingDisableDislikeButton',
                'type'     => 'checkbox',
                'title'    => __('Disable the Rating dislike button', 'kong-helpdesk'),
                'default'  => '0',
                'required' => array('enableFAQ','equals','1'),
            ),
            array(
                'id'       => 'FAQMasonry',
                'type'     => 'checkbox',
                'title'    => __('Masonry', 'kong-helpdesk'),
                'default'  => '0',
                'required' => array('enableFAQ','equals','1'),
            ),
            array(
                'id'       => 'FAQLayout',
                'type'     => 'select',
                'title'    => __('Topics Layout', 'kong-helpdesk'),
                'options'  => array(
                    'list' => __('List Layout', 'kong-helpdesk'),
                    'boxed' => __('Boxed Layout', 'kong-helpdesk'),
                ),
                'default' => 'boxed',
                'required' => array('enableFAQ','equals','1'),
            ),
            array(
                'id'        =>'FAQColumns',
                'title'     => __('Topics Columns', 'kong-helpdesk'),
                'subtitle'  => __('Default Topic columns. Needs to be deviable by 12.', 'kong-helpdesk'),
                'type'     => 'spinner',
                'default'  => '2',
                'min'      => '1',
                'step'     => '1',
                'max'      => '10',
                'required' => array('enableFAQ','equals','1'),
            ),
            array(
                'id'       => 'FAQItemMasonry',
                'type'     => 'checkbox',
                'title'    => __('Item Masonry', 'kong-helpdesk'),
                'default'  => '0',
                'required' => array('enableFAQ','equals','1'),
            ),
            array(
                'id'        =>'FAQItemColumns',
                'title'     => __('Item Columns', 'kong-helpdesk'),
                'subtitle'  => __('Default Topic columns. Needs to be deviable by 12.', 'kong-helpdesk'),
                'type'     => 'spinner',
                'default'  => '1',
                'min'      => '1',
                'step'     => '1',
                'max'      => '12',
                'required' => array('enableFAQ','equals','1'),
            ),
            array(
                'id'        =>'FAQLoggedInHideInKnowledgeBase',
                'type'     => 'checkbox',
                'title'     => __('Hide FAQ excerpts for not logged in', 'kong-helpdesk'),
                'subtitle'  => __('Hide all FAQ excerpts for not logged in users in the Knwoledge Base', 'kong-helpdesk'),
                'default'   => '0',
                'required'  => array('enableFAQ','equals','1'),
            ),
            array(
                'id'       => 'FAQSingleLoggedIn',
                'type'     => 'checkbox',
                'title'    => __('Hide All Single FAQs pages', 'kong-helpdesk'),
                'subtitle'  => __('Hide all Single FAQ pages for not logged in users.', 'kong-helpdesk'),
                'default'  => '0',
                'required' => array('enableFAQ','equals','1'),
            ),
            array(
                'id'     =>'FAQLoggedInOnly',
                'type' => 'select',
                'data' => 'posts',
                'args' => array('post_type' => array('faq'), 'posts_per_page' => -1),
                'multi' => true,
                'title' => __('Hide some FAQs', 'kong-helpdesk'), 
                'subtitle' => __('Show the following FAQs only to logged in Users:', 'kong-helpdesk'),
                'required' => array('enableFAQ','equals','1'),
            ),
            array(
                'id'     =>'FAQTopicsLoggedInOnly',
                'type' => 'select',
                'data' => 'terms',
                'args' => array(
                    'taxonomies' => array( 'faq_topics' ),
                    'hide_empty' => false,
                ),
                'multi' => true,
                'title' => __('Hide some Topics', 'kong-helpdesk'), 
                'subtitle' => __('Hide complete Topics from not logged in users:', 'kong-helpdesk'),
                'required' => array('enableFAQ','equals','1'),
            ),
        )
    ));

    Redux::setSection($opt_name, array(
        'title'      => __('Form Fields', 'kong-helpdesk'),
        'id'         => 'fields-settings',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'fieldsSimple',
                'type'     => 'section',
                'title'    => __('Simple Form Fields', 'kong-helpdesk'),
                'subtitle' => __('Fields for the Simple [new_ticket] form. ', 'kong-helpdesk'),
                'indent'   => false,
            ),
            array(
                'id'       => 'fieldsSimpleSystem',
                'type'     => 'checkbox',
                'title'    => __('Systems / Project Select Field', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'       => 'fieldsSimplePriority',
                'type'     => 'checkbox',
                'title'    => __('Priority Select Field', 'kong-helpdesk'),
                'default'  => '0',
            ),
            array(
                'id'       => 'fieldsSimpleTypes',
                'type'     => 'checkbox',
                'title'    => __('Types Select Field', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'       => 'fieldsSimpleWebsiteURL',
                'type'     => 'checkbox',
                'title'    => __('Website URL', 'kong-helpdesk'),
                'default'  => '0',
            ),
            array(
                'id'       => 'fieldsSimpleAttachments',
                'type'     => 'checkbox',
                'title'    => __('Attachments field', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'       => 'fieldsEnvato',
                'type'     => 'section',
                'title'    => __('Envato Form Fields', 'kong-helpdesk'),
                'subtitle' => __('Fields for the Simple [new_ticket type="Envato"] form. ', 'kong-helpdesk'),
                'indent'   => false,
            ),
            array(
                'id'       => 'fieldsEnvatoSystem',
                'type'     => 'checkbox',
                'title'    => __('Systems / Project Select Field', 'kong-helpdesk'),
                'default'  => '0',
            ),
            array(
                'id'       => 'fieldsEnvatoPriority',
                'type'     => 'checkbox',
                'title'    => __('Priority Select Field', 'kong-helpdesk'),
                'default'  => '0',
            ),
            array(
                'id'       => 'fieldsEnvatoTypes',
                'type'     => 'checkbox',
                'title'    => __('Types Select Field', 'kong-helpdesk'),
                'default'  => '0',
            ),
            array(
                'id'       => 'fieldsEnvatoWebsiteURL',
                'type'     => 'checkbox',
                'title'    => __('Website URL', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'       => 'fieldsEnvatoAttachments',
                'type'     => 'checkbox',
                'title'    => __('Attachments field', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'       => 'fieldsEnvatoPurchaseCode',
                'type'     => 'checkbox',
                'title'    => __('Purchase Code', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'       => 'fieldsEnvatoItems',
                'type'     => 'checkbox',
                'title'    => __('Envato Items', 'kong-helpdesk'),
                'subtitle' => __('A selected Item will be overwritten when an item is found within the purchase code.', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'       => 'fieldsWooCommerce',
                'type'     => 'section',
                'title'    => __('WooCommerce Form Fields', 'kong-helpdesk'),
                'subtitle' => __('Fields for the Simple [new_ticket type="WooCommerce"] form. ', 'kong-helpdesk'),
                'indent'   => false,
            ),
            array(
                'id'       => 'fieldsWooCommerceSystem',
                'type'     => 'checkbox',
                'title'    => __('Systems / Project Select Field', 'kong-helpdesk'),
                'default'  => '0',
            ),
            array(
                'id'       => 'fieldsWooCommercePriority',
                'type'     => 'checkbox',
                'title'    => __('Priority Select Field', 'kong-helpdesk'),
                'default'  => '0',
            ),
            array(
                'id'       => 'fieldsWooCommerceTypes',
                'type'     => 'checkbox',
                'title'    => __('Types Select Field', 'kong-helpdesk'),
                'default'  => '0',
            ),
            array(
                'id'       => 'fieldsWooCommerceWebsiteURL',
                'type'     => 'checkbox',
                'title'    => __('Website URL', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'       => 'fieldsWooCommerceAttachments',
                'type'     => 'checkbox',
                'title'    => __('Attachments field', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'       => 'fieldsWooCommerceProducts',
                'type'     => 'checkbox',
                'title'    => __('Product Support (lists all Products)', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'       => 'fieldsWooCommerceOrders',
                'type'     => 'checkbox',
                'title'    => __('Order Support (lists all Orders)', 'kong-helpdesk'),
                'default'  => '1',
            ),
        )
    ));

    Redux::setSection($opt_name, array(
        'title'      => __('Inbox', 'kong-helpdesk'),
        'id'         => 'mail-settings',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enableInbox',
                'type'     => 'checkbox',
                'title'    => __('Enable Inbox', 'kong-helpdesk'),
                'subtitle' => __('Enable this to allow ticket creation via Email.', 'kong-helpdesk'),
                'default'  => '0',
            ),
            array(
                'id'       => 'mailAccountRecurrence',
                'type'     => 'select',
                'title'    => __('Recurrence', 'kong-helpdesk'),
                'subtitle' => __('How often should Emails be fetched.', 'kong-helpdesk'),
                'options' => array(
                    'hourly' => __('Hourly', 'kong-helpdesk'),
                    'twicedaily' => __('Twice daily', 'kong-helpdesk'),
                    'daily' => __('Daily', 'kong-helpdesk'),
                    ),
                'default' => 'hourly',
                'required' => array('enableInbox','equals','1'),
            ),
            array(
                'id'       => 'mailAccountSection',
                'type'     => 'section',
                'title'    => __('Mail Account Settings', 'kong-helpdesk'),
                'subtitle' => __('Settings for your Mail Account.', 'kong-helpdesk'),
                'indent'   => false,
                'required' => array('enableInbox','equals','1'),
            ),
            array(
                'id'       => 'mailAccountEmail',
                'type'     => 'text',
                'title'    => __('Email', 'kong-helpdesk'),
                'required' => array('enableInbox','equals','1'),
            ),
            array(
                'id'          => 'mailAccountUser',
                'type'        => 'password',
                'username'    => true,
                'title'       => __('Username & Password', 'kong-helpdesk'),
                'placeholder' => array(
                    'username'   => __('Enter your Username', 'kong-helpdesk'),
                    'password'   => __('Enter your Password', 'kong-helpdesk'),
                ),
                'required' => array('enableInbox','equals','1'),
            ),
            array(
                'id'       => 'mailAccountHost',
                'type'     => 'text',
                'title'    => __('Host', 'kong-helpdesk'),
                'required' => array('enableInbox','equals','1'),
            ),
            array(
                'id'       => 'mailAccountFolder',
                'type'     => 'text',
                'title'    => __('Inbox Folder', 'kong-helpdesk'),
                'subtitle' => __('The folder where to scan for new mails. Most of the time it is the INBOX folder.', 'kong-helpdesk'),
                'default'  => 'INBOX',
                'required' => array('enableInbox','equals','1'),
            ),
            array(
                'id'       => 'mailAccountArchiveFolder',
                'type'     => 'text',
                'title'    => __('Archive Folder', 'kong-helpdesk'),
                'subtitle'    => __('The target folder, where processed mails should be moved (eg. Archiv).', 'kong-helpdesk'),
                'default'  => 'Archiv',
                'required' => array('enableInbox','equals','1'),
            ),
            array(
                'id'       => 'mailAccountProtocol',
                'type'     => 'select',
                'title'    => __('Protocol', 'kong-helpdesk'),
                'options'  => array(
                    'tls' => __('TLS', 'kong-helpdesk'),
                    'ssl' => __('SSL', 'kong-helpdesk'),
                ),
                'default' => 'tls',
                'required' => array('enableInbox','equals','1'),
            ),
            array(
                'id'       => 'mailAccountPort',
                'type'     => 'select',
                'title'    => __('Port', 'kong-helpdesk'),
                'options'  => array(
                    '143' => __('143', 'kong-helpdesk'),
                    '993' => __('993', 'kong-helpdesk'),
                    '110' => __('110', 'kong-helpdesk'),
                    '995' => __('995', 'kong-helpdesk'),
                ),
                'default' => '143',
                'required' => array('enableInbox','equals','1'),
            ),
            array(
                'id'       => 'mailAccountType',
                'type'     => 'select',
                'title'    => __('Protocol', 'kong-helpdesk'),
                'options'  => array(
                    'imap' => __('IMAP', 'kong-helpdesk'),
                    'imaps' => __('IMAPS', 'kong-helpdesk'),
                    'pop3' => __('POP3', 'kong-helpdesk'),
                    'pop3s' => __('POP3S', 'kong-helpdesk'),
                ),
                'default' => 'imap',
                'required' => array('enableInbox','equals','1'),
            ),
            array(
                'id'       => 'mailAccountNovalidateCert',
                'type'     => 'checkbox',
                'title'    => __('No Validate Cert', 'kong-helpdesk'),
                'subtitle' => __('Do not validate Certificates.', 'kong-helpdesk'),
                'default'  => '0',
                'required' => array('enableInbox','equals','1'),
            ),
            array(
                'id'   => 'mailInfo',
                'type' => 'info',
                'desc' => '<a href="' . admin_url('edit.php?post_type=ticket&page=kong_helpdesk_options_options&check-inbox=true') . '" class="button button-primary">' . __('Test Mail Account', 'kong-helpdesk') . '</a>  <a href="' . admin_url('edit.php?post_type=ticket&page=kong_helpdesk_options_options&check-folders=true') . '" class="button button-primary">' . __('Check Folter', 'kong-helpdesk') . '</a> <a href="' . admin_url('edit.php?post_type=ticket&page=kong_helpdesk_options_options&fetch-now=true') . '" class="button button-primary">' . __('Fetch Emails Now', 'kong-helpdesk') . '</a>',
                'required' => array('enableInbox','equals','1'),
            ),
        )
    ));

    /*Redux::setSection($opt_name, array(
        'title'      => __('Integrations', 'kong-helpdesk'),
        'id'         => 'integration-settings',
        'subsection' => true,
        'fields'     => array(
            // 
            array(
                'id'       => 'integrationsInvisibleRecaptcha',
                'type'     => 'checkbox',
                'title'    => __('Invisible Recaptcha Integration', 'kong-helpdesk'),
                'subtitle'    => __('Install & Setup the <a href="https://wordpress.org/plugins/invisible-recaptcha/" target="_blank">invisible recaptcha plugin from here</a>. Then check this option.', 'kong-helpdesk'),
                'default'  => '0',
            ),
            // Envato
            array(
                'id'       => 'integrationsEnvato',
                'type'     => 'checkbox',
                'title'    => __('Envato Integration', 'kong-helpdesk'),
                'default'  => '0',
            ),
            array(
                'id'       => 'integrationsEnvatoUsername',
                'type'     => 'text',
                'title'    => __('Your Envato Username', 'kong-helpdesk'),
                'required' => array('integrationsEnvato','equals','1'),
            ),
            array(
                'id'       => 'integrationsEnvatoAPIKey',
                'type'     => 'text',
                'title'    => __('Envato API Key', 'kong-helpdesk'),
                'subtitle'  => __('<a href="https://build.Envato.com/my-apps/" target="_blank">Click here to get your API key > Person Tokens</a>.', 'kong-helpdesk'),
                'required' => array('integrationsEnvato','equals','1'),
            ),
            array(
                'id'       => 'integrationsEnvatoPurchaseCodeRequired',
                'type'     => 'checkbox',
                'title'    => __('Purchase Code required?', 'kong-helpdesk'),
                'subtitle'  => __('If enabled manual ticket creations will require a purchase code. Requests without a purchase code will automatically be denied and a reply will be sent with the request for the code.', 'kong-helpdesk'),
                'default'  => '0',
                'required' => array('integrationsEnvato','equals','1'),
            ),
            array(
                'id'       => 'integrationsEnvatoPurchaseCodeSupportRequired',
                'type'     => 'checkbox',
                'title'    => __('Check Support until for Purchase?', 'kong-helpdesk'),
                'subtitle'  => __('If enabled purchases, where the support is expired will not be created as a ticket.', 'kong-helpdesk'),
                'default'  => '0',
                'required' => array('integrationsEnvato','equals','1'),
            ),
            // WooCommerce
            array(
                'id'       => 'integrationsWooCommerce',
                'type'     => 'checkbox',
                'title'    => __('WooCommerce Integration', 'kong-helpdesk'),
                'default'  => '0',
            ),
            array(
                'id'       => 'integrationsWooCommerceFAQ',
                'type'     => 'checkbox',
                'title'    => __('Show FAQs Tab on Product pages', 'kong-helpdesk'),
                'default'  => '0',
                'required' => array('integrationsWooCommerce','equals','1'),
            ),
            array(
                'id'       => 'integrationsWooCommercePreventAdminBar',
                'type'     => 'checkbox',
                'title'    => __('Hide Admin bar for Reporters', 'kong-helpdesk'),
                'default'  => '0',
                'required' => array('integrationsWooCommerce','equals','1'),
            ),
            array(
                'id'       => 'integrationsWooCommercePreventAdminAccess',
                'type'     => 'checkbox',
                'title'    => __('Prevent Admin access for Reporters', 'kong-helpdesk'),
                'default'  => '0',
                'required' => array('integrationsWooCommerce','equals','1'),
            ),
            // Slack
            array(
                'id'       => 'integrationsSlack',
                'type'     => 'checkbox',
                'title'    => __('Slack Integration', 'kong-helpdesk'),
                'default'  => '0',
            ),
            array(
                'id'       => 'integrationsSlackWebhokURL',
                'type'     => 'text',
                'title'    => __('Webhook URL', 'kong-helpdesk'),
                'subtitle'  => __('Then <a href="https://my.slack.com/services/new/incoming-webhook" target="_blank">create an incoming webhook</a> on your Slack account for the package to use. You\'ll need the webhook URL to instantiate the client.', 'kong-helpdesk'),
                'default'  => __('https://hooks.slack.com/services/...', 'kong-helpdesk'),
                'required' => array('integrationsSlack','equals','1'),
            ),
            array(
                'id'       => 'integrationsSlackChannel',
                'type'     => 'text',
                'title'    => __('Channel', 'kong-helpdesk'),
                'subtitle'  => __('Channel where to post to.', 'kong-helpdesk'),
                'default'  => '#general',
                'required' => array('integrationsSlack','equals','1'),
            ),
            array(
                'id'       => 'integrationsSlackIcon',
                'type'     => 'text',
                'title'    => __('Icon', 'kong-helpdesk'),
                'subtitle'  => __('Set your custom Slack Icon', 'kong-helpdesk'),
                'type'      => 'media',
                'url'       => true,
                'args'      => array(
                    'teeny'            => false,
                ),
                'required' => array('integrationsSlack','equals','1'),
            ),
            array(
                'id'       => 'integrationsSlackNewTicket',
                'type'     => 'checkbox',
                'title'    => __('New Ticket Notification', 'kong-helpdesk'),
                'default'  => '0',
                'required' => array('integrationsSlack','equals','1'),
            ),
            array(
                'id'       => 'integrationsSlackStatusChange',
                'type'     => 'checkbox',
                'title'    => __('Status Change Notification', 'kong-helpdesk'),
                'default'  => '0',
                'required' => array('integrationsSlack','equals','1'),
            ),
            array(
                'id'       => 'integrationsSlackCommentAdded',
                'type'     => 'checkbox',
                'title'    => __('Enable Comment added Notification', 'kong-helpdesk'),
                'default'  => '0',
                'required' => array('integrationsSlack','equals','1'),
            ),
            array(
                'id'       => 'integrationsSlackAgentChanged',
                'type'     => 'checkbox',
                'title'    => __('Enable Comment added Notification', 'kong-helpdesk'),
                'default'  => '0',
                'required' => array('integrationsSlack','equals','1'),
            ),
            array(
                'id'       => 'enableLiveChatFBMessenger',
                'type'     => 'checkbox',
                'title'    => __('Enable FB Messenger Live Chat.', 'kong-helpdesk'),
                'subtitle' => __('Learn more here: <a href="https://developers.facebook.com/docs/messenger-platform/discovery/customer-chat-plugin">Click</a>', 'kong-helpdesk'),
                'default'  => '0',
            ),
            array(
                'id'       => 'liveChatFBMessengerCode',
                'type'     => 'ace_editor',
                'mode'     => 'js',
                'title'    => __('FBMessenger Code', 'kong-helpdesk'),
                'subtitle' => __('<a href="https://developers.facebook.com/docs/messenger-platform/discovery/customer-chat-plugin#steps">Follow the steps here to get the Messenger code.</a>', 'kong-helpdesk'),
                'required' => array('enableLiveChatFBMessenger','equals','1'),
            ), 
            array(
                'id'       => 'enableLiveChatCrisp',
                'type'     => 'checkbox',
                'title'    => __('Enable Crisp Live Chat.', 'kong-helpdesk'),
                'subtitle' => __('Learn more here: https://crisp.chat/en/', 'kong-helpdesk'),
                'default'  => '0',
            ),
            array(
                'id'       => 'liveChatCrispCode',
                'type'     => 'ace_editor',
                'mode'     => 'js',
                'title'    => __('Crisp Code', 'kong-helpdesk'),
                'subtitle' => __('Copy & paste the HTML code here. https://app.crisp.chat/settings/websites/', 'kong-helpdesk'),
                'required' => array('enableLiveChatCrisp','equals','1'),
            ),
            array(
                'id'       => 'enableLiveChatPureChat',
                'type'     => 'checkbox',
                'title'    => __('Enable PureChat Live Chat.', 'kong-helpdesk'),
                'subtitle' => __('Learn more here: https://www.purechat.com/', 'kong-helpdesk'),
                'default'  => '0',
            ),
            array(
                'id'       => 'liveChatPureChatCode',
                'type'     => 'ace_editor',
                'mode'     => 'js',
                'title'    => __('PureChat Code', 'kong-helpdesk'),
                'subtitle' => __('Copy & paste the HTML code here. https://app.purechat.com/websites/install-first', 'kong-helpdesk'),
                'required' => array('enableLiveChatPureChat','equals','1'),
            ), 
            array(
                'id'       => 'enableLiveChatChatra',
                'type'     => 'checkbox',
                'title'    => __('Enable Chatra Live Chat.', 'kong-helpdesk'),
                'subtitle' => __('Learn more here: https://chatra.io', 'kong-helpdesk'),
                'default'  => '0',
            ),
            array(
                'id'       => 'liveChatChatraCode',
                'type'     => 'ace_editor',
                'mode'     => 'js',
                'title'    => __('Chatra Code', 'kong-helpdesk'),
                'subtitle' => __('Copy & paste the HTML code here. https://app.chatra.io/settings/general', 'kong-helpdesk'),
                'required' => array('enableLiveChatChatra','equals','1'),
            ), 
        )
    ));*/

    Redux::setSection($opt_name, array(
        'title'      => __('Mail Notifications', 'kong-helpdesk'),
        'id'         => 'notifications-settings',
        'subsection' => true,
        'fields'     => array(
            // New Ticket
            array(
                'id'       => 'newTicket',
                'type'     => 'section',
                'title'    => __('New Ticket Notification', 'kong-helpdesk'),
                'subtitle' => __('Notification when a new ticket has been created. ', 'kong-helpdesk'),
                'indent'   => false,
            ),
            array(
                'id'       => 'notificationsNewTicket',
                'type'     => 'checkbox',
                'title'    => __('New Ticket Notification', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'       => 'notificationsNewTicketReporter',
                'type'     => 'checkbox',
                'title'    => __('Notify the reporter.', 'kong-helpdesk'),
                'default'  => '1',
                'required' => array('notificationsNewTicket','equals','1'),
            ),
            array(
                'id'       => 'notificationsNewTicketAgent',
                'type'     => 'checkbox',
                'title'    => __('Notify the by default assigned agent.', 'kong-helpdesk'),
                'default'  => '1',
                'required' => array('notificationsNewTicket','equals','1'),
            ),
            array(
                'id'       => 'notificationsNewTicketUsers',
                'type'     => 'select',
                'title'    => __('Notify the the following users:', 'kong-helpdesk'),
                'data' => 'users',
                'multi' => true,
                'required' => array('notificationsNewTicket','equals','1'),
            ),
            array(
                'id'       => 'statusChange',
                'type'     => 'section',
                'title'    => __('Tag Change Notification', 'kong-helpdesk'),
                'subtitle' => __('Notification when a ticket status has been changed. ', 'kong-helpdesk'),
                'indent'   => false,
            ),
            array(
                'id'       => 'notificationsStatusChange',
                'type'     => 'checkbox',
                'title'    => __('Tag Change Notification', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'       => 'notificationsStatusChangeReporter',
                'type'     => 'checkbox',
                'title'    => __('Notify the reporter.', 'kong-helpdesk'),
                'default'  => '1',
                'required' => array('notificationsStatusChange','equals','1'),
            ),
            array(
                'id'       => 'notificationsStatusChangeAgent',
                'type'     => 'checkbox',
                'title'    => __('Notify the assigned agent.', 'kong-helpdesk'),
                'default'  => '1',
                'required' => array('notificationsStatusChange','equals','1'),
            ),
            array(
                'id'       => 'notificationsStatusChangeUsers',
                'type'     => 'select',
                'title'    => __('Notify the the following users:', 'kong-helpdesk'),
                'data' => 'users',
                'multi' => true,
                'required' => array('notificationsStatusChange','equals','1'),
            ),
            array(
                'id'       => 'commentAdded',
                'type'     => 'section',
                'title'    => __('Comment Added Notification', 'kong-helpdesk'),
                'subtitle' => __('Whenever a comment has been added. ', 'kong-helpdesk'),
                'indent'   => false,
            ),
            array(
                'id'       => 'notificationsCommentAdded',
                'type'     => 'checkbox',
                'title'    => __('Enable Comment added Notification', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'       => 'notificationsCommentAddedReporter',
                'type'     => 'checkbox',
                'title'    => __('Notify the reporter.', 'kong-helpdesk'),
                'subtitle' => __('The reporter will not be notified, when he has made the comment.'),
                'default'  => '1',
                'required' => array('notificationsCommentAdded','equals','1'),
            ),
            array(
                'id'       => 'notificationsCommentAddedAgent',
                'type'     => 'checkbox',
                'title'    => __('Notify the agent.', 'kong-helpdesk'),
                'subtitle' => __('The agent will not be notified, when he has made the comment.'),
                'default'  => '1',
                'required' => array('notificationsCommentAdded','equals','1'),
            ),
            array(
                'id'       => 'notificationsCommentAddedUsers',
                'type'     => 'select',
                'title'    => __('Notify the the following users:', 'kong-helpdesk'),
                'data' => 'users',
                'multi' => true,
                'required' => array('notificationsCommentAdded','equals','1'),
            ),
            array(
                'id'       => 'agentChanged',
                'type'     => 'section',
                'title'    => __('Assigned Agent Changed Notification', 'kong-helpdesk'),
                'subtitle' => __('Whenever the assigned agent changed. ', 'kong-helpdesk'),
                'indent'   => false,
            ),
            array(
                'id'       => 'notificationsAgentChanged',
                'type'     => 'checkbox',
                'title'    => __('Enable agent changed added Notification', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'       => 'notificationsAgentChangedReporter',
                'type'     => 'checkbox',
                'title'    => __('Notify the reporter.', 'kong-helpdesk'),
                'default'  => '1',
                'required' => array('notificationsAgentChanged','equals','1'),
            ),
            array(
                'id'       => 'notificationsAgentChangedAgent',
                'type'     => 'checkbox',
                'title'    => __('Notify the agent.', 'kong-helpdesk'),
                'default'  => '1',
                'required' => array('notificationsAgentChanged','equals','1'),
            ),
            array(
                'id'       => 'notificationsAgentChangedUsers',
                'type'     => 'select',
                'title'    => __('Notify the the following users:', 'kong-helpdesk'),
                'data' => 'users',
                'multi' => true,
                'required' => array('notificationsAgentChanged','equals','1'),
            ),
            array(
                'id'       => 'supportNotificationSettings',
                'type'     => 'section',
                'title'    => __('Notification settings', 'kong-helpdesk'),
                'subtitle' => __('The default notification settings for mails etc.', 'kong-helpdesk'),
                'indent'   => false,
            ),
            array(
                'id'       => 'supportName',
                'type'     => 'text',
                'title'    => __('Name', 'kong-helpdesk'),
                'subtitle' => __('This is the default from name for your mail notifications.', 'kong-helpdesk'),
                'default'  => __('Helpdesk', 'kong-helpdesk'),
            ),
            array(
                'id'        =>'supportLogo',
                'type'      => 'media',
                'url'       => true,
                'title'     => __('Set a Logo', 'kong-helpdesk'),
                'subtitle'  => __('The logo will be used in all Mail notifications.', 'kong-helpdesk'),
                'args'      => array(
                    'teeny'            => false,
                )
            ),
            array(
                'id'       => 'supportMail',
                'type'     => 'text',
                'title'    => __('Support Mail Address', 'kong-helpdesk'),
                'subtitle' => __('This will be used in your mail notifications as the default reply to address.', 'kong-helpdesk'),
                'default'  => 'support@yourdomain.com',
            ),
            array(
                'id'       => 'supportFooter',
                'type'     => 'editor',
                'title'    => __('Footer for Mails', 'kong-helpdesk'),
                'default'  => 'You can reply to this Email. Ticket created by Kong Helpdesk Software.',
            ),
        )
    ));

    Redux::setSection($opt_name, array(
        'title'      => __('Live Chat', 'kong-helpdesk'),
        'id'         => 'chat-settings',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enableLiveChat',
                'type'     => 'checkbox',
                'title'    => __('Enable Live Chat', 'kong-helpdesk'),
                'default'  => '0',
            ),
            array(
                'id'       => 'liveChatAllowGuest',
                'type'     => 'checkbox',
                'title'    => __('Allow Guest-Chat', 'kong-helpdesk'),
                'default'  => '0',
                'required' => array('enableLiveChat','equals','1'),
            ),
            array(
                'id'       => 'liveChatAllowAttachments',
                'type'     => 'checkbox',
                'title'    => __('Allow Attachments', 'kong-helpdesk'),
                'default'  => '1',
                'required' => array('enableLiveChat','equals','1'),
            ),
            array(
                'id'       => 'liveChatHideAgentsOffline',
                'type'     => 'checkbox',
                'title'    => __('Hide Livechat when agents offline', 'kong-helpdesk'),
                'default'  => '0',
                'required' => array('enableLiveChat','equals','1'),
            ),
            array(
                'id'        =>'liveChatDefaultIcon',
                'type'      => 'media',
                'url'       => true,
                'title'     => __('Default Icon', 'kong-helpdesk'),
                'subtitle'  => __('The icon must be in square format.', 'kong-helpdesk'),
                'args'      => array(
                    'teeny'            => false,
                ),
                'required' => array('enableLiveChat','equals','1'),
            ),
            array(
                'id'     =>'liveChatAccentColor',
                'type' => 'color',
                'title' => __('Chat Accent Color', 'woocommerce-group-attributes'), 
                'validate' => 'color',
                'default' => '#1786e5',
                'required' => array('enableLiveChat','equals','1'),
            ),
            array(
                'id'       => 'liveChatTitle',
                'type'     => 'text',
                'title'    => __('Live Chat Title', 'kong-helpdesk'),
                'default'  => __('Live Chat', 'kong-helpdesk'),
                'required' => array('enableLiveChat','equals','1'),
            ),
            array(
                'id'       => 'liveChatStatusOnline',
                'type'     => 'text',
                'title'    => __('Status Text (Online)', 'kong-helpdesk'),
                'default'  => __('Our customer service is available.', 'kong-helpdesk'),
                'required' => array('enableLiveChat','equals','1'),
            ),
            array(
                'id'       => 'liveChatStatusOffline',
                'type'     => 'text',
                'title'    => __('Status Text (Offline)', 'kong-helpdesk'),
                'default'  => __('No agents online.', 'kong-helpdesk'),
                'required' => array('enableLiveChat','equals','1'),
            ),
            array(
                'id'       => 'liveChatWelcomeOnline',
                'type'     => 'editor',
                'title'    => __('Welcome Text (Online)', 'kong-helpdesk'),
                'default'  => __('Hi %s,<br><br>Please tell us your subject and your concerns.', 'kong-helpdesk'),
                'required' => array('enableLiveChat','equals','1'),
            ),
            array(
                'id'       => 'liveChatWelcomeOffline',
                'type'     => 'editor',
                'title'    => __('Welcome Text (Offline)', 'kong-helpdesk'),
                'default'  => __('Sorry,<br><br>None of our agents is online right now. But you can leave a message.', 'kong-helpdesk'),
                'required' => array('enableLiveChat','equals','1'),
            ),
            array(
                'id'       => 'liveChatButtonText',
                'type'     => 'text',
                'title'    => __('Button Text', 'kong-helpdesk'),
                'default'  => __('Enter Chat', 'kong-helpdesk'),
                'required' => array('enableLiveChat','equals','1'),
            ),
            array(                
                'id'       => 'liveChatAJAXInterval',
                'type'     => 'spinner',
                'title'    => __('AJAX Interval', 'kong-helpdesk'),
                'subtitle' => __('Increase the interval (in miliseconds) to save server performance.', 'kong-helpdesk'),
                'default'  => '2000',
                'min'      => '1',
                'step'     => '10',
                'max'      => '9999999999',
                'required' => array('enableLiveChat','equals','1'),
            ),    
        )
    ));

    Redux::setSection($opt_name, array(
        'title'      => __('Saved Replies (BOT)', 'kong-helpdesk'),
        'id'         => 'saved-replies',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enableSavedReplies',
                'type'     => 'checkbox',
                'title'    => __('Enable Saved Replies', 'kong-helpdesk'),
                'subtitle' => __('This allows you to save comments into a saved reply, that can be reused for later tickets.', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'       => 'savedRepliesAutomatic',
                'type'     => 'checkbox',
                'title'    => __('Enable Automatic Replies', 'kong-helpdesk'),
                'subtitle' => __('This will use your saved replies based on tags & word matches to automatically reply.', 'kong-helpdesk'),
                'default'  => '0',
            ),
            array(
                'id'       => 'savedRepliesAutomaticUser',
                'type'     => 'select',
                'title'    => __('Automatic Reply User', 'kong-helpdesk'),
                'subtitle' => __('This User will be used for automatic replies. You can create a user Bot for example and set it here.', 'kong-helpdesk'),
                'data' => 'users',
                'required' => array('savedRepliesAutomatic','equals','1'),
            ),
            array(
                'id'       => 'savedRepliesAutomaticNewTicket',
                'type'     => 'checkbox',
                'title'    => __('Enable Automatic Replies for new Tickets', 'kong-helpdesk'),
                'default'  => '0',
                'required' => array('savedRepliesAutomatic','equals','1'),
            ),
            array(
                'id'       => 'savedRepliesAutomaticNewReply',
                'type'     => 'checkbox',
                'title'    => __('Enable Automatic Reply for new Ticket Replies', 'kong-helpdesk'),
                'default'  => '0',
                'required' => array('savedRepliesAutomatic','equals','1'),
            ),
        )
    ));

    /*Redux::setSection($opt_name, array(
        'title'      => __('Support Rating', 'kong-helpdesk'),
        'desc'       => __('Send out Emails when tickets are completed and ask for a support rating.', 'kong-helpdesk'),
        'id'         => 'rate-settings',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'enableSupportRating',
                'type'     => 'checkbox',
                'title'    => __('Enable Support Rating', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'     =>'supportRatingStatus',
                'type' => 'select',
                'data' => 'terms',
                'args' => array(
                    'taxonomies' => array( 'ticket_status' ),
                    'hide_empty' => false,
                ),
                'title' => __('Rating Status', 'kong-helpdesk'),
                'subtitle' => __('The status when the rating support email should be sent out.', 'kong-helpdesk'),
                'required' => array('enableSupportRating','equals','1'),
            ),
            array(
                'id'       => 'supportRatingFeedbackPage',
                'type'     => 'select',
                'title'    => __('Feedback Page', 'kong-helpdesk'),
                'subtitle' => __('Make sure the [helpdesk_feedback] shortcode is placed there. After saving here go to settings > permalinks and save.', 'kong-helpdesk'),
                'data'     => 'pages',
                'required' => array('enableSupportRating','equals','1'),
            ),
            array(
                'id'     =>'supportRatingEmailSubject',
                'type'     => 'text',
                'title'    => __('Email Subject', 'kong-helpdesk'),
                'default'  => __('How would you rate the support?', 'kong-helpdesk'),
                'required' => array('enableSupportRating','equals','1'),
            ),
            array(
                'id'     =>'supportRatingEmailIntro',
                'type'     => 'editor',
                'title'    => __('Email Intro', 'kong-helpdesk'),
                'default'  => __('Hello %s,<br/><br/>We\'d love to hear about your support experience. Please take a moment to answer one simple question by clicking either link below:<br/><br/>How would you rate the support you received?<br/><br/>', 'kong-helpdesk'),
                'required' => array('enableSupportRating','equals','1'),
            ),
            array(
                'id'     =>'supportRatingEmailSatisfied',
                'type'     => 'text',
                'title'    => __('Email Satisfied Text', 'kong-helpdesk'),
                'default'  => __('Good, I\'m satisfied', 'kong-helpdesk'),
                'required' => array('enableSupportRating','equals','1'),
            ),
            array(
                'id'     =>'supportRatingEmailUnsatisfied',
                'type'     => 'text',
                'title'    => __('Email Satisfied Text', 'kong-helpdesk'),
                'default'  => __('Bad, I\'m unsatisfied', 'kong-helpdesk'),
                'required' => array('enableSupportRating','equals','1'),
            ),
            array(
                'id'     =>'supportRatingEmailOutro',
                'type'     => 'editor',
                'title'    => __('Email Outro', 'kong-helpdesk'),
                'default'  => __('Not solved yet?<br><br>The message you add to your feedback will not be forwarded as a reply. If you have further questions you can reply to this email.', 'kong-helpdesk'),
                'required' => array('enableSupportRating','equals','1'),
            ),
        )
    ));*/

    Redux::setSection($opt_name, array(
        'title'      => __('Tickets', 'kong-helpdesk'),
        'desc'       => __('My Tickets options.', 'kong-helpdesk'),
        'id'         => 'tickets',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'supportCloseTicketsAutomatically',
                'type'     => 'checkbox',
                'title'    => __('Automatically Close Tickets', 'kong-helpdesk'),
                'subtitle' => __('Automatically set Tickets to close / solved after X Days no comment / update was made. You need to set a default solved status!', 'kong-helpdesk'),
                'default'  => '0',
            ),
            array(                
                'id'       => 'supportCloseTicketsAutomaticallyDays',
                'type'     => 'spinner',
                'title'    => __('Days after a Ticket gets closed', 'kong-helpdesk'),
                'default'  => '31',
                'min'      => '1',
                'step'     => '1',
                'max'      => '9999999999',
                'required' => array('supportCloseTicketsAutomatically','equals','1'),
            ),
            array(
                'id'       => 'myTicketsSection',
                'type'     => 'section',
                'title'    => __('My tickets', 'kong-helpdesk'),
                'subtitle'    => __('Configure what you want to show in the My tickets table.', 'kong-helpdesk'),
                'indent'   => false,
            ),
            /*array(
                'id'       => 'myTicketsDatatablesEnable',
                'type'     => 'checkbox',
                'title'    => __( 'Enable Datatables', 'woocommerce-variations-table' ),
                'default'  => 1,
            ),
            array(
                'id'       => 'myTicketsDatatablesLanguage',
                'type'     => 'select',
                'title'    => __('Datatables Language', 'woocommerce-variations-table'),
                'subtitle' => __('Set a language for the datatable.', 'woocommerce-variations-table'),
                'default'  => 'English',
                'options'  => array( 
                    'Afrikaans' => __('Afrikaans', 'woocommerce-variations-table'),
                    'Albanian' => __('Albanian', 'woocommerce-variations-table'),
                    'Amharic' => __('Amharic', 'woocommerce-variations-table'),
                    'Arabic' => __('Arabic', 'woocommerce-variations-table'),
                    'Armenian' => __('Armenian', 'woocommerce-variations-table'),
                    'Azerbaijan' => __('Azerbaijan', 'woocommerce-variations-table'),
                    'Bangla' => __('Bangla', 'woocommerce-variations-table'),
                    'Basque' => __('Basque', 'woocommerce-variations-table'),
                    'Belarusian' => __('Belarusian', 'woocommerce-variations-table'),
                    'Bulgarian' => __('Bulgarian', 'woocommerce-variations-table'),
                    'Catalan' => __('Catalan', 'woocommerce-variations-table'),
                    'Chinese-traditional' => __('traditional', 'woocommerce-variations-table'),
                    'Chinese' => __('Chinese', 'woocommerce-variations-table'),
                    'Croatian' => __('Croatian', 'woocommerce-variations-table'),
                    'Czech' => __('Czech', 'woocommerce-variations-table'),
                    'Danish' => __('Danish', 'woocommerce-variations-table'),
                    'Dutch' => __('Dutch', 'woocommerce-variations-table'),
                    'English' => __('English', 'woocommerce-variations-table'),
                    'Estonian' => __('Estonian', 'woocommerce-variations-table'),
                    'Filipino' => __('Filipino', 'woocommerce-variations-table'),
                    'Finnish' => __('Finnish', 'woocommerce-variations-table'),
                    'French' => __('French', 'woocommerce-variations-table'),
                    'Galician' => __('Galician', 'woocommerce-variations-table'),
                    'Georgian' => __('Georgian', 'woocommerce-variations-table'),
                    'German' => __('German', 'woocommerce-variations-table'),
                    'Greek' => __('Greek', 'woocommerce-variations-table'),
                    'Gujarati' => __('Gujarati', 'woocommerce-variations-table'),
                    'Hebrew' => __('Hebrew', 'woocommerce-variations-table'),
                    'Hindi' => __('Hindi', 'woocommerce-variations-table'),
                    'Hungarian' => __('Hungarian', 'woocommerce-variations-table'),
                    'Icelandic' => __('Icelandic', 'woocommerce-variations-table'),
                    'Indonesian-Alternative' => __('Alternative', 'woocommerce-variations-table'),
                    'Indonesian' => __('Indonesian', 'woocommerce-variations-table'),
                    'Irish' => __('Irish', 'woocommerce-variations-table'),
                    'Italian' => __('Italian', 'woocommerce-variations-table'),
                    'Japanese' => __('Japanese', 'woocommerce-variations-table'),
                    'Kazakh' => __('Kazakh', 'woocommerce-variations-table'),
                    'Korean' => __('Korean', 'woocommerce-variations-table'),
                    'Kyrgyz' => __('Kyrgyz', 'woocommerce-variations-table'),
                    'Latvian' => __('Latvian', 'woocommerce-variations-table'),
                    'Lithuanian' => __('Lithuanian', 'woocommerce-variations-table'),
                    'Macedonian' => __('Macedonian', 'woocommerce-variations-table'),
                    'Malay' => __('Malay', 'woocommerce-variations-table'),
                    'Mongolian' => __('Mongolian', 'woocommerce-variations-table'),
                    'Nepali' => __('Nepali', 'woocommerce-variations-table'),
                    'Norwegian-Bokmal' => __('Bokmal', 'woocommerce-variations-table'),
                    'Norwegian-Nynorsk' => __('Nynorsk', 'woocommerce-variations-table'),
                    'Pashto' => __('Pashto', 'woocommerce-variations-table'),
                    'Persian' => __('Persian', 'woocommerce-variations-table'),
                    'Polish' => __('Polish', 'woocommerce-variations-table'),
                    'Portuguese-Brasil' => __('Brasil', 'woocommerce-variations-table'),
                    'Portuguese' => __('Portuguese', 'woocommerce-variations-table'),
                    'Romanian' => __('Romanian', 'woocommerce-variations-table'),
                    'Russian' => __('Russian', 'woocommerce-variations-table'),
                    'Serbian' => __('Serbian', 'woocommerce-variations-table'),
                    'Sinhala' => __('Sinhala', 'woocommerce-variations-table'),
                    'Slovak' => __('Slovak', 'woocommerce-variations-table'),
                    'Slovenian' => __('Slovenian', 'woocommerce-variations-table'),
                    'Spanish' => __('Spanish', 'woocommerce-variations-table'),
                    'Swahili' => __('Swahili', 'woocommerce-variations-table'),
                    'Swedish' => __('Swedish', 'woocommerce-variations-table'),
                    'Tamil' => __('Tamil', 'woocommerce-variations-table'),
                    'telugu' => __('telugu', 'woocommerce-variations-table'),
                    'Thai' => __('Thai', 'woocommerce-variations-table'),
                    'Turkish' => __('Turkish', 'woocommerce-variations-table'),
                    'Ukrainian' => __('Ukrainian', 'woocommerce-variations-table'),
                    'Urdu' => __('Urdu', 'woocommerce-variations-table'),
                    'Uzbek' => __('Uzbek', 'woocommerce-variations-table'),
                    'Vietnamese' => __('Vietnamese', 'woocommerce-variations-table'),
                    'Welsh' => __('Welsh', 'woocommerce-variations-table'),
                ),
                'required' => array('myTicketsDatatablesEnable','equals','1'),
            ),*/
            array(
                'id'       => 'myTicketsShowName',
                'type'     => 'checkbox',
                'title'    => __('Show Name', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'       => 'myTicketsShowDate',
                'type'     => 'checkbox',
                'title'    => __('Show Date', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'       => 'myTicketsShowStatus',
                'type'     => 'checkbox',
                'title'    => __('Show Status', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'       => 'myTicketsShowSystem',
                'type'     => 'checkbox',
                'title'    => __('Show System', 'kong-helpdesk'),
                'default'  => '1',
            ),
            array(
                'id'       => 'myTicketsShowType',
                'type'     => 'checkbox',
                'title'    => __('Show Type', 'kong-helpdesk'),
                'default'  => '1',
            ),
        )
    ));

    Redux::setSection($opt_name, array(
        'title'      => __('Advanced settings', 'kong-helpdesk'),
        'desc'       => __('Custom stylesheet / javascript.', 'kong-helpdesk'),
        'id'         => 'advanced',
        'subsection' => true,
        'fields'     => array(
            array(
                'id'       => 'useThemesTemplate',
                'type'     => 'checkbox',
                'title'    => __('Use Theme Template', 'kong-helpdesk'),
                'subtitle'    => __('Enable this to override the custom templates.', 'kong-helpdesk'),
                'default'  => '0',
            ),
            array(
                'id'       => 'supportMailTemplate',
                'type'     => 'ace_editor',
                'mode'     => 'html',
                'title'    => __('Support Mail Template', 'kong-helpdesk'),
                'subtitle' => __('This will be used for notifications.', 'kong-helpdesk'),
                'default'  => file_get_contents(dirname(__FILE__) . '/views/emailTemplate.html'),
            ),
            array(
                'id'       => 'customCSS',
                'type'     => 'ace_editor',
                'mode'     => 'css',
                'title'    => __('Custom CSS', 'kong-helpdesk'),
                'subtitle' => __('Add some stylesheet if you want.', 'kong-helpdesk'),
            ),
            array(
                'id'       => 'customJS',
                'type'     => 'ace_editor',
                'mode'     => 'js',
                'title'    => __('Custom JS', 'kong-helpdesk'),
                'subtitle' => __('Add some stylesheet if you want.', 'kong-helpdesk'),
            ),            
        )
    ));