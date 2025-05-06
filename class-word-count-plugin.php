<?php

/**
 * Summary of Word_Count_Plugin
 */
class Word_Count_Plugin {

	/**
	 * Summary of __construct
	 * initializes the settings menu and the filter to add statistics to posts
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_page' ) );
		add_action( 'admin_init', array( $this, 'settings' ) );
		add_filter( 'the_content', array( $this, 'if_wrap' ) );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Load plugin text domain.
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'word-count', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Summary of if_wrap
	 *
	 * @param mixed $content content of the post.
	 */
	public function if_wrap( $content ) {
		if (
			( is_main_query() && is_single() )
			&&
			( get_option( 'wcp_wordcount', '1' ) || get_option( 'wcp_charactercount', '1' ) || get_option( 'wcp_readtime', '1' ) )
		) {
			return $this->create_html( $content );
		}
		return $content;
	}

	/**
	 * Summary of create_html
	 *
	 * @param mixed $content content of the post.
	 * @return string
	 */
	public function create_html( $content ) {
		$html = '<h3>' . esc_html( get_option( 'wcp_headline', __( 'Post Statistics', 'word-count' ) ) ) . '</h3><p>';

		if ( get_option( 'wcp_wordcount', '1' ) || get_option( 'wcp_readtime', '1' ) ) {
			$word_count = str_word_count( strip_tags( $content ) );
		}

		if ( get_option( 'wcp_wordcount', '1' ) ) {
			$html .= __( 'This post has ', 'word-count' ) . $word_count . __( ' words. ', 'word-count' ) . '<br>';
		}

		if ( get_option( 'wcp_charactercount', '1' ) ) {
			$html .= __( 'This post has ', 'word-count' ) . strlen( strip_tags( $content ) ) . __( ' characters. ', 'word-count' ) . '<br>';
		}

		if ( get_option( 'wcp_readtime', '1' ) ) {
			$html .= __( 'This post will take about ', 'word-count' ) . round( $word_count / 225 ) . __( ' minute(s) to read. ', 'word-count' ) . '<br>';
		}

		$html . '</p>';

		if ( get_option( 'wcp_location', '0' ) == '0' ) {
			return $html . $content;
		}
		return $content . $html;
	}

	/**
	 * Summary of settings
	 *
	 * @return void
	 */
	public function settings() {
		add_settings_section( 'wcp_first_section', null, null, 'word-count-settings-page' );

		add_settings_field( 'wcp_location', __( 'Display Location', 'word-count' ), array( $this, 'location_html' ), 'word-count-settings-page', 'wcp_first_section' );
		register_setting(
			'wordcountplugin',
			'wcp_location',
			array(
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '0',
			)
		);

		add_settings_field( 'wcp_headline', __( 'Headline Text', 'word-count' ), array( $this, 'headline_html' ), 'word-count-settings-page', 'wcp_first_section' );
		register_setting(
			'wordcountplugin',
			'wcp_headline',
			array(
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => __( 'Post Statistics', 'word-count' ),
			)
		);

		add_settings_field( 'wcp_wordcount', __( 'Word Count', 'word-count' ), array( $this, 'wordcount_html' ), 'word-count-settings-page', 'wcp_first_section' );
		register_setting(
			'wordcountplugin',
			'wcp_wordcount',
			array(
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '1',
			)
		);

		add_settings_field( 'wcp_charactercount', __( 'Character Count', 'word-count' ), array( $this, 'charactercount_html' ), 'word-count-settings-page', 'wcp_first_section' );
		register_setting(
			'wordcountplugin',
			'wcp_charactercount',
			array(
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '1',
			)
		);

		add_settings_field( 'wcp_readtime', __( 'Read Time', 'word-count' ), array( $this, 'readtime_html' ), 'word-count-settings-page', 'wcp_first_section' );
		register_setting(
			'wordcountplugin',
			'wcp_readtime',
			array(
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '1',
			)
		);
	}

	/**
	 * Summary of readtime_html
	 *
	 * @return void
	 */
	public function readtime_html() {
		?>
		<input type="checkbox" name="wcp_readtime" value="1" <?php checked( get_option( 'wcp_readtime' ), '1' ); ?>>
		<?php
	}

	/**
	 * Summary of charactercount_html
	 *
	 * @return void
	 */
	public function charactercount_html() {
		?>
		<input type="checkbox" name="wcp_charactercount" value="1" <?php checked( get_option( 'wcp_charactercount' ), '1' ); ?>>
		<?php
	}

	/**
	 * Summary of wordcount_html
	 *
	 * @return void
	 */
	public function wordcount_html() {
		?>
		<input type="checkbox" name="wcp_wordcount" value="1" <?php checked( get_option( 'wcp_wordcount' ), '1' ); ?>>
		<?php
	}

	/**
	 * Summary of headline_html
	 *
	 * @return void
	 */
	public function headline_html() {
		?>
		<input type="text" name="wcp_headline" value="<?php echo esc_attr( get_option( 'wcp_headline' ) ); ?>">
		<?php
	}

	/**
	 * Summary of location_html
	 *
	 * @return void
	 */
	public function location_html() {
		?>
		<select name="wcp_location">
			<option value="0" <?php selected( get_option( 'wcp_location' ), '0' ); ?>><?php esc_html_e( 'Beginning of post', 'word-count' ); ?></option>
			<option value="1" <?php selected( get_option( 'wcp_location' ), '1' ); ?>><?php esc_html_e( 'End of post', 'word-count' ); ?></option>
		</select>
		<?php
	}

	/**
	 * Summary of admin_page
	 *
	 * @return void
	 */
	public function admin_page() {
		add_options_page( __( 'Word Count Settings', 'word-count' ), __( 'Word Count', 'word-count' ), 'manage_options', 'word-count-settings-page', array( $this, 'settings_page_html' ) );
	}

	/**
	 * Summary of settings_page_html
	 *
	 * @return void
	 */
	public function settings_page_html() {
		?>
		<div class="wrap">
			<h1>Word Count Settings</h1>
			<form action="options.php" method="POST">
				<?php
				settings_fields( 'wordcountplugin' );
				do_settings_sections( 'word-count-settings-page' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}