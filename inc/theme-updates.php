<?php
/**
 * Update the parent theme from the latest stable GitHub Release (WordPress 6.1+).
 * Loaded by the parent even when a child theme is active.
 */
defined( 'ABSPATH' ) || exit;

/** Return validated update metadata, or false when GitHub is unavailable. */
function picostrap_get_github_release() {
    $cache_key = 'picostrap5_github_release';
    $cached = get_site_transient( $cache_key );
    if ( false !== $cached ) {
        return is_array( $cached ) ? $cached : false;
    }

    // Cache failures briefly too, to avoid repeated requests when rate limited.
    set_site_transient( $cache_key, 'unavailable', 5 * MINUTE_IN_SECONDS );
    $response = wp_remote_get( 'https://api.github.com/repos/livecanvas-team/picostrap5/releases/latest', array(
        'timeout' => 10,
        'headers' => array( 'Accept' => 'application/vnd.github+json' ),
    ) );
    if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
        return false;
    }

    $release = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( ! is_array( $release ) || ! isset( $release['draft'], $release['prerelease'], $release['tag_name'] )
        || false !== $release['draft'] || false !== $release['prerelease'] || ! is_string( $release['tag_name'] )
        || ! preg_match( '/^v?(\d+\.\d+\.\d+)$/D', $release['tag_name'], $matches ) ) {
        return false;
    }

    $base = 'https://github.com/livecanvas-team/picostrap5';
    $tag = rawurlencode( $release['tag_name'] );
    $package = $base . '/archive/refs/tags/' . $tag . '.zip';
    foreach ( isset( $release['assets'] ) && is_array( $release['assets'] ) ? $release['assets'] : array() as $asset ) {
        if ( isset( $asset['name'], $asset['browser_download_url'] ) && 'picostrap5.zip' === $asset['name']
            && $base . '/releases/download/' . $tag . '/picostrap5.zip' === $asset['browser_download_url'] ) {
            $package = $asset['browser_download_url'];
            break;
        }
    }

    $update = array(
        'version' => $matches[1],
        'url'     => $base . '/releases/tag/' . $tag,
        'package' => $package,
    );
    set_site_transient( $cache_key, $update, 6 * HOUR_IN_SECONDS );
    return $update;
}

/** WordPress compares the release version with the installed parent version. */
function picostrap_github_theme_update( $update, $theme_data, $stylesheet ) {
    if ( get_template() !== $stylesheet || 'https://github.com/livecanvas-team/picostrap5' !== $theme_data['UpdateURI'] ) {
        return $update;
    }
    $release = picostrap_get_github_release();
    return $release ? array_merge( $release, array( 'theme' => $stylesheet ) ) : $update;
}
add_filter( 'update_themes_github.com', 'picostrap_github_theme_update', 10, 3 );

/** Honor the native Dashboard > Updates > Check again action. */
function picostrap_force_github_update_check() {
    if ( ! empty( $_GET['force-check'] ) && current_user_can( 'update_themes' ) ) {
        delete_site_transient( 'picostrap5_github_release' );
        delete_site_transient( 'update_themes' );
    }
}
add_action( 'load-update-core.php', 'picostrap_force_github_update_check', 5 );

/** GitHub source archives have a version suffix; preserve the installed slug. */
function picostrap_github_update_directory( $source, $remote_source, $upgrader, $hook_extra ) {
    if ( is_wp_error( $source ) || empty( $hook_extra['theme'] ) || get_template() !== $hook_extra['theme']
        || 'theme' !== ( $hook_extra['type'] ?? '' ) || 'update' !== ( $hook_extra['action'] ?? '' ) ) {
        return $source;
    }
    $destination = trailingslashit( $remote_source ) . $hook_extra['theme'] . '/';
    if ( untrailingslashit( $source ) === untrailingslashit( $destination ) ) {
        return $source;
    }
    global $wp_filesystem;
    if ( ! $wp_filesystem->move( $source, $destination ) ) {
        return new WP_Error( 'picostrap_update_directory', __( 'Unable to prepare the Picostrap update directory.', 'picostrap5' ) );
    }
    return $destination;
}
add_filter( 'upgrader_source_selection', 'picostrap_github_update_directory', 10, 4 );
