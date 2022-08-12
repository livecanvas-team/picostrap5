<?php 
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

//DISABLE BLOCK EDITOR FOR WIDGETS
add_filter( 'use_widgets_block_editor', '__return_false' );