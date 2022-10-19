<?php



//FOOTER TEXT CUSTOMIZATION
if(!function_exists('picostrap_site_info')):
	function picostrap_site_info(){
		$footer_text_setting = get_theme_mod("picostrap_footer_text");
		?>
			<div class="site-info small">
				<?php if (strlen($footer_text_setting) > 0) echo $footer_text_setting; ?>
				<?php if (current_user_can("administrator") && strlen($footer_text_setting) <= 0): ?> You can edit this footer text using the WordPress Customizer.<?php endif ?>
			</div>
						
		<?php
	}
endif;

///SHARING BUTTONS ////
if(!function_exists('picostrap_the_sharing_buttons')):
	function picostrap_the_sharing_buttons(){

		global $post;
		$url_to_share=esc_attr(get_permalink($post->ID));
		?>
		<div class="picostrap-sharing-buttons mt-4 mb-5" >
		
			<!-- Basic Share Links -->
			<span class="d-block mb-3 h4"><?php _e( 'Share this post', 'picostrap' ); ?>: &nbsp; </span>
		
			<!-- Facebook (url) -->
			<a class="btn btn-outline-dark btn-sm btn-facebook" href="https://www.facebook.com/sharer.php?u=<?php echo $url_to_share ?>" target="_blank" rel="nofollow" title="Share on Facebook">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="2em" height="2em" lc-helper="svg-icon" fill="currentColor">
				<path d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z"></path>
			</svg>
				<span class="d-none d-lg-inline"> Facebook</span>
			</a>
			
			<!-- Whatsapp (url) -->
			<a class="btn btn-outline-dark  btn-sm btn-whatsapp" href="https://api.whatsapp.com/send?text=<?php echo $url_to_share ?>" target="_blank" rel="nofollow" title="Share on Whatsapp">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="2em" height="2em" lc-helper="svg-icon" fill="currentColor">
					<path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"></path>
				</svg>
				<span class="d-none d-lg-inline"> Whatsapp</span>
			</a>
			
			<!-- Telegram (url) -->
			<a class="btn btn-outline-dark  btn-sm btn-telegram" href="https://telegram.me/share/url?url=<?php echo $url_to_share ?>&text=" target="_blank" rel="nofollow" title="Share on Telegram">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" width="2em" height="2em" lc-helper="svg-icon" fill="currentColor">
					<path d="M446.7 98.6l-67.6 318.8c-5.1 22.5-18.4 28.1-37.3 17.5l-103-75.9-49.7 47.8c-5.5 5.5-10.1 10.1-20.7 10.1l7.4-104.9 190.9-172.5c8.3-7.4-1.8-11.5-12.9-4.1L117.8 284 16.2 252.2c-22.1-6.9-22.5-22.1 4.6-32.7L418.2 66.4c18.4-6.9 34.5 4.1 28.5 32.2z"></path>
				</svg>
				<span class="d-none d-lg-inline"> Telegram</span>
			</a>
			
			
			<!-- Twitter (url, text, @mention) -->
			<a class="btn btn-outline-dark  btn-sm btn-twitter" href="https://twitter.com/share?url=<?php echo $url_to_share ?>&text=<?php echo esc_attr(get_the_title()) ?>&via=<?php echo get_the_author_meta( 'twitter', get_the_author_meta( 'ID' ) ) ?>" target="_blank" rel="nofollow" title="Share on Twitter">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="2em" height="2em" lc-helper="svg-icon" fill="currentColor">
					<path d="M459.37 151.716c.325 4.548.325 9.097.325 13.645 0 138.72-105.583 298.558-298.558 298.558-59.452 0-114.68-17.219-161.137-47.106 8.447.974 16.568 1.299 25.34 1.299 49.055 0 94.213-16.568 130.274-44.832-46.132-.975-84.792-31.188-98.112-72.772 6.498.974 12.995 1.624 19.818 1.624 9.421 0 18.843-1.3 27.614-3.573-48.081-9.747-84.143-51.98-84.143-102.985v-1.299c13.969 7.797 30.214 12.67 47.431 13.319-28.264-18.843-46.781-51.005-46.781-87.391 0-19.492 5.197-37.36 14.294-52.954 51.655 63.675 129.3 105.258 216.365 109.807-1.624-7.797-2.599-15.918-2.599-24.04 0-57.828 46.782-104.934 104.934-104.934 30.213 0 57.502 12.67 76.67 33.137 23.715-4.548 46.456-13.32 66.599-25.34-7.798 24.366-24.366 44.833-46.132 57.827 21.117-2.273 41.584-8.122 60.426-16.243-14.292 20.791-32.161 39.308-52.628 54.253z"></path>
				</svg>
				<span class="d-none d-lg-inline"> Twitter</span>
			</a>
		
		
			<!-- Email (subject, body) --> 
			<a class="btn btn-outline-dark  btn-sm btn-email" href="mailto:?subject=<?php echo esc_attr(get_the_title()) ?>&amp;body=<?php echo $url_to_share ?>" target="_blank" rel="nofollow" title="Share via Email">
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="2em" height="2em" viewBox="0 0 24 24" lc-helper="svg-icon" fill="currentColor">
					<path d="M22 6C22 4.9 21.1 4 20 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6M20 6L12 11L4 6H20M20 18H4V8L12 13L20 8V18Z"></path>
				</svg>
				<span class="d-none d-lg-inline"> Email</span>
			</a>
		
		</div>
		<?php
	} //end function
endif;
 