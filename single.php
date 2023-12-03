<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header();


// Loop through posts if there are any
if (have_posts()):
    while (have_posts()): the_post();

        // Check if the post has a featured image
        if (has_post_thumbnail()) {  
            ?>
            <div class="container-fluid d-flex p-0 bg-body" style="height:50vh;">
                <?php
                // Output the featured image with custom classes and styles
                the_post_thumbnail('full', [
                    'class' => 'img-fluid w-100 h-100',
                    'style' => 'object-fit:cover;',
                    'alt' => esc_attr(get_post_meta(get_post_thumbnail_id() , '_wp_attachment_image_alt', true)), 
                ]);
                ?>
            </div>
        <?php
        } else {
            // Default block if no featured image is found
            ?>
            <div class="container-fluid d-flex py-6"></div>
        <?php
        }
        ?>
    
    <div id="container-content-single" class="container position-relative p-5 bg-body text-body-emphasis shadow mt-lg-n7 rounded" >
        <div class="row text-center mb-2">
            
            <div class="col-md-12">
                <?php 
                //CATS
                if (!get_theme_mod("singlepost_disable_entry_cats") &&  has_category() ) {
                        ?>
                        <div class="entry-categories">
                            <span class="screen-reader-text"><?php _e( 'Categories', 'picostrap5' ); ?></span>
                            <div class="entry-categories-inner">
                                <?php the_category( ' ' ); ?>
                            </div><!-- .entry-categories-inner -->
                        </div><!-- .entry-categories -->
                        <?php
                }
                
                ?>

                <h1 class="display-4"><?php the_title(); ?></h1>
                
                <?php if (!get_theme_mod("singlepost_disable_date") OR !get_theme_mod("singlepost_disable_author")  ): ?>
                    <div class="post-meta" id="single-post-meta">
                        <p class="lead opacity-75">
                            
                            <?php if (!get_theme_mod("singlepost_disable_date") ): ?>
                                <span class="post-date"><?php the_date(); ?> </span>
                            <?php endif; ?>

                            <?php if (!get_theme_mod("singlepost_disable_author") ): ?>
                                <span class="post-author"> <?php _e( 'by', 'picostrap5' ) ?> <?php the_author(); ?></span>
                            <?php endif; ?>
                        </p>
                    </div> 
                <?php endif; ?>

            </div><!-- /col -->
        </div>
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <?php 
                
                the_content();
                
                if( get_theme_mod("enable_sharing_buttons")) picostrap_the_sharing_buttons();
                
                edit_post_link( __( 'Edit this post', 'picostrap5' ), '<p class="text-end">', '</p>' );
                
                // If comments are open or we have at least one comment, load up the comment template.
                if (!get_theme_mod("singlepost_disable_comments")) if ( comments_open() || get_comments_number() ) {
                    comments_template();
                }
                
                ?>

            </div><!-- /col -->
        </div>
    </div>

<?php
    endwhile;
 else :
     _e( 'Sorry, no posts matched your criteria.', 'picostrap5' );
 endif;
 ?>


 

<?php get_footer();
