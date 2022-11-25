<?php

namespace WeDevs\PM\Core\Pro;

/**
 * Class Menu
 *
 * @since 2.5.1
 *
 * @package WeDevs\PM\Core\Pro
 */
class Menu {

    /**
     * Prompts menu capabilities.
     *
     * @since 2.5.1
     *
     * @var string
     */
    private static $capability = 'read';

    /**
     * Register pro prompts menu.
     *
     * @since 2.5.1
     *
     * @param string $slug
     *
     * @return void
     */
    public function admin_menu( $slug ) {
        global $submenu, $wedevs_pm_pro;

        // If pm pro exists then stop pro execution.
        if ( $wedevs_pm_pro ) {
            return;
        }

        if ( pm_has_manage_capability() ) {
            $submenu['pm_projects'][] = array( self::pro_menu_string( 'Woo Project' ), self::$capability, 'admin.php?page=pm_projects#/woo-project' );
        }

        $submenu['pm_projects'][] = array( self::pro_menu_string( 'Calendar' ), self::$capability, 'admin.php?page=pm_projects#/calendar' );
        if ( pm_has_manage_capability() ) {
            $submenu['pm_projects'][] = array( $this->pro_menu_string( 'Progress' ), self::$capability, 'admin.php?page=pm_projects#/progress' );
            $submenu['pm_projects'][] = array( $this->pro_menu_string( 'Reports' ), self::$capability, 'admin.php?page=pm_projects#/reports' );
            $submenu['pm_projects'][] = array( __( 'Modules', 'wedevs-project-manager' ), self::$capability, 'admin.php?page=pm_projects#/modules' );
        }

        add_action( 'admin_print_styles-' . $slug, array( $this, 'scripts' ) );
        wp_enqueue_style( 'pm-badge-styles' );
    }

    /**
     * Get the menu string with pro badge.
     *
     * @since 2.5.1
     *
     * @param string $menu_name
     *
     * @return string
     */
    private function pro_menu_string( $menu_name ) {
        return sprintf(
            __( '%1$s %2$sPro%3$s', 'wedevs-project-manager' ),
            $menu_name,
            '<span class="pm-pro-badge">',
            '</span>'
        );
    }

    /**
     * Get the upgrade to pro url from the PRO Prompts.
     *
     * @since 2.5.1
     *
     * @return string
     */
    public function get_upgrade_to_pro_popup_url() {
        return esc_url( 'https://wedevs.com/wp-project-manager-pro/pricing/?utm_source=freeplugin&utm_medium=pm-action-link&utm_campaign=pm-pro-prompt' );
    }

    /**
     * Enqueue pro prompts related styles.
     *
     * @since 2.5.1
     *
     * @return void
     */
	public function scripts() {
        wp_enqueue_style( 'swiffy-slider' );
        wp_enqueue_style( 'pm-pro-styles' );

        wp_enqueue_script( 'swiffy-slider' );
        wp_enqueue_script( 'swiffy-slider-extension' );
	}
}