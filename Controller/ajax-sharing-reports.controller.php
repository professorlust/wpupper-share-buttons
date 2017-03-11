<?php
/**
 *
 * @package WPUpper Share Buttons
 * @author  Victor Freitas
 * @subpackage Ajax sharing reports
 * @version 3.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	 // Exit if accessed directly.
	exit(0);
}

class WPUSB_Ajax_Sharing_Reports_Controller {

	private $table;

	private $reference;

	private $post_title;

	private $post_date;

	private $facebook;

	private $twitter;

	private $google;

	private $linkedin;

	private $pinterest;

	private $tumblr;

	private $total;

	/**
	* Initialize the plugin by ajax requests
	*
	* @since 3.30
	*/
	public function __construct() {
		$this->init( WPUSB_App::SLUG );
	}

	/**
	 * Init actions
	 *
	 * @since 3.30
	 * @param String $prefix
	 * @return void
	 */
	public function init( $prefix ) {
		if ( WPUSB_Utils::is_sharing_report_disabled() ) {
			return;
		}

		$action = "{$prefix}_share_count_reports";

		add_action( "wp_ajax_{$action}", array( $this, 'request_init' ) );
		add_action( "wp_ajax_nopriv_{$action}", array( $this, 'request_init' ) );
	}

	/**
	 * Check is valid request and init proccess
	 *
	 * @since 3.30
	 * @param null
	 * @return void
	 */
	public function request_init() {
		$this->_validate();
		$this->_set_fields();
		$this->_insert_init();
	}

	/**
	 * Check is valid request and set reference
	 *
	 * @since 3.30
	 * @param null
	 * @return void
	 */
	private function _validate() {
		if ( ! WPUSB_Utils::is_request_ajax() ) {
			exit(0);
		}

		$nonce = WPUSB_Utils::post( 'nonce', false );

		if ( ! wp_verify_nonce( $nonce, WPUSB_Setting::NONCE_SHARING_REPORT ) ) {
			$this->_send_json( __( 'Not valid request', WPUSB_App::TEXTDOMAIN ) );
		}

		$reference = WPUSB_Utils::post( 'reference', false, 'intval' );

		if ( ! $reference ) {
			$this->_send_json( __( 'Not valid reference', WPUSB_App::TEXTDOMAIN ) );
		}

		$this->reference = $reference;
	}

	/**
	 * Set properties
	 *
	 * @since 3.30
	 * @param null
	 * @return void
	 */
	private function _set_fields() {
		$this->table      = WPUSB_Utils::get_table_name();
		$this->post_date  = WPUSB_Utils::get_post_date( $this->reference );
		$this->post_title = WPUSB_Utils::rm_tags( get_the_title( $this->reference ), true );
		$this->facebook   = WPUSB_Utils::post( 'count_facebook', 0, 'intval' );
		$this->twitter    = WPUSB_Utils::post( 'count_twitter', 0, 'intval' );
		$this->google     = WPUSB_Utils::post( 'count_google', 0, 'intval' );
		$this->linkedin   = WPUSB_Utils::post( 'count_linkedin', 0, 'intval' );
		$this->pinterest  = WPUSB_Utils::post( 'count_pinterest', 0, 'intval' );
		$this->tumblr     = WPUSB_Utils::post( 'count_tumblr', 0, 'intval' );
		$this->total      = ( $this->facebook + $this->twitter + $this->google + $this->linkedin + $this->pinterest + $this->tumblr );
	}

	/**
	 * Retrieve the requests
	 *
	 * @since 3.30
	 * @global $wpdb
	 * @return Void
	 */
	private function _insert_init() {
		global $wpdb;

		if ( $this->total > 0 ) {
			$id = $wpdb->get_var( $wpdb->prepare(
				"SELECT
					`id`
				 FROM
				 	`{$this->table}`
				 WHERE
				 	`post_id` = %d
				",
				$this->reference
			) );
			$this->_add_counts( intval( $id ) );
		}

		$this->_send_json( __( 'Empty counts', WPUSB_App::TEXTDOMAIN ) );
	}

	/**
	 * Select the table and check for records
	 *
	 * @since 3.30
	 * @global $wpdb
	 * @param Integer $id
	 * @return Void
	 */
	private function _add_counts( $id ) {
		global $wpdb;

		if ( $id ) {
			$this->_update();
		}

		$this->_insert();
	}

	/**
	 * Update records in the table
	 *
	 * @since 1.0
	 * @since 3.30
	 * @global $wpdb
	 * @param String $table
	 * @param Array $data
	 * @return Void
	 */
	private function _update() {
		global $wpdb;

		$updated = $wpdb->update(
			$this->table,
			array(
				'post_title' => $this->post_title,
				'post_date'  => $this->post_date,
				'facebook'   => $this->facebook,
				'twitter'    => $this->twitter,
				'google'     => $this->google,
				'linkedin'   => $this->linkedin,
				'pinterest'  => $this->pinterest,
				'tumblr'     => $this->tumblr,
				'total'      => $this->total,
			),
			array(
				'post_id' => $this->reference,
			),
			array(
				'%s',
				'%s',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
			),
			array(
				'%d',
			)
		);

		$this->_send_json( '', $updated );
	}

	/**
	 * Insert records in the table
	 *
	 * @since 1.0
	 * @since 3.30
	 * @global $wpdb
	 * @param String $table
	 * @param Array $data
	 * @return Void
	 */
	private function _insert() {
		global $wpdb;

		$inserted = $wpdb->insert(
			$this->table,
			array(
				'post_id'    => $this->reference,
				'post_title' => $this->post_title,
				'post_date'  => $this->post_date,
				'facebook'   => $this->facebook,
				'twitter'    => $this->twitter,
				'google'     => $this->google,
				'linkedin'   => $this->linkedin,
				'pinterest'  => $this->pinterest,
				'tumblr'     => $this->tumblr,
				'total'      => $this->total,
			),
			array(
				'%d',
				'%s',
				'%s',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
				'%d',
			)
		);

		$this->_send_json( '', $inserted );
	}

	/**
	 * Send json request error
	 *
	 * @since 3.30
	 * @param String $message
	 * @param Boolean $success
	 * @return Void
	 */
	private function _send_json( $message, $success = false ) {
		wp_send_json( array( 'success' => (bool)$success, 'message' => $message ) );
	}
}