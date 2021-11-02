<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
?>
  
 

<section class="py-6 bg-light text-center">
  <div class="container">
    <h1><?php the_archive_title() ?></h1>
    <div class="lead text-muted col-md-8 offset-md-2 archive-description"><?php echo category_description(); ?></div> 
 
    <!-- <p>
      <a href="#" class="btn btn-primary my-2">Action</a>
      <a href="#" class="btn btn-secondary my-2">Secondary action</a>
    </p> -->
  </div>
</section>

<section class="album py-5">
  <div class="container">
    <div class="row">
    <?php 
        if ( have_posts() ) : 
            while ( have_posts() ) : the_post();
              get_template_part('loops/cards');
            endwhile;
        else :
            _e( 'Sorry, no posts matched your criteria.', 'textdomain' );
        endif;
        ?>
    </div>

    <div class="row">
      <div class="col lead text-center w-100">
        <div class="d-inline-block"><?php picostrap_pagination() ?></div>
      </div><!-- /col -->
    </div> <!-- /row -->
  </div>
</section>
 
<?php get_footer();
