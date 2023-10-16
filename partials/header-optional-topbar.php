<?php 

if (get_theme_mod("enable_topbar")): ?>
    <!-- ******************* The Topbar Area ******************* -->
    <div id="wrapper-topbar"
        class="py-2 <?php echo get_theme_mod('topbar_bg_color_choice', 'bg-light') ?> <?php echo get_theme_mod('topbar_text_color_choice', 'text-dark') ?>">
        <div class="container">
            <div class="row">
                <div id="topbar-content" class="col-md-12 text-center small">
                    <?php echo do_shortcode(get_theme_mod('topbar_content')) ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>