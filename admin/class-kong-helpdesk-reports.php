<?php

class Kong_Helpdesk_Reports extends Kong_Helpdesk
{
    protected $plugin_name;
    protected $version;

    /**
     * Construct Reports Class
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $plugin_name        [description]
     * @param   [type]                       $version            [description]
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Init Reports Page in Admin
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @return  [type]                       [description]
     */
    public function init()
    {
        global $kong_helpdesk_options;
        $this->options = $kong_helpdesk_options;
        
       add_menu_page(
            'Reports',
            'Reports',
            'manage_options',
            'helpdesk-reports',
            array($this, 'render_helpdesk_reports'),
            '',
            '3'
        );
    }

    /**
     * Action for date filter
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @return  [type]                       [description]
     */
    public function filter_report()
    {
        $query = array(
           // 'post_type' => 'ticket',
            'page' => 'helpdesk-reports',
            'date_from' => $_GET['date_from'],
            'date_until' => $_GET['date_until'],
        );
        $url = admin_url('admin.php?' . http_build_query($query));
        wp_redirect($url);
        exit();
    }

    /**
     * Display callback for report page
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @return  [type]                       [description]
     */
    public function render_helpdesk_reports()
    {

        $date_from = '';
        if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
           $date_from = $this->validateDate($_GET['date_from']);
            if ($date_from) {
                $date_from = $_GET['date_from'];
            } else {
                echo __('Date From is not valid!', 'kong-helpdesk');
            }
        }

        $date_until = '';
        if (isset($_GET['date_until']) && !empty($_GET['date_until'])) {
            $date_until = $this->validateDate($_GET['date_until']);
            if ($date_until) {
                $date_until = $_GET['date_until'];
            } else {
                echo __('Date Until is not valid!', 'kong-helpdesk');
            }
        }

        $date_query = array();
        if ($date_until && $date_from) {
            $date_query = array(
                'after' => $date_from,
                'before' => $date_until,
                'inclusive' => true,
            );
        } elseif ($date_from) {
            $date_query = array(
                'after' => $date_from,
            );
        } elseif ($date_until) {
            $date_query = array(
                'before' => $date_until,
            );
        }

        $args = array(
            'post_type' => 'ticket',
            'posts_per_page' => -1,
        );

        if (!empty($date_query)) {
            $args['date_query'] = $date_query;
        }

        $tickets = get_posts( $args);
         
       

        $years = array();
        $months = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');

        $firstTicket = end($tickets);
        $firstTicket_post_date = '';
        if(isset($firstTicket->post_date)){
            $firstTicket_post_date = $firstTicket->post_date;
           
        }
        $firstTicketDateTime = new DateTime($firstTicket_post_date);
        $firstTicketYear = (int) $firstTicketDateTime->format('Y');

        $ticketsCreatedByMonth = array();
        $ticketsCreatedByYear = array();
        $ticketsCreatedByYearMonth = array();
        for ($i = $firstTicketYear; $i <= date('Y'); $i++) {
            $years[] = $i;
            $ticketsCreatedByYear[$i] = array(
                'label' => $i,
                'value' => 0,
            );
            foreach ($months as $month) {
                $ticketsCreatedByYearMonth[$month] = array(
                    'y' => $month,
                    $i => 0,
                );
                $ticketsCreatedByMonth[$month] = array(
                    'label' => $month,
                    'value' => 0,
                );
            }
        }

        $ticketsAssignedToAgent = array();
        $ticketsAssignedToAgents =array();
        $ticketsByReporter = array();
        $ticketsBySatisfaction = array();
        $ticketsBySource = array();
        $ticketsByagents = array();
        $ticket_ids = array();
        $ticket_ids = wp_list_pluck( $tickets, 'ID' );
        
        
        foreach ($tickets as $ticket) {
            $d = new DateTime($ticket->post_date);
            $year_created = $d->format('Y');
            $month_created = $d->format('m');
            $weekday_number_created = $d->format('N');
            $weekday_name_created = $d->format('D');
            $weekday_count = 1;
            
            $ticketsCreatedByYearMonth[$month_created][$year_created]++;
            //Tickets by agents
            $agent = get_post_meta($ticket->ID, 'agent', true);
            $agent_email = $agent_display_name = '';
            $agent_total_reply = 0;
           
        
            if (empty($agent)) {
                $agent = __('unassigned', 'kong-helpdesk');
            } else {        
                $agent_details = get_userdata($agent)->data;
                $agent = $agent_details->user_email;
            }

            $count = 1;
            if (isset($ticketsAssignedToAgent[$agent])) {
                $count = $ticketsAssignedToAgent[$agent]['value'] + 1;
                
            }

            $ticketsAssignedToAgent[$agent] = array(
                'label' => $agent,
                'value' => $count,
            );


            // Tickets by Reporter
            $reporter = $ticket->post_author;
            if (empty($ticket->post_author)) {
                $reporter = 'None';
            } else {
                $reporter = get_userdata($reporter)->data->display_name;
            }

            $count = 1;
            if (isset($ticketsByReporter[$reporter])) {
                $count = $ticketsByReporter[$reporter]['value'] + 1;
            }
            $ticketsByReporter[$reporter] = array(
                'label' => $reporter,
                'value' => $count,
            );

            // Tickets by Satisfaction
            $satisfied = get_post_meta($ticket->ID, 'satisfied', true);
            if (empty($satisfied)) {
                $satisfied = __('Waiting', 'kong-helpdesk');
            }

            $count = 1;
            if (isset($ticketsBySatisfaction[$satisfied])) {
                $count = $ticketsBySatisfaction[$satisfied]['value'] + 1;
            }
            $ticketsBySatisfaction[$satisfied] = array(
                'label' => $satisfied,
                'value' => $count,
            );

            // Tickets by Source
            $source = get_post_meta($ticket->ID, 'source', true);
            if (empty($source)) {
                $source = __('No Source', 'kong-helpdesk');
            }

            $count = 1;
            if (isset($ticketsBySource[$source])) {
                $count = $ticketsBySource[$source]['value'] + 1;
            }
            $ticketsBySource[$source] = array(
                'label' => $source,
                'value' => $count,
            );

            // Tickets By Year
            if (isset($ticketsCreatedByYear[$year_created])) {
                $count = $ticketsCreatedByYear[$year_created]['value'] + 1;
            }
            $ticketsCreatedByYear[$year_created] = array(
                'label' => $year_created,
                'value' => $count,
            );

            // Tickets by Month
            if (isset($ticketsCreatedByMonth[$month_created])) {
                $count = $ticketsCreatedByMonth[$month_created]['value'] + 1;
            }
            $ticketsCreatedByMonth[$month_created] = array(
                'label' => $month_created,
                'value' => $count,
            );
        }

        
    

        $ticketsByStatus = $this->get_tickets_by_status();
        $ticketsByType = $this->get_tickets_by_type();
        $ticketsByPriority = $this->get_tickets_by_priority();

        // tickets by department
        $ticketsBySystem = $this->get_tickets_by_system($ticket_ids);
        echo '#department';
        //print_r($ticketsBySystem);
        
        // tickets by agents
        $except= ''; // unassigned;
        $ticketsByagents = $this->get_tickets_by_agents($ticketsAssignedToAgent , $ticket_ids,$except);
        echo '#agents';
        //print_r($ticketsByagents);

        // Time to first reply graph
        $timeto_first_reply_response = $this->first_response_time_selected_period($ticketsByagents,$ticket_ids);
        echo '#firstreplygraph';
        //print_r($timeto_first_reply_response);

        //weekdayreport
        echo '#weekdayreport';
        $buseiest_day_response = $this->get_busiest_time_by_agents($ticketsByagents,$ticket_ids);
        //print_r($buseiest_day_response);
        
        ?>
       
        <div class="wrap">
            <div class="kong-helpdesk-container">
                <h1><?php _e('Reports', 'kong-helpdesk'); ?></h1>
                <form action="<?php echo admin_url('edit.php?page=helpdesk-reports') ?>" method="get" style="background-color: #FFF; padding: 5px 20px 20px;">
                    <h2><?php _e('Date Filter', 'kong-helpdesk') ?></h2>
                    <input type="hidden" name="action" value="kong_helpdesk_report_filter">
                     <div class="kong-helpdesk-row">
                        <div class="kong-helpdesk-col-sm-2">
                            <label for="date_from"><?php echo __('Date From (JJJJ-MM-DD)', 'kong-helpdesk') ?></label><br/>
                            <input type="text" name="date_from" placeholder="JJJJ-MM-DD" value="<?php echo $date_from ?>">
                        </div>
                        <div class="kong-helpdesk-col-sm-2">
                            <label for="date_until"><?php echo __('Date Until (JJJJ-MM-DD)', 'kong-helpdesk') ?></label><br/>
                            <input type="text" name="date_until" placeholder="JJJJ-MM-DD" value="<?php echo $date_until ?>">
                        </div>
                        <div class="kong-helpdesk-col-sm-2">
                            <br/><input type="submit" class="button button-primary" value="<?php echo __('Submit', 'kong-helpdesk') ?>" >
                        </div>
                    </div>
                </form>
                <div class="kong-helpdesk-col-sm-12">
                    <h2><?php echo __('Filtered', 'kong-helpdesk') ?></h2>
                    <p><?php echo __('Filtered Tickets:', 'kong-helpdesk') . ' ' . count($tickets) ?></p>
                </div>
                <div class="kong-helpdesk-row">
                    <div class="kong-helpdesk-col-sm-3">
                        <h3 class="kong-helpdesk-center"><?php echo __('Tickets by Agent', 'kong-helpdesk') ?></h3>
                        <div id="tickets-by-agent"></div>
                        <script>Morris.Donut({
                          element: 'tickets-by-agent',
                          colors: ['#F44336', '#2196F3', '#FFEB3B', '#4CAF50', '#FF9800', '#795548', '#673AB7'],
                          data: <?php echo json_encode(array_values($ticketsAssignedToAgent)) ?>
                        });</script>
                    </div>
                    <div class="kong-helpdesk-col-sm-3">
                        <h3 class="kong-helpdesk-center"><?php echo __('Tickets by Reporter', 'kong-helpdesk') ?></h3>
                        <div id="tickets-by-reporter"></div>
                        <script>Morris.Donut({
                          element: 'tickets-by-reporter',
                          colors: ['#F44336', '#2196F3', '#FFEB3B', '#4CAF50', '#FF9800', '#795548', '#673AB7'],
                          data: <?php echo json_encode(array_values($ticketsByReporter)) ?>
                        });</script>
                    </div>
                    <div class="kong-helpdesk-col-sm-3">
                        <h3 class="kong-helpdesk-center"><?php echo __('Tickets by Satisfaction', 'kong-helpdesk') ?></h3>
                        <div id="tickets-by-satisfaction"></div>
                        <script>Morris.Donut({
                          element: 'tickets-by-satisfaction',
                          colors: ['#F44336', '#2196F3', '#FFEB3B', '#4CAF50', '#FF9800', '#795548', '#673AB7'],
                          data: <?php echo json_encode(array_values($ticketsBySatisfaction)) ?>
                        });</script>
                    </div>
                    <div class="kong-helpdesk-col-sm-3">
                        <h3 class="kong-helpdesk-center"><?php echo __('Tickets by Source', 'kong-helpdesk') ?></h3>
                        <div id="tickets-by-source"></div>
                        <script>Morris.Donut({
                          element: 'tickets-by-source',
                          colors: ['#F44336', '#2196F3', '#FFEB3B', '#4CAF50', '#FF9800', '#795548', '#673AB7'],
                          data: <?php echo json_encode(array_values($ticketsBySource)) ?>
                        });</script>
                    </div>
                    <div class="kong-helpdesk-col-sm-3">
                        <h3 class="kong-helpdesk-center"><?php echo __('Tickets by Year', 'kong-helpdesk') ?></h3>
                        <div id="tickets-by-year"></div>
                        <script>Morris.Donut({
                          element: 'tickets-by-year',
                          colors: ['#F44336', '#2196F3', '#FFEB3B', '#4CAF50', '#FF9800', '#795548', '#673AB7'],
                          data: <?php echo json_encode(array_values($ticketsCreatedByYear)) ?>
                        });</script>
                    </div>
                    <div class="kong-helpdesk-col-sm-3">
                        <h3 class="kong-helpdesk-center"><?php echo __('Tickets by Month', 'kong-helpdesk') ?></h3>
                        <div id="tickets-by-month"></div>
                        <script>Morris.Donut({
                          element: 'tickets-by-month',
                          colors: ['#F44336', '#2196F3', '#FFEB3B', '#4CAF50', '#FF9800', '#795548', '#673AB7'],
                          data: <?php echo json_encode(array_values($ticketsCreatedByMonth)) ?>
                        });</script>
                    </div>
                    <div class="kong-helpdesk-col-sm-6">
                        <h3 class="kong-helpdesk-center"><?php echo __('Tickets created by Year / Month', 'kong-helpdesk') ?></h3>
                        <div id="tickets-by-year-month"></div>
                        <script>Morris.Line({
                            element: 'tickets-by-year-month',
                            colors: ['#F44336', '#2196F3', '#FFEB3B', '#4CAF50', '#FF9800', '#795548', '#673AB7'],
                            data: <?php echo json_encode(array_values($ticketsCreatedByYearMonth)) ?>,
                            xkey: 'y',
                            ykeys: <?php echo json_encode(array_values($years)) ?>,
                            labels: <?php echo json_encode(array_values($years)) ?>,
                            xLabels: 'year-month',
                            parseTime: false
                        });
                        </script>
                    </div>
                </div>
                <div class="kong-helpdesk-row">
                    <div class="kong-helpdesk-col-sm-12">
                        <h2><?php echo __('Total', 'kong-helpdesk') ?></h2>
                        <p><?php echo __('No Date filter applied here:', 'kong-helpdesk') ?></p>
                    </div>
                    <div class="kong-helpdesk-col-sm-3">
                        <h3 class="kong-helpdesk-center"><?php echo __('Tickets by Status', 'kong-helpdesk') ?></h3>
                        <div id="tickets-by-status"></div>
                        <script>Morris.Donut({
                          element: 'tickets-by-status',
                          colors: ['#F44336', '#2196F3', '#FFEB3B', '#4CAF50', '#FF9800', '#795548', '#673AB7'],
                          data: <?php echo json_encode(array_values($ticketsByStatus)) ?>
                        });</script>
                    </div>
                    <div class="kong-helpdesk-col-sm-3">
                        <h3 class="kong-helpdesk-center"><?php echo __('Tickets by Type', 'kong-helpdesk') ?></h3>
                        <div id="tickets-by-type"></div>
                        <script>Morris.Donut({
                          element: 'tickets-by-type',
                          colors: ['#F44336', '#2196F3', '#FFEB3B', '#4CAF50', '#FF9800', '#795548', '#673AB7'],
                          data: <?php echo json_encode(array_values($ticketsByType)) ?>
                        });</script>
                    </div>
                    <div class="kong-helpdesk-col-sm-3">
                        <h3 class="kong-helpdesk-center"><?php echo __('Tickets by Department', 'kong-helpdesk') ?></h3>
                        <div id="tickets-by-system"></div>
                        <script>Morris.Donut({
                          element: 'tickets-by-system',
                          colors: ['#F44336', '#2196F3', '#FFEB3B', '#4CAF50', '#FF9800', '#795548', '#673AB7'],
                          data: <?php echo json_encode(array_values($ticketsBySystem)) ?>
                        });</script>
                    </div>
                    <div class="kong-helpdesk-col-sm-3">
                        <h3 class="kong-helpdesk-center"><?php echo __('Tickets by Priority', 'kong-helpdesk') ?></h3>
                        <div id="tickets-by-priority"></div>
                        <script>Morris.Donut({
                          element: 'tickets-by-priority',
                          colors: ['#F44336', '#2196F3', '#FFEB3B', '#4CAF50', '#FF9800', '#795548', '#673AB7'],
                          data: <?php echo json_encode(array_values($ticketsByPriority)) ?>
                        });</script>
                    </div>
                    <div class="kong-helpdesk-col-sm-6">
                        <h3 class="kong-helpdesk-center">Created / Completed Tickets by Month</h3>
                        <div id="created-completed-tickets-by-month"></div>
                        <script>Morris.Bar({
                            element: 'created-completed-tickets-by-month',
                            colors: ['#F44336', '#2196F3', '#FFEB3B', '#4CAF50', '#FF9800', '#795548', '#673AB7'],
                            data: [
                                { y: '2006', a: 100, b: 90 },
                                { y: '2007', a: 75,  b: 65 },
                                { y: '2008', a: 50,  b: 40 },
                                { y: '2009', a: 75,  b: 65 },
                                { y: '2010', a: 50,  b: 40 },
                                { y: '2011', a: 75,  b: 65 },
                                { y: '2012', a: 100, b: 90 }
                              ],
                              xkey: 'y',
                              ykeys: ['a', 'b'],
                              labels: ['Series A', 'Series B']
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Get Tickets by Status
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * 
     * @return  [type]                       [description]
     */
    private function get_tickets_by_status()
    {
        $ticketsByStatus = array();
        $stati = get_terms('ticket_status', array('hide_empty' => false));
        foreach ($stati as $status) {
            $ticketsByStatus[$status->term_id] = array(
                'label' => $status->name,
                'value' => $status->count,
            );
        }

        return $ticketsByStatus;
    }

    /**
     * Get Tickets by Type
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @return  [type]                       [description]
     */
    private function get_tickets_by_type()
    {
        $ticketsByTypes = array();
        $types = get_terms('ticket_type', array('hide_empty' => false));
        foreach ($types as $type) {
            $ticketsByTypes[$type->term_id] = array(
                'label' => $type->name,
                'value' => $type->count,
            );
        }

        return $ticketsByTypes;
    }

    /**
     * Get Tickets by Type
     * @author CN
     * @version 1.0.0
     * @since   1.2.4
     * @return  [type]                       [description]
     */
    private function get_tickets_by_priority()
    {
        $ticketsByPriority = array();
        $priorities = get_terms('ticket_priority', array('hide_empty' => false));
        foreach ($priorities as $priority) {
            $ticketsByPriority[$priority->term_id] = array(
                'label' => $priority->name,
                'value' => $priority->count,
            );
        }

        return $ticketsByPriority;
    }

    /**
     * Get Tickets by System
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     */
    private function get_tickets_by_system($ticket_ids)
    {
        
        $ticketsBySystems = array();
        foreach($ticket_ids as $ticketid) {
            
            $term_list = wp_get_post_terms( $ticketid, 'ticket_system', array( 'fields' => 'all' ) );
            foreach ($term_list as $key=>$val) {
                $count = 1;
                if(isset($ticketsBySystems[$val->term_id])) {
                    $count = $ticketsBySystems[$val->term_id]['count']+1;
                }
                  $ticketsBySystems[$val->term_id]= array('label'=>$val->name,'count'=>$count);
              }
        }
        return $ticketsBySystems;
    }

    /**
     * Validate Date
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @param   [type]                       $date   [description]
     * @param   string                       $format [description]
     * @return  [type]                               [description]
     */
    private function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    // return comments by userid 
    private function get_comments_by_userid($userid ,$ticket_ids) {
        $args = array(
            'comment_status' => 'approve',
            'status'  => 'approve', 
            'order'   => 'ASC', 
            'orderby' => 'comment_date_gmt',
            'user_id' =>$userid,
            'post__in' => $ticket_ids
        );
        $comments = get_comments( $args );
        return $comments;
    }

    // return total number of solved tickets by user 
    private function get_solved_tickets_by_userid($userid , $ticket_ids) {

         $solved_array = get_posts(
            array(
                'showposts' => -1,
                'post__in' => $ticket_ids,
                'post_type' => 'ticket',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'ticket_status',
                        'field' => 'slug',
                        'terms' => 'closed',
                    )
                ),
                'meta_query' => array(
                   array(
                       'key' => 'agent',
                       'value' => $userid,
                       'compare' => '=',
                   )
               )
            )
            );
            return count($solved_array); 

    }

    // return total number of reply/comment by user 
    private function get_reply_count_by_userid($userid , $ticket_ids) {

         $comments = $this->get_comments_by_userid($userid , $ticket_ids);
         return count($comments);

    }

    // return Average first response time in selected period
    private function first_response_time_selected_period($agents , $ticket_ids){
        $data = $agent_detail = [];
        $total_hours = $total_comment = $avg_response_time = 0;
        $period_value ='';

        if(!empty($agents)) {
            foreach ($agents as $agent) {
                $agent_detail = $this->get_first_response_time_by_userid($agent['ID'],$ticket_ids);
                $total_hours += $agent_detail['total_hours'];
                $total_comment += $agent_detail['total_comment'];        

            }
            $avg_response_time = round($total_hours/$total_comment,2);

            if($avg_response_time < 2 ) {
                $period_value = '0-1';
            }else if($avg_response_time >=2 && $avg_response_time < 8 ){
                $period_value = '1-8';
            }else if($avg_response_time >=8 && $avg_response_time < 24 ){
                $period_value = '8-24';
            }else{
                $period_value = '>24';
            }
        }



        return (['total_hours'=>$total_hours,'period'=>$period_value,'total_comment'=>$total_comment, 'avg_response_time'=>$avg_response_time]);
    }

    // return avg response time for first response of user
    private function get_first_response_time_by_userid($userid ,$ticket_ids) {

        $comments = $this->get_comments_by_userid($userid , $ticket_ids);
        $comment = $first_comment = [];
        $calculate_interval = $avg_response_time = 0;
        $total_tickets = 0;

        if(!empty($comments)) {
            foreach ($comments as $key => $value) {
                if (!in_array($value->comment_post_ID, $first_comment))
                {
                    $first_comment[] = $value->comment_post_ID; 
                    $post_date = strtotime(get_the_time('Y-m-d H:i:s', $value->comment_post_ID));
                    $comment_date = strtotime($value->comment_date);
                    $interval = abs($comment_date - $post_date);
                    $days    = floor($interval / 86400);
                    $hours   = round($interval / ( 60 * 60 ),2);
                    $minutes = round($interval / ( 60 * 60 * 60));
                    $seconds = round($interval / ( 60 * 60 * 60 * 60 ));
                    $calculate_interval += $hours;


                    $comment[$value->comment_ID] = [
                        'post_id' => $value->comment_post_ID,
                        'posts_date' => get_the_time('Y-m-d H:i:s', $value->comment_post_ID),
                        'comment_date' => date('Y-m-d H:i:s', strtotime($value->comment_date)),
                        'comment_time_hours' => date('H', strtotime($value->comment_date)),
                        'comment_weekday_hours' => date('D', strtotime($value->comment_date)),
                        'interval'=> $hours
                    ];
                    $total_tickets ++;
                }
                
            }
            $avg_response_time = round($calculate_interval / count($comment),2);
        }

       

        return ['data'=>$comment,'total_comment'=>$total_tickets,'total_hours'=>$calculate_interval,'avg_response_time'=>$avg_response_time];

    
    }

    // return avg, response time for all response of user
    private function get_response_time_by_userid($userid ,$ticket_ids) {

        $comments = $this->get_comments_by_userid($userid , $ticket_ids);
        $comment =  [];
        $calculate_interval = $avg_response_time = 0;
        $total_tickets = 0;

        if(!empty($comments)) {
            foreach ($comments as $key => $value) {
                $post_date = strtotime(get_the_time('Y-m-d H:i:s', $value->comment_post_ID));
                $comment_date = strtotime($value->comment_date);
                $interval = abs($comment_date - $post_date);
                $days    = floor($interval / 86400);
                $hours   = round($interval / ( 60 * 60 ),2);
                $minutes = round($interval / ( 60 * 60 * 60));
                $seconds = round($interval / ( 60 * 60 * 60 * 60 ));
                $calculate_interval += $hours;
                $comment[$value->comment_ID] = [
                    'post_id' => $value->comment_post_ID,
                    'posts_date' => get_the_time('Y-m-d H:i:s', $value->comment_post_ID),
                    'comment_date' => date('Y-m-d H:i:s', strtotime($value->comment_date)),
                    'comment_time_hours' => date('H', strtotime($value->comment_date)),
                    'comment_weekday_hours' => date('D', strtotime($value->comment_date)),
                    'interval'=> $hours
                ];
                $total_tickets ++;
            }
            $avg_response_time = round($calculate_interval / count($comment),2);
        }

       

        return ['data'=>$comment,'total_comment'=>$total_tickets,'total_hours'=>$calculate_interval,'avg_response_time'=>$avg_response_time];

    
    }

    // return avg, response time for user
    private function get_busiest_time_by_agents($agents ,$ticket_ids) {
        $weekdays = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
        $times = $this->get_busiest_time_interval_array();
        $busiest_days = [];
        foreach($weekdays as $week) {
            $busiest_days[$week] = $times;
        }
       
        if(!empty($agents)) {
            foreach ($agents as $agent) {
                $count =1;
                $agent_detail = $this->get_response_time_by_userid($agent['ID'],$ticket_ids);
                foreach($agent_detail['data'] as $res){
                    $time_interval = $this->get_busiest_time_interval_by_hours($res['comment_time_hours']);
                    if(isset($busiest_days[$res['comment_weekday_hours']][$time_interval])) {
                        $count = $busiest_days[$res['comment_weekday_hours']][$time_interval] + 1;
                    }
                    $busiest_days[$res['comment_weekday_hours']][$time_interval] = $count;
                }
                

            }
          
        }
      return ($busiest_days);
    
    }


    // return details of assigned agents
    private function get_tickets_by_agents($agents = [] , $ticket_ids=[] ) {
        if(is_array($agents)) {
            unset($agents["unassigned"]); 
            $ticket_by_agents =array();
            foreach ($agents as $key => $agent) {
                 $user = get_user_by( 'email', $key );
                 $ticket_by_agents[$key] = array(
                    'ID'=>($user->ID > 0 ?$user->ID :0),
                    'email' => $agent['label'],
                    'no_of_tickets_assigned' => $agent['value'],
                    'displayname' => $user->display_name,
                    'reply_count' => $this->get_reply_count_by_userid($user->ID,$ticket_ids),
                    'solved'    => $this->get_solved_tickets_by_userid($user->ID, $ticket_ids),
                    'avg_response_time' => $this->get_first_response_time_by_userid($user->ID , $ticket_ids)['avg_response_time']
                 );
                
            }
        }

        return $ticket_by_agents;

    }

    // return details of assigned agents with unassigned
    private function get_tickets_by_agents_with_unassigned($agents = [] , $ticket_ids=[] ) {
        if(is_array($agents)) {
            $ticket_by_agents =array();
            foreach ($agents as $key => $agent) {
                 $user = get_user_by( 'email', $key );
                 $ticket_by_agents[$key] = array(
                    'ID'=>($user->ID > 0 ?$user->ID :0),
                    'email' => $agent['label'],
                    'no_of_tickets_assigned' => $agent['value'],
                    'displayname' => $user->display_name,
                    'reply_count' => $this->get_reply_count_by_userid($user->ID,$ticket_ids),
                    'solved'    => $this->get_solved_tickets_by_userid($user->ID, $ticket_ids),
                    'avg_response_time' => $this->get_first_response_time_by_userid($user->ID , $ticket_ids)['avg_response_time']
                 );
                
            }
        }

        return $ticket_by_agents;

    }

    // return busiest day time interval array
    private function get_busiest_time_interval_array() {

        $buseiest_day = [];
        $count=0;
        for($i=0;$i<24;$i+=2) {
            $buseiest_day[$i.'-'.($i+2)] = 0;
        }

        return $buseiest_day;

    }

    // return busiest day time interval by hour
    private function get_busiest_time_interval_by_hours($hours) {

        $buseiest_day = $this->get_busiest_time_interval_array();
        
        foreach ($buseiest_day as $key => $value) {
            $val = explode("-", $key);
            if ( in_array($hours, range($val[0], $val[1])) ) {
                return $val[0].'-'.$val[1];
            }
        }

    
                
    }

}