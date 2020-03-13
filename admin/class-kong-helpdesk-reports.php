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
     * @param   [type]                                           
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
     * @return  [type]  
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
            '4'
        );
    }

    /**
     * Action for date filter
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @return  [type]  
     */
    public function filter_report()
    {
        $query = array(
           // 'post_type' => 'ticket',
            'page' => 'helpdesk-reports',
            'primary_range' => $_GET['primary_range'],
            'date_from' => $_GET['date_from'],
            'date_until' => $_GET['date_until'],
            'compare_range' => $_GET['compare_range'],
            'date_from_compare' => $_GET['date_from_compare'],
            'date_until_compare' => $_GET['date_until_compare'],
            
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
     * @return  [type]  
     */
    public function render_helpdesk_reports()
    {
        $format ='Y-m-d';
        $date_from = date($format, strtotime('today - 30 days'));
        $date_until = date($format);
        $date_from_compare = date($format, strtotime('today - 30 days'));
        $date_until_compare = date($format);
        $primary_range = isset($_GET['primary_range'])? $_GET['primary_range'] : 'last_30_days';
        $compare_range = isset($_GET['compare_range'])? $_GET['compare_range'] : '';
        $ticket_created = $ticket_created_compare = [];
        $tickets = $compare_tickets = $primary_tickets_data = $compare_tickets_data = [];
        $date_query = $date_query_compare = $alldates = [];

        if (isset($_GET['date_from']) && !empty($_GET['date_from'])) {
            $date_from = $this->validateDate($_GET['date_from']);
             if ($date_from) {
                 $date_from = $_GET['date_from'];
 
             } else {
                 echo __('Primary Date From is not valid!', 'kong-helpdesk');
             }
         }
 
         
         if (isset($_GET['date_until']) && !empty($_GET['date_until'])) {
             $date_until = $this->validateDate($_GET['date_until']);
             if ($date_until) {
                 $date_until = $_GET['date_until'];
             } else {
                 echo __('Primary Date Until is not valid!', 'kong-helpdesk');
             }
         }
         

        $ticket_created = $this->getalldates($primary_range,$format);
        
        $tickets = $this->getTicketsReports($ticket_created['start_date'],$ticket_created['end_date']);
        $primary_tickets_data = $this->ticketProcessing($tickets,$ticket_created);
        $primary_tickets_data['ticket_statictics']['title'] = 'Primary';
        $primary_tickets_data['first_reply_response']['title'] = 'Primary';

        //echo "#primary";
        //echo count($tickets);
        //print_r($primary_tickets_data);

        if(isset($_GET['compare_range']) && $_GET['compare_range']!='') {

            if (isset($_GET['date_from_compare']) && !empty($_GET['date_from_compare'])) {
                $date_from_compare = $this->validateDate($_GET['date_from_compare']);
                 if ($date_from_compare) {
                     $date_from_compare = $_GET['date_from_compare'];
     
                 } else {
                     echo __('Compare Date From is not valid!', 'kong-helpdesk');
                 }
             }
     
             
             if (isset($_GET['date_until_compare']) && !empty($_GET['date_until_compare'])) {
                 $date_until_compare = $this->validateDate($_GET['date_until_compare']);
                 if ($date_until_compare) {
                     $date_until_compare = $_GET['date_until_compare'];
                 } else {
                     echo __('Compare Date Until is not valid!', 'kong-helpdesk');
                 }
             }
             $ticket_created_compare = $this->getalldates($compare_range,$format);
             $compare_tickets = $this->getTicketsReports($ticket_created_compare['start_date'],$ticket_created_compare['end_date']);
             $compare_tickets_data = $this->ticketProcessing($compare_tickets,$ticket_created_compare);
             $compare_tickets_data['ticket_statictics']['title'] = 'Compare';
             $compare_tickets_data['first_reply_response']['title'] = 'Compare';
        }


        //echo "#compare";
        //echo count($compare_tickets);
        //print_r($compare_tickets_data);
        

        
        ?>


        <!-- Custom html -->

        <div class="warp">
            <div class="kong-helpdesk-container">
            <form action="<?php echo admin_url('edit.php?page=helpdesk-reports') ?>" method="get" style="background-color: #FFF; padding: 5px 20px 20px;">
                <input type="hidden" name="action" value="kong_helpdesk_report_filter">
                    <div class="row">
                        <div class="col s12 m6 l6">
                            <div class="helpdesk-input">
                                <div class="input-field">
                                    <label for="mail_template_select" class="active">Report Period:</label>
                                    <div class="inputs-btn">
                                        <select id="primary_range" name="primary_range" class="kong-helpdesk-col-sm-8 ng-pristine ng-valid ng-touched">
                                            <option trans="" <?php echo $primary_range =='last_30_days' ? 'selected="true"' : '';?> value="last_30_days">Last 30 days</option>
                                            <option trans="" value="last_month" <?php echo $primary_range=='last_month' ? 'selected="true"' : '';?>>Last Month</option>
                                            <option trans="" value="last_7_days" <?php echo $primary_range =='last_7_days' ? 'selected="true"' : '';?>>Last 7 days</option>
                                            <option trans="" value="last_week" <?php echo $primary_range =='last_week' ? 'selected="true"' : '';?>>Last Week</option>
                                            <option trans="" value="today" <?php echo $primary_range =='today' ? 'selected="true"' : '';?>>Today</option>
                                            <option trans="" value="custom" <?php echo $primary_range =='custom' ? 'selected="true"' : '';?>>Custom Dates</option>
                                        </select>
                                        <div class="kong-helpdesk-col-sm">
                                            <input type="submit" class="btn btn-golden" value="<?php echo __('Update', 'kong-helpdesk') ?>" >
                                        </div>
                                    </div>    
                                </div>
                                <div class="date_range_cls kong-helpdesk-row" style="display: none;">
                                    <div class="kong-helpdesk-col-sm-6">
                                        <label for="date_from"><?php echo __('Date From', 'kong-helpdesk') ?></label><br/>
                                        <input type="text" class="datepicker_chart" name="date_from" placeholder="YYYY-mm-dd" value="<?php echo $date_from ?>">
                                    </div>
                                    <div class="kong-helpdesk-col-sm-6">
                                        <label for="date_until"><?php echo __('Date Until', 'kong-helpdesk') ?></label><br/>
                                        <input type="text" class="datepicker_chart" name="date_until" placeholder="YYYY-mm-dd" value="<?php echo $date_until ?>">
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="col s12 m6 l6">
                            <div class="helpdesk-input">
                                <div class="input-field">
                                    <label for="mail_template_select" class="active">Compare Period:</label>
                                    <div class="inputs-btn">
                                        <select id="compare_range" name="compare_range" class="ng-pristine ng-valid ng-touched">
                                            <option value=''>Select Any</option>
                                            <option trans="" <?php echo isset($_GET['compare_range']) && $_GET['compare_range']=='last_30_days' ? 'selected="true"' : '';?> value="last_30_days">Last 30 days</option>
                                            <option trans="" value="last_month" <?php echo ($compare_range =='last_month') ? 'selected="true"' : '';?>>Last Month</option>
                                            <option trans="" value="last_7_days" <?php echo $compare_range =='last_7_days' ? 'selected="true"' : '';?>>Last 7 days</option>
                                            <option trans="" value="last_week" <?php echo $compare_range =='last_week' ? 'selected="true"' : '';?>>Last Week</option>
                                            <option trans="" value="today" <?php echo $compare_range =='today' ? 'selected="true"' : '';?>>Today</option>
                                            <option trans="" value="custom" <?php echo $compare_range =='custom' ? 'selected="true"' : '';?>>Custom Dates</option>
                                        </select>
                                        <div class="kong-helpdesk-col-sm">
                                            <input type="submit" class="button button-primary" value="<?php echo __('Compare', 'kong-helpdesk') ?>" >
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="date_range_cls kong-helpdesk-row" style="display: none;">
                                    <div class="kong-helpdesk-col-sm-6">
                                        <label for="date_from"><?php echo __('Date From', 'kong-helpdesk') ?></label><br/>
                                        <input type="text" class="datepicker_chart" name="date_from_compare"  value="<?php echo $date_from_compare; ?>">
                                    </div>
                                    <div class="kong-helpdesk-col-sm-6">
                                        <label for="date_until"><?php echo __('Date Until', 'kong-helpdesk') ?></label><br/>
                                        <input type="text" class="datepicker_chart" name="date_until_compare"  value="<?php echo $date_until_compare; ?>">
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </form> 
                <div>
                    <div class="left-graph kong-helpdesk-col-sm-7">
                        <!--- tickets graph ---->
                        <div class="ticket-graph">
                            <h4>Ticket Statistics</h4>
                            <ul>
                                <li>
                                    <span><?php echo isset($primary_tickets_data['ticket_statictics']['count'])?$primary_tickets_data['ticket_statictics']['count']: 0; ?></span>
                                    <p>New Tickets</p>
                                </li>
                                <li>
                                    <span><?php echo isset($primary_tickets_data['ticket_by_tags']['closed'])?$primary_tickets_data['ticket_by_tags']['closed']['count']: 0; ?></span>
                                    <p>Solved Tickets</p>
                                </li>
                                <li>
                                    <span><?php echo isset($primary_tickets_data['ticket_by_tags']['open'])?$primary_tickets_data['ticket_by_tags']['open']['count']: 0; ?></span>
                                    <p>Open Tickets</p>
                                </li>
                                <li>
                                    <span><?php echo isset($primary_tickets_data['first_reply_response']['total'])?$primary_tickets_data['first_reply_response']['total']: 0; ?> hours</span>
                                    <p>Time to first reply</p>
                                </li>
                                <li>
                                    <span><?php echo isset($primary_tickets_data['ticket_by_agents']['total'])?$primary_tickets_data['ticket_by_agents']['total']: 0; ?> hours</span>
                                    <p>Average response time</p>
                                </li>
                            </ul>
                            <?php if(isset($compare_tickets_data['ticket_statictics']))
                                    {?>
                            <h4>Ticket Statistics(Compare)</h4>
                            <ul>
                                <li>
                                    <span><?php echo isset($compare_tickets_data['ticket_statictics']['count'])?$compare_tickets_data['ticket_statictics']['count']: 0; ?></span>
                                    <p>New Tickets</p>
                                </li>
                                <li>
                                    <span><?php echo isset($compare_tickets_data['ticket_by_tags']['closed'])?$compare_tickets_data['ticket_by_tags']['closed']['count']: 0; ?></span>
                                    <p>Solved Tickets</p>
                                </li>
                                <li>
                                    <span><?php echo isset($compare_tickets_data['ticket_by_tags']['open'])?$compare_tickets_data['ticket_by_tags']['open']['count']: 0; ?></span>
                                    <p>Open Tickets</p>
                                </li>
                                <li>
                                    <span><?php echo isset($compare_tickets_data['first_reply_response']['total'])?$compare_tickets_data['first_reply_response']['total']: 0; ?> hours</span>
                                    <p>Time to first reply</p>
                                </li>
                                <li>
                                    <span><?php echo isset($compare_tickets_data['ticket_by_agents']['total'])?$compare_tickets_data['ticket_by_agents']['total']: 0; ?> hours</span>
                                    <p>Average response time</p>
                                </li>
                            </ul>
                            <?php } ?>
                            <div class="main-graph">
                                 <div id="ticket-statictics" class="ct-chart ct-major-tenth"></div>
                            <?php $ticket_statictics_chart = array($primary_tickets_data['ticket_statictics']);
                                if(isset($compare_tickets_data['ticket_statictics']))
                                    {
                                    array_push($ticket_statictics_chart,$compare_tickets_data['ticket_statictics']);
                                    }
                                    
                            ?>
                               <script  type="text/javascript" charset="utf-8" async defer>
                          
                                var line_chart = new Chartist.Line('#ticket-statictics', {
                                        series: [
                                            <?php foreach($ticket_statictics_chart as $ticket_stat){?>{
                                                name: '<?php echo $ticket_stat['title'];?>',
                                                data: [<?php foreach($ticket_stat['date_range'] as $key=>$value){?>
                                                        {x: new Date(<?php echo strtotime($key); ?>), y: '<?php echo $value;?>'},
                                                    <?php } ?>
                                                ]
                                             },
                                            <?php } ?>
                                        ],
                                        }, {
                                            width: '100%',
                                            height: '100%',
                                            axisX: {

                                                type: Chartist.FixedScaleAxis,
                                                divisor: 5,
                                                labelInterpolationFnc: function(value) {
                                                    //console.log(value);
                                                return moment.unix(value).format('MMM DD,YY');
                                                }
                                            },
                                            axisY: {
                                                onlyInteger: true,
                                                offset: 20,

                                              },

                                            showArea: true,
                                            lineSmooth: true,
                                            plugins: [
                                                Chartist.plugins.legend({
                                                  className: 'linechart_helpdesk'
                                                }),
                                                Chartist.plugins.tooltip({
                                                    html : true,
                                                    transformTooltipTextFnc: function(tooltip) {
                                                        var xy = tooltip.split(",");
                                                        return '<p>'+moment.unix(xy[0]).format('MMM DD,YY')+'<br/>New tickets : '+ xy[1]+ '</p>';
                                                      }
                                                })
                                            ]
                                        }).on('draw', function(data) {
                                            if(data.type === 'line' || data.type === 'area') {
                                                data.element.animate({
                                                d: {
                                                    begin: 2000 * data.index,
                                                    dur: 2000,
                                                    from: data.path.clone().scale(1, 0).translate(0, data.chartRect.height()).stringify(),
                                                    to: data.path.clone().stringify(),
                                                    easing: Chartist.Svg.Easing.easeOutQuint
                                                }
                                                });
                                            }
                                         }).on('created', function() {
                                                      if(window.__anim21278907127) {
                                                        clearTimeout(window.__anim21278907127);
                                                        window.__anim21278907127 = null;
                                                      }
                                                      window.__anim21278907127 = setTimeout(line_chart.update.bind(line_chart), 20000);
                                                    });

                        </script>
                   
                        </div>
                        </div>

                        
                    </div>   
                    <div class="right-graph kong-helpdesk-col-sm-5"> 
                        <div class="ticket-graph">
                            <h4>Time to first reply</h4>
                            <?php 
                          $timetofirstreply_chart = array($primary_tickets_data['first_reply_response']);
                           if(isset($compare_tickets_data['first_reply_response']))
                            {
                               array_push($timetofirstreply_chart,$compare_tickets_data['first_reply_response']);
                            }
                        
                        
                        $timetofirstreply_chart_keys = array_keys($timetofirstreply_chart[0]['data']);
                       // array_pop($timetofirstreply_chart_keys);
                       $timetofirstreply_chart_data =[];
                       foreach ($timetofirstreply_chart as $key => $value) {
                        
                                 $timetofirstreply_chart_data[] =array(
                                    'meta'=>$value['title'],
                                    'name'=>$value['title'],
                                    'data'=>array_column($value['data'], 'percentage')

                             );               
                       }
                      ?>
                            <div class="time-reply">
                                <h2>
                                    <?php echo isset($timetofirstreply_chart[0]['total'])?$timetofirstreply_chart[0]['total']: 0; ?> Hours
                                </h2>
                                <?php if(isset($compare_tickets_data['first_reply_response']))
                                {?>
                                   <h2>
                                    (Compare): <?php echo isset($timetofirstreply_chart[1]['total'])?$timetofirstreply_chart[1]['total']: 0; ?> Hours
                                </h2>
                                <?php }?>
                                 
                                <p>Average first response time in selected period</p>
                            </div>
                            <div class="main-graph">
                                 <div id="time-to-first-reply" class="ct-chart ct-major-tenth"></div>
                      
                       <script>
                           var bar_chart = new Chartist.Bar('#time-to-first-reply', {
                              labels: <?php echo json_encode($timetofirstreply_chart_keys);?>,
                              series: 
                                <?php echo json_encode($timetofirstreply_chart_data);?>
                              
                            }, {
                              width: '100%',
                              height: '300px',
                              seriesBarDistance: 10,
                              axisX: {
                                offset: 60
                              },
                              axisY: {
                                 onlyInteger: true,
                                offset: 80,
                                labelInterpolationFnc: function(value) {
                                  return value + ' %'
                                },
                                scaleMinSpace: 10
                              },
                               plugins: [
                                Chartist.plugins.tooltip({
                                    transformTooltipTextFnc: function(tooltip) {

                                        return 'Pertentage of all tickets : '+ tooltip + '%';
                                      }
                                })
                                ],
                            }).on('draw', function(data) {
                                    if(data.type == 'bar') {
                                    data.element.attr({
                                      style: `stroke-width: 50px;stroke-linecap: butt;stroke-dasharray: 0;`
                                    });
                                    data.element.animate({
                                        y2: {
                                            dur: '0.8s',
                                            from: data.y1,
                                            to: data.y2
                                        }
                                    });
                                }
                            }).on('created', function(bar) {
                              if(window.__anim21278907125) {
                                clearTimeout(window.__anim21278907125);
                                window.__anim21278907125 = null;
                              }
                              window.__anim21278907125 = setTimeout(bar_chart.update.bind(bar_chart), 20000);
                            });

                       </script>
                            </div>
                        </div>
                        

                    </div> 
                    <div class="graph kong-helpdesk-col-sm-6">
                        <!-- Tags n Agents section -->
                        <div class="ticket-graph">
                                    <h4>Tickets by Categories</h4>
                                    <?php $ticketbycategory_chart = $primary_tickets_data['ticket_by_category'];
                                            if(isset($compare_tickets_data['ticket_by_category']))
                                                    {
                                                    $ticketbycategory_chart = $compare_tickets_data['ticket_by_category'];
                                                    }
                                         if(!empty($ticketbycategory_chart)) { ?>
                                    <div class="main-graph">
                                        <div id="tickets-by-system" class="ct-chart ct-major-tenth"></div>
                                       
                                            <script>
                                                   var data = {
                                labels: <?php echo json_encode((array_column($ticketbycategory_chart, 'label')))?>,
                                series: <?php echo json_encode((array_column($ticketbycategory_chart, 'count')))?>
                                };

                            var sum = function(a, b) { //console.log(a.value);
                                return a.value + b.value };
                              
                            var pie_chart = new Chartist.Pie('#tickets-by-system', data, {
                                    width: '100%',
                                    height: '300px',
                                    donut: true,
                                    donutWidth: 40,
                                    showLabel: true,
                                    plugins: [
                                        Chartist.plugins.legend({
                                          className: 'pie_helpdesk',
                                          position: 'top'
                                        }),
                                        Chartist.plugins.tooltip({
                                            transformTooltipTextFnc: function(tooltip) {

                                                return 'No of tickets : '+ tooltip;
                                              }
                                        })
                                    ],
                                    labelInterpolationFnc: function(label, index) {
                                        return Math.round(data.series[index].value / data.series.reduce(sum) * 100) + '%';
                                    }
                                }).on('draw', function(data) {
                                  if(data.type === 'slice') {
                                    // Get the total path length in order to use for dash array animation
                                    var pathLength = data.element._node.getTotalLength();

                                    // Set a dasharray that matches the path length as prerequisite to animate dashoffset
                                    data.element.attr({
                                      'stroke-dasharray': pathLength + 'px ' + pathLength + 'px'
                                    });

                                    // Create animation definition while also assigning an ID to the animation for later sync usage
                                    var animationDefinition = {
                                      'stroke-dashoffset': {
                                        id: 'anim' + data.index,
                                        dur: 1000,
                                        from: -pathLength + 'px',
                                        to:  '0px',
                                        easing: Chartist.Svg.Easing.easeOutQuint,
                                        // We need to use `fill: 'freeze'` otherwise our animation will fall back to initial (not visible)
                                        fill: 'freeze'
                                      }
                                    };

                                    // If this was not the first slice, we need to time the animation so that it uses the end sync event of the previous animation
                                    if(data.index !== 0) {
                                      animationDefinition['stroke-dashoffset'].begin = 'anim' + (data.index - 1) + '.end';
                                    }

                                    // We need to set an initial value before the animation starts as we are not in guided mode which would do that for us
                                    data.element.attr({
                                      'stroke-dashoffset': -pathLength + 'px'
                                    });

                                    // We can't use guided mode as the animations need to rely on setting begin manually
                                    // See http://gionkunz.github.io/chartist-js/api-documentation.html#chartistsvg-function-animate
                                    data.element.animate(animationDefinition, false);
                                  }
                                }).on('created', function() {
                                  if(window.__anim21278907124) {
                                    clearTimeout(window.__anim21278907124);
                                    window.__anim21278907124 = null;
                                  }
                                  window.__anim21278907124 = setTimeout(pie_chart.update.bind(pie_chart), 20000);
                                });

                                                    
                                            </script>
                        
                                    </div>
                                     <?php }else{
                                            echo '<p style="text-align:center;">No ticket found.</p>';
                                        } ?>
                                </div>
                    
                    </div>
                    <div class="graph kong-helpdesk-col-sm-6">
                        <div class="ticket-graph">
                                        <h4>Tickets by Agent</h4>
                                    </div>
                                    <div class="ticket-graph-table">
                                        <div class="table_head">
                                            <div>Agent</div>
                                            <div>Replies</div>
                                            <div>Solved</div>
                                            <div>Avg Resp Time</div>
                                        </div>
                                        <?php $ticketbyagent = $primary_tickets_data['ticket_by_agents'];
                                    if(isset($compare_tickets_data['ticket_by_agents']))
                                            {
                                            $ticketbyagent = $compare_tickets_data['ticket_by_agents'];
                                            }
                                        if(!empty($ticketbyagent['data'])) {
                                            foreach($ticketbyagent['data'] as $agent){?>
                                                <div class="table_body">
                                                    <div><?php echo $agent['email']; ?></div>
                                                    <div><?php echo $agent['reply_count']; ?></div>
                                                    <div><?php echo $agent['solved']; ?></div>
                                                    <div><?php echo $agent['avg_response_time']; ?>h</div>
                                                </div>
                                        <?php } 
                                        }else{
                                            echo '<p style="text-align:center;">No agent found.</p>';
                                        } ?>
                                    </div>
                        </div>
                    </div>    
                    <div class="graph kong-helpdesk-col-sm-12">
                        <!-- Business Time graph -->
                        <div class="ticket-graph">
                            <h4>Busiest time of day</h4>
                            <?php 
                            $busiestday_chart = $primary_tickets_data['buseiest_day_response'];
                             if(isset($compare_tickets_data['buseiest_day_response']))
                            {
                               $busiestday_chart = $compare_tickets_data['buseiest_day_response'];
                            }
                            ?>


                            <div class="business-graph">
                                <div class="points-business">
                                    <div class="boxes box-blank"></div>
                                <?php foreach ($busiestday_chart['Mon'] as $key => $value) {?>
                                    <div class="boxes"><?php echo $key;?></div>
                                <?php } ?>
                                </div>

                                <?php 
                                foreach ($busiestday_chart as $key => $result) {?>
                                    <div class="points-business">
                                    <div class="boxes"><?php echo $key;?></div>
                                    <?php foreach ($result as $key => $value) { 
                                        ?>
                                        <div class="boxes tooltipped" style="background-color:<?php echo $this->getColorCode($value);?>" data-position="top" data-tooltip="<?php echo $value.' conversation';?>"></div>
                                    <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>

        <?php
    }

    // return color code depending on conversation count
    private function getColorCode($count = 0) {
            $colorcode = '';
            if ( $count == 0 ) {
                $colorcode = 'rgba(152,117,45,.2)';
            }else if ( in_array($count, range(1,7)) ) {
                $colorcode = 'rgba(152,117,45,.5)';
            }else if(in_array($count, range(8,10))) {
                $colorcode = 'rgba(152,117,45,.7)';
            }else {
                $colorcode = 'rgba(152,117,45,1)';
            }

            return $colorcode;
    }

    // list of primary tickets
    private function getTicketsReports($date_from,$date_until){

    
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

        return $tickets;
    }

    // ticket proccessing 
    private function ticketProcessing($tickets,$ticket_created){
        $years = [];
        $months = array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');

        $ticketsCreatedByMonth = [];
        $ticketsCreatedByYear = [];
        $ticketsAssignedToAgent = [];
        $ticketsAssignedToAgents = [];
        $ticketsByReporter = [];
        $ticketsBySatisfaction = [];
        $ticketsBySource = [];
        $ticketsByTags = [];
        $ticketsByagents = [];
        $ticket_ids = [];
        $ticket_data = [];
        $ticket_created_count = 1;

        
        $ticket_ids = wp_list_pluck( $tickets, 'ID' );
        foreach ($tickets as $ticket) {
            $d = new DateTime($ticket->post_date);
            $year_created = $d->format('Y');
            $month_created = $d->format('m');
            $weekday_number_created = $d->format('N');
            $weekday_name_created = $d->format('D');
            
            
            $count_ticket =1;

            
            if(isset($ticket_created['date_range'][$d->format('Y-m-d')])) {
                $ticket_created_count = $ticket_created['date_range'][$d->format('Y-m-d')] + 1;
            }
            
            
            if (array_key_exists($d->format('Y-m-d'),$ticket_created['date_range']))
            {
                $ticket_created['date_range'][$d->format('Y-m-d')] = $ticket_created_count;
            }
            
         

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

        // ticket statictics
        $ticket_created['count'] = count($ticket_ids);
        $ticket_data['ticket_statictics'] = $ticket_created;
        // tickets by tags
        $ticketsByTags = $this->get_tickets_by_status($ticket_ids);
        $ticket_data['ticket_by_tags'] = $ticketsByTags ;


        // tickets by department
        $ticketsBySystem = $this->get_tickets_by_system($ticket_ids);
        $ticket_data['ticket_by_category'] = $ticketsBySystem ;
        
        // tickets by agents
        $ticketsByagents = $this->get_tickets_by_agents($ticketsAssignedToAgent , $ticket_ids);
        $ticket_data['ticket_by_agents'] = $ticketsByagents;

        // Time to first reply graph
        $timeto_first_reply_response = $this->first_response_time_selected_period($ticketsByagents,$ticket_ids);
        $ticket_data['first_reply_response'] = $timeto_first_reply_response;

        //weekdayreport
        $buseiest_day_response = $this->get_busiest_time_by_agents($ticketsByagents,$ticket_ids);
        $ticket_data['buseiest_day_response'] = $buseiest_day_response;
        
        return $ticket_data;
    }


    //return all possible dates in between start and end dates
    private function getalldates($primary_range,$format) {
        $startdate = $enddate ='';
        $getDatesFromRange =[];

        if($primary_range == 'last_month'){
            $startdate =  date($format, strtotime('first day of previous month'));
            $enddate =  date($format, strtotime('last day of previous month'));

        }else if($primary_range == 'last_7_days'){
            $startdate = date($format, strtotime('-7 days'));
            $enddate = date($format);

        }else if($primary_range == 'last_week'){
            $startdate = date($format, strtotime('monday last week'));
            $enddate = date($format, strtotime('sunday last week'));
            
        }else if($primary_range == 'today'){
            $startdate = $enddate = date($format);
        }else if($primary_range == 'custom'){

            $startdate = $this->validateDate($_GET['date_from']);
            if($startdate){
                $startdate = $_GET['date_from'];
            }else{
                $startdate = date($format, strtotime('today - 30 days'));
            }
            $enddate = $this->validateDate($_GET['date_until']);
            if($enddate){
                $enddate = $_GET['date_until'];
            }else{
                $enddate = date($format);
            }
            
        }else{
            $startdate = date($format, strtotime('today - 30 days'));
            $enddate = date($format);
        }
            
          
        // Loop from the start date to end date and output all dates inbetween  
        $getDatesFromRange = $this->getDatesFromRange($startdate,$enddate,$format);  

        return ['start_date'=>$startdate,'end_date'=>$enddate,'date_range'=>$getDatesFromRange];
    }

    /**
     * Get Tickets by Status
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * 
     * @return  [type]  
     */
    private function get_tickets_by_status($ticket_ids)
    {
        $ticketsByStatus = [];
        foreach($ticket_ids as $ticketid) {
            
            $term_list = wp_get_post_terms( $ticketid, 'ticket_status', array( 'fields' => 'all' ) );
            foreach ($term_list as $key=>$val) {
                $count = 1;
                if(isset($ticketsByStatus[$val->slug])) {
                    $count = $ticketsByStatus[$val->slug]['count']+1;
                }
                  $ticketsByStatus[$val->slug]= array('term_id'=>$val->term_id,'count'=>$count);
              }
        }
        return $ticketsByStatus;

    }

    /**
     * Get Tickets by Type
     * @author CN
     * @version 1.0.0
     * @since   1.0.0
     * @return  [type]  
     */
    private function get_tickets_by_type()
    {
        $ticketsByTypes = [];
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
     * @return  [type]  
     */
    private function get_tickets_by_priority()
    {
        $ticketsByPriority = [];
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
        
        $ticketsBySystems = [];
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

    // Function to get all the dates in given range 
    private function getDatesFromRange($start, $end, $format) { 
        
        // Declare an empty array 
        $array = []; 
        
        // Variable that store the date interval 
        // of period 1 day 
        $interval = new DateInterval('P1D'); 
    
        $realEnd = new DateTime($end); 
        $realEnd->add($interval); 
    
        $period = new DatePeriod(new DateTime($start), $interval, $realEnd); 
    
        // Use loop to store date into array 
        foreach($period as $date) {                  
            $array[$date->format($format)] = 0;  
        } 
    
        // Return the array elements 
        return $array; 
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

    // display number of total,open,close tickets
    private function get_tickets_data($ticket_ids) {

    }

    // return Average first response time in selected period
    private function first_response_time_selected_period($agents , $ticket_ids){
        $data = $agent_detail = $period_array_data = [];
        $period_array = [
            '0-1'=>['count'=>0,'percentage'=>0],
            '1-8'=>['count'=>0,'percentage'=>0],
            '8-24'=>['count'=>0,'percentage'=>0],
            '>24'=>['count'=>0,'percentage'=>0],
        ];
       
        $total_hours = $total_comment = $avg_response_time = 0;
        $period_value = '';
        $percentage =1;


        if(!empty($agents)) {
            foreach ($agents['data'] as $agent) {
                $agent_detail = $this->get_first_response_time_by_userid($agent['ID'],$ticket_ids);
               // print_r($agent_detail);

                foreach($agent_detail['data'] as $res){
                    $count = 1;
                    $avg_response_time = $res['comment_time_hours'];
                    if($avg_response_time < 2 ) {
                        $period_value = '0-1';
                    }else if($avg_response_time >=2 && $avg_response_time < 8 ){
                        $period_value = '1-8';
                    }else if($avg_response_time >=8 && $avg_response_time < 24 ){
                        $period_value = '8-24';
                    }else{
                        $period_value = '>24';
                    }
                    if(isset($period_array[$period_value]['count'])) {
                        $count = $period_array[$period_value]['count'] + 1;
                    }
                    
                    $percentage = ($count / count($ticket_ids)) * 100;

                    $period_array[$period_value] = array(
                        'count'=>$count,
                        'percentage'=>round($percentage,2)
                    );
                }

            }

            $period_array_data = array ('data'=>$period_array,'total'=>array_sum(array_column($period_array,'percentage')));
            
        }

        return $period_array_data;
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
        $calculate_interval = $avg_response_time = $total_tickets = 0;
       

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
                $total_tickets = 0;
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
            foreach ($agents['data'] as $agent) {
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
            $ticket_by_agents = $ticket_by_agents_data = [];
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

            $ticket_by_agents_data = array('data'=>$ticket_by_agents,'total' => array_sum(array_column($ticket_by_agents,'avg_response_time')));
        }

        return $ticket_by_agents_data;
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