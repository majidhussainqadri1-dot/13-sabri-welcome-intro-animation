<?php
namespace Sabri\WelcomeIntro;
if ( ! defined( 'ABSPATH' ) ) { exit; }

final class Renderer {
	private static $rendered = false;

	public static function register() {
		add_action( 'sabri_shell_welcome_intro_invoke', array( __CLASS__, 'invoke' ), 10, 1 );
		add_action( 'template_redirect', array( __CLASS__, 'maybe_preview' ), 1 );
	}

	public static function invoke( $context = array() ) {
		if ( self::$rendered || ! is_array( $context ) || ! Eligibility::public_request_eligible( $context ) ) { return; }
		self::$rendered = true;
		$config = Settings::get();
		$tokens = self::visual_tokens();

		wp_enqueue_script( 'swi-welcome-intro', SWI_URL . 'assets/js/welcome-intro.js', array(), SWI_VERSION, true );
		wp_localize_script( 'swi-welcome-intro', 'SWI_INTRO', array(
			'version' => absint( $config['config_version'] ),
			'durationMs' => absint( $config['duration_ms'] ),
			'recurrenceDays' => absint( $config['recurrence_days'] ),
			'analyticsEnabled' => ! empty( $config['analytics_enabled'] ),
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'eventNonce' => wp_create_nonce( Analytics::NONCE_ACTION ),
			'preview' => false,
		) );
		echo self::style_block( $tokens ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo self::markup( $config, false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	public static function maybe_preview() {
		if ( ! get_query_var( Eligibility::PREVIEW_QUERY_VAR ) ) { return; }
		if ( ! is_user_logged_in() ) { auth_redirect(); exit; }
		Authorization::require_manage( 'preview_intro' );
		nocache_headers();
		header( 'X-Robots-Tag: noindex, nofollow, noarchive', true );

		$state = isset( $_GET['state'] ) ? sanitize_key( wp_unslash( $_GET['state'] ) ) : 'default';
		if ( ! in_array( $state, array( 'default', 'reduced', 'error', 'disabled', 'skipped' ), true ) ) { $state = 'default'; }
		$config = Settings::get();
		$tokens = self::visual_tokens();

		echo '<!doctype html><html ' . get_language_attributes() . '><head><meta charset="' . esc_attr( get_bloginfo( 'charset' ) ) . '"><meta name="viewport" content="width=device-width,initial-scale=1"><title>' . esc_html__( 'Welcome Intro Preview', SWI_TEXT_DOMAIN ) . '</title>';
		echo self::style_block( $tokens ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '</head><body class="swi-preview-page"><main><h1>' . esc_html__( 'Underlying page remains available', SWI_TEXT_DOMAIN ) . '</h1><p>' . esc_html__( 'This preview does not modify public recurrence or session state.', SWI_TEXT_DOMAIN ) . '</p></main>';

		if ( 'error' === $state ) {
			echo '<div class="swi-preview-note">' . esc_html__( 'Failure preview: the intro is suppressed and content is revealed immediately.', SWI_TEXT_DOMAIN ) . '</div>';
		} elseif ( 'disabled' === $state || 'skipped' === $state ) {
			echo '<div class="swi-preview-note">' . esc_html__( 'Intro suppressed in this preview state.', SWI_TEXT_DOMAIN ) . '</div>';
		} else {
			echo self::markup( $config, true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$data = array( 'version' => absint( $config['config_version'] ), 'durationMs' => 'reduced' === $state ? 800 : absint( $config['duration_ms'] ), 'recurrenceDays' => absint( $config['recurrence_days'] ), 'analyticsEnabled' => false, 'ajaxUrl' => '', 'eventNonce' => '', 'preview' => true, 'forceReducedMotion' => 'reduced' === $state );
			echo '<script>window.SWI_INTRO=' . wp_json_encode( $data ) . ';</script><script src="' . esc_url( SWI_URL . 'assets/js/welcome-intro.js?ver=' . rawurlencode( SWI_VERSION ) ) . '"></script>';
		}
		echo '</body></html>';
		exit;
	}

	private static function markup( array $config, $preview ) {
		$id = $preview ? 'swi-welcome-intro-preview' : 'swi-welcome-intro';
		$html = '<aside id="' . esc_attr( $id ) . '" class="swi-intro" hidden data-swi-version="' . esc_attr( absint( $config['config_version'] ) ) . '" aria-label="' . esc_attr__( 'Welcome to Sabri Homeopathy', SWI_TEXT_DOMAIN ) . '"><div class="swi-intro__panel">';
		$html .= '<div class="swi-intro__logo" aria-hidden="true"><svg viewBox="0 0 72 72" width="72" height="72" focusable="false"><circle cx="36" cy="36" r="32"></circle><text x="36" y="42" text-anchor="middle">SH</text></svg></div>';
		$html .= '<div class="swi-intro__copy" role="status" aria-live="polite" aria-atomic="true"><strong class="swi-intro__heading">' . esc_html( $config['heading'] ) . '</strong><span class="swi-intro__claim">' . esc_html( $config['claim'] ) . '</span><span class="swi-intro__line" aria-hidden="true"></span></div>';
		$html .= '<button type="button" class="swi-intro__skip">' . esc_html__( 'Skip intro', SWI_TEXT_DOMAIN ) . '</button></div></aside>';
		return $html;
	}

	private static function style_block( array $tokens ) {
		$p = esc_attr( $tokens['primary'] ); $d = esc_attr( $tokens['dark'] ); $l = esc_attr( $tokens['light'] );
		return '<style id="swi-intro-critical-css">'
			. '.swi-intro[hidden]{display:none!important}.swi-intro{position:fixed;z-index:2147482000;inset-block-start:1rem;inset-inline:1rem;display:flex;justify-content:center;pointer-events:none;font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;direction:inherit}'
			. '.swi-intro__panel{pointer-events:auto;inline-size:min(520px,calc(100vw - 2rem));box-sizing:border-box;padding:1rem 1.1rem;border:1px solid ' . $l . ';border-radius:14px;background:#fff;color:#171a18;box-shadow:0 14px 40px rgba(0,0,0,.16);display:grid;grid-template-columns:auto 1fr auto;gap:.9rem;align-items:center}'
			. '.swi-intro__logo svg circle{fill:' . $l . ';stroke:' . $p . ';stroke-width:2}.swi-intro__logo svg text{fill:' . $d . ';font-size:18px;font-weight:700}.swi-intro__copy{min-inline-size:0}.swi-intro__heading,.swi-intro__claim{display:block}.swi-intro__heading{font-size:1.08rem;line-height:1.25;color:' . $d . '}.swi-intro__claim{margin-block-start:.22rem;font-size:.9rem;line-height:1.45}.swi-intro__line{display:block;block-size:3px;inline-size:0;margin-block-start:.6rem;border-radius:999px;background:' . $p . ';animation:swi-line var(--swi-duration,3200ms) ease-out forwards}'
			. '.swi-intro__skip{appearance:none;border:1px solid ' . $p . ';background:#fff;color:' . $d . ';border-radius:999px;padding:.55rem .75rem;font:inherit;cursor:pointer;white-space:nowrap}.swi-intro__skip:focus-visible{outline:3px solid ' . $p . ';outline-offset:3px}.swi-intro.swi-intro--enter .swi-intro__panel{animation:swi-enter 220ms ease-out both}.swi-intro.swi-intro--leave .swi-intro__panel{animation:swi-leave 160ms ease-in both}'
			. '@keyframes swi-enter{from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:none}}@keyframes swi-leave{to{opacity:0;transform:translateY(-6px)}}@keyframes swi-line{to{inline-size:100%}}'
			. '@media(max-width:560px){.swi-intro{inset-block-start:.5rem;inset-inline:.5rem}.swi-intro__panel{inline-size:100%;grid-template-columns:auto 1fr;gap:.7rem}.swi-intro__skip{grid-column:1/-1;justify-self:end;min-block-size:44px}.swi-intro__logo svg{width:56px;height:56px}}'
			. '@media(prefers-reduced-motion:reduce){.swi-intro *{animation:none!important;scroll-behavior:auto!important}.swi-intro__line{inline-size:100%}}.swi-preview-page{margin:0;padding:2rem;background:#f7f7f7}.swi-preview-note{margin:1rem;padding:1rem;border:1px solid #d9dde2;background:#fff}[dir="rtl"] .swi-intro{text-align:right}</style>';
	}

	public static function visual_tokens() {
		$fallback = array( 'primary' => '#087a4e', 'dark' => '#065c3b', 'light' => '#e7f5ee' );
		$contract = apply_filters( 'sabri_shell_file25_visual_contract', array() );
		if ( ! is_array( $contract ) || empty( $contract['tokens'] ) || ! is_array( $contract['tokens'] ) ) { return $fallback; }
		$tokens = $contract['tokens'];
		return array(
			'primary' => self::hex( $tokens['primary_color'] ?? '', $fallback['primary'] ),
			'dark' => self::hex( $tokens['primary_dark'] ?? '', $fallback['dark'] ),
			'light' => self::hex( $tokens['primary_light'] ?? '', $fallback['light'] ),
		);
	}

	private static function hex( $value, $fallback ) {
		$value = strtolower( trim( (string) $value ) );
		return preg_match( '/^#[0-9a-f]{6}$/', $value ) ? $value : $fallback;
	}
}
