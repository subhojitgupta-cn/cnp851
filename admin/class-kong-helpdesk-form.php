<?php

class Kong_Helpdesk_Form extends Kong_Helpdesk
{
    protected $plugin_name;
    protected $version;

    /**
     * Construct Form
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $plugin_name      [description]
     * @param   [type]                       $version          [description]
     * @param   [type]                       $ticket_processor [description]
     */
    public function __construct($plugin_name, $version, $ticket_processor)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->ticket_processor = $ticket_processor;
    }

    /**
     * Init Helpdesk Forms
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    public function init()
    {
        global $kong_helpdesk_options;

        $this->options = $kong_helpdesk_options;

        add_shortcode('new_ticket', array( $this, 'new_ticket_form' ));
    }

    /**
     * Render new ticket shortcode [new_ticket type="Simple|WooCommerce|Envato|Chat"]
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $atts [description]
     * @return  [type]                             [description]
     */
    public function new_ticket_form($atts)
    {
        $args = shortcode_atts(array(
            'type' => 'Simple',
            'types' => '',
            'departments' => '',
            'priorities' => '',
        ), $atts);

        $type = $args['type'];
        $types = $args['types'];
        $departments = $args['departments'];
        $priorities = $args['priorities'];

        do_action('kong_helpdesk_start_new_ticket_form');

        ob_start();
        echo '<div class="kong-helpdesk">';
            echo '<div class="kong-helpdesk-row">';

                $checks = array('both', 'only_ticket');
                if(in_array($this->get_option('supportSidebarDisplay'), $checks) && ($this->get_option('supportSidebarPosition') == "left") && ($type !== "WooCommerce")) {
                ?>
                <div class="kong-helpdesk-col-sm-4 kong-helpdesk-sidebar">
                    <?php dynamic_sidebar('helpdesk-sidebar'); ?>
                </div>
                <?php
                }

                $checks = array('none', 'only_faq');
                if(in_array($this->get_option('supportSidebarDisplay'), $checks) || ($type == "WooCommerce")) {
                    echo '<div class="kong-helpdesk-col-sm-12">';
                } else {
                    echo '<div class="kong-helpdesk-col-sm-8">';
                }

                $supportMyTicketsPage = $this->get_option('supportMyTicketsPage');
                if (!empty($supportMyTicketsPage)) {
                    $redirect_base = get_permalink($supportMyTicketsPage);
                    echo '<a href="' . $redirect_base . '" id="kong_helpdesk_back_to_my_tickets" class="kong_helpdesk_back_to_my_tickets">' 
                    . __('< Back to My Tickets', 'kong-helpdesk') . 
                    '</a>';
                }

                // Ticket submitted Check
                if (isset($_POST['helpdesk_form'])) {

                    $is_valid = true;
                    if($this->get_option('integrationsInvisibleRecaptcha')) {
                        $is_valid = apply_filters('google_invre_is_valid_request_filter', true);
                    }

                    if(!$is_valid) {
                        echo '<div class="alert alert-danger" role="alert">';
                            echo __('Recaptcha not passed!', 'kong-helpdesk') . '<br/>';
                        echo '</div>';
                    } else {

                        $status = $this->ticket_processor->form_sanitation($_POST, $type);
                        if ($status && $is_valid) {
                            echo '<div class="alert alert-success" role="alert">';
                                echo sprintf(__('Ticket successfully created! You can <a href="%s">view it here</a>.<br/>', 'kong-helpdesk'), get_permalink($this->ticket_processor->post_id));
                                echo implode('<br/>', $this->ticket_processor->success);
                            echo '</div>';
                            unset($_POST);
                        } else {
                            echo '<div class="alert alert-danger" role="alert">';
                                echo __('Ticket could not be created!', 'kong-helpdesk') . '<br/>';
                                echo implode('<br/>', $this->ticket_processor->errors);
                            echo '</div>';
                        };
                    }
                }

                if ($type === "WooCommerce") {
                    $this->get_woo_form_types();
                    echo '<form action="' . esc_url($_SERVER['REQUEST_URI']) . '" enctype="multipart/form-data" class="kong-helpdesk-form kong-helpdesk-' . $type . '" style="display: none;" method="post">';
                } else {
                    echo '<form action="' . esc_url($_SERVER['REQUEST_URI']) . '" enctype="multipart/form-data" class="kong-helpdesk-form kong-helpdesk-' . $type . '" method="post">';
                }
                
                if($this->get_option('integrationsInvisibleRecaptcha')) {
                    do_action('google_invre_render_widget_action');
                }

                echo '<input class="form-control" name="helpdesk_form" type="hidden" value="' . $type . '">';

                do_action('kong_helpdesk_before_new_ticket_form');

                if (!is_user_logged_in()) {
                    if ($type === "WooCommerce") {
                        echo sprintf(__('Please <a href="%s" title="Login">login to submit a ticket.</a>', 'kong-helpdesk'), wp_login_url(get_permalink()));
                            $output_string = ob_get_contents();
                            ob_end_clean();
                            return $output_string;
                    } else {
                        if($this->get_option('supportOnlyLoggedIn')) {
                            echo sprintf(__('Please <a href="%s" title="Login">login to submit a ticket.</a>', 'kong-helpdesk'), wp_login_url(get_permalink()));
                            $output_string = ob_get_contents();
                            ob_end_clean();
                            return $output_string;
                        }
                        $this->getUsernameField();
                        $this->getEmailField();
                    }
                }

                if ($type === "Envato") {
                    $this->getPurchaseCodeField($type);
                    $this->getEnvatoItemsField($type);
                }

                if ($type === "WooCommerce") {
                    echo '<div class="kong-helpdesk-order-form kong-helpdesk-hidden">';
                        $this->getOrderField($type);
                        $this->getOrderSubjectField($type);
                    echo '</div>';
                    echo '<div class="kong-helpdesk-product-form kong-helpdesk-hidden">';
                        $this->getProductsField($type);
                        $this->getProductsSubjectField($type);
                    echo '</div>';
                    echo '<div class="kong-helpdesk-other-form kong-helpdesk-hidden">';
                        $this->getSubjectField($type);
                    echo '</div>';
                } else {
                    $this->getSubjectField($type);
                }

                $this->getDepartmentField($type, $departments);
                $this->getTypesField($type, $types);
                $this->getPriorityField($type, $priorities);

                $this->getWebsiteURLField($type);
                $this->getAttachmentsField($type);

                $this->getMessageField();

                do_action('kong_helpdesk_after_new_ticket_form');

                echo '<div class="form-group"><input type="submit" name="helpdesk_submitted" value="' . __('Create Ticket', 'kong-helpdesk') . '"/></div>';
                echo '</form>';

                echo '</div>';

                $checks = array('both', 'only_ticket');
                if(in_array($this->get_option('supportSidebarDisplay'), $checks) && ($this->get_option('supportSidebarPosition') == "right") && ($type !== "WooCommerce")) {
                ?>
                <div class="kong-helpdesk-col-sm-4 kong-helpdesk-sidebar">
                    <?php dynamic_sidebar('helpdesk-sidebar'); ?>
                </div>
                <?php
                }
            echo '</div>';
        echo '</div>';
        
        $output_string = ob_get_contents();
        ob_end_clean();
        return $output_string;
    }

    /**
     * Extra WooCommerce Fields
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    public function get_woo_form_types()
    {
        ?>
        <div class="kong-helpdesk-row kong-helpdesk-WooCommerce-types">
            <?php if ($this->get_option('fieldsWooCommerceOrders')) { ?>
            <a class="kong-helpdesk-woo-form-show" data-show="order" href="#">
            <div class="kong-helpdesk-col-sm-4 kong-helpdesk-center">
                <div class="kong-helpdesk-box">
                    <i class="fa fa-truck fa-3x"></i><br/>
                    <strong><?php echo __('Order Support', 'kong-helpdesk') ?></strong>
                </div>
            </div>
            </a>
            <?php } ?>
            <?php if ($this->get_option('fieldsWooCommerceProducts')) { ?>
            <a class="kong-helpdesk-woo-form-show" data-show="product" href="#">
            <div class="kong-helpdesk-col-sm-4 kong-helpdesk-center">
                <div class="kong-helpdesk-box">
                    <i class="fa fa-archive fa-3x"></i><br/>
                    <strong><?php echo __('Product Support', 'kong-helpdesk') ?></strong>
                </div>
            </div>
            </a>
            <?php } ?>
            <a class="kong-helpdesk-woo-form-show" data-show="other" href="#">
            <div class="kong-helpdesk-col-sm-4 kong-helpdesk-center">
                <div class="kong-helpdesk-box">
                    <i class="fa fa-question fa-3x"></i><br/>
                    <strong><?php echo __('Other', 'kong-helpdesk') ?></strong>
                </div>
            </div>
            </a>
        </div>
        <?php
    }

    /**
     * Get Username Field
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    private function getUsernameField()
    {
        echo '<div class="form-group">';
            echo '<label for="helpdesk_username">' . __('Your Name (required)', 'kong-helpdesk') . '</label>';
            echo '<input class="form-control" type="text" name="helpdesk_username" pattern="[a-zA-Z0-9 \u00C0-\u00ff]+" value="' . ( isset($_POST["username"]) ? esc_attr($_POST["username"]) : '' ) . '" size="40" />';
        echo '</div>';
    }

    /**
     * Get Email Field
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    private function getEmailField()
    {
        echo '<div class="form-group">';
            echo '<label for="helpdesk_email">' . __('Your Email (required)', 'kong-helpdesk') . '</label>';
            echo '<input class="form-control" type="email" name="helpdesk_email" value="' . ( isset($_POST["email"]) ? esc_attr($_POST["email"]) : '' ) . '" size="40" />';
        echo '</div>';
    }

    /**
     * Get Subject Field
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    private function getSubjectField()
    {
        echo '<div class="form-group">';
            echo '<label for="helpdesk_subject">' . __('Subject (required)', 'kong-helpdesk') . '</label>';
            echo '<input class="form-control kong-helpdesk-faq-searchterm " type="text" name="helpdesk_subject" value="' . ( isset($_POST["subject"]) ? esc_attr($_POST["subject"]) : '' ) . '" />';
            echo '<div class="kong-helpdesk-faq-live-search-results" style="display: none;"></div>';
        echo '</div>';
    }

    /**
     * Get Subject Field
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    private function getProductsSubjectField()
    {
        echo '<div class="form-group">';
            echo '<label for="helpdesk_product_subject">' . __('Subject (required)', 'kong-helpdesk') . '</label>';
            echo '<input class="form-control kong-helpdesk-faq-searchterm " type="text" name="helpdesk_product_subject" value="' . ( isset($_POST["subject"]) ? esc_attr($_POST["subject"]) : '' ) . '" />';
            echo '<div class="kong-helpdesk-faq-live-search-results" style="display: none;"></div>';
        echo '</div>';
    }
    

    /**
     * Get Message Field
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    private function getMessageField()
    {
        $settings = array(
            'textarea_rows' => 15,
            'media_buttons' => false,
            'teeny' => true,
            'drag_drop_upload' => true,
        );

        echo '<div class="form-group">';
            echo '<label for="helpdesk_message">' . __('Message (required) ', 'kong-helpdesk') . '</label>';
            wp_editor('', 'helpdesk_message', $settings);
            // echo '<textarea class="form-control wp-editor-area" rows="10" cols="35" name="helpdesk_message">' . ( isset($_POST["message"]) ? esc_attr($_POST["message"]) : '' ) . '</textarea>';
        echo '</div>';
    }

    /**
     * Get System Field
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    private function getDepartmentField($type, $defaultDepartments)
    {
        if (!$this->get_option('fields' . $type . 'System')) {
            return false;
        }
        $systems = apply_filters('kong_helpdesk_new_ticket_' . $type .'_systems', get_terms(array(
            'taxonomy' => 'ticket_system',
            'hide_empty' => false,
            'include' => $defaultDepartments
        )));

        if (!empty($systems)) {
            echo '<div class="form-group">';
            echo '<label for="helpdesk_system">' . __('Department (required)', 'kong-helpdesk') . '</label>';
            echo '<select name="helpdesk_system" class="form-control">';
            echo '<option value="">' . __('Select a Department', 'kong-helpdesk') . '</option>';
            foreach ($systems as $system) {
                if($system->parent !== 0) {
                    $system->name = '-- ' . $system->name;
                }
                echo '<option value="' . $system->term_id . '">' . $system->name . '</option>';
            }
            echo '</select>';
            echo '</div>';
        }
    }

    /**
     * Get Priority Field
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    private function getPriorityField($type, $defaultPriorities)
    {
        if (!$this->get_option('fields' . $type . 'Priority')) {
            return false;
        }

        $priorities = apply_filters('kong_helpdesk_new_ticket_' . $type .'_priorities', get_terms(array(
            'taxonomy' => 'ticket_priority',
            'hide_empty' => false,
            'include' => $defaultPriorities
        )));
        if (!empty($priorities)) {
            echo '<div class="form-group">';
                echo '<label for="helpdesk_priority">' . __('Priority (required)', 'kong-helpdesk') . '</label>';
                echo '<select name="helpdesk_priority" class="form-control">';
                    echo '<option value="">' . __('Select a priority', 'kong-helpdesk') . '</option>';
            foreach ($priorities as $priority) {
                if($priority->parent !== 0) {
                    $priority->name = '-- ' . $priority->name;
                }
                echo '<option value="' . $priority->term_id . '">' . $priority->name . '</option>';
            }
                echo '</select>';
            echo '</div>';
        }
    }

    /**
     * Get Types Field
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    private function getTypesField($type, $defaultTypes)
    {
        if (!$this->get_option('fields' . $type . 'Types')) {
            return false;
        }

        $types = apply_filters('kong_helpdesk_new_ticket_' . $type .'_types', get_terms(array(
            'taxonomy' => 'ticket_type',
            'hide_empty' => false,
            'include' => $defaultTypes
        )));
        if (!empty($types)) {
            echo '<div class="form-group">';
                echo '<label for="helpdesk_type">' . __('Type (required)', 'kong-helpdesk') . '</label>';
                echo '<select name="helpdesk_type" class="form-control">';
                    echo '<option value="">' . __('Select a type', 'kong-helpdesk') . '</option>';
            foreach ($types as $type) {
                if($type->parent !== 0) {
                    $type->name = '-- ' . $type->name;
                }
                echo '<option value="' . $type->term_id . '">' . $type->name . '</option>';
            }
                echo '</select>';
            echo '</div>';
        }
    }

    /**
     * Get Order Field
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    private function getOrderField($type)
    {
        if (!$this->get_option('fields' . $type . 'Orders')) {
            return false;
        }

        $orders = apply_filters('kong_helpdesk_new_ticket_' . $type .'_orders', get_posts(array(
            'posts_per_page' => -1,
            'meta_key'    => '_customer_user',
            'meta_value'  => get_current_user_id(),
            'post_type'   => wc_get_order_types(),
            'post_status' => array_keys(wc_get_order_statuses()),
        )));

        if (!empty($orders)) {
            echo '<div class="form-group">';
                echo '<label for="helpdesk_order">' . __('Your Order', 'kong-helpdesk') . '</label>';
                echo '<select name="helpdesk_order" class="form-control">';
                    echo '<option value="">' . __('Select your Order', 'kong-helpdesk') . '</option>';
            foreach ($orders as $order) {
                echo '<option value="' . $order->ID . '">#' . $order->ID . '</option>';
            }
                echo '</select>';
            echo '</div>';
        }
    }

    /**
     * Get Order Subject Fields
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    private function getOrderSubjectField($type)
    {   
        $order_subjects = apply_filters('kong_helpdesk_new_ticket_' . $type .'_order_subjects', array(
             __('Where is my stuff?', 'kong-helpdesk'),
             __('Problem with an order', 'kong-helpdesk'),
             __('Returns and refunds', 'kong-helpdesk'),
             __('Gift Cards', 'kong-helpdesk'),
             __('Payment issues', 'kong-helpdesk'),
             __('Change an order', 'kong-helpdesk'),
             __('Promotions and deals', 'kong-helpdesk'),
             __('More order issues', 'kong-helpdesk'),
        ));

        if (!empty($order_subjects)) {
            echo '<div class="form-group">';
                echo '<label for="helpdesk_order_subject">' . __('Your Subject', 'kong-helpdesk') . '</label>';
                echo '<select name="helpdesk_order_subject" class="form-control">';
                    echo '<option value="">' . __('Select your Subject', 'kong-helpdesk') . '</option>';
            foreach ($order_subjects as $order_subject) {
                echo '<option value="' . $order_subject . '">' . $order_subject . '</option>';
            }
                echo '</select>';
            echo '</div>';
        }
    }

    /**
     * Get Products Fields
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    private function getProductsField($type)
    {
        if (!$this->get_option('fields' . $type . 'Products')) {
            return false;
        }

        $products = apply_filters('kong_helpdesk_new_ticket_' . $type .'_products', get_posts(array(
            'posts_per_page' => -1,
            'suppress_filters' => false,
            'post_type'   => 'product',
            'orderby'          => 'title',
            'order'            => 'ASC',
        )));

        if (!empty($products)) {
            echo '<div class="form-group">';
                echo '<label for="helpdesk_product">' . __('Product', 'kong-helpdesk') . '</label>';
                echo '<select name="helpdesk_product" class="form-control">';
                    echo '<option value="">' . __('Select your product', 'kong-helpdesk') . '</option>';
            foreach ($products as $product) {
                $sku = get_post_meta($product->ID, '_sku', true);
                if (empty($sku)) {
                    echo '<option value="' . $product->ID . '">' . $product->post_title . '</option>';
                } else {
                    echo '<option value="' . $product->ID . '">' . $product->post_title . ' (' . $sku . ')</option>';
                }
            }
                echo '</select>';
            echo '</div>';
        }
    }

    /**
     * Get Envato Purchase Code Field
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    private function getPurchaseCodeField($type)
    {
        if (!$this->get_option('fields' . $type . 'PurchaseCode')) {
            return false;
        }

        echo '<div class="form-group">';
            echo '<label for="helpdesk_">' . __('Purchase Code', 'kong-helpdesk') . '</label>';
            echo '<input class="form-control" type="text" name="helpdesk_purchase_code" pattern="[a-zA-Z0-9\-]+" value="' . ( isset($_POST["purchase_code"]) ? esc_attr($_POST["purchase_code"]) : '' ) . '" size="40" />';
        echo '</div>';
    }

    /**
     * Get Envato Items Field
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    private function getEnvatoItemsField($type)
    {
        if (!$this->get_option('fields' . $type . 'Items')) {
            return false;
        }

        $items = apply_filters('kong_helpdesk_new_' . $type . '_ticket_items', $this->getEnvatoItems());
        if (!empty($items)) {
            echo '<div class="form-group">';
                echo '<label for="helpdesk_item">' . __('Select Item', 'kong-helpdesk') . '</label>';
                echo '<select name="helpdesk_item" class="form-control">';
                    echo '<option value="">' . __('Select an Item', 'kong-helpdesk') . '</option>';
            foreach ($items as $item) {
                echo '<option value="' . $item->term_id . '">' . $item->name . '</option>';
            }
                echo '</select>';
            echo '</div>';
        }
    }

    /**
     * Get Envato Items
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    private function getEnvatoItems()
    {
        $items = array();
        if (!empty($this->get_option('integrationsEnvatoAPIKey')) && (!empty($this->get_option('integrationsEnvatoUsername')))) {
            $token = $this->get_option('integrationsEnvatoAPIKey');
            $username = $this->get_option('integrationsEnvatoUsername');
        }

        $Envato = new DB_Envato($token);

        $items = $Envato->call('/discovery/search/search/item?sort_by=name&sort_direction=asc&username=' . $username);
        
        if (isset($items->error)) {
            $items = array();
            return $items;
        }
        
        return $items->matches;
    }

    /**
     * Get Website URL Field
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    private function getWebsiteURLField($type)
    {
        if (!$this->get_option('fields' . $type . 'WebsiteURL')) {
            return false;
        }

        echo '<div class="form-group">';
            echo '<label for="helpdesk_website_url">' . __('Website URL', 'kong-helpdesk') . '</label>';
            echo '<input class="form-control" type="url" name="helpdesk_website_url"value="' . ( isset($_POST["website_url"]) ? esc_attr($_POST["website_url"]) : '' ) . '" />';
        echo '</div>';
    }

    /**
     * Get Attachments Field
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       [description]
     */
    private function getAttachmentsField($type)
    {
        if (!$this->get_option('fields' . $type . 'Attachments')) {
            return false;
        }

         echo '<div class="form-group">';
            echo '<label for="helpdesk_website_url">' . __('Attachments', 'kong-helpdesk') . '</label>';
            echo '<input name="helpdesk-attachments[]" type="file" multiple>';
         echo '</div>';
    }
}