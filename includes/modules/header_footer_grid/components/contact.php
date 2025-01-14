<?php
/**
 * Button Component class for Header Footer Grid.
 *
 * Name:    Header Footer Grid
 * Author:  Bogdan Preda <bogdan.preda@themeisle.com>
 *
 * @version 1.0.0
 * @package HFG
 */

namespace Neve_Pro\Modules\Header_Footer_Grid\Components;

use HFG\Core\Components\Abstract_Component;
use HFG\Core\Settings\Manager as SettingsManager;
use HFG\Main;
use Neve_Pro\Core\Loader;

/**
 * Class Contact
 *
 * @package Neve_Pro\Modules\Header_Footer_Grid\Components
 */
class Contact extends Abstract_Component {
	const COMPONENT_ID  = 'contact';
	const REPEATER_ID   = 'content_setting';
	const ICON_POSITION = 'icon_position';
	const ITEM_SPACING  = 'item_spacing';
	const ICON_COLOR    = 'icon_color';
	const TEXT_COLOR    = 'text_color';

	/**
	 * Repeater defaults
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var array
	 */
	private $repeater_default = array(
		array(
			'title'      => 'email@example.com',
			'icon'       => 'envelope',
			'item_type'  => 'email',
			'visibility' => 'yes',
		),
		array(
			'title'      => '202-555-0191',
			'icon'       => 'phone',
			'item_type'  => 'phone',
			'visibility' => 'yes',
		),
		array(
			'title'      => '499 Pirate Island Plaza',
			'icon'       => 'map-marker',
			'item_type'  => 'text',
			'visibility' => 'yes',
		),
	);

	/**
	 * Typography control default values.
	 *
	 * @var array
	 */
	protected $typography_default = array(
		'fontSize'      => array(
			'suffix'  => array(
				'mobile'  => 'em',
				'tablet'  => 'em',
				'desktop' => 'em',
			),
			'mobile'  => 0.85,
			'tablet'  => 0.85,
			'desktop' => 0.85,
		),
		'lineHeight'    => array(
			'mobile'  => 1.6,
			'tablet'  => 1.6,
			'desktop' => 1.6,
		),
		'letterSpacing' => array(
			'mobile'  => 0,
			'tablet'  => 0,
			'desktop' => 0,
		),
		'fontWeight'    => '300',
		'textTransform' => 'none',
	);

	/**
	 * Button constructor.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function init() {
		$this->set_property( 'label', __( 'Contact', 'neve-pro-addon' ) );
		$this->set_property( 'id', self::COMPONENT_ID );
		$this->set_property( 'width', 6 );
		$this->set_property( 'section', 'contact' );
		$this->set_property( 'icon', 'email' );
		$this->set_property( 'has_typeface_control', true );
		$this->set_property( 'default_typography_selector', '.builder-item--' . $this->get_id() );
	}

	/**
	 * Called to register component controls.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function add_settings() {

		SettingsManager::get_instance()->add(
			array(
				'id'                => self::REPEATER_ID,
				'group'             => self::COMPONENT_ID,
				'tab'               => SettingsManager::TAB_GENERAL,
				'transport'         => 'post' . self::COMPONENT_ID,
				'sanitize_callback' => array( $this, 'sanitize_repeater_inputs' ),
				'default'           => wp_json_encode( $this->repeater_default ),
				'label'             => __( 'Content', 'neve-pro-addon' ),
				'type'              => Loader::has_compatibility( 'repeater_control' ) ? '\Neve\Customizer\Controls\React\Repeater' : 'Neve_Pro\Customizer\Controls\Repeater',
				'options'           => array(
					'fields' => array(
						'title'     => array(
							'type'  => 'text',
							'label' => __( 'Text', 'neve-pro-addon' ),
						),
						'icon'      => array(
							'type'  => 'icon',
							'label' => __( 'Icon', 'neve-pro-addon' ),
						),
						'item_type' => array(
							'type'    => 'select',
							'label'   => __( 'Type', 'neve-pro-addon' ),
							'choices' => array(
								'text'  => __( 'Text', 'neve-pro-addon' ),
								'email' => __( 'Email', 'neve-pro-addon' ),
								'phone' => __( 'Phone', 'neve-pro-addon' ),
							),
						),
					),
				),
				'section'           => $this->section,
			)
		);

		SettingsManager::get_instance()->add(
			array(
				'id'                 => self::ICON_POSITION,
				'group'              => self::COMPONENT_ID,
				'tab'                => SettingsManager::TAB_STYLE,
				'transport'          => 'post' . self::COMPONENT_ID,
				'sanitize_callback'  => array( $this, 'sanitize_icon_position' ),
				'default'            => 'left',
				'label'              => __( 'Icon Position', 'neve-pro-addon' ),
				'type'               => 'select',
				'conditional_header' => true,
				'options'            => array(
					'choices' => array(
						'left'  => __( 'Left', 'neve-pro-addon' ),
						'right' => __( 'Right', 'neve-pro-addon' ),
					),
				),
				'section'            => $this->section,
			)
		);

		SettingsManager::get_instance()->add(
			array(
				'id'                    => self::ITEM_SPACING,
				'group'                 => self::COMPONENT_ID,
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'post' . self::COMPONENT_ID,
				'sanitize_callback'     => 'absint',
				'default'               => 10,
				'label'                 => __( 'Item Spacing (px)', 'neve-pro-addon' ),
				'type'                  => 'neve_range_control',
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'     => '--spacing',
						'suffix'   => 'px',
						'selector' => '.builder-item--' . $this->get_id(),
					],
				],
				'options'               => array(
					'input_attr' => array(
						'step'    => 1,
						'min'     => 0,
						'max'     => 50,
						'default' => 10,
					),
				),
				'section'               => $this->section,
			)
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::TEXT_COLOR,
				'group'                 => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'post' . $this->get_class_const( 'COMPONENT_ID' ),
				'sanitize_callback'     => 'neve_sanitize_colors',
				'default'               => '',
				'label'                 => __( 'Text Color', 'neve-pro-addon' ),
				'type'                  => 'neve_color_control',
				'section'               => $this->section,
				'conditional_header'    => true,
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'     => '--color',
						'selector' => '.builder-item--' . $this->get_id(),
					],
				],
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::ICON_COLOR,
				'group'                 => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => 'neve_sanitize_colors',
				'default'               => '',
				'label'                 => __( 'Icons Color', 'neve-pro-addon' ),
				'type'                  => 'neve_color_control',
				'section'               => $this->section,
				'conditional_header'    => true,
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					'cssVar' => [
						'vars'     => '--iconcolor',
						'selector' => '.builder-item--' . $this->get_id(),
					],
				],
			]
		);
	}

	/**
	 * Sanitization function for repeater
	 *
	 * @param string $input Repeater input.
	 *
	 * @return string
	 */
	public function sanitize_repeater_inputs( $input ) {
		if ( empty( $input ) ) {
			return $input;
		}
		$repeater_data = json_decode( $input, true );

		if ( empty( $repeater_data ) ) {
			return $input;
		}

		$available_types = [ 'text', 'email', 'phone' ];
		$sanitized_data  = [];
		foreach ( $repeater_data as $repeater_item ) {
			$sanitized_item               = [];
			$sanitized_item['title']      = ! empty( $repeater_item['title'] ) ? wp_kses_post( $repeater_item['title'] ) : '';
			$sanitized_item['icon']       = ! empty( $repeater_item['icon'] ) ? sanitize_text_field( $repeater_item['icon'] ) : '';
			$sanitized_item['item_type']  = ! empty( $repeater_item['item_type'] ) && in_array( $repeater_item['item_type'], $available_types, true ) ? $repeater_item['item_type'] : 'text';
			$sanitized_item['visibility'] = array_key_exists( 'visibility', $repeater_item ) && in_array(
				$repeater_item['visibility'],
				array(
					'yes',
					'no',
				),
				true
			) ? $repeater_item['visibility'] : 'yes';
			$sanitized_data[]             = $sanitized_item;
		}

		return wp_json_encode( $sanitized_data );
	}

	/**
	 * Method to add Component css styles.
	 *
	 * @param array $css_array An array containing css rules.
	 *
	 * @return array
	 * @since   1.0.0
	 * @access  public
	 */
	public function add_style( array $css_array = array() ) {
		$rules       = [
			'--color'     => [
				'key' => $this->id . '_' . self::TEXT_COLOR,
			],
			'--iconcolor' => [
				'key' => $this->id . '_' . self::ICON_COLOR,
			],
			'--spacing'   => [
				'key'    => $this->id . '_' . self::ITEM_SPACING,
				'suffix' => 'px',
			],
		];
		$css_array[] = [
			'selectors' => '.builder-item--' . $this->get_id(),
			'rules'     => $rules,
		];

		return parent::add_style( $css_array );
	}

	/**
	 * The render method for the component.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function render_component() {
		Main::get_instance()->load( 'component-contact' );
	}

	/**
	 * Sanitize the icon position value.
	 *
	 * @param string $value icon position value.
	 *
	 * @return string
	 */
	public function sanitize_icon_position( $value ) {
		if ( ! in_array( $value, array( 'left', 'right' ), true ) ) {
			return 'left';
		}

		return $value;
	}
}
