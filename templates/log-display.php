<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
/*
 * Template Name: TFDON Log Display
 *
 * Template that displays log file entries 
 */

require_once (ABSPATH . PLUGINDIR .'/tf-paypal-donate/inc/tfdon-log.php');
require_once (ABSPATH . PLUGINDIR .'/tf-paypal-donate/inc/tfdon-display-logs.php');
get_header(); ?>

	<div id="primary" class="featured-content content-area">
		<main id="main" class="site-main">

			<?php
			while ( have_posts() ) : the_post();
			//	get_template_part( 'template-parts/content', 'page' );
				?><div class="entry-content tfdon-content">
					<?php
						the_content();

						if ( ! $is_page_builder_used )
							wp_link_pages( array( 'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'Divi' ), 'after' => '</div>' ) );
					?>
					</div> <!-- .entry-content -->
					<?php
        // Put code here for display log file
					$file = $_GET['file']; 
					tfdon_log("Inside log-display: file= ", $file); 
					tfdon_display_log_file ($file);
			endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php

get_footer();