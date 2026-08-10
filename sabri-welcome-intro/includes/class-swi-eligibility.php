<?php

defined( 'ABSPATH' ) || exit;

final class SWI_Eligibility {
	/**
	 * Resolve server-side eligibility. Guest frequency remains client-side so full-page caches do not vary by cookie.
	 *
	 * @param array<string,mixed> $config Normalized config.
	 * @param bool                $preview Authorized preview.
	 * @return array{eligible:bool,reason:string,path:string}
	 */
	public function resolve( array $config, $preview = false ) {
		$decision = array(
			'eligible' => false,
			'reason'   => 'unknown',
			'path'     => $this->request_path(),
		);

		if ( $preview ) {
			$decision['eligible'] = true;
			$decision['reason']   = 'authorized_preview';
			return $this->filtered( $decision, $config );
		}

		if ( empty( $config['enabled'] ) ) {
			$decision['reason'] = 'disabled';
			return $this->filtered( $decision, $config );
		}

		if ( $this->safe_mode_active() ) {
			$decision['reason'] = 'safe_mode';
			return $this->filtered( $decision, $config );
		}

		if ( is_admin() || wp_doing_ajax() || ( function_exists( 'wp_is_json_request' ) && wp_is_json_request() ) ) {
			$decision['reason'] = 'non_visual_request';
			return $this->filtered( $decision, $config );
		}

		if ( is_feed() || is_robots() || is_trackback() || ( function_exists( 'is_embed' ) && is_embed() ) ) {
			$decision['reason'] = 'non_page_request';
			return $this->filtered( $decision, $config );
		}

		if ( $this->outside_schedule( $config ) ) {
			$decision['reason'] = 'outside_schedule';
			return $this->filtered( $decision, $config );
		}

		foreach ( (array) $config['suppressed_prefixes'] as $prefix ) {
			if ( $this->path_matches_prefix( $decision['path'], $prefix ) ) {
				$decision['reason'] = 'suppressed_route';
				return $this->filtered( $decision, $config );
			}
		}

		$allowed = false;
		foreach ( (array) $config['eligible_routes'] as $route ) {
			if ( '*' === $route || $this->path_matches_route( $decision['path'], $route ) ) {
				$allowed = true;
				break;
			}
		}
		if ( ! $allowed ) {
			$decision['reason'] = 'route_not_eligible';
			return $this->filtered( $decision, $config );
		}

		if ( is_user_logged_in() && $this->user_recently_dismissed( get_current_user_id(), $config ) ) {
			$decision['reason'] = 'account_frequency_suppressed';
			return $this->filtered( $decision, $config );
		}

		$decision['eligible'] = true;
		$decision['reason']   = 'eligible';
		return $this->filtered( $decision, $config );
	}

	public function safe_mode_active() {
		$active = defined( 'SWI_DISABLE' ) && SWI_DISABLE;
		$active = $active || ( defined( 'SABRI_PLATFORM_SAFE_MODE' ) && SABRI_PLATFORM_SAFE_MODE );
		$active = (bool) apply_filters( 'swi_force_disabled', $active );
		$active = (bool) apply_filters( 'sabri_platform_safe_mode', $active, 'file-13-welcome-intro' );
		return $active;
	}

	private function outside_schedule( array $config ) {
		$now = time();
		if ( ! empty( $config['starts_at'] ) && strtotime( $config['starts_at'] ) > $now ) {
			return true;
		}
		if ( ! empty( $config['ends_at'] ) && strtotime( $config['ends_at'] ) < $now ) {
			return true;
		}
		return false;
	}

	private function user_recently_dismissed( $user_id, array $config ) {
		$external = apply_filters( 'swi_user_last_seen_timestamp', null, $user_id, $config );
		$last     = null === $external ? get_user_meta( $user_id, SWI_Config::USER_META_LAST, true ) : $external;
		$last     = is_numeric( $last ) ? (int) $last : 0;
		return $last > 0 && ( time() - $last ) < DAY_IN_SECONDS * absint( $config['frequency_days'] );
	}

	private function request_path() {
		$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/';
		$path        = wp_parse_url( $request_uri, PHP_URL_PATH );
		return SWI_Config::normalize_path( $path ?: '/' ) ?: '/';
	}

	private function path_matches_prefix( $path, $prefix ) {
		$prefix = SWI_Config::normalize_path( $prefix );
		if ( '' === $prefix ) {
			return false;
		}
		return $path === $prefix || 0 === strpos( $path . '/', trailingslashit( $prefix ) );
	}

	private function path_matches_route( $path, $route ) {
		$route = SWI_Config::normalize_path( $route );
		return '' !== $route && $path === $route;
	}

	private function filtered( array $decision, array $config ) {
		$filtered = apply_filters( 'swi_eligibility_decision', $decision, $config );
		if ( ! is_array( $filtered ) || ! isset( $filtered['eligible'], $filtered['reason'] ) ) {
			return array( 'eligible' => false, 'reason' => 'invalid_filter_result', 'path' => $decision['path'] );
		}
		$filtered['eligible'] = (bool) $filtered['eligible'];
		$filtered['reason']   = sanitize_key( $filtered['reason'] );
		$filtered['path']     = isset( $filtered['path'] ) ? SWI_Config::normalize_path( $filtered['path'] ) : $decision['path'];

		// Integration filters are restriction-only. A companion plugin may suppress
		// an otherwise eligible intro, but it must never broaden a denied state and
		// bypass the kill switch, Safe Mode, route policy, schedule or frequency.
		if ( empty( $decision['eligible'] ) && ! empty( $filtered['eligible'] ) ) {
			$decision['eligible'] = false;
			$decision['reason']   = sanitize_key( $decision['reason'] );
			$decision['path']     = SWI_Config::normalize_path( $decision['path'] ) ?: '/';
			return $decision;
		}

		return $filtered;
	}
}
