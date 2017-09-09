<?php

namespace CPM\Core\WP;

class Enqueue_Scripts {

	public static function scripts() {
		$scripts_id = [
			'pm-config',
			'pm-scripts'
		];

		foreach ( $scripts_id as $script_id ) {
			do_action( 'before_loaded' . $script_id );
			
			wp_enqueue_script( $script_id );

			do_action( 'after_loaded' . $script_id );
		}

		wp_localize_script( 'pm-scripts', 'PM_Vars', array(
			'ajaxurl'         => admin_url( 'admin-ajax.php' ),
			'nonce'           => wp_create_nonce( 'cpm_nonce' ),
			'base_url'        => home_url(),
			'rest_api_prefix' => rest_get_url_prefix(),
        ));
	}

	public static function styles() {
		$styles_id = [
			'cpm-jquery-ui',
			'cpm-fontawesome',
			'cpm-nprogress',
			'cpm-style',
		];

		foreach ( $styles_id as $style ) {
			do_action( 'before_loaded' . $style );
			
			wp_enqueue_style( $style );

			do_action( 'after_loaded' . $style );
		}
	}
}