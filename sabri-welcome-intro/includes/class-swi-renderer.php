<?php

defined( 'ABSPATH' ) || exit;

final class SWI_Renderer {
	/** @var SWI_Eligibility */ private $eligibility;
	/** @var bool */ private $rendered = false;
	/** @var bool|null */ private $authorized_preview = null;
	/** @var bool|null */ private $authorized_replay = null;
	/** @var string */ private $preview_state = 'default';
	/** @var string */ private $preview_variant = 'full';
	/** @var string */ private $preview_profile = 'auto';
	/** @var array<string,mixed>|null */ private $config = null;
	/** @var array<string,mixed>|null */ private $decision = null;

	public function __construct( SWI_Eligibility $eligibility ) { $this->eligibility = $eligibility; }
	public function hooks() {
		add_filter( 'query_vars', array( $this, 'register_query_var' ) );
		add_action( 'init', array( $this, 'register_preview_route' ), 0 );
		add_action( 'init', array( $this, 'prepare_special_request' ), 1 );
		add_action( 'template_redirect', array( $this, 'redirect_unauthorized_preview' ), 0 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ), 1 );
		add_action( 'wp_body_open', array( $this, 'render' ), 0 );
		add_action( 'wp_footer', array( $this, 'render' ), 0 );
	}
	public function register_query_var( $vars ) { $vars[] = 'swi_preview_route'; return $vars; }
	public function register_preview_route() { add_rewrite_rule( '^welcome-intro-preview/?$', 'index.php?swi_preview_route=1', 'top' ); }

	public function prepare_special_request() {
		if ( ! $this->is_authorized_preview() && ! $this->is_authorized_replay() ) { return; }
		if ( ! defined( 'DONOTCACHEPAGE' ) ) { define( 'DONOTCACHEPAGE', true ); }
		add_filter( 'wp_robots', static function ( $robots ) { $robots['noindex'] = true; $robots['noarchive'] = true; return $robots; } );
	}

	public function redirect_unauthorized_preview() {
		if ( ! $this->preview_requested() || $this->is_authorized_preview() ) {
			if ( $this->is_authorized_preview() || $this->is_authorized_replay() ) { nocache_headers(); header( 'X-Robots-Tag: noindex, noarchive, nofollow', true ); }
			return;
		}
		$route_preview = function_exists( 'get_query_var' ) && '1' === (string) get_query_var( 'swi_preview_route' );
		$clean_url = $route_preview ? home_url( '/' ) : remove_query_arg( array( 'swi_preview', 'swi_state', 'swi_variant', 'swi_profile', '_wpnonce' ) );
		wp_safe_redirect( $clean_url, 302, 'Sabri Welcome Intro' ); exit;
	}

	public function enqueue_assets() {
		if ( ! $this->should_load() ) { return; }
		wp_enqueue_style( 'sabri-welcome-intro', SWI_URL . 'assets/css/welcome-intro.css', array(), SWI_VERSION );
		wp_enqueue_script( 'sabri-welcome-intro', SWI_URL . 'assets/js/welcome-intro.js', array(), SWI_VERSION, true );
		wp_script_add_data( 'sabri-welcome-intro', 'strategy', 'defer' );
	}

	public function render() {
		if ( $this->rendered || ! $this->should_load() ) { return; }
		$this->rendered = true;
		$config = $this->get_config(); $preview = $this->is_authorized_preview(); $replay = $this->is_authorized_replay();
		$state = $preview ? $this->preview_state : 'default'; $rest_url = rest_url( SWI_REST::NAMESPACE ); $direction = is_rtl() ? 'rtl' : 'ltr';
		$copy = SWI_Experience::localized_copy( $config ); $user_id = is_user_logged_in() ? get_current_user_id() : 0; $prefs = SWI_Experience::account_preferences( $user_id, $config );
		$profile = $preview ? $this->preview_profile : (string) $prefs['profile']; $variant = $preview ? $this->preview_variant : 'full';
		?>
		<aside id="swi-intro" class="swi-intro" role="dialog" aria-modal="true" aria-labelledby="swi-brand-name" aria-describedby="swi-brand-claim swi-screenreader-note" aria-live="polite" tabindex="-1" hidden
			dir="<?php echo esc_attr( $direction ); ?>"
			data-swi-css-check="1"
			data-duration="<?php echo esc_attr( $config['duration_ms'] ); ?>"
			data-reduced-duration="<?php echo esc_attr( $config['reduced_duration_ms'] ); ?>"
			data-frequency-days="<?php echo esc_attr( $config['frequency_days'] ); ?>"
			data-config-version="<?php echo esc_attr( $config['config_version'] ); ?>"
			data-experience-version="<?php echo esc_attr( $config['experience_version'] ); ?>"
			data-cookie-name="<?php echo esc_attr( SWI_Config::COOKIE_NAME ); ?>"
			data-session-key="<?php echo esc_attr( SWI_Config::SESSION_KEY ); ?>"
			data-local-key="<?php echo esc_attr( SWI_Config::LOCAL_KEY ); ?>"
			data-claim-key="<?php echo esc_attr( SWI_Config::CLAIM_KEY ); ?>"
			data-never-key="<?php echo esc_attr( SWI_Config::NEVER_KEY ); ?>"
			data-profile-key="<?php echo esc_attr( SWI_Config::PROFILE_KEY ); ?>"
			data-preview="<?php echo esc_attr( $preview ? '1' : '0' ); ?>"
			data-preview-state="<?php echo esc_attr( $state ); ?>"
			data-preview-variant="<?php echo esc_attr( $variant ); ?>"
			data-replay="<?php echo esc_attr( $replay ? '1' : '0' ); ?>"
			data-rest-url="<?php echo esc_url( $rest_url ); ?>"
			data-rest-nonce="<?php echo esc_attr( is_user_logged_in() ? wp_create_nonce( 'wp_rest' ) : '' ); ?>"
			data-event-nonce="<?php echo esc_attr( empty( $config['analytics_enabled'] ) ? '' : wp_create_nonce( 'swi_public_event' ) ); ?>"
			data-analytics="<?php echo esc_attr( empty( $config['analytics_enabled'] ) ? '0' : '1' ); ?>"
			data-account-authoritative="<?php echo esc_attr( is_user_logged_in() ? '1' : '0' ); ?>"
			data-user-id="<?php echo esc_attr( $user_id ); ?>"
			data-account-seen-at="<?php echo esc_attr( (int) $prefs['last_seen'] ); ?>"
			data-account-never-show="<?php echo esc_attr( ! empty( $prefs['never_show'] ) ? '1' : '0' ); ?>"
			data-account-experience="<?php echo esc_attr( (string) $prefs['experience_version'] ); ?>"
			data-accessibility-profile="<?php echo esc_attr( SWI_Experience::sanitize_profile( $profile ) ); ?>"
			data-adaptive="<?php echo esc_attr( empty( $config['adaptive_mode'] ) ? '0' : '1' ); ?>"
			data-never-show-enabled="<?php echo esc_attr( empty( $config['never_show_enabled'] ) ? '0' : '1' ); ?>"
			data-replay-enabled="<?php echo esc_attr( empty( $config['replay_enabled'] ) ? '0' : '1' ); ?>"
			data-version-replay="<?php echo esc_attr( empty( $config['version_replay_enabled'] ) ? '0' : '1' ); ?>"
			data-guest-reconcile="<?php echo esc_attr( empty( $config['guest_reconcile_enabled'] ) ? '0' : '1' ); ?>"
			data-instant-exit="<?php echo esc_attr( empty( $config['instant_exit_enabled'] ) ? '0' : '1' ); ?>"
			data-data-saver-static="<?php echo esc_attr( empty( $config['data_saver_static'] ) ? '0' : '1' ); ?>"
			data-performance-circuit="<?php echo esc_attr( empty( $config['performance_circuit_breaker'] ) ? '0' : '1' ); ?>"
			data-performance-budget="<?php echo esc_attr( $config['performance_budget_ms'] ); ?>">
			<div class="swi-ambient" aria-hidden="true"></div>
			<div class="swi-controls"><button class="swi-icon-button" type="button" data-swi-close aria-label="<?php esc_attr_e( 'Close welcome intro', 'sabri-welcome-intro' ); ?>"><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M6.4 5 12 10.6 17.6 5 19 6.4 13.4 12 19 17.6 17.6 19 12 13.4 6.4 19 5 17.6 10.6 12 5 6.4z"/></svg></button></div>
			<div class="swi-content">
				<img class="swi-logo" src="<?php echo esc_url( SWI_URL . 'assets/images/sabri-sh-logo.svg' ); ?>" width="160" height="160" alt="<?php esc_attr_e( 'Sabri circular SH logo', 'sabri-welcome-intro' ); ?>" decoding="async">
				<p id="swi-brand-name" class="swi-brand-name" lang="<?php echo esc_attr( $copy['language'] ); ?>" dir="auto"><?php echo esc_html( $copy['name'] ); ?></p>
				<p id="swi-brand-claim" class="swi-brand-claim" lang="<?php echo esc_attr( $copy['language'] ); ?>" dir="auto"><?php echo esc_html( $copy['claim'] ); ?></p>
				<div class="swi-line-track" aria-hidden="true"><span class="swi-line"></span></div>
				<p id="swi-screenreader-note" class="swi-visually-hidden"><?php esc_html_e( 'Welcome. Continue to enter the platform or skip this introduction. Press Escape to close.', 'sabri-welcome-intro' ); ?></p>
				<div class="swi-actions">
					<button class="swi-button swi-button-primary" type="button" data-swi-continue><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 11h10.2l-4.1-4.1L12.5 5l7.5 7-7.5 7-1.4-1.9 4.1-4.1H5z"/></svg><span><?php esc_html_e( 'Continue to Platform', 'sabri-welcome-intro' ); ?></span></button>
					<button class="swi-button swi-button-secondary" type="button" data-swi-skip><svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M5 5h2v14H5zm4 7 10-7v14z"/></svg><span><?php esc_html_e( 'Skip Intro', 'sabri-welcome-intro' ); ?></span></button>
					<?php if ( ! empty( $config['never_show_enabled'] ) ) : ?><button class="swi-button swi-button-tertiary" type="button" data-swi-never><span><?php esc_html_e( 'Never Show Again', 'sabri-welcome-intro' ); ?></span></button><?php endif; ?>
				</div>
			</div>
		</aside>
		<noscript><style>#swi-intro{display:none!important}</style></noscript>
		<?php
	}

	/** @return array<string,mixed> */ public function decision() { $this->should_load(); return is_array( $this->decision ) ? $this->decision : array( 'eligible' => false, 'reason' => 'not_resolved', 'path' => '/' ); }
	private function should_load() {
		if ( null === $this->decision ) { $this->decision = $this->eligibility->resolve( $this->get_config(), $this->is_authorized_preview(), $this->is_authorized_replay() ); }
		if ( $this->is_authorized_preview() ) { return true; }
		return ! empty( $this->decision['eligible'] );
	}
	/** @return array<string,mixed> */ private function get_config() { if ( null === $this->config ) { $this->config = SWI_Config::get(); } return $this->config; }
	private function preview_requested() { $route_preview = function_exists( 'get_query_var' ) && '1' === (string) get_query_var( 'swi_preview_route' ); $query_preview = isset( $_GET['swi_preview'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['swi_preview'] ) ); return $route_preview || $query_preview; }
	private function is_authorized_preview() {
		if ( null !== $this->authorized_preview ) { return $this->authorized_preview; } $this->authorized_preview = false;
		if ( ! $this->preview_requested() || ! is_user_logged_in() || ! swi_current_user_can_manage() ) { return false; }
		$nonce = isset( $_GET['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ) : ''; $this->authorized_preview = (bool) wp_verify_nonce( $nonce, 'swi_preview' );
		$state = isset( $_GET['swi_state'] ) ? sanitize_key( wp_unslash( $_GET['swi_state'] ) ) : 'default'; $allowed_states = array( 'default','reduced','skipped','disabled','error','data-saver','offline' ); $this->preview_state = in_array( $state, $allowed_states, true ) ? $state : 'default';
		$this->preview_variant = SWI_Experience::sanitize_variant( isset( $_GET['swi_variant'] ) ? wp_unslash( $_GET['swi_variant'] ) : 'full' );
		$this->preview_profile = SWI_Experience::sanitize_profile( isset( $_GET['swi_profile'] ) ? wp_unslash( $_GET['swi_profile'] ) : 'auto' );
		return $this->authorized_preview;
	}
	private function is_authorized_replay() {
		if ( null !== $this->authorized_replay ) { return $this->authorized_replay; } $this->authorized_replay = false; $config = $this->get_config();
		if ( empty( $config['replay_enabled'] ) || ! isset( $_GET['swi_replay'] ) || '1' !== sanitize_text_field( wp_unslash( $_GET['swi_replay'] ) ) ) { return false; }
		$nonce = isset( $_GET['_swi_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_swi_nonce'] ) ) : ''; $this->authorized_replay = (bool) wp_verify_nonce( $nonce, 'swi_public_replay' ); return $this->authorized_replay;
	}
}
