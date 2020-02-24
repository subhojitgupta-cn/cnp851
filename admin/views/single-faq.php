<?php
global $post, $kong_helpdesk_options;

$sidebarClass = '';
$contentClass = '';
if($kong_helpdesk_options['supportSidebarPosition'] == "left") {
	$sidebarClass = 'kong-helpdesk-pull-left';
	$contentClass = 'kong-helpdesk-pull-right';
} elseif($kong_helpdesk_options['supportSidebarPosition'] == "right") {
	$sidebarClass = 'kong-helpdesk-pull-right';
	$contentClass = 'kong-helpdesk-pull-left';
}

get_header();
?>
<div class="clearfix"></div>
<div class="kong-helpdesk">
	<div id="main-content" class="main-content">
		<div class="container">
			<div class="container_inner default_template_holder clearfix page_container_inner">
				<div class="kong-helpdesk-row">
					<?php
			        $checks = array('none', 'only_ticket');
			        if(in_array($kong_helpdesk_options['supportSidebarDisplay'], $checks)) {
			            echo '<div class="kong-helpdesk-col-sm-12">';
			        } else {
			            echo '<div class="kong-helpdesk-col-sm-8 ' . $contentClass . '">';
			        }
			        ?>
						<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

						    <div <?php post_class() ?> id="post-<?php the_ID(); ?>">

						    	<div class="kong-helpdesk-row">
						    		<div class="kong-helpdesk-col-sm-12">
						        		<h1 class="kong-helpdesk-single-title"><?php the_title(); ?></h1>
							            <div class="kong-helpdesk-meta-information">
								        <?php

								        // Topics
										$topics = get_the_terms($post->ID, 'faq_topics');
								        if (!empty($topics)) {							        
									        // Topics
									        foreach ($topics as $topic) {
									            $topic_color = get_term_meta($topic->term_id, 'kong_helpdesk_color');
									            if (isset($topic_color) && !empty($topic_color)) {
									                $topic_color = $topic_color[0];
									            } else {
									                $topic_color = '#000000';
									            }
									            echo '<a href="' . get_term_link($topic->term_id) . '">'
									                    . '<span class="kong-helpdesk-topics label kong-helpdesk-topic-' . $topic->slug . '" style="background-color: ' . $topic_color . '">'
									                        . $topic->name .
									                    '</span>' .
									                '</a> ';
									        }
								        }

								        // Views
								        if($kong_helpdesk_options['FAQShowViews'] === "1") {
									        $count = get_post_meta($post->ID, 'faq_popularity', true);
									        echo ' <span class="kong-helpdesk-viewed label" style="background-color: #03A9F4">' . sprintf(__('Viewed: %s', 'kong-helpdesk'), $count) . '</span>';
								        }
								        
								        // Rating System
								        if($kong_helpdesk_options['FAQRatingEnable'] === "1") {

								        	$likes = get_post_meta($post->ID, 'faq_likes', true);
							        		if(!$likes) {
							        			$likes = 0;
							        		}
								        	echo '<div class="kong-helpdesk-faq-rating">';

									        		echo '<a class="kong-helpdesk-faq-rating-like" data-post_id="' . $post->ID . '" href="#">';
									        			echo '<i class="fa fa-thumbs-up"></i> <span id="kong-helpdesk-faq-rating-like-count">' . $likes . '</span>';
									        		echo '</a>';
												
									        	if($kong_helpdesk_options['FAQRatingDisableDislikeButton'] === "0") {

									        		$dislikes = get_post_meta($post->ID, 'faq_dislikes', true);
									        		if(!$dislikes) {
									        			$dislikes = 0;
									        		}
								        			echo '<a class="kong-helpdesk-faq-rating-dislike" data-post_id="' . $post->ID . '" href="#">';
								        				echo '<i class="fa fa-thumbs-down"></i> <span id="kong-helpdesk-faq-rating-dislike-count">' . $dislikes . '</span>';
								        			echo '</a>';
							        			}
						        			echo '</div>';
								        }
								        ?>
							            </div>
									</div>
								</div>
						        <div class="kong-helpdesk-row">
									<div class="kong-helpdesk-col-sm-12">
										<div class="entry">
						            		<?php the_content(); ?>
						            	</div>
					            	</div>
						        </div>

								<div class="kong-helpdesk-row">
									<div class="kong-helpdesk-col-sm-12">
										<div class="kong-helpdesk-comments">
					            			<?php comments_template(); ?>
					            		</div>
					            	</div>
						        </div>
						    </div>
					    <?php endwhile; endif; ?>
					</div>
					<?php
					$checks = array('both', 'only_faq');
					if(in_array($kong_helpdesk_options['supportSidebarDisplay'], $checks)) {
					?>
					<div class="kong-helpdesk-col-sm-4 kong-helpdesk-sidebar <?php echo $sidebarClass ?>">
						<?php dynamic_sidebar('helpdesk-sidebar'); ?>
					</div>
					<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
get_footer();