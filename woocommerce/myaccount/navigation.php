<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );
?>

<nav class="woocommerce-MyAccount-navigation mb-4 mb-md-0" role="navigation" aria-label="<?php esc_html_e( 'Account pages', 'woocommerce' ); ?>">
	<div class="list-group nav nav-pills flex-column">
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
			<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"
			   class="list-group-item list-group-item-action nav-link <?php echo wc_is_current_account_menu_item( $endpoint ) ? 'active' : ''; ?>"
			   <?php echo wc_is_current_account_menu_item( $endpoint ) ? 'aria-current="page"' : ''; ?>>
				<?php echo esc_html( $label ); ?>
			</a>
		<?php endforeach; ?>
	</div>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
