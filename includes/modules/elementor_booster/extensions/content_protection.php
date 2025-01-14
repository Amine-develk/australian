<?php
/**
 * Content protection class
 *
 * @package Neve_Pro\Modules\Elementor_Booster\Extensions
 */

namespace Neve_Pro\Modules\Elementor_Booster\Extensions;

use Elementor\Controls_Manager;
use Elementor\Frontend;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Neve_Pro\Modules\Elementor_Booster\Widgets\Elementor_Booster_Base;

/**
 * Class Content_Protection
 *
 * @package Neve_Pro\Modules\Elementor_Booster\Extensions
 */
class Content_Protection {

	/**
	 * Content_Protection constructor.
	 */
	public function __construct() {
		add_action(
			'elementor/element/common/_section_style/after_section_end',
			array(
				$this,
				'register_controls',
			),
			10
		);
		add_filter( 'elementor/widget/render_content', array( $this, 'render_content' ), 10, 2 );
	}

	/**
	 * Register Content Protection Controls.
	 *
	 * @param Object $element Elementor instance.
	 */
	public function register_controls( $element ) {
		$element->start_controls_section(
			'neb_content_protection_section',
			[
				'label' => esc_html__( 'Content Protection', 'neve-pro-addon' ),
				'tab'   => Controls_Manager::TAB_ADVANCED,
			]
		);

		$element->add_control(
			'neb_content_protection',
			[
				'label'        => __( 'Enable Content Protection', 'neve-pro-addon' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'no',
				'label_on'     => __( 'Yes', 'neve-pro-addon' ),
				'label_off'    => __( 'No', 'neve-pro-addon' ),
				'return_value' => 'yes',
			]
		);

		$element->add_control(
			'neb_content_protection_type',
			[
				'label'       => esc_html__( 'Protection Type', 'neve-pro-addon' ),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'role'             => esc_html__( 'User role', 'neve-pro-addon' ),
					'password'         => esc_html__( 'Password protected', 'neve-pro-addon' ),
					'logged-in'        => esc_html__( 'User is logged', 'neve-pro-addon' ),
					'start-end-date'   => esc_html__( 'Start / End date', 'neve-pro-addon' ),
					'days-of-the-week' => esc_html__( 'Days of the week', 'neve-pro-addon' ),
				],
				'default'     => 'role',
				'condition'   => [
					'neb_content_protection' => 'yes',
				],
			]
		);

		$element->add_control(
			'neb_content_protection_role',
			[
				'label'       => __( 'Select Roles', 'neve-pro-addon' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => $this->get_user_roles(),
				'condition'   => [
					'neb_content_protection'      => 'yes',
					'neb_content_protection_type' => 'role',
				],
			]
		);

		$element->add_control(
			'neb_content_protection_password',
			[
				'label'      => esc_html__( 'Set Password', 'neve-pro-addon' ),
				'type'       => Controls_Manager::TEXT,
				'input_type' => 'password',
				'condition'  => [
					'neb_content_protection'      => 'yes',
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'neb_content_protection_password_placeholder',
			[
				'label'     => esc_html__( 'Input Placeholder', 'neve-pro-addon' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Enter Password',
				'condition' => [
					'neb_content_protection'      => 'yes',
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'neb_content_protection_password_submit_btn_txt',
			[
				'label'     => esc_html__( 'Submit Button Text', 'neve-pro-addon' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => 'Submit',
				'condition' => [
					'neb_content_protection'      => 'yes',
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$date_format  = get_option( 'date_format' );
		$time_format  = get_option( 'time_format' );
		$current_time = gmdate( $date_format . ' ' . $time_format );
		/* translators: %s is the current time */
		$description = sprintf( __( 'Current time: %s', 'neve-pro-addon' ), $current_time );

		$element->add_control(
			'server_time_note',
			[
				'type'       => Controls_Manager::RAW_HTML,
				'raw'        => $description,
				'conditions' => [
					'relation' => 'or',
					'terms'    => [
						[
							'name'     => 'neb_content_protection_type',
							'operator' => '===',
							'value'    => 'start-end-date',
						],
						[
							'name'     => 'neb_content_protection_type',
							'operator' => '===',
							'value'    => 'days-of-the-week',
						],
					],
				],
			]
		);

		$element->add_control(
			'neb_content_protection_period_date',
			[
				'label'          => __( 'Period', 'neve-pro-addon' ),
				'type'           => Controls_Manager::DATE_TIME,
				'condition'      => [
					'neb_content_protection'      => 'yes',
					'neb_content_protection_type' => 'start-end-date',
				],
				'picker_options' => [
					'mode' => 'range',
				],
			]
		);

		$element->add_control(
			'neb_content_protection_days_of_week',
			[
				'label'       => __( 'Every', 'neve-pro-addon' ),
				'type'        => Controls_Manager::SELECT2,
				'label_block' => true,
				'multiple'    => true,
				'options'     => $this->get_days_of_week(),
				'condition'   => [
					'neb_content_protection'      => 'yes',
					'neb_content_protection_type' => 'days-of-the-week',
				],
			]
		);

		$element->add_control(
			'neb_content_protection_days_of_week_time_from',
			[
				'label'          => __( 'From', 'neve-pro-addon' ),
				'type'           => Controls_Manager::DATE_TIME,
				'condition'      => [
					'neb_content_protection'               => 'yes',
					'neb_content_protection_type'          => 'days-of-the-week',
					'neb_content_protection_days_of_week!' => '',
				],
				'picker_options' => [
					'noCalendar' => true,
					'enableTime' => true,
					'dateFormat' => 'h:i K',
				],
			]
		);

		$element->add_control(
			'neb_content_protection_days_of_week_time_to',
			[
				'label'          => __( 'To', 'neve-pro-addon' ),
				'type'           => Controls_Manager::DATE_TIME,
				'condition'      => [
					'neb_content_protection'               => 'yes',
					'neb_content_protection_type'          => 'days-of-the-week',
					'neb_content_protection_days_of_week!' => '',
					'neb_content_protection_days_of_week_time_from!' => '',
				],
				'picker_options' => [
					'noCalendar' => true,
					'enableTime' => true,
					'dateFormat' => 'h:i K',
				],
			]
		);

		$element->start_controls_tabs(
			'neb_content_protection_tabs',
			[
				'condition' => [
					'neb_content_protection' => 'yes',
				],
			]
		);

		$element->start_controls_tab(
			'neb_content_protection_tab_message',
			[
				'label' => __( 'Message', 'neve-pro-addon' ),
			]
		);

		$element->add_control(
			'neb_content_protection_message_type',
			[
				'label'       => esc_html__( 'Message Type', 'neve-pro-addon' ),
				'label_block' => false,
				'type'        => Controls_Manager::SELECT,
				'description' => esc_html__( 'Set a message or a saved template when the content is protected.', 'neve-pro-addon' ),
				'options'     => [
					'none'     => esc_html__( 'None', 'neve-pro-addon' ),
					'text'     => esc_html__( 'Message', 'neve-pro-addon' ),
					'template' => esc_html__( 'Saved Templates', 'neve-pro-addon' ),
				],
				'default'     => 'text',
			]
		);

		$element->add_control(
			'neb_content_protection_message_text',
			[
				'label'     => esc_html__( 'Public Text', 'neve-pro-addon' ),
				'type'      => Controls_Manager::WYSIWYG,
				'default'   => esc_html__( 'You do not have permission to see this content.', 'neve-pro-addon' ),
				'dynamic'   => [
					'active' => true,
				],
				'condition' => [
					'neb_content_protection_message_type' => 'text',
				],
			]
		);

		$element->add_control(
			'neb_content_protection_message_template',
			[
				'label'     => __( 'Choose Template', 'neve-pro-addon' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => Elementor_Booster_Base::get_page_templates(),
				'condition' => [
					'neb_content_protection_message_type' => 'template',
				],
			]
		);

		$element->end_controls_tab();

		$element->start_controls_tab(
			'neb_content_protection_tab_style',
			[
				'label' => __( 'Style', 'neve-pro-addon' ),
			]
		);

		$element->add_control(
			'neb_content_protection_message_styles',
			[
				'label'     => __( 'Message', 'neve-pro-addon' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'neb_content_protection_message_type' => 'text',
				],
			]
		);

		$element->add_control(
			'neb_content_protection_message_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'neve-pro-addon' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .neb-protected-content-message' => 'color: {{VALUE}};',
				],
				'condition' => [
					'neb_content_protection_message_type' => 'text',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'neb_content_protection_message_text_typography',
				'scheme'    => Typography::TYPOGRAPHY_2,
				'selector'  => '{{WRAPPER}} .neb-protected-content-message, {{WRAPPER}} .protected-content-error-msg',
				'condition' => [
					'neb_content_protection_message_type' => 'text',
				],
			]
		);

		$element->add_responsive_control(
			'neb_content_protection_message_text_alignment',
			[
				'label'       => esc_html__( 'Text Alignment', 'neve-pro-addon' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => true,
				'options'     => [
					'left'   => [
						'title' => esc_html__( 'Left', 'neve-pro-addon' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'neve-pro-addon' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'neve-pro-addon' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'     => 'left',
				'selectors'   => [
					'{{WRAPPER}} .neb-protected-content-message, {{WRAPPER}} .protected-content-error-msg' => 'text-align: {{VALUE}};',
				],
				'condition'   => [
					'neb_content_protection_message_type' => 'text',
				],
			]
		);

		$element->add_responsive_control(
			'neb_content_protection_message_text_padding',
			[
				'label'      => esc_html__( 'Padding', 'neve-pro-addon' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .neb-protected-content-message' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'neb_content_protection_message_type' => 'text',
				],
			]
		);

		$element->add_control(
			'neb_content_protection_input_styles',
			[
				'label'     => __( 'Password Field', 'neve-pro-addon' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'neb_content_protection_input_width',
			[
				'label'     => esc_html__( 'Input Width', 'neve-pro-addon' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 1000,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password' => 'width: {{SIZE}}px;',
				],
				'condition' => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_responsive_control(
			'neb_content_protection_input_alignment',
			[
				'label'       => esc_html__( 'Input Alignment', 'neve-pro-addon' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => true,
				'options'     => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'neve-pro-addon' ),
						'icon'  => 'fa fa-align-left',
					],
					'center'     => [
						'title' => esc_html__( 'Center', 'neve-pro-addon' ),
						'icon'  => 'fa fa-align-center',
					],
					'flex-end'   => [
						'title' => esc_html__( 'Right', 'neve-pro-addon' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'     => 'left',
				'selectors'   => [
					'{{WRAPPER}} .neb-password-protected-content-fields > form' => 'justify-content: {{VALUE}};',
				],
				'condition'   => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_responsive_control(
			'neb_content_protection_password_input_padding',
			[
				'label'      => esc_html__( 'Padding', 'neve-pro-addon' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_responsive_control(
			'neb_content_protection_password_input_margin',
			[
				'label'      => esc_html__( 'Margin', 'neve-pro-addon' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'neb_content_protection_input_border_radius',
			[
				'label'     => esc_html__( 'Border Radius', 'neve-pro-addon' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password' => 'border-radius: {{SIZE}}px;',
				],
				'condition' => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'neb_content_protection_password_input_color',
			[
				'label'     => esc_html__( 'Color', 'neve-pro-addon' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password' => 'color: {{VALUE}};',
				],
				'condition' => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'neb_content_protection_password_input_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'neve-pro-addon' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'neb_content_protection_password_input_border',
				'label'     => esc_html__( 'Border', 'neve-pro-addon' ),
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-password',
				'condition' => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'neb_content_protection_password_input_shadow',
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-password',
				'condition' => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'neb_content_protection_input_styles_hover',
			[
				'label'     => __( 'Password Field Hover', 'neve-pro-addon' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'neb_protected_content_password_input_hover_color',
			[
				'label'     => esc_html__( 'Color', 'neve-pro-addon' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'neb_protected_content_password_input_hover_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'neve-pro-addon' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields input.neb-password:hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'neb_protected_content_password_input_hover_border',
				'label'     => esc_html__( 'Border', 'neve-pro-addon' ),
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-password:hover',
				'condition' => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'neb_protected_content_password_input_hover_shadow',
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-password:hover',
				'condition' => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'neb_content_protection_submit_button_styles',
			[
				'label'     => __( 'Submit Button', 'neve-pro-addon' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'neb_content_protection_submit_button_color',
			[
				'label'     => esc_html__( 'Text Color', 'neve-pro-addon' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields .neb-submit' => 'color: {{VALUE}};',
				],
				'condition' => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'neb_content_protection_submit_button_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'neve-pro-addon' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields .neb-submit' => 'background: {{VALUE}};',
				],
				'condition' => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'neb_content_protection_submit_button_border',
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-submit',
				'condition' => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'neb_content_protection_submit_button_box_shadow',
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-submit',
				'condition' => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'neb_content_protection_submit_button_styles_hover',
			[
				'label'     => __( 'Submit Button Hover', 'neve-pro-addon' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'neb_content_protection_submit_button_hover_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'neve-pro-addon' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields .neb-submit:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_control(
			'neb_content_protection_submit_button_hover_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'neve-pro-addon' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#333333',
				'selectors' => [
					'{{WRAPPER}} .neb-password-protected-content-fields .neb-submit:hover' => 'background: {{VALUE}};',
				],
				'condition' => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'neb_content_protection_submit_button_hover_border',
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-submit:hover',
				'condition' => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'neb_content_protection_submit_button_hover_box_shadow',
				'selector'  => '{{WRAPPER}} .neb-password-protected-content-fields .neb-submit:hover',
				'condition' => [
					'neb_content_protection_type' => 'password',
				],
			]
		);

		$element->end_controls_tab();

		$element->end_controls_tabs();

		$element->end_controls_section();
	}

	/**
	 * Get user roles.
	 *
	 * @return array
	 */
	private function get_user_roles() {
		global $wp_roles;
		$roles = $wp_roles->roles;
		if ( empty( $roles ) ) {
			return array();
		}

		$all_roles = array();
		foreach ( $roles as $key => $value ) {
			$all_roles[ $key ] = $roles[ $key ]['name'];
		}

		return $all_roles;
	}

	/**
	 * Return an array with days of the week.
	 *
	 * @return array
	 */
	private function get_days_of_week() {
		return array(
			6 => __( 'Saturday', 'neve-pro-addon' ),
			0 => __( 'Sunday', 'neve-pro-addon' ),
			1 => __( 'Monday', 'neve-pro-addon' ),
			2 => __( 'Tuesday', 'neve-pro-addon' ),
			3 => __( 'Wednesday', 'neve-pro-addon' ),
			4 => __( 'Thursday', 'neve-pro-addon' ),
			5 => __( 'Friday', 'neve-pro-addon' ),
		);
	}

	/**
	 * Render Content Protection.
	 *
	 * @param string $content Content.
	 * @param Object $widget Widget instance.
	 *
	 * @return string
	 */
	public function render_content( $content, $widget ) {
		$settings = $widget->get_settings_for_display();

		if ( $settings['neb_content_protection'] !== 'yes' ) {
			return $content;
		}

		if ( $settings['neb_content_protection_type'] === 'role' ) {
			if ( $this->current_user_privileges( $settings ) === true ) {
				return $content;
			}

			return '<div class="neb-protected-content">' . $this->render_message( $settings ) . '</div>';
		}

		if ( $settings['neb_content_protection_type'] === 'password' ) {
			if ( empty( $settings['neb_content_protection_password'] ) ) {
				return $content;
			}

			$html     = '';
			$unlocked = false;

			if ( isset( $_POST['neb_content_protection_password'] ) && isset( $_POST['neb_password_content_nonce'] ) ) {
				if ( $settings['neb_content_protection_password'] === $_POST['neb_content_protection_password'] && wp_verify_nonce( sanitize_key( $_POST['neb_password_content_nonce'] ), 'neb_password_content' ) ) {
					$unlocked = true;

					$html .= "<script>
                        var expires = new Date();
                        expires.setTime( expires.getTime() + ( 60 * 60 * 1000 ) );
                        document.cookie = 'neb_content_protection_password=true;expires=' + expires.toUTCString();
                    </script>";
				}
			}

			if ( isset( $_COOKIE['neb_content_protection_password'] ) || $unlocked ) {
				$html .= $content;
			} else {
				$html .= '<div class="neb-protected-content">' . $this->render_message( $settings ) . $this->password_protected_form( $settings ) . '</div>';
			}

			return $html;
		}

		if ( $settings['neb_content_protection_type'] === 'logged-in' ) {
			if ( is_user_logged_in() ) {
				return $content;
			}

			return '<div class="neb-protected-content">' . $this->render_message( $settings ) . '</div>';
		}

		$current_time = strtotime( gmdate( 'Y-m-d H:i' ) );

		if ( $settings['neb_content_protection_type'] === 'start-end-date' ) {
			$period = $settings['neb_content_protection_period_date'];
			if ( empty( $period ) ) {
				return $content;
			}

			$start_end = explode( ' to ', $period );
			if ( count( $start_end ) !== 2 ) {
				return $content;
			}

			$start_date = strtotime( $start_end[0] );
			$end_date   = strtotime( $start_end[1] );
			if ( $start_date <= $current_time && $current_time <= $end_date ) {
				return '<div class="neb-protected-content">' . $this->render_message( $settings ) . '</div>';
			}

			return $content;
		}

		if ( $settings['neb_content_protection_type'] === 'days-of-the-week' ) {
			$current_day  = gmdate( 'w', $current_time );
			$blocked_days = ! empty( $settings['neb_content_protection_days_of_week'] ) ? $settings['neb_content_protection_days_of_week'] : array();
			if ( in_array( $current_day, $blocked_days, true ) ) {
				if ( isset( $settings['neb_content_protection_days_of_week_time_from'] ) && isset( $settings['neb_content_protection_days_of_week_time_to'] ) ) {
					$start = strtotime( 'today ' . $settings['neb_content_protection_days_of_week_time_from'] );
					$end   = strtotime( 'today ' . $settings['neb_content_protection_days_of_week_time_to'] );
					if ( $start <= $current_time && $current_time <= $end ) {
						return '<div class="neb-protected-content">' . $this->render_message( $settings ) . '</div>';
					}

					return $content;
				}

				return '<div class="neb-protected-content">' . $this->render_message( $settings ) . '</div>';
			}

			return $content;
		}

		return $content;
	}

	/**
	 * Check current user role exists inside of the roles array.
	 *
	 * @param array $settings Current widget settings.
	 *
	 * @return bool
	 */
	private function current_user_privileges( $settings ) {
		if ( ! is_user_logged_in() ) {
			return false;
		}

		$user_role = reset( wp_get_current_user()->roles );

		return in_array( $user_role, (array) $settings['neb_content_protection_role'], true );
	}

	/**
	 * Render Content Protection Message.
	 *
	 * @param array $settings Widget Settings.
	 *
	 * @return string
	 */
	protected function render_message( $settings ) {
		$html = '<div class="neb-protected-content-message">';

		if ( $settings['neb_content_protection_message_type'] === 'text' ) {
			$html .= '<div class="neb-protected-content-message-text">' . $settings['neb_content_protection_message_text'] . '</div>';
		} elseif ( $settings['neb_content_protection_message_type'] === 'template' ) {
			if ( ! empty( $settings['neb_content_protection_message_template'] ) ) {
				$template_id = $settings['neb_content_protection_message_template'];
				$frontend    = new Frontend();

				$html .= $frontend->get_builder_content( $template_id, true );
			}
		}
		$html .= '</div>';

		return $html;
	}

	/**
	 * Render Content Protection form.
	 *
	 * @param array $settings Widget settings.
	 *
	 * @return string
	 */
	public function password_protected_form( $settings ) {
		$html = '<div class="neb-password-protected-content-fields">
            <form method="post">
                <input type="password" name="neb_content_protection_password" class="neb-password" placeholder="' . $settings['neb_content_protection_password_placeholder'] . '">
                <input type="submit" value="' . $settings['neb_content_protection_password_submit_btn_txt'] . '" class="neb-submit">' . wp_nonce_field( 'neb_password_content', 'neb_password_content_nonce', false, false ) . '
            </form>';

		if ( isset( $_POST['neb_content_protection_password'] ) && isset( $_POST['neb_password_content_nonce'] ) ) {
			if ( $settings['neb_content_protection_password'] !== $_POST['neb_content_protection_password'] && wp_verify_nonce( sanitize_key( $_POST['neb_password_content_nonce'] ), 'neb_password_content' ) ) {
				/* translators: %s is Incorrect password message */
				$html .= sprintf(
					'<p class="">%s</p>',
					__( 'Password does not match.', 'neve-pro-addon' )
				);
			}
		}

		$html .= '</div>';

		return $html;
	}
}
