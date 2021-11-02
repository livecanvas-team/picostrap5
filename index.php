<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="py-6">
    <div class="container text-center">
        <h1 class="display-4"><?php the_title(); ?></h1>
        <p class="lead">This is a modified py-6 that occupies the entire horizontal space of its parent.</p>
  </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2 py-5">
            <?php 

            if ( have_posts() ) : 
                while ( have_posts() ) : the_post();
                    the_content();
                endwhile;
            else :
                _e( 'Sorry, no posts matched your criteria.', 'textdomain' );
            endif;
            ?>
        </div>
    </div>
</div>


<?php get_footer();
