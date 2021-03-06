<?php
/**
 * File for spending tracker actions
 *
 * @author Alexandre Techer <me@alexandretecher.fr>
 * @since 1.0
 * @package Spending_Tracker
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class for spending tracker actions
 *
 * @author Alexandre Techer <me@alexandretecher.fr>
 * @since 1.0
 */
class Spending_Tracker_Actions {

	/**
	 * The single instance of the class.
	 *
	 * @var Spending_Tracker_Actions
	 * @since 1.0
	 */
	protected static $_instance = null;

	/**
	 * Spending tracker actions main initialisation
	 */
	public function __construct() {
		/** Call wordpress hook for backend styles and scripts definition */
		add_action( 'admin_enqueue_scripts', array( $this, 'backend_assets' ) );

		/** Call wordpress hook for adding our own menu in administration */
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	/**
	 * Main Spending Tracker Actions Instance.
	 *
	 * Ensures only one instance of Spending Tracker Actions is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 *
	 * @return Spending Tracker Actions - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Call wordpress hook for adding scripts and styles for backend
	 */
	public function backend_assets() {
		/**
		 * Scripts
		 */
		wp_enqueue_script( 'atst-backend-js', SPENDTRACK_URL . 'assets/js/backend.js', array( 'jquery-ui-dialog', 'jquery-ui-button', 'jquery-ui-datepicker', 'jquery-form' ), Spending_Tracker::instance()->version );
		/**
		 * Localize scripts texts
		 */
		$translation_array = array(
			'dialog_title'						=> __( 'New transaction', 'spending_tracker' ),
			'save_call_button_text'		=> __( 'Save', 'spending_tracker' ),
			'cancel_call_button_text' => __( 'Cancel', 'spending_tracker' ),
		);
		wp_localize_script( 'atst-backend-js', 'spending_tracker', $translation_array );

		/**
		 * Styles
		 */
		// wp_enqueue_style( 'atst-jquery-datepicker-css', SPENDTRACK_URL . 'assets/css/jquery-ui.min.css', array(), Spending_Tracker::instance()->version );
		wp_enqueue_style( 'atst-backend-css', SPENDTRACK_URL . 'assets/css/backend.css', array( 'wp-jquery-ui-dialog' ), Spending_Tracker::instance()->version );
		// wp_enqueue_style( 'jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' );
	}

	/**
	 * Define the administration menu for spending tracking
	 */
	public function admin_menu() {
		add_menu_page( __( 'Spending tracker', 'spending_tracker' ), __( 'Spending tracker', 'spending_tracker' ), 'manage_options', 'spending-tracker-dashboard', array( $this, 'display_dashboard' ), 'dashicons-money' );
		add_submenu_page( 'spending-tracker-dashboard', __( 'Spending tracker dashboard', 'spending_tracker' ), __( 'Dashboard', 'spending_tracker' ), 'manage_options', 'spending-tracker-dashboard' );
	}

	/**
	 * Create administration dashboard for spending tracking
	 */
	public function display_dashboard() {
		/** Get current screen in order to attach metaboxes to the right place */
		$screen = get_current_screen();

		// add_meta_box( $id, $title, $callback, $screen = null, $context = 'advanced', $priority = 'default', $callback_args = null );!
		add_meta_box( 'atst-dashboard-transaction-form', __( 'New transaction', 'spending_tracker' ), array( Spending_Tracker_Transactions::instance(), 'display_transaction_form' ), $screen, 'atst-normal' );
		add_meta_box( 'atst-dashboard-transaction-coming', __( 'Coming transaction', 'spending_tracker' ), array( Spending_Tracker_Transactions::instance(), 'display_next_transaction' ), $screen, 'atst-normal', 'default', array( 'transaction_type' => 'next' ) );
		add_meta_box( 'atst-dashboard-transaction-last', __( 'Last transaction', 'spending_tracker' ), array( Spending_Tracker_Transactions::instance(), 'display_last_transaction' ), $screen, 'atst-normal', 'default', array( 'transaction_type' => 'last' ) );

		/** Call the dashboard display */
		AT_Display_Utils::get_template_part( SPENDTRACK_DIR, SPENDTRACK_PATH . 'templates', '', 'dashboard' );
	}

}
