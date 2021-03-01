<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
/*
 * Template Name: TFDON Log Display
 *
 * Template that displays log file entries 
 */

include('inc/tfdon-log.php');
include('inc/tfdon-display-logs.php');
get_header(); ?>

	<div id="primary" class="featured-content content-area">
		<main id="main" class="site-main">

			<?php
			while ( have_posts() ) : the_post();
				get_template_part( 'template-parts/content', 'page' );
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