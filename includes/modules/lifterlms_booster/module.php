<?php
/**
 * LifterLMS Booster Main Class
 *
 * @package Neve Pro Addon
 */

namespace Neve_Pro\Modules\LifterLMS_Booster;

use Neve_Pro\Core\Abstract_Module;

/**
 * Class Module
 *
 * @package Neve_Pro\Modules\LifterLMS_Booster
 */
class Module extends Abstract_Module {

	/**
	 * Holds the base module namespace
	 * Used to load submodules.
	 *
	 * @var string $module_namespace
	 */
	private $module_namespace = 'Neve_Pro\Modules\LifterLMS_Booster';

	/**
	 * Define module properties.
	 *
	 * @access  public
	 * @return void
	 *
	 * @version 1.0.0
	 */
	public function define_module_properties() {
		$this->slug              = 'lifterlms_booster';
		$this->name              = __( 'LifterLMS Booster', 'neve-pro-addon' );
		$this->description       = __( 'Boost your users learning process with cool new features designed to work smoothly with LifterLMS.', 'neve-pro-addon' );
		$this->dependent_plugins = array(
			'lifterlms' => array(
				'path' => 'lifterlms/lifterlms.php',
				'name' => 'LifterLMS',
			),
		);
		// TODO: Add documentation link
		$this->documentation     = array(
			'url'   => 'https://docs.themeisle.com/article/1084-lifterlms-booster-documentation',
			'label' => __( 'Learn more', 'neve-pro-addon' ),
		);
		$this->order             = 8;
		$this->min_req_license   = 2;
		$this->has_dynamic_style = true;
	}

	/**
	 * Check if module should load.
	 *
	 * @return bool
	 */
	public function should_load() {
		return ( $this->is_active() && class_exists( 'LifterLMS' ) );
	}

	/**
	 * Run LifterLMS Booster Module
	 */
	public function run_module() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'neve_pro_filter_customizer_modules', array( $this, 'add_customizer_classes' ) );
		$submodules = array(
			$this->module_namespace . '\Rest\Server',
			$this->module_namespace . '\Views\Course_Membership',
		);

		$mods = [];
		foreach ( $submodules as $index => $mod ) {
			if ( class_exists( $mod ) ) {
				$mods[ $index ] = new $mod();
				$mods[ $index ]->register_hooks();
			}
		}
	}

	/**
	 * Add customizer classes.
	 *
	 * @param array $classes loaded classes.
	 *
	 * @return array
	 */
	public function add_customizer_classes( $classes ) {
		return array_merge(
			array(
				'Modules\LifterLMS_Booster\Customizer\Course_Membership',
			),
			$classes
		);
	}

	/**
	 * Enqueue scripts and styles.
	 */
	public function enqueue_scripts() {

		global $wp_query;

		$this->rtl_enqueue_style( 'neve-pro-addon-lifter-booster', NEVE_PRO_INCLUDES_URL . 'modules/lifterlms_booster/assets/style.min.css', array(), NEVE_PRO_VERSION );

		wp_register_script( 'neve-pro-addon-lifter-booster', NEVE_PRO_INCLUDES_URL . 'modules/lifterlms_booster/assets/js/build/script.js', array(), NEVE_PRO_VERSION, true );

		global $post;
		if ( is_singular() && has_shortcode( $post->post_content, 'lifterlms_courses' ) ) {
			$wp_query->query = array(
				'post_type' => 'course',
			);
		}

		if ( is_singular() && has_shortcode( $post->post_content, 'lifterlms_memberships' ) ) {
			$wp_query->query = array(
				'post_type' => 'memberships',
			);
		}

		wp_localize_script(
			'neve-pro-addon-lifter-booster',
			'neveLifterBooster',
			array(
				'infiniteCoursesEndpoint'     => rest_url( NEVE_PRO_REST_NAMESPACE . '/courses/page/' ),
				'infiniteMembershipsEndpoint' => rest_url( NEVE_PRO_REST_NAMESPACE . '/memberships/page/' ),
				'infiniteScrollQuery'         => wp_json_encode( $wp_query->query ),
				'nonce'                       => wp_create_nonce( 'wp_rest' ),
			)
		);

		wp_script_add_data( 'neve-pro-addon-lifter-booster', 'async', true );
		wp_enqueue_script( 'neve-pro-addon-lifter-booster' );
	}

}
