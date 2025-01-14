<?php
/**
 * Helper functions for Custom Layouts
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\Custom_Layouts;

use Neve_Pro\Admin\Conditional_Display;
use Neve_Pro\Core\Loader;
use Neve_Pro\Modules\Custom_Layouts\Admin\Inside_Layout;
use Neve_Pro\Modules\Custom_Layouts\Admin\Layouts_Metabox;

trait Utilities {
	/**
	 * Get priority value of the given custom layout post.
	 *
	 * @param  int     $post_id Custom layout post ID.
	 * @param  boolean $is_new Is that a new custom layout that haven't saved yet?.
	 * @return int
	 */
	private static function get_priority( $post_id, $is_new = false ) {
		$priority               = get_post_meta( $post_id, Layouts_Metabox::META_PRIORITY, true );
		$backward_default_value = 1; // backward compatibility for old users.

		if ( empty( $priority ) && $priority !== 0 ) {
			if ( $is_new ) {
				return 10;
			}

			return $backward_default_value;
		}

		return $priority;
	}

	/**
	 * Get the layouts Options.
	 *
	 * @return array
	 */
	public static function get_layouts() {
		$layouts = array(
			'individual'  => __( 'Individual', 'neve-pro-addon' ),
			'header'      => __( 'Header', 'neve-pro-addon' ),
			'inside'      => __( 'Inside content', 'neve-pro-addon' ),
			'footer'      => __( 'Footer', 'neve-pro-addon' ),
			'global'      => __( 'Global', 'neve-pro-addon' ),
			'hooks'       => __( 'Hooks', 'neve-pro-addon' ),
			'not_found'   => __( '404 Page', 'neve-pro-addon' ),
			'single_post' => __( 'Single Post', 'neve-pro-addon' ),
			'single_page' => __( 'Single Page', 'neve-pro-addon' ),
			'search'      => __( 'Search', 'neve-pro-addon' ),
			'archives'    => __( 'Archives', 'neve-pro-addon' ),
		);

		if ( Loader::has_compatibility( 'custom_post_types_sidebar' ) ) {
			$layouts['sidebar'] = __( 'Sidebar', 'neve-pro-addon' );
		}

		if ( defined( 'PWA_VERSION' ) ) {
			$layouts['offline']      = __( 'Offline Page', 'neve-pro-addon' );
			$layouts['server_error'] = __( 'Internal Server Error Page', 'neve-pro-addon' );
		}
		return $layouts;
	}

	/**
	 * Sidebar positions Options.
	 *
	 * @return array
	 */
	private static function get_sidebar_positions() {
		$sidebar_positions = [
			'blog' => __( 'Blog', 'neve-pro-addon' ),
		];
		if ( class_exists( 'LifterLMS', false ) ) {
			$sidebar_positions['lifter_lms'] = 'Lifter LMS';
		}
		if ( class_exists( 'WooCommerce', false ) ) {
			$sidebar_positions['woocommerce'] = 'WooCommerce';
		}
		return $sidebar_positions;
	}

	/**
	 * Sidebar actions Options.
	 *
	 * @return array
	 */
	private static function get_sidebar_actions() {
		return [
			'replace' => __( 'By selecting this option, the whole sidebar will be replaced with the content of this post.', 'neve-pro-addon' ),
			'append'  => __( 'By selecting this option, the content of this post will be added just after the sidebar.', 'neve-pro-addon' ),
			'prepend' => __( 'By selecting this option, the content of this post will be added just before the sidebar.', 'neve-pro-addon' ),
		];
	}

	/**
	 * Inside content Options.
	 *
	 * @return array[]
	 */
	private static function get_inside_positions() {
		return [
			'after' => [
				''                            => __( 'Select', 'neve-pro-addon' ),
				Inside_Layout::AFTER_HEADINGS => __( 'After certain number of headings', 'neve-pro-addon' ),
				Inside_Layout::AFTER_BLOCKS   => __( 'After certain number of blocks', 'neve-pro-addon' ),
			],
		];
	}

	/**
	 * Return all select options for the select controls.
	 * Used by the modal inside Custom Layouts Page.
	 *
	 * @return array
	 */
	public static function get_modal_select_options() {
		$layout           = self::get_layouts();
		$layout_templates = [ 'not_found', 'single_post', 'single_page', 'search', 'archives' ];
		$excluded         = [ 'hooks', 'global' ];

		$templates_filtered = array_filter(
			$layout,
			function ( $key ) use ( $layout_templates ) {
				return in_array( $key, $layout_templates, true );
			},
			ARRAY_FILTER_USE_KEY 
		);

		$components_filtered = array_filter(
			$layout,
			function ( $key ) use ( $layout_templates, $excluded ) {
				return ! in_array( $key, array_merge( $layout_templates, $excluded ), true );
			},
			ARRAY_FILTER_USE_KEY 
		);

		$templates  = array_merge( [ 'none' => __( 'Select', 'neve-pro-addon' ) ], $templates_filtered );
		$components = array_merge( [ 'none' => __( 'Select', 'neve-pro-addon' ) ], $components_filtered );
		$hooks      = array_merge( [ 'none' => __( 'Select a hook', 'neve-pro-addon' ) ], neve_hooks() );

		return [
			'templates'  => $templates,
			'components' => $components,
			'hooks'      => $hooks,
		];
	}

	/**
	 * Return all select options for the select controls.
	 * Used by the sidebar inside Gutenberg.
	 *
	 * @return array
	 */
	public static function get_sidebar_select_options() {
		$layout            = array_merge( [ 'none' => __( 'Select', 'neve-pro-addon' ) ], self::get_layouts() );
		$hooks             = array_merge( [ 'none' => __( 'Select a hook', 'neve-pro-addon' ) ], neve_hooks() );
		$sidebar_positions = self::get_sidebar_positions();
		$sidebar_actions   = array_merge( [ 'none' => __( 'Select an action', 'neve-pro-addon' ) ], self::get_sidebar_actions() );
		$inside_positions  = self::get_inside_positions();

		$conditional_display = new Conditional_Display();

		return [
			'layouts'          => $layout,
			'hooks'            => $hooks,
			'sidebarPositions' => $sidebar_positions,
			'sidebarActions'   => $sidebar_actions,
			'insidePositions'  => $inside_positions,
			'conditions'       => [
				'root' => $conditional_display->get_root_ruleset(),
				'end'  => $conditional_display->get_end_ruleset(),
				'map'  => $conditional_display->get_ruleset_map(),
			],
		];
	}
}
