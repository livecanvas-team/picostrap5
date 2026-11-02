<?php
/** One font-code inspector with an advanced editor for the existing theme-code setting. */
defined( 'ABSPATH' ) || exit;

class Picostrap_Font_Loading_Control extends WP_Customize_Control {
    public $type = 'picostrap-font-loading';

    public function render_content() {
        $wordpress_code = picostrap_get_wordpress_font_header_code();
        $preview = picostrap_get_font_header_preview( $this->value(), get_theme_mod( 'picostrap_fonts_header_code_disable', false ), $wordpress_code );
        ?>
        <label for="pico-font-header-preview"><span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span></label>
        <p id="pico-font-header-description" class="description"><?php esc_html_e( 'Code preview for your current font settings: theme code plus WordPress font-face rules. WordPress manages the local rules and outputs them once; they are not copied into your saved theme code.', 'picostrap5' ); ?></p>
        <p id="pico-font-header-status" class="description" role="status"></p>
        <textarea id="pico-font-header-preview" rows="12" readonly spellcheck="false" aria-describedby="pico-font-header-description pico-font-header-status"><?php echo esc_textarea( $preview ); ?></textarea>
        <textarea id="pico-wordpress-font-source" hidden readonly aria-hidden="true" tabindex="-1"><?php echo esc_textarea( $wordpress_code ); ?></textarea>
        <details class="pico-font-code-editor">
            <summary><?php esc_html_e( 'Edit theme font code', 'picostrap5' ); ?></summary>
            <label for="_customize-input-picostrap_fonts_header_code"><?php esc_html_e( 'Theme font code (editable)', 'picostrap5' ); ?></label>
            <p class="description"><?php esc_html_e( 'This saves your CDN or custom font-loading code. Font-picker changes regenerate it and can replace manual edits. WordPress-hosted fonts are managed in the Font Library and appear in the preview above. Publish to save code edits.', 'picostrap5' ); ?></p>
            <textarea id="_customize-input-picostrap_fonts_header_code" rows="8" spellcheck="false" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
            <p><a href="#" id="regenerate-font-loading-code"><?php esc_html_e( 'Regenerate theme code from font choices', 'picostrap5' ); ?></a></p>
        </details>
        <?php
    }
}
