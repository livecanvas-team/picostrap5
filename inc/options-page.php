<?php

//GET PARENT THEME VERSION
function pico_get_parent_theme_version(){
		
	//get active parent theme / framework information
	if (get_template_directory() === get_stylesheet_directory())  { $my_theme = wp_get_theme(); } else { $my_theme = wp_get_theme()->parent(); }
				
	//print theme version 
	return $my_theme->get( 'Version' );
}

//ADD THEME OPTIONS PAGE
function add_picostrap_theme_page() {
    add_theme_page( 'Picostrap Theme Options Page', 'Picostrap Options', 'edit_theme_options', 'picostrap-theme-options', 'theme_option_page' );
}
add_action( 'admin_menu', 'add_picostrap_theme_page' );
 
function theme_option_page() {
	if (isset($_GET['successful-import'])){
		?> 
			<div class='wrap' style="padding: 20px;    font-size: 18px;    background: azure;    margin-top: 10px;     ">Settings imported from file.</div> 
			<script>
				jQuery('document').ready(function(){
					ps_recompile_sass_in_admin();
				});
			</script>
		<?php 
    }
	?>
	
	<div id='ps-panel-actions-loading-target' class="wrap" style="padding: 20px;    font-size: 18px;    background: lightgoldenrodyellow;    margin-top: 10px;    display: none;"></div>
	<style>
		.pico-wrap {    margin: 10px 20px 0 2px;}
		.pico-welcome-panel {
			position: relative;
			overflow: auto;
			margin: 16px 0;
			padding: 28px 22px 28px;
			border: 1px solid #c3c4c7;
			box-shadow: 0 1px 1px rgb(0 0 0 / 4%);
			background: #fff;
			font-size: 13px;
			line-height: 1.7;

		}
		.pico-container {
			display: flex;
			max-width: 700px;
		}


		/* PICO UTILS */

		ul#pico-utils li {
		margin-bottom: 10px;
		}
		ul#pico-utils li a svg {
			color:#007cba;
			width:20px;
			margin-right:10px;
			vertical-align: middle;
		}
		
		ul#pico-utils li a span {
			font-weight: 700;
			text-decoration: underline;
		}

	</style>
    
	<script>
		function ps_recompile_sass_in_admin() {
			jQuery('#ps-panel-actions-loading-target').text('Rebuilding CSS. Please wait...').show(); 
			jQuery.ajax({
				url: ajaxurl, // this will point to admin-ajax.php
				type: 'POST',
				data: {
					'action': 'picostrap_recompile_sass',  
					'nonce': '<?php echo wp_create_nonce( 'picostrap_livereload' ) ?>' 
				}, 
				success: function (response) {
					jQuery('#ps-panel-actions-loading-target').html(response);
				}
			});	

		} //end function definition



		
		function ps_reset_theme_settings() {
			if(!confirm('This will DESTROY all your Customizer settings. Are you sure?')) return FALSE;
			jQuery('#ps-panel-actions-loading-target').text('Resetting options and rebuilding CSS. Please wait...').show(); 
			jQuery.ajax({
				url: ajaxurl, // this will point to admin-ajax.php
				type: 'POST',
				data: {
					'action': 'picostrap_reset_theme_options',  
					'nonce': '<?php echo wp_create_nonce( 'picostrap_livereload' ) ?>' 
				}, 
				success: function (response) {
					jQuery('#ps-panel-actions-loading-target').html(response);
				}
			});	

		} //end function definition

	</script>
	
	
	
	<div class="pico-wrap">

        <div class="pico-welcome-panel">
        
            <div class="pico-welcome-panel-content">
				<svg width="300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 968 191"><style>tspan { white-space:pre }</style><g><path fill="#26c6da" d="M151.13 59.58Q151.13 85.18 121.74 108.25Q92.67 131 71.18 131Q56.96 131 50.33 129.1Q44.01 127.21 36.11 117.73Q30.74 122.15 21.57 140.48Q12.41 158.49 12.41 171.45Q12.41 177.45 18.41 187.25Q19.04 188.83 17.46 190.09Q15.88 191.04 14.94 191.04Q14.3 191.04 13.67 190.72Q0.4 183.46 0.4 162.6Q0.4 141.43 35.16 89.92Q70.24 38.1 83.51 33.67Q86.67 33.04 87.62 33.04Q92.99 33.04 92.99 44.73Q96.15 41.57 105.94 38.73Q117.95 35.57 123.01 35.57Q134.38 35.57 142.6 42.2Q151.13 48.52 151.13 59.58ZM135.02 56.42Q135.02 44.42 120.8 44.42Q106.58 44.42 94.88 50.42Q89.51 52.95 65.18 78.23Q40.85 103.51 40.85 110.78Q40.85 116.46 49.38 119.31Q58.23 122.15 67.08 122.15Q84.46 122.15 109.74 102.56Q135.02 82.65 135.02 56.42ZM219.7 2.07Q224.44 2.07 228.55 5.23Q232.98 8.08 232.98 12.18Q232.98 16.29 228.87 19.45Q225.08 22.3 220.34 22.3Q215.6 22.3 211.49 18.82Q207.7 15.34 207.7 10.92Q207.7 2.07 219.7 2.07ZM162.51 134.48Q146.39 134.48 146.39 113.62Q146.39 96.56 165.04 66.54Q183.68 36.2 195.06 35.88Q198.22 35.88 202.01 38.41Q206.12 40.94 206.12 43.47Q204.54 45.68 202.32 48.52Q200.11 51.05 197.58 54.21Q195.06 57.37 190.63 63.38Q186.21 69.06 182.1 74.44Q158.4 105.09 158.4 116.78Q158.4 128.16 166.3 128.16Q172.94 128.16 184.31 120.89Q210.86 104.46 226.34 84.86Q232.03 77.6 233.92 77.6Q236.14 77.6 236.14 80.12Q236.14 82.65 228.87 90.24Q221.6 97.82 217.18 102.24Q213.07 106.67 211.49 108.25Q209.91 109.51 205.48 113.62Q201.38 117.41 199.16 118.99Q196.95 120.57 192.84 123.73Q188.74 126.89 185.58 128.47Q182.42 129.74 178.31 131.63Q171.67 134.48 162.51 134.48ZM246.56 135.11Q243.09 135.11 240.24 134.16Q220.02 128.47 220.02 111.41Q220.02 89.29 247.83 60.22Q275.64 31.14 297.76 31.14Q305.02 31.14 309.45 35.25Q314.19 39.04 314.19 46Q314.19 52.63 310.08 60.22Q305.97 67.48 297.76 67.48Q289.54 67.48 289.54 62.43Q289.54 58.95 293.65 52Q298.07 44.73 298.07 44.42Q298.07 43.78 297.12 43.78Q288.28 44.73 272.79 56.42Q257.62 68.12 251.94 76.02L239.61 94.03Q236.45 99.08 234.87 106.04Q233.61 112.99 233.61 116.46Q233.61 119.94 237.4 124.36Q241.51 128.79 248.78 128.79Q264.58 128.79 287.96 110.46Q311.34 91.82 323.98 75.07Q325.56 73.17 327.78 73.17Q329.99 73.17 329.99 74.44Q329.99 75.7 328.41 77.6Q311.03 100.66 286.7 118.04Q262.68 135.11 246.56 135.11ZM394.45 64.01Q394.45 75.07 373.28 100.98Q348.32 131.63 330.62 131.63L325.56 131.63Q317.35 131.63 312.61 123.1Q308.82 117.1 308.82 109.83Q308.82 90.87 336.31 56.74Q364.12 22.3 381.18 22.3Q397.3 22.3 397.3 39.36Q397.3 43.78 395.72 50.74Q396.03 51.68 401.72 51.68Q407.41 51.68 419.1 45.68Q418.78 51.05 394.14 61.16Q394.45 62.43 394.45 64.01ZM373.91 47.58Q363.17 47.58 343.26 72.86Q323.67 98.14 323.67 110.14Q323.67 122.15 331.25 122.15Q344.84 122.15 365.7 97.5Q386.55 72.86 386.55 62.11Q381.5 59.27 373.91 47.58Z"/><path fill="#e83e8c" d="M454.26 119.62L444.14 126.26Q434.98 131.63 421.39 131.63Q408.12 131.63 400.85 122.78Q393.9 113.94 388.84 94.66Q383.16 100.35 380.94 100.35Q379.36 100.35 379.36 98.14Q379.36 95.92 380.94 94.34Q391.37 81.39 416.34 57.06L456.15 19.77Q473.22 3.65 476.38 3.65Q486.49 3.65 486.49 11.24Q486.49 15.03 483.33 18.82Q480.48 22.3 478.9 24.51Q477.64 26.4 476.06 28.3Q470.37 38.1 464.37 68.75Q458.68 99.4 455.52 111.41Q481.75 111.09 509.56 77.28Q512.72 74.12 514.3 74.12Q515.88 74.12 515.88 76.33Q515.88 78.23 513.03 81.7Q494.7 103.19 478.9 111.72Q469.42 116.46 454.26 119.62ZM401.48 91.18Q401.48 101.93 408.44 108.88Q415.7 115.52 424.55 115.52Q433.4 115.52 438.14 112.04Q444.46 108.56 450.46 82.97Q456.78 57.37 458.05 37.78L459.31 27.04Q441.93 39.99 422.02 60.53Q402.43 81.07 401.8 87.71Q401.48 89.6 401.48 91.18ZM581.92 29.25L627.42 28.62Q628.37 29.25 628.37 29.56Q628.37 32.41 588.87 37.46Q549.69 42.52 546.53 43.47Q540.52 48.84 524.41 75.7Q508.29 102.24 508.29 114.57Q508.29 126.26 524.41 126.26Q531.99 126.26 544.32 121.52Q556.64 116.78 563.28 112.67Q580.97 101.61 601.2 80.12Q605.62 75.7 607.2 75.7Q608.15 75.7 608.15 77.6Q608.15 79.49 603.41 85.18Q586.98 105.4 566.44 117.73Q541.79 132.9 519.35 132.9Q509.56 132.9 503.24 128.79Q497.23 124.36 497.23 111.41Q497.23 98.45 508.61 76.02Q519.98 53.26 525.99 42.2Q521.56 43.15 518.4 43.15Q509.24 43.15 509.24 35.88Q509.24 30.51 516.82 28.3Q517.77 28.3 519.67 27.98L538 27.98Q543.68 20.72 550.95 11.24Q559.8 -0.14 562.96 -0.14Q566.12 -0.14 570.86 2.39Q575.6 4.92 575.6 8.39Q575.6 11.87 560.43 28.93Q568.65 29.25 581.92 29.25ZM657.76 132.58Q634.06 132.58 634.06 106.35Q634.06 93.08 641.01 77.28Q647.96 61.48 656.18 50.74Q654.6 51.05 652.07 51.05Q644.49 51.05 639.43 40.31L616.05 70.64Q596.46 94.98 593.3 94.98Q591.72 94.98 591.72 93.71Q591.72 90.55 597.09 84.55Q624.58 52.63 635.96 30.51Q643.22 3.02 654.28 3.02Q661.55 3.02 661.55 9.34Q661.55 15.66 653.65 25.46L646.07 35.57Q646.07 42.2 655.86 42.2Q661.55 42.2 668.5 38.1L676.72 33.04Q677.35 32.72 679.88 32.72Q682.41 32.72 685.25 35.88Q688.1 39.04 687.15 41.57L665.34 69.06Q645.12 94.98 645.12 110.14Q645.12 125 659.02 125Q673.56 125 694.73 109.51Q707.37 100.35 717.8 88.97Q727.6 77.6 728.54 77.6Q730.76 77.6 730.76 80.12Q730.76 82.34 727.91 85.5L717.8 96.87Q709.27 105.72 698.84 113.94Q673.88 132.58 657.76 132.58ZM822.71 36.52L827.14 35.57Q830.61 35.57 833.77 38.41Q836.93 41.26 836.93 42.84Q836.93 44.42 817.34 73.49Q797.75 102.24 797.75 113.62Q797.75 114.57 798.06 115.52Q799.64 122.78 805.02 122.78Q821.45 122.78 865.06 68.43Q866 67.17 867.27 67.17Q868.53 67.17 868.53 69.7Q868.53 71.91 865.69 75.7Q825.24 129.74 804.07 129.74Q801.22 129.74 798.7 128.79Q784.79 123.42 784.79 107.93Q784.79 102.56 786.06 97.19L766.46 116.15Q747.82 132.58 739.29 132.58Q731.07 132.58 723.49 127.21Q714.96 120.89 714.96 110.46Q714.96 87.39 747.19 55.48Q779.42 23.24 806.28 23.24Q816.71 23.24 820.18 30.51Q821.13 33.04 821.76 34.94Q822.4 36.52 822.71 36.52ZM728.23 114.25Q728.23 125.63 736.13 125.63Q754.46 125.63 786.06 85.5L819.55 39.36Q817.02 34.94 811.34 34.94Q792.06 34.94 760.14 64.96Q728.23 94.66 728.23 114.25ZM967.12 59.58Q967.12 85.18 937.74 108.25Q908.66 131 887.18 131Q872.96 131 866.32 129.1Q860 127.21 852.1 117.73Q846.73 122.15 837.56 140.48Q828.4 158.49 828.4 171.45Q828.4 177.45 834.4 187.25Q835.04 188.83 833.46 190.09Q831.88 191.04 830.93 191.04Q830.3 191.04 829.66 190.72Q816.39 183.46 816.39 162.6Q816.39 141.43 851.15 89.92Q886.23 38.1 899.5 33.67Q902.66 33.04 903.61 33.04Q908.98 33.04 908.98 44.73Q912.14 41.57 921.94 38.73Q933.94 35.57 939 35.57Q950.38 35.57 958.59 42.2Q967.12 48.52 967.12 59.58ZM951.01 56.42Q951.01 44.42 936.79 44.42Q922.57 44.42 910.88 50.42Q905.5 52.95 881.17 78.23Q856.84 103.51 856.84 110.78Q856.84 116.46 865.37 119.31Q874.22 122.15 883.07 122.15Q900.45 122.15 925.73 102.56Q951.01 82.65 951.01 56.42Z"/></g></svg>
				
                <h2>Welcome to picostrap5 v<?php echo pico_get_parent_theme_version() ?></h2>
                <p class="about-description">The best way to experience Bootstrap 5 and WordPress</p>
                 
                <p><i>Build your own Bootstrap CSS easily. Gain total control!</i></p>
                <p><b>Use  the WordPress Customizer</b> to control Bootstrap variables  
                and <b>set your own project colors</b>, typography and graphical options.<br>
                Hit "<b>Publish</b>" and picostrap will recompile the Bootstrap SCSS code "on the fly" (and optionally include YOUR additional CSS / SCSS files). </p>
                <p>A <b>single, minified <a href="<?php echo picostrap_get_css_url() ?>" target="_blank">CSS file</a></b> is generated and served. </p>
                <p><small>If you're not a fan of the Customizer, you can alternatively edit the <b> sass/_theme-variables.scss </b> file too, inside  your child theme folder. 
				Changes will be automatically "picked".</small></p>
                 

                
                <div class="pico-container">
                    
					<div class="pico-column" style="width:55%;">
							<style>
								.button-hero { min-width: 58%;text-align: center; }
							</style>
                            <h3>Get Started</h3>
                            <a class="button button-hero" target="_blank" href="https://picostrap.com/documentation/">Theme Documentation</a>
							<br><br>
							<a class="button button-primary button-hero" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>">Customize Your Site</a>
                            <p>  to make your own Bootstrap build!		</p>
							
							
                    </div>

                    <div class="pico-column">

                        <h3>Secondary Utilities</h3>

                        <ul id="pico-utils">
                                    <li>
										<a onclick="ps_recompile_sass_in_admin()" href="#" class="">
											<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16" style="" lc-helper="svg-icon">
												<path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"></path>
												<path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"></path>
											</svg>
											<span>Force CSS Bundle Rebuild</a> </span>
										<small>(No fear)</small> 
									</li>
                                    
									
									<li>
										<a onclick="ps_reset_theme_settings()"   href="#" >
											<svg viewBox="0 0 24 24">
    										<path fill="currentColor" d="M16.24,3.56L21.19,8.5C21.97,9.29 21.97,10.55 21.19,11.34L12,20.53C10.44,22.09 7.91,22.09 6.34,20.53L2.81,17C2.03,16.21 2.03,14.95 2.81,14.16L13.41,3.56C14.2,2.78 15.46,2.78 16.24,3.56M4.22,15.58L7.76,19.11C8.54,19.9 9.8,19.9 10.59,19.11L14.12,15.58L9.17,10.63L4.22,15.58Z" />
											</svg>
											<span>Reset Theme Options</span>
										</a> 
										<small style="color:red">(Destructive!)</small>
										
									</li>
                                       
                                    
									
									<li>
										<a target="_blank" href="https://picostrap.com/" class="">
											<svg xmlns="http://www.w3.org/2000/svg"  fill="currentColor" viewBox="0 0 16 16" style="" lc-helper="svg-icon">
												<path d="M8.354 1.146a.5.5 0 0 0-.708 0l-6 6A.5.5 0 0 0 1.5 7.5v7a.5.5 0 0 0 .5.5h4.5a.5.5 0 0 0 .5-.5v-4h2v4a.5.5 0 0 0 .5.5H14a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.146-.354L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293L8.354 1.146zM2.5 14V7.707l5.5-5.5 5.5 5.5V14H10v-4a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5v4H2.5z"></path>
											</svg>
											<span>Learn more at picostrap.com</span>
										</a>
									
									</li>
                        </ul>
                       
                
				    </div>
				</div>
        	</div>
    	</div>                        
    </div> <!-- close wrap -->






    <div class="wrap">
		
		<h2> How to add your own CSS / SCSS code</h2>
		 
		<div class="metabox-holder">
			<div class="postbox" style="padding: 0 10px;"> 
				<?php 
				if (get_template_directory() === get_stylesheet_directory()) {
					echo '
					<p style="font-style:italic"><b style="font-family:Courier;font-size:20px;">Please do not edit directly the picostrap theme folder. </b></p>
					
					<p>To add your own [SASSy] CSS code, you need to enable a child theme (free at picostrap.com). </p>
					
					';
					} else {
					echo '<p>You can freely edit the <b style="font-family:Courier;font-size:20px;"> sass/_custom.scss </b> file inside your child theme folder.</p>
					<p>Open this file with your favourite text editor, save, and view the page as admin in your browser:<br>
					A new CSS bundle will be built and served via ajax after a few seconds.<br> So while working on your CSS / SCSS code, you can immediately see the "results" of your new styling edits without reloading the page.</p>
					
					<p style="font-style:italic" >Stop hitting cmd-R like a drunken monkey!</p>';
					}
				?>
			</div><!-- .postbox -->
 
		</div><!-- .metabox-holder -->

	</div><!--end .wrap-->





    <div class="wrap">
		<h2> Import / Export Theme Settings</h2>

		<div class="metabox-holder">
			<div class="postbox">
				<h3><span><?php _e( 'Export Settings' ); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'Export the theme settings for this site as a .json file. This allows you to easily import the configuration into another site.' ); ?></p>
					<form method="post">
						<p><input type="hidden" name="pico_action" value="export_settings" /></p>
						<p>
							<?php wp_nonce_field( 'pico_export_nonce', 'pico_export_nonce' ); ?>
							<?php submit_button( __( 'Export' ), 'secondary', 'submit', false ); ?>
						</p>
					</form>
				</div><!-- .inside -->
			</div><!-- .postbox -->

			<div class="postbox">
				<h3><span><?php _e( 'Import Settings' ); ?></span></h3>
				<div class="inside">
					<p><?php _e( 'Import the plugin settings from a .json file. This file can be obtained by exporting the settings on another site using the form above.' ); ?></p>
					<form method="post" enctype="multipart/form-data">
						<p>
							<input type="file" name="import_file"/>
						</p>
						<p>
							<input type="hidden" name="pico_action" value="import_settings" />
							<?php wp_nonce_field( 'pico_import_nonce', 'pico_import_nonce' ); ?>
							<?php submit_button( __( 'Import' ), 'secondary', 'submit', false ); ?>
						</p>
					</form>
				</div><!-- .inside -->
			</div><!-- .postbox -->
		</div><!-- .metabox-holder -->

	</div><!--end .wrap-->




    <?php
}


///EXPORT AS JSON FILE
function pico_process_settings_export() {

	if( empty( $_POST['pico_action'] ) || 'export_settings' != $_POST['pico_action'] )
		return;

	if( ! wp_verify_nonce( $_POST['pico_export_nonce'], 'pico_export_nonce' ) )
		return;

	if( ! current_user_can( 'manage_options' ) )
		return;

	$settings = array();

	//add version
	$settings['theme_version'] = pico_get_parent_theme_version();

    foreach (get_theme_mods() as $setting_name => $setting_value):
		if ($setting_name=="picostrap_scss_last_filesmod_timestamp") continue;
        if ($setting_name=="picostrap_scss_last_filesmod_timestamp_v2") continue;
        if ($setting_name=="custom_css_post_id") continue;
        $settings[$setting_name]=$setting_value;
    endforeach;


	ignore_user_abort( true );

	nocache_headers();
	header( 'Content-Type: application/json; charset=utf-8' );
	header( 'Content-Disposition: attachment; filename=pico-settings-export-' . date( 'm-d-Y' ) . '.json' );
	header( "Expires: 0" );

	echo json_encode( $settings );
	exit;
}
add_action( 'admin_init', 'pico_process_settings_export' );




//IMPORT FROM JSON

function pico_process_settings_import() {

	if( empty( $_POST['pico_action'] ) || 'import_settings' != $_POST['pico_action'] )
		return;

	if( ! wp_verify_nonce( $_POST['pico_import_nonce'], 'pico_import_nonce' ) )
		return;

	if( ! current_user_can( 'manage_options' ) )
		return;

	@$extension = end( explode( '.', $_FILES['import_file']['name'] ) );

	if( $extension != 'json' ) {
		wp_die( __( 'Please upload a valid .json file' ) );
	}

	$import_file = $_FILES['import_file']['tmp_name'];

	if( empty( $import_file ) ) {
		wp_die( __( 'Please upload a file to import' ) );
	}

	// Retrieve the settings from the file and convert the json object to an array.
	$settings = (array) json_decode( file_get_contents( $import_file ), TRUE );

	//version check - should not be necessary anymore, so its disabled
	//if (!isset($settings['theme_version']) OR $settings['theme_version']!=pico_get_parent_theme_version()) wp_die("<h1>Invalid JSON format</h1><h4> You can only import json exported from the same version of the theme</h4>");

	//should we choose to whitelist only some values, some example code
	/*
	// eliminate non - string / boolean values like nav_menu_locations as they break the customizer after importing
	foreach($settings as $setting_name => $setting_value):

		if (!is_string($setting_value) && !is_bool($setting_value)) {
			//echo ("eliminate ".$setting_name."<br><br>"); //for debug only
			unset ($settings[$setting_name]);
		}

	endforeach; 

	//var_dump ($settings); die; // for debug only

	*/

	$theme = get_option( 'stylesheet' );

	update_option( "theme_mods_$theme", $settings );
    wp_safe_redirect( admin_url( 'themes.php?page=picostrap-theme-options&successful-import' ) ); 
    
    exit;

}
add_action( 'admin_init', 'pico_process_settings_import' );


