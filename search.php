<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();
?>
  
 

<section class="py-6 text-center">
  <div class="container">
    <h1><?php 
								printf(
									/* translators: %s: query term */
									esc_html__( 'Search Results for: %s', 'picostrap' ),
									'<span class="text-muted">"' . get_search_query() . '"</span>'
								);
								?></h1>
    <div class="lead text-muted col-md-8 offset-md-2 archive-description">
     
    </div> 
 
    <!-- <p>
      <a href="#" class="btn btn-primary my-2">Action</a>
      <a href="#" class="btn btn-secondary my-2">Secondary action</a>
    </p> -->
  </div>
</section>

<section class="album py-5 bg-light">
  <div class="container">
    <div class="row">
    <?php 
        if ( have_posts() ) : 
            while ( have_posts() ) : the_post();
                ?>
                <div class="col-md-4 col-sm-6">
                    <div class="card mb-4 shadow-sm">
                    <svg hidden class="bd-placeholder-img card-img-top" width="100%" height="225" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img" aria-label="Placeholder: Thumbnail"><title>Placeholder</title><rect width="100%" height="100%" fill="#55595c"></rect><text x="50%" y="50%" fill="#eceeef" dy=".3em">Thumbnail</text></svg>
                    <?php the_post_thumbnail() ?>
                    <div class="card-body">
                        <?php if (!get_theme_mod("singlepost_disable_date") ): ?>
                          <small class="text-muted"><?php the_date() ?></small>
                        <?php endif; ?>
                        <h2><a class="stretched-link" href="<?php the_permalink() ?>"><?php the_title() ?></a></h2>
                        <p class="card-text"><?php the_excerpt(); ?></p>
                        <!--
                        <div class="d-flex justify-content-between align-items-center"> 
                            <div class="btn-group">
                              <button type="button" class="btn btn-sm btn-outline-secondary">View</button>
                              <button type="button" class="btn btn-sm btn-outline-secondary">Edit</button>
                            </div>
                        </div>
                        -->
                    </div>
                    </div>
                </div>
                <?php
                
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
    </div <!-- /row -->
  </div>
</section>
 
<?php get_footer();
