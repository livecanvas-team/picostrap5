<?php
 
//ADD SOME JS TO THE FOOTER 
add_action( 'wp_footer', function(){ ?>
	 
	<a id="backToTop" onclick="window.scroll({  top: 0,   left: 0,   behavior: 'smooth'});" class="bg-light text-dark rounded"> 		
		<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-chevron-up" fill="currentColor" xmlns="http://www.w3.org/2000/svg">  <path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708l6-6z"/></svg>
	</a>
	 
	<script>
	window.addEventListener('scroll', function(){
		if(window.pageYOffset >= 1000) document.getElementById('backToTop').style.visibility="visible"; else document.getElementById('backToTop').style.visibility="hidden";
		}, { capture: false, passive: true});
	</script>
	
<?php } );
 