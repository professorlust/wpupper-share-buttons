<?php
/**
 *
 * @author  Victor Freitas
 * @package WPUpper Share Buttons
 * @subpackage Social Icons Display
 * @since 3.0.0
 * @version 1.0
 */

if ( ! function_exists( 'add_action' ) )
	exit(0);

use WPUSB_Utils as Utils;
use WPUSB_Setting as Setting;
use WPUSB_Social_Elements as Elements;
use WPUSB_App as App;

abstract class WPUSB_Utils_Share
{
	public static $count_elements = 0;
	public static $layout_fixed = 'position_fixed';
	public static $social_media = array();

	/**
	 * Set buttons args
	 *
	 * @since 3.0.0
	 * @param object $social
	 * @param array $args
	 * @return String HTML
	 *
	 */
	public static function set_buttons_args( $social, $args, $permalink, $title )
	{
		$social  = self::replace_link( $social, $permalink, $title );
		$buttons = self::_set_buttons(array(
			'social'       => $social,
			'class_second' => $args['class_second'],
			'class_icon'   => $args['class_icon'],
			'class_link'   => $args['class_link'],
			'layout'       => $args['layout'],
			'elements'     => $args['elements'],
		));

		return $buttons;
	}

	public static function replace_link( $social, $permalink, $title )
	{
		$item         = (array) $social;
		$search       = array( '_permalink_', '_title_' );
		$replace      = array( $permalink, $title );
		$item['link'] = str_replace( $search, $replace, $social->link );

		return (object) $item;
	}

	/**
	 * Set and verify buttons
	 *
	 * @since 3.0.0
	 * @param Array $args
	 * @return String HTML
	 *
	 */
	private static function _set_buttons( $args = array() )
	{
		$prefix  = Setting::PREFIX;
		$buttons = self::get_content_by_layout(
			(object) array(
				'reference'    => $args['social'],
				'prefix'       => $prefix,
				'class_second' => $args['class_second'],
				'class_icon'   => $args['class_icon'],
				'class_link'   => $args['class_link'],
				'layout'       => $args['layout'],
				'elements'     => $args['elements'],
				'share_full'   => '',
			),
			'items'
		);

		return apply_filters( App::SLUG . 'set-buttons-html', $buttons );
	}

	/**
	 * Get buttons
	 *
	 * @since 3.7.0
	 * @version 2.0.0
	 * @param array $args
	 * @return String
	 */
	public static function get_buttons( $args = array(), $fixed = false )
	{
		$model            = new Setting();
		$args             = apply_filters( App::SLUG . 'buttons-args', $args );
		$args['layout']   = self::get_layout( $args, $model->layout, $fixed );
		$args['is_fixed'] = $fixed;
		$buttons          = self::get_buttons_open( $args, $model );
		$permalink        = Utils::get_real_permalink( $fixed );
		$title            = Utils::get_real_title( $fixed );
		$elements         = Elements::social_media();
		$social_items     = self::get_social_media( $model, $args['items'] );

		foreach ( $social_items as $item ) :
			if ( ! Elements::items_available( $item ) ) {
				continue;
			}

			self::$count_elements++;
			$buttons .= self::set_buttons_args( $elements->{$item}, $args, $permalink, $title );
		endforeach;

		self::$count_elements = 0;

		$buttons .= self::get_content_by_layout( (object) $args, 'end' );

		return $buttons;
	}

	/**
	 * Get social media items active
	 *
	 * @since 3.7.0
	 * @version 1.1.0
	 * @param Object Model | false $model
	 * @param Mixed Array|String $items
	 * @return Array
	 */
	public static function get_social_media( $model = false, $items = '' )
	{
		if ( ! empty( $items ) )
			return self::get_selected_items( $items );

		if ( ! $model )
			$model = new Setting();

		$social_media = $model->social_media;

		if ( isset( $social_media['order'] ) )
			unset( $social_media['order'] );

		return (array) $social_media;
	}

	/**
	 * Get social media items active by the user
	 *
	 * @since 3.7.0
	 * @version 1.0.0
	 * @param Mixed Array|String $items
	 * @return Array
	 */
	public static function get_selected_items( $items )
	{
		if ( is_array( $items ) )
			return $items;

		$items = preg_replace( '/[^a-z,]+/', '', strtolower( $items ) );

		return explode( ',', $items );
	}

	/**
	 * Set social media items active
	 *
	 * @since 3.2.2
	 * @version 1.0.0
	 * @param array $social_media
	 * @return Void
	 */
	private static function _set_social_media( $social_media )
	{
		if ( isset( $social_media['order'] ) )
			unset( $social_media['order'] );

		self::$social_media = (array) $social_media;
	}

	public static function get_layout( $args, $layout, $fixed )
	{
		if ( $fixed )
			return self::$layout_fixed;

		return ( ! empty( $args['layout'] ) ) ? $args['layout'] : $layout;
	}

	/**
	 * Get buttons open
	 *
	 * @since 3.0.0
	 * @param Array $args
	 * @param object $model
	 * @return String HTML
	 *
	 */
	public static function get_buttons_open( $args, $model )
	{
		$prefix  = Setting::PREFIX;
		$buttons = self::get_content_by_layout(
			(object) array(
				'class_first'    => $args['class_first'],
				'custom_class'   => $model->class,
				'layout'         => $args['layout'] ? $args['layout'] : 'default',
				'prefix'         => $prefix,
				'position_fixed' => ( $model->position_fixed ) ? "{$prefix}-{$model->position_fixed}" : '',
				'remove_counter' => $args['elements']['remove_counter'],
			),
			'init'
		);

		return $buttons;
	}

	/**
	 * Create nonce from ajax counter share
	 *
	 * @since 3.0.0
	 * @param String $action
	 * @return String
	 *
	 */
	public static function nonce( $action )
	{
		$nonce = wp_create_nonce( $action );

		return Utils::rip_tags( $nonce );
	}

	/**
	 * Arguments for content buttons start
	 *
	 * @since 3.0.0
	 * @param empty
	 * @return Array
	 *
	 */
	public static function content_args()
	{
		$prefix = Setting::PREFIX;

		return array(
			'nonce'       => self::nonce( Setting::AJAX_VERIFY_NONCE_COUNTER ),
			'nonce-gplus' => self::nonce( Setting::AJAX_VERIFY_GPLUS_COUNTS ),
			'token'       => Utils::option( 'bitly_token' ),
			'prefix'      => $prefix,
			'post_id'     => Utils::get_id(),
			'tracking'    => Utils::option( 'tracking' ),
			'permalink'   => Utils::get_permalink(),
			'fixed_top'   => self::data_fixed_top( $prefix ),
		);
	}

	/**
	 * Get data element fixed top
	 *
	 * @since 3.0.0
	 * @param String $prefix
	 * @return String
	 *
	 */
	public static function data_fixed_top( $prefix )
	{
		$element = '';

		if ( Utils::is_fixed_top() )
			$element = "data-element=\"{$prefix}-fixed-top\"";

		return $element;
	}

	/**
	 * Get data token short url
	 *
	 * @since 3.0.0
	 * @param String $token
	 * @return String
	 *
	 */
	public static function get_data_token( $token )
	{
		if ( empty( $token ) )
			return '';

		return "data-token=\"{$token}\"";
	}

	/**
	 * Verfy type and return links from icons
	 *
	 * @since 3.0.0
	 * @param String $url_share
	 * @return String
	 */
	public static function link_type( $url_share )
	{
		$attr_link = "href=\"{$url_share}\" target=\"_blank\"";

		return apply_filters( App::SLUG . 'attr-link', $attr_link );
	}

	/**
	 * Get classes for container
	 *
	 * @since 3.0.0
	 * @param Object $atts
	 * @return String
	 *
	 */
	public static function get_classes_first( $atts )
	{
		$classes  = $atts->prefix;
		$classes .= " {$atts->prefix}-{$atts->layout}";
		$classes .= " {$atts->class_first} {$atts->custom_class}";

		return $classes;
	}

	/**
	 * Verify is active counters
	 *
	 * @since 3.0.0
	 * @param Array $atts
	 * @return Boolean
	 *
	 */
	public static function is_active_couter( $atts )
	{
		$args         = (object) $atts;
		$atts_counter = $args->remove_counter;
		$opt_counter  = Utils::option( 'disabled_count', false, 'intval' );

		return ! ( ( '' !== $atts_counter ) ? $atts_counter : $opt_counter );
	}

	/**
	 * Verify is active title
	 *
	 * @since 3.0.0
	 * @param Array $atts
	 * @return Boolen
	 *
	 */
	public static function is_active_inside( $atts )
	{
		$atts_inside = $atts['remove_inside'];
		$opt_inside  = Utils::option( 'disabled_inside', false, 'intval' );

		return ! ( ( '' !== $atts_inside ) ? $atts_inside : $opt_inside );
	}

	/**
	 * Get container button by layout
	 *
	 * @since 3.0.0
	 * @param Array $args
	 * @param String $method
	 * @return String
	 *
	 */
	public static function get_content_by_layout( $args, $method )
	{
		$layouts = self::get_layouts();
		$content = '';

		switch( $args->layout ) :
			case $layouts['plus'] :
				$content = WPUSB_Square_Plus::$method( $args );
				break;

			case $layouts['default'] :
			case $layouts['buttons'] :
			case $layouts['rounded'] :
			case $layouts['square'] :
				$content = WPUSB_Layouts_Primary::$method( $args );
				break;

			case $layouts['fixed'] :
				$content = WPUSB_Fixed_Left::$method( $args );
				break;
		endswitch;

		return $content;
	}

	/**
	 * Set all layouts
	 *
	 * @since 3.0.0
	 * @param Null
	 * @return Array
	 *
	 */
	public static function get_layouts()
	{
		return array(
			'default' => 'default',
			'buttons' => 'buttons',
			'rounded' => 'rounded',
			'square'  => 'square',
			'plus'    => 'square-plus',
			'fixed'   => self::$layout_fixed,
		);
	}

	/**
	 * Verifies is first item
	 *
	 * @since 3.0.0
	 * @param Null
	 * @return Boolean
	 *
	 */
	public static function is_first()
	{
		if ( self::$count_elements > 1 )
			return false;

		return true;
	}

	/**
	 * Verifies is position fixed
	 *
	 * @since 3.0.0
	 * @param Null
	 * @return Boolean
	 *
	 */
	public static function is_position_fixed()
	{
		return ( 'on' === Utils::option( 'fixed' ) );
	}

	/**
	 * Add data featured by referrer
	 *
	 * @since 3.0.0
	 * @param Object $atts
	 * @return String
	 *
	 */
	public static function get_data_referrer( $atts )
	{
		$opt_referrer = Utils::option( 'referrer', false );

		if ( $opt_referrer === 'yes' ) {
			$attr  = "data-referrer=\"{$atts->reference->element}\"";
			$attr .= " data-ref-title=\"{$atts->reference->inside}\"";

			return $attr;
		}
	}

	/**
	 * Create custom class from icons
	 *
	 * @since 1.0
	 * @param Array $atts
	 * @param Boolean $fixed
	 * @return String
	 */
	public static function buttons_share( $atts = array(), $fixed = false )
	{
		$args = array(
			'class_first'  => Utils::isset_get( $atts, 'class_first' ),
			'class_second' => Utils::isset_get( $atts, 'class_second' ),
			'class_link'   => Utils::isset_get( $atts, 'class_link' ),
			'class_icon'   => Utils::isset_get( $atts, 'class_icon' ),
			'layout'       => Utils::isset_get( $atts, 'layout' ),
			'items'        => Utils::isset_get( $atts, 'items' ),
			'elements'     => array(
				'remove_inside'  => Utils::isset_get( $atts, 'remove_inside' ),
				'remove_counter' => Utils::isset_get( $atts, 'remove_counter' ),
			),
		);

		$args = self::sanitize_atts( $args );

		return self::get_buttons( $args, $fixed );
	}

	/**
	 * Sanitize values atts
	 *
	 * @since 1.0
	 * @param Array $atts
	 * @return Array
	 */
	public static function sanitize_atts( $atts = array() )
	{
		return array(
			'class_first'  => Utils::esc_class( $atts['class_first'] ),
			'class_second' => Utils::esc_class( $atts['class_second'] ),
			'class_link'   => Utils::esc_class( $atts['class_link'] ),
			'class_icon'   => Utils::esc_class( $atts['class_icon'] ),
			'layout'       => Utils::rip_tags( $atts['layout'] ),
			'items'        => Utils::rip_tags( $atts['items'] ),
			'elements'     => array(
				'remove_inside'  => self::get_remove_type( $atts, 'remove_inside' ),
				'remove_counter' => self::get_remove_type( $atts, 'remove_counter' ),
			),
		);
	}

	/**
	 * Verify index elements remove inside and counter
	 *
	 * @since 1.0
	 * @param Array $atts
	 * @param String $type
	 * @return String
	 */
	public static function get_remove_type( $atts, $type )
	{
		if ( isset( $atts['elements'] ) )
			return $atts['elements'][$type];

		return $atts[$type];
	}
}