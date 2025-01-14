<?php
/**
 * Elementor Typed Headline Widget.
 *
 * @example https://developers.elementor.com/creating-a-new-widget/
 * @package Neve_Pro\Modules\Elementor_Booster\Widgets
 */

namespace Neve_Pro\Modules\Elementor_Booster\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;

/**
 * Class Typed_Headline
 *
 * @package Neve_Pro\Modules\Elementor_Booster\Widgets
 */
class Typed_Headline extends Elementor_Booster_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'neve_typed_headline';
	}

	/**
	 * Widget Label.
	 *
	 * @return string
	 */
	public function get_title() {
		return 'Typed Headline';
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'fas fa-h-square';
	}

	/**
	 * Retrieve the list of scripts the typed headline widget depended on.
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return [ 'neb-typed-script' ];
	}

	/**
	 * The render function.
	 */
	public function render() {
		$settings = $this->get_settings();
		$tag      = $settings['tag'];

		wp_enqueue_script( 'eaw-pro-scripts' );
		wp_script_add_data( 'eaw-pro-scripts', 'async', true );

		$this->add_render_attribute( 'typed', 'class', 'eaw-typed-text' );
		$this->add_render_attribute( 'speed', 'class', 'eaw-speed' );

		echo '<' . esc_attr( $tag ) . ' ';
		echo $this->get_render_attribute_string( 'typed' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '>';
		if ( ! empty( $settings['before_text'] ) ) {
			echo '<span class="eaw-typed-text-plain eaw-typed-text-wrapper">';
			echo wp_kses_post( $settings['before_text'] );
			echo '</span> ';
		}

		if ( ! empty( $settings['typed_text'] ) ) {
			echo ' <span class="eaw-typed-text-placeholder"></span>';
		}

		if ( ! empty( $settings['after_text'] ) ) {
			echo ' <span class="eaw-typed-text-plain eaw-typed-text-wrapper">';
			echo wp_kses_post( $settings['after_text'] );
			echo '</span>';
		}
		echo '</' . esc_attr( $tag ) . '>';
	}

	/**
	 * Register content related controls
	 */
	protected function register_content_controls() {
		$this->start_controls_section(
			'section_title',
			array(
				'label' => esc_html__( 'Settings', 'neve-pro-addon' ),
			)
		);

		$this->add_control(
			'before_text',
			array(
				'label'       => __( 'Before', 'neve-pro-addon' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => __( 'This is an', 'neve-pro-addon' ),
				'placeholder' => __( 'Before Typed Text', 'neve-pro-addon' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'typed_text',
			array(
				'label'              => __( 'Typed Text', 'neve-pro-addon' ),
				'type'               => Controls_Manager::TEXTAREA,
				'placeholder'        => __( 'Enter each word in a separate line', 'neve-pro-addon' ),
				'default'            => "Awesome\nEngaging\n",
				'rows'               => 5,
				'frontend_available' => true,
			)
		);

		$this->add_control(
			'after_text',
			array(
				'label'       => __( 'After', 'neve-pro-addon' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'After Typed Text', 'neve-pro-addon' ),
				'default'     => __( 'Typed Text', 'neve-pro-addon' ),
				'label_block' => true,
			)
		);

		$this->add_control(
			'tag',
			array(
				'label'   => __( 'HTML Tag', 'neve-pro-addon' ),
				'type'    => Controls_Manager::SELECT,
				'options' => array(
					'h1'   => 'H1',
					'h2'   => 'H2',
					'h3'   => 'H3',
					'h4'   => 'H4',
					'h5'   => 'H5',
					'h6'   => 'H6',
					'div'  => 'div',
					'span' => 'span',
					'p'    => 'p',
				),
				'default' => 'h3',
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Register styles related controls
	 */
	protected function register_style_controls() {
		$this->start_controls_section(
			'section_style',
			array(
				'label' => __( 'Headline', 'neve-pro-addon' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'color',
			array(
				'label'     => __( 'Color', 'neve-pro-addon' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .eaw-typed-text' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .eaw-typed-text',
				'scheme'   => Typography::TYPOGRAPHY_1,
			)
		);

		$this->add_responsive_control(
			'alignment',
			array(
				'label'       => __( 'Alignment', 'neve-pro-addon' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => array(
					'left'   => array(
						'title' => __( 'Left', 'neve-pro-addon' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'neve-pro-addon' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'neve-pro-addon' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'default'     => 'center',
				'separator'   => 'before',
				'selectors'   => array(
					'{{WRAPPER}} .eaw-typed-text' => 'text-align: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'              => __( 'Typing Duration', 'neve-pro-addon' ),
				'type'               => Controls_Manager::SLIDER,
				'range'              => array(
					'px' => array(
						'min'  => 10,
						'max'  => 500,
						'step' => 10,
					),
				),
				'default'            => array(
					'size' => 110,
				),
				'frontend_available' => true,
			)
		);

		$this->end_controls_section();
	}
}
