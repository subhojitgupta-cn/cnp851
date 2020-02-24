<?php
global $kong_helpdesk_options;

$queried_object = get_queried_object();
get_header();
?>
<div class="container">
	<div class="container_inner default_template_holder clearfix page_container_inner">
		<div class="kong-helpdesk-row">
			<?php

			$sidebarClass = '';
			$contentClass = '';
			if($kong_helpdesk_options['supportSidebarPosition'] == "left") {
				$sidebarClass = 'kong-helpdesk-pull-left';
				$contentClass = 'kong-helpdesk-pull-right';
			} elseif($kong_helpdesk_options['supportSidebarPosition'] == "right") {
				$sidebarClass = 'kong-helpdesk-pull-right';
				$contentClass = 'kong-helpdesk-pull-left';
			}

	        $checks = array('none', 'only_ticket');
	        if(in_array($kong_helpdesk_options['supportSidebarDisplay'], $checks)) {
	            echo '<div class="kong-helpdesk-col-sm-12">';
	        } else {
	            echo '<div class="kong-helpdesk-col-sm-8 ' . $contentClass . '">';
	        }
	        ?>
				<?php echo do_shortcode('[faqs topic="' . $queried_object->term_id . '" show_children="false" show_child_categories="true" max_faqs="-1"]'); ?>
			</div>
			<?php
			$checks = array('both', 'only_faq');
			if(in_array($kong_helpdesk_options['supportSidebarDisplay'], $checks)) {
			?>
			<div class="kong-helpdesk-col-sm-4 kong-helpdesk-pull-right kong-helpdesk-sidebar <?php echo $sidebarClass ?>">
				<?php dynamic_sidebar('helpdesk-sidebar'); ?>
			</div>
			<?php
			}
			?>
		</div>
	</div>
</div>

<?php
get_footer();