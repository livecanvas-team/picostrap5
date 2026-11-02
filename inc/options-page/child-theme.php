<?php
/** Install the official starter from a published stable GitHub release. */
defined( 'ABSPATH' ) || exit;

function picostrap_get_child_theme_release() {
    $response = wp_remote_get( 'https://api.github.com/repos/livecanvas-team/picostrap5-child-base/releases/latest', array(
        'timeout' => 15,
        'headers' => array( 'Accept' => 'application/vnd.github+json' ),
    ) );
    $error = new WP_Error( 'picostrap_child_release', __( 'The latest stable child theme release could not be verified. Please try again later or review the releases on GitHub.', 'picostrap5' ) );
    if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) return $error;
    $release = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( ! is_array( $release ) || ! isset( $release['draft'], $release['prerelease'], $release['tag_name'] )
        || false !== $release['draft'] || false !== $release['prerelease'] || ! is_string( $release['tag_name'] )
        || ! preg_match( '/^v?((?:0|[1-9]\d*)\.(?:0|[1-9]\d*)\.(?:0|[1-9]\d*))$/D', $release['tag_name'], $match ) ) return $error;
    return array(
        'version' => $match[1],
        'tag'     => $release['tag_name'],
        'package' => 'https://github.com/livecanvas-team/picostrap5-child-base/archive/refs/tags/' . rawurlencode( $release['tag_name'] ) . '.zip',
    );
}

/** Keep installation separate from activation and preserve existing project files. */
function picostrap_options_child_theme_button() {
    $theme = wp_get_theme( 'picostrap5-child-base' );
    if ( $theme->exists() ) {
        ?>
        <div class="pico-actions"><a class="pico-button" href="<?php echo esc_url( add_query_arg( 'theme', 'picostrap5-child-base', self_admin_url( 'themes.php' ) ) ); ?>"><?php esc_html_e( 'Review installed child theme', 'picostrap5' ); ?></a></div>
        <p class="pico-help"><?php esc_html_e( 'The starter child theme is already installed. Review or activate it in Themes; its files will not be overwritten.', 'picostrap5' ); ?></p>
        <?php
        return;
    }
    if ( ! current_user_can( 'install_themes' ) || ! wp_is_file_mod_allowed( 'themes' ) ) {
        ?><p class="pico-help"><?php esc_html_e( 'Ask an administrator with theme installation permissions to install the Picostrap starter child theme.', 'picostrap5' ); ?></p><?php
        return;
    }
    ?>
    <form class="pico-child-install" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="picostrap_install_child_theme">
        <?php wp_nonce_field( 'picostrap_install_child_theme' ); ?>
        <button class="pico-button" type="submit" aria-describedby="pico-child-install-help">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12m-5-5 5 5 5-5M4 16v5h16v-5"/></svg>
            <?php esc_html_e( 'Install starter child theme', 'picostrap5' ); ?>
        </button>
        <p class="pico-help" id="pico-child-install-help"><?php esc_html_e( 'Downloads the latest stable tagged release from the official GitHub repository. WordPress will offer activation after installation; your active theme stays unchanged until you choose Activate.', 'picostrap5' ); ?></p>
    </form>
    <?php
}

/** Check package identity before installing under the stable theme directory name. */
function picostrap_prepare_child_theme_source( $source, $remote_source, $release ) {
    $slug = 'picostrap5-child-base';
    global $wp_filesystem;
    $header = $wp_filesystem->get_contents( trailingslashit( $source ) . 'style.css' );
    if ( ! is_string( $header )
        || ! preg_match( '/^[ \t\/*#@]*Template:\s*([^\r\n]+)/mi', $header, $parent )
        || 'picostrap5' !== trim( $parent[1] )
        || ! preg_match( '/^[ \t\/*#@]*Version:\s*([^\r\n]+)/mi', $header, $version )
        || $release['version'] !== trim( $version[1] ) ) {
        return new WP_Error( 'picostrap_child_package', __( 'The child theme package does not match its stable release version or Picostrap parent. Installation was stopped.', 'picostrap5' ) );
    }
    $destination = trailingslashit( $remote_source ) . $slug . '/';
    if ( untrailingslashit( $source ) !== untrailingslashit( $destination ) && ! $wp_filesystem->move( $source, $destination, false ) ) {
        return new WP_Error( 'picostrap_child_directory', __( 'Unable to prepare the child theme directory.', 'picostrap5' ) );
    }
    return $destination;
}

add_action( 'admin_post_picostrap_install_child_theme', 'picostrap_install_child_theme' );
function picostrap_install_child_theme() {
    if ( ! current_user_can( 'install_themes' ) || ! wp_is_file_mod_allowed( 'themes' ) ) {
        wp_die( esc_html__( 'You are not allowed to install themes.', 'picostrap5' ), '', array( 'response' => 403 ) );
    }
    check_admin_referer( 'picostrap_install_child_theme' );
    $slug = 'picostrap5-child-base';
    if ( wp_get_theme( $slug )->exists() || is_dir( get_theme_root() . '/' . $slug ) ) {
        wp_safe_redirect( add_query_arg( 'theme', $slug, self_admin_url( 'themes.php' ) ) );
        exit;
    }
    $release = picostrap_get_child_theme_release();
    if ( is_wp_error( $release ) ) {
        wp_die( esc_html( $release->get_error_message() ), '', array( 'back_link' => true ) );
    }
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    $skin = new Theme_Installer_Skin( array(
        'type'  => 'web',
        'url'   => admin_url( 'admin-post.php?action=picostrap_install_child_theme' ),
        'nonce' => 'picostrap_install_child_theme',
        'title' => __( 'Install Picostrap starter child theme', 'picostrap5' ),
        'api'   => (object) array( 'name' => 'Picostrap Child Base', 'version' => $release['version'] ),
    ) );
    $upgrader = new Theme_Upgrader( $skin );
    // GitHub adds the tag to the ZIP directory name. Validate and normalize it.
    $prepare_source = function ( $source, $remote_source, $instance ) use ( $upgrader, $release ) {
        if ( $instance !== $upgrader || is_wp_error( $source ) ) return $source;
        return picostrap_prepare_child_theme_source( $source, $remote_source, $release );
    };
    add_filter( 'upgrader_source_selection', $prepare_source, 20, 3 );
    set_current_screen( 'themes' );
    global $title, $parent_file;
    $title = __( 'Install Picostrap starter child theme', 'picostrap5' );
    $parent_file = 'themes.php';
    require ABSPATH . 'wp-admin/admin-header.php';
    $upgrader->install( $release['package'], array( 'overwrite_package' => false ) );
    remove_filter( 'upgrader_source_selection', $prepare_source, 20 );
    require ABSPATH . 'wp-admin/admin-footer.php';
    exit;
}
