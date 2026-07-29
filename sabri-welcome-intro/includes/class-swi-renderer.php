<?php

defined( 'ABSPATH' ) || exit;

final class SWI_Renderer {
	const COOKIE_NAME      = 'sabri_welcome_seen';
	const DURATION         = 8000;
	const REDUCED_DURATION = 1200;

	/** @var bool */
	private $rendered = false;

	/** @var bool|null */
	private $authorized_preview = null;

	public function hooks() {
		add_action( 'init', array( $this, 'prepare_preview_request' ), 0 );
		add_action( 'template_redirect', array( $this, 'redirect_unauthorized_preview' ), 0 );
		add_action( 'wp_head', array( $this, 'print_bootstrap' ), 0 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ), 1 );
		add_action( 'wp_body_open', array( $this, 'render' ), 0 );
		// Supports older themes that do not call wp_body_open().
		add_action( 'wp_footer', array( $this, 'render' ), 0 );
	}

	public function prepare_preview_request() {
		if ( ! $this->is_authorized_preview() ) {
			return;
		}

		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}

		add_filter(
			'wp_robots',
			static function ( $robots ) {
				$robots['noindex']   = true;
				$robots['noarchive'] = true;
				return $robots;
			}
		);
	}

	public function redirect_unauthorized_preview() {
		if ( ! $this->preview_requested() || $this->is_authorized_preview() ) {
			if ( $this->is_authorized_preview() ) {
				nocache_headers();
				header( 'X-Robots-Tag: noindex, noarchive', true );
			}
			return;
		}

		$clean_url = remove_query_arg( array( 'swi_preview', '_wpnonce' ) );
		wp_safe_redirect( $clean_url, 302, 'Sabri Welcome Intro' );
		exit;
	}

	public function enqueue_assets() {
		if ( ! $this->should_load() ) {
			return;
		}

		wp_enqueue_style(
			'sabri-welcome-intro',
			SWI_URL . 'assets/css/welcome-intro.css',
			array(),
			SWI_VERSION
		);

		wp_enqueue_script(
			'sabri-welcome-intro',
			SWI_URL . 'assets/js/welcome-intro.js',
			array(),
			SWI_VERSION,
			true
		);
	}

	public function print_bootstrap() {
		if ( ! $this->should_load() ) {
			return;
		}

		$path   = SWI_DIR . 'assets/js/welcome-bootstrap.js';
		$script = is_readable( $path ) ? file_get_contents( $path ) : false;

		if ( false === $script || '' === trim( $script ) ) {
			// The overlay is hidden by default, so a missing bootstrap fails open.
			return;
		}

		wp_print_inline_script_tag(
			$script,
			array(
				'id'               => 'swi-welcome-bootstrap',
				'data-swi-preview' => $this->is_authorized_preview() ? '1' : '0',
				'data-swi-cookie'  => self::COOKIE_NAME,
				'data-no-optimize' => '1',
				'data-cfasync'     => 'false',
			)
		);
	}

	public function render() {
		if ( $this->rendered || ! $this->should_load() ) {
			return;
		}

		$this->rendered = true;
		?>
		<aside
			id="swi-intro"
			class="swi-intro"
			role="dialog"
			aria-modal="true"
			aria-labelledby="swi-brand-name"
			aria-describedby="swi-brand-claim"
			tabindex="-1"
			data-duration="<?php echo esc_attr( self::DURATION ); ?>"
			data-reduced-duration="<?php echo esc_attr( self::REDUCED_DURATION ); ?>"
			data-cookie-name="<?php echo esc_attr( self::COOKIE_NAME ); ?>"
			data-preview="<?php echo esc_attr( $this->is_authorized_preview() ? '1' : '0' ); ?>"
		>
			<button class="swi-skip" type="button" data-swi-skip>
				<?php esc_html_e( 'Skip Intro', 'sabri-welcome-intro' ); ?>
			</button>

			<div class="swi-ambient" aria-hidden="true"></div>

			<div class="swi-content">
				<img
					class="swi-logo"
					src="<?php echo esc_url( SWI_URL . 'assets/images/sabri-sh-logo.svg' ); ?>"
					width="160"
					height="160"
					alt="<?php esc_attr_e( 'Sabri Homeopathy circular SH logo', 'sabri-welcome-intro' ); ?>"
					fetchpriority="high"
					decoding="async"
				>

				<p id="swi-brand-name" class="swi-brand-name" lang="en-US">
					Sabri Homeopathy
				</p>

				<p id="swi-brand-claim" class="swi-brand-claim" lang="en-US">
					The Tridimensional Healing System of Soul, Vital Force, and Matter
				</p>

				<div class="swi-line-track" aria-hidden="true">
					<span class="swi-line"></span>
				</div>
			</div>
		</aside>
		<noscript><style>#swi-intro{display:none!important}</style></noscript>
		<?php
	}

	private function preview_requested() {
		return isset( $_GET['swi_preview'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['swi_preview'] ) );
	}

	private function is_authorized_preview() {
		if ( null !== $this->authorized_preview ) {
			return $this->authorized_preview;
		}

		$this->authorized_preview = false;

		if ( ! $this->preview_requested() || ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : '';
		$this->authorized_preview = (bool) wp_verify_nonce( $nonce, 'swi_preview' );

		return $this->authorized_preview;
	}

	private function should_load() {
		if ( is_admin() || wp_doing_ajax() ) {
			return false;
		}

		if ( function_exists( 'wp_is_json_request' ) && wp_is_json_request() ) {
			return false;
		}

		if ( is_feed() || is_robots() || is_trackback() ) {
			return false;
		}

		return $this->is_authorized_preview() || (bool) get_option( 'swi_enabled', 1 );
	}
}
