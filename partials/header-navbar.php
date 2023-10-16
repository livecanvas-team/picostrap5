<!-- ******************* The Navbar Area ******************* -->
<div id="wrapper-navbar" itemscope itemtype="http://schema.org/WebSite">

    <a class="skip-link visually-hidden-focusable" href="#theme-main">
        <?php esc_html_e('Skip to content', 'picostrap5'); ?>
    </a>


    <nav data-bs-theme="<?php echo get_theme_mod('picostrap_header_navbar_color_scheme_attr', 'dark') ?>"
        class="navbar <?php echo get_theme_mod('picostrap_header_navbar_expand', 'navbar-expand-lg'); ?> <?php echo get_theme_mod('picostrap_header_navbar_position') . " " . get_theme_mod('picostrap_header_navbar_color_choice', 'bg-dark'); ?>"
        aria-label="Main Navigation">
        <div class="container">
            <div id="logo-tagline-wrap">
                <!-- Your site title as branding in the menu -->
                <?php if (!has_custom_logo()) { ?>

                    <?php if (is_front_page() && is_home()): ?>

                        <div class="navbar-brand mb-0 h3"><a rel="home" href="<?php echo esc_url(home_url('/')); ?>"
                                title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" itemprop="url">
                                <?php bloginfo('name'); ?>
                            </a></div>

                    <?php else: ?>

                        <a class="navbar-brand mb-0 h3" rel="home" href="<?php echo esc_url(home_url('/')); ?>"
                            title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" itemprop="url">
                            <?php bloginfo('name'); ?>
                        </a>

                    <?php endif; ?>


                <?php } else {
                    the_custom_logo();
                } ?><!-- end custom logo -->


                <?php if (!get_theme_mod('header_disable_tagline')): ?>
                    <small id="top-description" class="text-muted d-none d-md-block mt-n2">
                        <?php bloginfo("description") ?>
                    </small>
                <?php endif ?>


            </div> <!-- /logo-tagline-wrap -->



            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarNavDropdown" aria-controls="navbarsExample05" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'primary',
                        'container' => false,
                        'menu_class' => '',
                        'fallback_cb' => '__return_false',
                        'items_wrap' => '<ul id="%1$s" class="navbar-nav me-auto mb-2 mb-md-0 %2$s">%3$s</ul>',
                        'walker' => new bootstrap_5_wp_nav_menu_walker()
                    )
                );
                ?>

                <?php if (get_theme_mod('enable_search_form')): ?>
                    <form action="<?php echo bloginfo('url') ?>" method="get" id="header-search-form" class="me-4">
                        <input class="form-control" type="text" placeholder="Search" aria-label="Search" name="s"
                            value="<?php the_search_query(); ?>">
                    </form>
                <?php endif ?>

                <?php if (get_theme_mod('enable_dark_mode_switch')): ?>
                    <div class="d-flex align-items-center gap-1 mt-4 mt-md-0 navbar-text">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-sun-fill me-1" viewBox="0 0 16 16">
                            <path
                                d="M8 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM8 0a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 0zm0 13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 8 13zm8-5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2a.5.5 0 0 1 .5.5zM3 8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1 0-1h2A.5.5 0 0 1 3 8zm10.657-5.657a.5.5 0 0 1 0 .707l-1.414 1.415a.5.5 0 1 1-.707-.708l1.414-1.414a.5.5 0 0 1 .707 0zm-9.193 9.193a.5.5 0 0 1 0 .707L3.05 13.657a.5.5 0 0 1-.707-.707l1.414-1.414a.5.5 0 0 1 .707 0zm9.193 2.121a.5.5 0 0 1-.707 0l-1.414-1.414a.5.5 0 0 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .707zM4.464 4.465a.5.5 0 0 1-.707 0L2.343 3.05a.5.5 0 1 1 .707-.707l1.414 1.414a.5.5 0 0 1 0 .708z" />
                        </svg>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="theme-toggle">
                            <label class="form-check-label visually-hidden" for="theme-toggle"> Toggle Dark / Light mode
                            </label>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor"
                            class="bi bi-moon-fill" viewBox="0 0 16 16">
                            <path
                                d="M6 .278a.768.768 0 0 1 .08.858 7.208 7.208 0 0 0-.878 3.46c0 4.021 3.278 7.277 7.318 7.277.527 0 1.04-.055 1.533-.16a.787.787 0 0 1 .81.316.733.733 0 0 1-.031.893A8.349 8.349 0 0 1 8.344 16C3.734 16 0 12.286 0 7.71 0 4.266 2.114 1.312 5.124.06A.752.752 0 0 1 6 .278z" />
                        </svg>
                    </div>
                <?php endif; ?>

            </div> <!-- .collapse -->
        </div> <!-- .container -->
    </nav> <!-- .site-navigation -->
    <?php

    //AS A TEST / DEMO for a mock-up megamenu
    //include("nav-static-mega.php");
    ?>
</div><!-- #wrapper-navbar end -->
