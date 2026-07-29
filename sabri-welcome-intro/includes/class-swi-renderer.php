<?php

defined( 'ABSPATH' ) || exit;

final class SWI_Renderer {
	const COOKIE_NAME      = 'sabri_welcome_seen';
	const DURATION         = 8000;
	const REDUCED_DURATION = 1200;

	/** @var bool */
	private $rendered = false;

	public function hooks() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ), 1 );
		add_action( 'wp_body_open', array( $this, 'render' ), 0 );
		// Supports older themes that do not call wp_body_open().
		add_action( 'wp_footer', array( $this, 'render' ), 0 );
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
			'sabri-welcome-bootstrap',
			SWI_URL . 'assets/js/welcome-bootstrap.js',
			array(),
			SWI_VERSION,
			false
		);

		wp_enqueue_script(
			'sabri-welcome-intro',
			SWI_URL . 'assets/js/welcome-intro.js',
			array(),
			SWI_VERSION,
			true
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
			data-duration="<?php echo esc_attr( self::DURATION ); ?>"
			data-reduced-duration="<?php echo esc_attr( self::REDUCED_DURATION ); ?>"
			data-cookie-name="<?php echo esc_attr( self::COOKIE_NAME ); ?>"
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
					alt="<?php esc_attr_e( 'Sabri Homeopathy logo', 'sabri-welcome-intro' ); ?>"
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

	private function should_load() {
		if ( is_admin() || wp_doing_ajax() || ! (bool) get_option( 'swi_enabled', 1 ) ) {
			return false;
		}

		if ( function_exists( 'wp_is_json_request' ) && wp_is_json_request() ) {
			return false;
		}

		return ! is_feed() && ! is_robots() && ! is_trackback();
	}
}
