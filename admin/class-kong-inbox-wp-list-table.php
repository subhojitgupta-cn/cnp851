<?php
/**
 * WP List Table Example class
 *
 * @package   WPListTableExample
 * @author    Matt van Andel
 * @copyright 2016 Matthew van Andel
 * @license   GPL-2.0+
 */

/**
 * Example List Table Child Class
 *
 * Create a new list table package that extends the core WP_List_Table class.
 * WP_List_Table contains most of the framework for generating the table, but we
 * need to define and override some methods so that our data can be displayed
 * exactly the way we need it to be.
 *
 * To display this example on a page, you will first need to instantiate the class,
 * then call $yourInstance->prepare_items() to handle any data manipulation, then
 * finally call $yourInstance->display() to render the table to the page.
 *
 * Our topic for this list table is going to be movies.
 *
 * @package WPListTableExample
 * @author  Matt van Andel
 */

/*if ( ! class_exists( 'WP_List_Table' ) ) {
      require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}*/
class Kong_Inbox_List_Table extends WP_List_Table {

	/**
	 * ***********************************************************************
	 * Normally we would be querying data from a database and manipulating that
	 * for use in your list table. For this example, we're going to simplify it
	 * slightly and create a pre-built array. Think of this as the data that might
	 * be returned by $wpdb->query()
	 *
	 * In a real-world scenario, you would run your own custom query inside
	 * the prepare_items() method in this class.
	 *
	 * @var array
	 * ************************************************************************
	 */
	

	/**
	 * Class_Kong_List_Table constructor.
	 *
	 * REQUIRED. Set up a constructor that references the parent constructor. We
	 * use the parent reference to set some default configs.
	 */
	public function __construct() {
		// Set parent defaults.
		parent::__construct( array(
			'singular' => 'inbox',     // Singular name of the listed records.
			'plural'   => 'inboxes',    // Plural name of the listed records.
			'ajax'     => false,       // Does this table support ajax?
		) );
	}

	/**
	 * Get a list of columns. The format is:
	 * 'internal-name' => 'Title'
	 *
	 * REQUIRED! This method dictates the table's columns and titles. This should
	 * return an array where the key is the column slug (and class) and the value
	 * is the column's title text. If you need a checkbox for bulk actions, refer
	 * to the $columns array below.
	 *
	 * The 'cb' column is treated differently than the rest. If including a checkbox
	 * column in your table you must create a `column_cb()` method. If you don't need
	 * bulk actions or checkboxes, simply leave the 'cb' entry out of your array.
	 *
	 * @see WP_List_Table::::single_row_columns()
	 * @return array An associative array containing column information.
	 */
	public function get_columns() {
		$columns = array(
			'cb'       => '<input type="checkbox" />', // Render a checkbox instead of text.
			'ticket_id' => _x( 'Number', 'Column label', 'wp-list-table-example' ),
			'customer'    => _x( 'Customer', 'Column label', 'wp-list-table-example' ),
			'ticket_summary'   => _x( 'Ticket Summary', 'Column label', 'wp-list-table-example' ),
			'assigned_to' => _x( 'Assigned To', 'Column label', 'wp-list-table-example' ),
			'last_updated' => _x( 'Last Updated', 'Column label', 'wp-list-table-example' ),
		);

		return $columns;
	}

	/**
	 * Get a list of sortable columns. The format is:
	 * 'internal-name' => 'orderby'
	 * or
	 * 'internal-name' => array( 'orderby', true )
	 *
	 * The second format will make the initial sorting order be descending
	 *
	 * Optional. If you want one or more columns to be sortable (ASC/DESC toggle),
	 * you will need to register it here. This should return an array where the
	 * key is the column that needs to be sortable, and the value is db column to
	 * sort by. Often, the key and value will be the same, but this is not always
	 * the case (as the value is a column name from the database, not the list table).
	 *
	 * This method merely defines which columns should be sortable and makes them
	 * clickable - it does not handle the actual sorting. You still need to detect
	 * the ORDERBY and ORDER querystring variables within `prepare_items()` and sort
	 * your data accordingly (usually by modifying your query).
	 *
	 * @return array An associative array containing all the columns that should be sortable.
	 */
	protected function get_sortable_columns() {
		$sortable_columns = array(
			'ticket_id'   => array( 'ticket_id', false ),
			'last_updated' => array( 'last_updated', false ),
		);

		return $sortable_columns;
	}

	/**
	 * Get default column value.
	 *
	 * Recommended. This method is called when the parent class can't find a method
	 * specifically build for a given column. Generally, it's recommended to include
	 * one method for each column you want to render, keeping your package class
	 * neat and organized. For example, if the class needs to process a column
	 * named 'title', it would first see if a method named $this->column_title()
	 * exists - if it does, that method will be used. If it doesn't, this one will
	 * be used. Generally, you should try to use custom column methods as much as
	 * possible.
	 *
	 * Since we have defined a column_title() method later on, this method doesn't
	 * need to concern itself with any column with a name of 'title'. Instead, it
	 * needs to handle everything else.
	 *
	 * For more detailed insight into how columns are handled, take a look at
	 * WP_List_Table::single_row_columns()
	 *
	 * @param object $item        A singular item (one full row's worth of data).
	 * @param string $column_name The name/slug of the column to be processed.
	 * @return string Text or HTML to be placed inside the column <td>.
	 */
	protected function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'customer':
			case 'ticket_summary':
			case 'assigned_to':
			case 'ticket_id':
			case 'last_updated':
				return $item[ $column_name ];
			default:
				return print_r( $item, true ); // Show the whole array for troubleshooting purposes.
		}
	}

	/**
	 * Get value for checkbox column.
	 *
	 * REQUIRED if displaying checkboxes or using bulk actions! The 'cb' column
	 * is given special treatment when columns are processed. It ALWAYS needs to
	 * have it's own method.
	 *
	 * @param object $item A singular item (one full row's worth of data).
	 * @return string Text to be placed inside the column <td>.
	 */
	protected function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'],  // Let's simply repurpose the table's singular label ("movie").
			$item['ticket_id']                // The value of the checkbox should be the record's ID.
		);
	}

	/**
	 * Get title column value.
	 *
	 * Recommended. This is a custom column method and is responsible for what
	 * is rendered in any column with a name/slug of 'title'. Every time the class
	 * needs to render a column, it first looks for a method named
	 * column_{$column_title} - if it exists, that method is run. If it doesn't
	 * exist, column_default() is called instead.
	 *
	 * This example also illustrates how to implement rollover actions. Actions
	 * should be an associative array formatted as 'slug'=>'link html' - and you
	 * will need to generate the URLs yourself. You could even ensure the links are
	 * secured with wp_nonce_url(), as an expected security measure.
	 *
	 * @param object $item A singular item (one full row's worth of data).
	 * @return string Text to be placed inside the column <td>.
	 */
	protected function column_title( $item ) {
		$page = wp_unslash( $_REQUEST['page'] ); // WPCS: Input var ok.

		// Build edit row action.
		$edit_query_args = array(
			'page'   => $page,
			'action' => 'edit',
			'inbox'  => $item['ticket_id'],
		);

		$actions['edit'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( wp_nonce_url( add_query_arg( $edit_query_args, 'admin.php' ), 'editmovie_' . $item['ticket_id'] ) ),
			_x( 'Edit', 'List table row action', 'wp-list-table-example' )
		);

		// Build delete row action.
		$delete_query_args = array(
			'page'   => $page,
			'action' => 'delete',
			'inbox'  => $item['ticket_id'],
		);

		$actions['delete'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url( wp_nonce_url( add_query_arg( $delete_query_args, 'admin.php' ), 'deletemovie_' . $item['ticket_id'] ) ),
			_x( 'Delete', 'List table row action', 'wp-list-table-example' )
		);

		// Return the title contents.
		return sprintf( '%1$s <span style="color:silver;">(id:%2$s)</span>%3$s',
			$item['ticket_id'],
			$item['last_updated'],
			$this->row_actions( $actions )
		);
	}

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table.
	 *
	 * Optional. If you need to include bulk actions in your list table, this is
	 * the place to define them. Bulk actions are an associative array in the format
	 * 'slug'=>'Visible Title'
	 *
	 * If this method returns an empty value, no bulk action will be rendered. If
	 * you specify any bulk actions, the bulk actions box will be rendered with
	 * the table automatically on display().
	 *
	 * Also note that list tables are not automatically wrapped in <form> elements,
	 * so you will need to create those manually in order for bulk actions to function.
	 *
	 * @return array An associative array containing all the bulk actions.
	 */
	protected function get_bulk_actions() {

		$menu_taxonomy_terms = get_terms( array(
                'taxonomy' => 'ticket_status',
                'orderby' => 'term_order',
                'order' => 'ASC',
                'hide_empty' => false,
            ) );
		$actions =[];
        foreach ($menu_taxonomy_terms as $term) {
        	$actions[$term->term_id] = $term->name;
        }
		return $actions;
	}

	/**
	 * Handle bulk actions.
	 *
	 * Optional. You can handle your bulk actions anywhere or anyhow you prefer.
	 * For this example package, we will handle it in the class to keep things
	 * clean and organized.
	 *
	 * @see $this->prepare_items()
	 */
	protected function process_bulk_action() {
		// Detect when a bulk action is being triggered.
	
		// security check!
        if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {

            $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
            $action = 'bulk-' . $this->_args['plural'];

            if ( ! wp_verify_nonce( $nonce, $action ) )
                wp_die( 'Nope! Security check failed!' );

        }
		$action = $this->current_action();

		if($action && !empty($_REQUEST['inbox'])){
		 	foreach ($_REQUEST['inbox'] as $ticketid) {
		 		 wp_set_post_terms($ticketid,$action, 'ticket_status'); 
		 	}
		}

        return;




	}

	/**
	 * Prepares the list of items for displaying.
	 *
	 * REQUIRED! This is where you prepare your data for display. This method will
	 * usually be used to query the database, sort and filter the data, and generally
	 * get it ready to be displayed. At a minimum, we should set $this->items and
	 * $this->set_pagination_args(), although the following properties and methods
	 * are frequently interacted with here.
	 *
	 * @global wpdb $wpdb
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 */
	function prepare_items() {
		global $wpdb; //This is used only if making any database queries

		/*
		 * First, lets decide how many records per page to show
		 */

		global $kong_helpdesk_options;      

		$per_page = isset($kong_helpdesk_options['inbox_post_per_page']) ? $kong_helpdesk_options['inbox_post_per_page'] : 10;


		/*
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		/*
		 * REQUIRED. Finally, we build an array to be used by the class for column
		 * headers. The $this->_column_headers property takes an array which contains
		 * three other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = array( $columns, $hidden, $sortable );

		/**
		 * Optional. You can handle your bulk actions however you see fit. In this
		 * case, we'll handle them within our package just to keep things clean.
		 */
		$this->process_bulk_action();

		/*
		 * GET THE DATA!
		 * 
		 * Instead of querying a database, we're going to fetch the example data
		 * property we created for use in this plugin. This makes this example
		 * package slightly different than one you might build on your own. In
		 * this example, we'll be using array manipulation to sort and paginate
		 * our dummy data.
		 * 
		 * In a real-world situation, this is probably where you would want to 
		 * make your actual database query. Likewise, you will probably want to
		 * use any posted sort or pagination data to build a custom query instead, 
		 * as you'll then be able to use the returned query data immediately.
		 *
		 * For information on making queries in WordPress, see this Codex entry:
		 * http://codex.wordpress.org/Class_Reference/wpdb
		 */
		$data = $this->get_tickets($per_page);

		//print_r($data);

		/*
		 * This checks for sorting input and sorts the data in our array of dummy
		 * data accordingly (using a custom usort_reorder() function). It's for 
		 * example purposes only.
		 *
		 * In a real-world situation involving a database, you would probably want
		 * to handle sorting by passing the 'orderby' and 'order' values directly
		 * to a custom query. The returned data will be pre-sorted, and this array
		 * sorting technique would be unnecessary. In other words: remove this when
		 * you implement your own query.
		 */
		usort( $data, array( $this, 'usort_reorder' ) );

		/*
		 * REQUIRED for pagination. Let's figure out what page the user is currently
		 * looking at. We'll need this later, so you should always include it in
		 * your own package classes.
		 */
		$current_page = $this->get_pagenum();

		/*
		 * REQUIRED for pagination. Let's check how many items are in our data array.
		 * In real-world use, this would be the total number of items in your database,
		 * without filtering. We'll need this later, so you should always include it
		 * in your own package classes.
		 */
		//$total_items_arr = $this->get_tickets('-1');
		//$total_items = count( $this->get_tickets() );
		$total_items = count( $this->get_tickets('-1') );

		/*
		 * The WP_List_Table class does not handle pagination for us, so we need
		 * to ensure that the data is trimmed to only the current page. We can use
		 * array_slice() to do that.
		 */
		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		/*
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		 */


		$this->items = $this->get_tickets($per_page);

		

		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items,                     // WE have to calculate the total number of items.
			'per_page'    => $per_page,                        // WE have to determine how many items to show on a page.
			'total_pages' => ceil( $total_items / $per_page ), // WE have to calculate the total number of pages.
		) );

	
	}

	// get all tickets
	protected function get_tickets($per_page) {
		global $wp_query;
		$taxonomy = $terms_slug ='';

		if(strpos($_REQUEST['page'],'konginbox' ) !== false){
			$taxonomy = 'ticket_status';
			$terms_slug = substr(strstr($_REQUEST['page'], '-'),1); 
		    
		}
		if(strpos($_REQUEST['page'],'kongfolder') !== false){
			$taxonomy = 'ticket_system';
			$terms_slug = substr(strstr($_REQUEST['page'], '-'),1);
		}


		$terms_slug = substr(strstr($_REQUEST['page'], '-'),1);
		$tickets_data =array();
		$paged = isset( $_REQUEST['paged'] ) ? $_REQUEST['paged'] : 1;
		$args = array(
            	//'author'        => get_current_user_id(),
				'paged' => $paged,
                'post_type' => 'ticket',
                'order' => 'DESC',
                'orderby' => 'post_date',
                'posts_per_page' => $per_page,
                'post_status' => 'publish',
                'tax_query' => array(
                  array(
                      'taxonomy' => $taxonomy,
                      'field' => 'slug',
        	           'terms' => $terms_slug
             )
            )
          );

		if ( !current_user_can( 'manage_options' ) ) {
		    $args['author'] = get_current_user_id();
		} 

		$wp_query = new wp_query($args);

        if($wp_query->have_posts()) { 
           	while($wp_query->have_posts()) { 
           		$wp_query->the_post();
           		global $post;
           		$term_list = '';
           		$customer = $ticket_summary = $assigned_to = $term_list_string = $last_updated ='';

           		if ( !empty( $post->post_author ) ) {
                   $author = get_userdata($post->post_author)->data;
                   $url = admin_url('edit.php?post_type=ticket&author=' . $author->ID);
                   $customer = '<a href="'.$url.'" target="_blank"><span>'.$author->display_name.' </span>'.get_avatar($post->post_author, 50, '', '', array('class' => 'helpdesk-avatar')).'</a>';
                }

                $agentID = get_post_meta( $post->ID, 'agent', true );
                if ( empty( $agentID ) ) {
                    $assigned_to = 'Unassigned';
                } else {
                    $author = get_userdata($agentID);
                    $url = admin_url('edit.php?post_type=ticket&author=' . $author->ID);
                    $assigned_to = '<a href="'.$url.'" target="_blank"><span>'.$author->display_name.'</span></a>';
                }

                $term_list = wp_get_post_terms( $post->ID, 'ticket_system', array( 'fields' => 'names' ) );

                if($term_list){
                	$term_list_string = implode(',', $term_list);
                }
                
                $title = $post->post_title;
                $args = array(
			        'post_id' => $post->ID,
			        'orderby' => array('comment_date'),
			        'order' => 'DESC',
			    );

			    $comment = get_comments( $args );
			    $comment_count = count($comment);
   				$comment_latest = isset($comment[0]->comment_content)? $comment[0]->comment_content : 0;
   				$last_updated = human_time_diff(get_the_time ( 'U' ), current_time( 'timestamp' )). ' ago';
   			
   				$ticket_summary = '<div class="info-warp"><div class="cat-name"><span>'.$term_list_string.'</span><p>'.$title.'</p></div><p>'.$comment_latest.'</p><span class="cat-count">'.$comment_count.'</span></div>';


           		$tickets_data[] = array(
           			'ticket_id' => $post->ID,
           			'customer' => $customer,
           			'ticket_summary' => $ticket_summary,
					'assigned_to' => $assigned_to,
					'last_updated' => $last_updated
           		);
           	}
       	}

          
            // update_post_meta(get_the_ID(),'wpb_post_views_count','0');

		return $tickets_data;
	}

	function column_ticket_id($item) {
	  $actions = array(
	            'edit'      => sprintf('<a href="post.php?post=%s&action=%s">Edit</a>',$item['ticket_id'],'edit'),

	           
	        );

	  return sprintf('%1$s %2$s', $item['ticket_id'], $this->row_actions($actions) );
	}

	/**
	 * Callback to allow sorting of example data.
	 *
	 * @param string $a First value.
	 * @param string $b Second value.
	 *
	 * @return int
	 */
	protected function usort_reorder( $a, $b ) {
		// If no sort, default to title.
		$orderby = ! empty( $_REQUEST['orderby'] ) ? wp_unslash( $_REQUEST['orderby'] ) : 'ticket_id'; // WPCS: Input var ok.

		// If no order, default to asc.
		$order = ! empty( $_REQUEST['order'] ) ? wp_unslash( $_REQUEST['order'] ) : 'asc'; // WPCS: Input var ok.

		// Determine sort order.
		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );

		return ( 'asc' === $order ) ? $result : - $result;
	}
}