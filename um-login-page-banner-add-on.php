<?php
/**
 * Plugin Name:       UM Login Page Banner Add-on
 * Plugin URI:        https://github.com/xjibin/UM-Login-Page-Banner-Add-on
 * Description:       Adds a configurable, clickable banner to the Ultimate Member login page through the [um_banner] shortcode. Set the banner image URL and the click-through redirect URL from the admin.
 * Version:           1.0.0
 * Requires at least: 5.6
 * Requires PHP:      7.2
 * Author:            Jibin Jose
 * Author URI:        https://github.com/xjibin/UM-Login-Page-Banner-Add-on
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       um-login-banner
 *
 * @package UM_Login_Page_Banner_Addon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Block direct access.
}

final class UM_Login_Page_Banner_Addon {

	/** Option key in wp_options. */
	const OPTION_KEY = 'um_login_banner_options';

	/** Admin menu / settings page slug. */
	const MENU_SLUG = 'um-login-banner';

	/** Settings group used by the Settings API. */
	const GROUP = 'um_login_banner_group';

	/** @var UM_Login_Page_Banner_Addon|null */
	private static $instance = null;

	/**
	 * Singleton accessor.
	 *
	 * @return UM_Login_Page_Banner_Addon
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
		add_shortcode( 'um_banner', array( $this, 'render_shortcode' ) );
	}

	/**
	 * Get saved options merged with defaults.
	 *
	 * @return array{image_url:string,redirect_url:string}
	 */
	public function get_options() {
		$defaults = array(
			'image_url'    => '',
			'redirect_url' => '',
		);
		return wp_parse_args( (array) get_option( self::OPTION_KEY, array() ), $defaults );
	}

	/* --------------------------------------------------------------------- *
	 *  Admin menu
	 * --------------------------------------------------------------------- */

	public function register_menu() {
		add_menu_page(
			__( 'UM Login Banner', 'um-login-banner' ),
			__( 'UM Login Banner', 'um-login-banner' ),
			'manage_options',
			self::MENU_SLUG,
			array( $this, 'render_settings_page' ),
			'dashicons-format-image',
			81
		);
	}

	/* --------------------------------------------------------------------- *
	 *  Settings API
	 * --------------------------------------------------------------------- */

	public function register_settings() {
		register_setting(
			self::GROUP,
			self::OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_options' ),
				'default'           => array(),
			)
		);

		add_settings_section(
			'um_login_banner_section',
			__( 'Banner Settings', 'um-login-banner' ),
			array( $this, 'render_section_intro' ),
			self::MENU_SLUG
		);

		add_settings_field(
			'image_url',
			__( 'Banner Image URL', 'um-login-banner' ),
			array( $this, 'render_image_field' ),
			self::MENU_SLUG,
			'um_login_banner_section',
			array( 'label_for' => 'um_banner_image_url' )
		);

		add_settings_field(
			'redirect_url',
			__( 'Redirect URL', 'um-login-banner' ),
			array( $this, 'render_redirect_field' ),
			self::MENU_SLUG,
			'um_login_banner_section',
			array( 'label_for' => 'um_banner_redirect_url' )
		);
	}

	/**
	 * Sanitize and validate both fields before saving.
	 *
	 * @param mixed $input Raw input.
	 * @return array
	 */
	public function sanitize_options( $input ) {
		$input  = is_array( $input ) ? $input : array();
		$output = array(
			'image_url'    => isset( $input['image_url'] ) ? esc_url_raw( trim( (string) $input['image_url'] ) ) : '',
			'redirect_url' => isset( $input['redirect_url'] ) ? esc_url_raw( trim( (string) $input['redirect_url'] ) ) : '',
		);
		return $output;
	}

	public function render_section_intro() {
		echo '<p>' . esc_html__( 'Enter the banner image URL and the URL to redirect to when the banner is clicked, then place the shortcode below on your login page.', 'um-login-banner' ) . '</p>';
	}

	public function render_image_field() {
		$value = $this->get_options()['image_url'];
		?>
		<input type="url"
			id="um_banner_image_url"
			name="<?php echo esc_attr( self::OPTION_KEY ); ?>[image_url]"
			value="<?php echo esc_attr( $value ); ?>"
			class="regular-text"
			placeholder="https://example.com/banner.png" />
		<button type="button" class="button um-banner-upload"><?php esc_html_e( 'Select Image', 'um-login-banner' ); ?></button>
		<p class="description"><?php esc_html_e( 'Paste an image URL or choose one from the Media Library.', 'um-login-banner' ); ?></p>
		<img src="<?php echo esc_url( $value ); ?>"
			class="um-banner-preview"
			alt=""
			style="max-width:240px;height:auto;margin-top:10px;border-radius:8px;<?php echo $value ? '' : 'display:none;'; ?>" />
		<?php
	}

	public function render_redirect_field() {
		$value = $this->get_options()['redirect_url'];
		?>
		<input type="url"
			id="um_banner_redirect_url"
			name="<?php echo esc_attr( self::OPTION_KEY ); ?>[redirect_url]"
			value="<?php echo esc_attr( $value ); ?>"
			class="regular-text"
			placeholder="https://example.com/" />
		<p class="description"><?php esc_html_e( 'Where visitors go when they click the banner. Leave blank to make the banner non-clickable.', 'um-login-banner' ); ?></p>
		<?php
	}

	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<form method="post" action="options.php">
				<?php
				settings_fields( self::GROUP );
				do_settings_sections( self::MENU_SLUG );
				submit_button();
				?>
			</form>

			<hr />
			<h2><?php esc_html_e( 'How to use', 'um-login-banner' ); ?></h2>
			<p><?php esc_html_e( 'Add this shortcode where you want the banner to appear on the login page (e.g. next to the Ultimate Member login form):', 'um-login-banner' ); ?></p>
			<p><code>[um_banner=1000]</code></p>
			<p class="description"><?php esc_html_e( 'The banner is output with the CSS class "quod-login-banner" so your existing login-page styling applies automatically.', 'um-login-banner' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Load the media uploader only on this plugin's settings screen.
	 *
	 * @param string $hook Current admin page hook suffix.
	 */
	public function enqueue_admin_assets( $hook ) {
		if ( 'toplevel_page_' . self::MENU_SLUG !== $hook ) {
			return;
		}

		wp_enqueue_media();

		$js = <<<'JS'
jQuery(function($){
	var frame;
	$('.um-banner-upload').on('click', function(e){
		e.preventDefault();
		if (frame) { frame.open(); return; }
		frame = wp.media({
			title: 'Select banner image',
			button: { text: 'Use this image' },
			multiple: false
		});
		frame.on('select', function(){
			var att = frame.state().get('selection').first().toJSON();
			$('#um_banner_image_url').val(att.url);
			$('.um-banner-preview').attr('src', att.url).show();
		});
		frame.open();
	});
	$('#um_banner_image_url').on('input', function(){
		var v = $(this).val();
		if (v) { $('.um-banner-preview').attr('src', v).show(); } else { $('.um-banner-preview').hide(); }
	});
});
JS;
		wp_add_inline_script( 'jquery-core', $js );
	}

	/* --------------------------------------------------------------------- *
	 *  Shortcode  [um_banner]
	 * --------------------------------------------------------------------- */

	/**
	 * Render the banner. Works with [um_banner], [um_banner=1000] or [um_banner id="1000"].
	 *
	 * @param array|string $atts    Shortcode attributes (ignored; settings are global).
	 * @param string|null  $content Enclosed content (unused).
	 * @return string
	 */
	public function render_shortcode( $atts = array(), $content = null ) {
		$options = $this->get_options();
		$image   = $options['image_url'];

		if ( empty( $image ) ) {
			return ''; // Nothing configured yet.
		}

		$redirect = $options['redirect_url'];
		$alt      = esc_attr__( 'Login banner', 'um-login-banner' );

		$img = sprintf( '<img src="%s" alt="%s" />', esc_url( $image ), $alt );

		if ( ! empty( $redirect ) ) {
			return sprintf(
				'<a class="quod-login-banner" href="%s" target="_blank" rel="noopener noreferrer">%s</a>',
				esc_url( $redirect ),
				$img
			);
		}

		// No redirect set: render a non-clickable banner with the same class for styling.
		return sprintf( '<span class="quod-login-banner">%s</span>', $img );
	}
}

UM_Login_Page_Banner_Addon::instance();
