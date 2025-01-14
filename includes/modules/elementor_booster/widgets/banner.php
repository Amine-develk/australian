<?php
/**
 * Elementor Banner Widget.
 *
 * @package Neve_Pro\Modules\Elementor_Booster\Widgets
 */

namespace Neve_Pro\Modules\Elementor_Booster\Widgets;

use Elementor\Control_Media;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Css_Filter;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Schemes\Typography;
use Elementor\Utils;

/**
 * Class Banner
 *
 * @package Neve_Pro\Modules\Elementor_Booster\Widgets
 */
class Banner extends Elementor_Booster_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'neve_banner';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 * @since 1.0.0
	 * @access public
	 */
	public function get_title() {
		return __( 'Banner', 'neve-pro-addon' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 * @since 1.0.0
	 * @access public
	 */
	public function get_icon() {
		return 'far fa-image';
	}

	/**
	 * Get widget keywords
	 *
	 * @return array
	 */
	public function get_keywords() {
		return [ 'banner', 'ads', 'sale', 'showcase', 'image', 'neve-pro-addon' ];
	}

	/**
	 * Register content related controls
	 */
	protected function register_content_controls() {
		$this->start_controls_section(
			'global_settings',
			[
				'label' => __( 'Image', 'neve-pro-addon' ),
			]
		);

		$this->add_control(
			'image',
			[
				'label'         => __( 'Upload Image', 'neve-pro-addon' ),
				'type'          => Controls_Manager::MEDIA,
				'dynamic'       => [ 'active' => true ],
				'default'       => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'show_external' => true,
			]
		);

		$this->add_control(
			'link_url_switch',
			[
				'label'     => __( 'Link', 'neve-pro-addon' ),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'image_link_switcher',
			[
				'label'     => __( 'Custom Link', 'neve-pro-addon' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'link_url_switch' => 'yes',
				],
			]
		);

		$this->add_control(
			'image_custom_link',
			[
				'label'         => __( 'Set custom Link', 'neve-pro-addon' ),
				'type'          => Controls_Manager::URL,
				'dynamic'       => [ 'active' => true ],
				'condition'     => [
					'image_link_switcher' => 'yes',
					'link_url_switch'     => 'yes',
				],
				'show_external' => false,
			]
		);

		$this->add_control(
			'image_existing_page_link',
			[
				'label'     => __( 'Existing Page', 'neve-pro-addon' ),
				'type'      => Controls_Manager::SELECT2,
				'condition' => [
					'image_link_switcher!' => 'yes',
					'link_url_switch'      => 'yes',
				],
				'multiple'  => false,
				'options'   => $this->get_all_post(),
			]
		);

		$this->add_control(
			'link_title',
			[
				'label'     => __( 'Link Title', 'neve-pro-addon' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => [ 'active' => true ],
				'condition' => [
					'link_url_switch' => 'yes',
				],
			]
		);

		$this->add_control(
			'image_link_open_new_tab',
			[
				'label'       => __( 'New Tab', 'neve-pro-addon' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Choose if you want the link be opened in a new tab or not', 'neve-pro-addon' ),
				'condition'   => [
					'link_url_switch' => 'yes',
				],
			]
		);

		$this->add_control(
			'image_link_add_nofollow',
			[
				'label'       => __( 'Nofollow Option', 'neve-pro-addon' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'if you choose yes, the link will not be counted in search engines', 'neve-pro-addon' ),
				'condition'   => [
					'link_url_switch' => 'yes',
				],
			]
		);

		$this->add_control(
			'image_animation',
			[
				'label'       => __( 'Effect', 'neve-pro-addon' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'animation1',
				'description' => __( 'Choose a hover effect for the banner', 'neve-pro-addon' ),
				'options'     => [
					'animation1'  => __( 'Effect 1', 'neve-pro-addon' ),
					'animation5'  => __( 'Effect 2', 'neve-pro-addon' ),
					'animation13' => __( 'Effect 3', 'neve-pro-addon' ),
					'animation2'  => __( 'Effect 4', 'neve-pro-addon' ),
					'animation4'  => __( 'Effect 5', 'neve-pro-addon' ),
					'animation6'  => __( 'Effect 6', 'neve-pro-addon' ),
				],
				'separator'   => 'before',
			]
		);

		$this->add_control(
			'active',
			[
				'label'       => __( 'Always Hovered', 'neve-pro-addon' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'Choose if you want the effect to be always active', 'neve-pro-addon' ),
			]
		);

		$this->add_control(
			'hover_effect',
			[
				'label'   => __( 'Hover Effect', 'neve-pro-addon' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'none'      => __( 'None', 'neve-pro-addon' ),
					'zoomin'    => __( 'Zoom In', 'neve-pro-addon' ),
					'zoomout'   => __( 'Zoom Out', 'neve-pro-addon' ),
					'scale'     => __( 'Scale', 'neve-pro-addon' ),
					'grayscale' => __( 'Grayscale', 'neve-pro-addon' ),
					'blur'      => __( 'Blur', 'neve-pro-addon' ),
					'bright'    => __( 'Bright', 'neve-pro-addon' ),
					'sepia'     => __( 'Sepia', 'neve-pro-addon' ),
				],
				'default' => 'none',
			]
		);

		$this->add_control(
			'height',
			[
				'label'       => __( 'Height', 'neve-pro-addon' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'default' => __( 'Default', 'neve-pro-addon' ),
					'custom'  => __( 'Custom', 'neve-pro-addon' ),
				],
				'default'     => 'default',
				'description' => __( 'Choose if you want to set a custom height for the banner or keep it as it is', 'neve-pro-addon' ),
			]
		);

		$this->add_responsive_control(
			'custom_height',
			[
				'label'       => __( 'Min Height', 'neve-pro-addon' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Set a minimum height value in pixels', 'neve-pro-addon' ),
				'condition'   => [
					'height' => 'custom',
				],
				'selectors'   => [
					'{{WRAPPER}} .neb-banner-ib' => 'height: {{VALUE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'img_vertical_align',
			[
				'label'     => __( 'Vertical Align', 'neve-pro-addon' ),
				'type'      => Controls_Manager::SELECT,
				'condition' => [
					'height' => 'custom',
				],
				'options'   => [
					'flex-start' => __( 'Top', 'neve-pro-addon' ),
					'center'     => __( 'Middle', 'neve-pro-addon' ),
					'flex-end'   => __( 'Bottom', 'neve-pro-addon' ),
					'inherit'    => __( 'Full', 'neve-pro-addon' ),
				],
				'default'   => 'flex-start',
				'selectors' => [
					'{{WRAPPER}} .neb-banner-img-wrap' => 'align-items: {{VALUE}}; -webkit-align-items: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'extra_class',
			[
				'label'       => __( 'Extra Class', 'neve-pro-addon' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'description' => __( 'Add extra class name that will be applied to the banner, and you can use this class for your customizations.', 'neve-pro-addon' ),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'image_section',
			[
				'label' => __( 'Content', 'neve-pro-addon' ),
			]
		);

		$this->add_control(
			'title',
			[
				'label'       => __( 'Title', 'neve-pro-addon' ),
				'placeholder' => __( 'Give a title to this banner', 'neve-pro-addon' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'label_block' => false,
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'       => __( 'HTML Tag', 'neve-pro-addon' ),
				'description' => __( 'Select a heading tag for the title. Headings are defined with H1 to H6 tags', 'neve-pro-addon' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'h3',
				'options'     => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
				],
				'label_block' => true,
			]
		);

		$this->add_control(
			'description_hint',
			[
				'label' => __( 'Description', 'neve-pro-addon' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'description',
			[
				'label'       => __( 'Description', 'neve-pro-addon' ),
				'description' => __( 'Give the description to this banner', 'neve-pro-addon' ),
				'type'        => Controls_Manager::WYSIWYG,
				'dynamic'     => [ 'active' => true ],
				'label_block' => true,
			]
		);

		$this->add_control(
			'link_switcher',
			[
				'label'     => __( 'Button', 'neve-pro-addon' ),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'link_url_switch!' => 'yes',
				],
			]
		);

		$this->add_control(
			'more_text',
			[
				'label'     => __( 'Text', 'neve-pro-addon' ),
				'type'      => Controls_Manager::TEXT,
				'dynamic'   => [ 'active' => true ],
				'default'   => 'Click Here',
				'condition' => [
					'link_switcher'    => 'yes',
					'link_url_switch!' => 'yes',
				],
			]
		);

		$this->add_control(
			'link_selection',
			[
				'label'       => __( 'Link Type', 'neve-pro-addon' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'url'  => __( 'URL', 'neve-pro-addon' ),
					'link' => __( 'Existing Page', 'neve-pro-addon' ),
				],
				'default'     => 'url',
				'label_block' => true,
				'condition'   => [
					'link_switcher'    => 'yes',
					'link_url_switch!' => 'yes',
				],
			]
		);

		$this->add_control(
			'link',
			[
				'label'       => __( 'Link', 'neve-pro-addon' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'default'     => [
					'url' => '#',
				],
				'placeholder' => 'https://premiumaddons.com/',
				'label_block' => true,
				'condition'   => [
					'link_selection'   => 'url',
					'link_switcher'    => 'yes',
					'link_url_switch!' => 'yes',
				],
			]
		);

		$this->add_control(
			'existing_link',
			[
				'label'       => __( 'Existing Page', 'neve-pro-addon' ),
				'type'        => Controls_Manager::SELECT2,
				'options'     => $this->get_all_post(),
				'multiple'    => false,
				'condition'   => [
					'link_selection'   => 'link',
					'link_switcher'    => 'yes',
					'link_url_switch!' => 'yes',
				],
				'label_block' => true,
			]
		);

		$this->add_control(
			'title_text_align',
			[
				'label'     => __( 'Alignment', 'neve-pro-addon' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'neve-pro-addon' ),
						'icon'  => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'neve-pro-addon' ),
						'icon'  => 'fa fa-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'neve-pro-addon' ),
						'icon'  => 'fa fa-align-right',
					],
				],
				'default'   => 'left',
				'toggle'    => false,
				'selectors' => [
					'{{WRAPPER}} .neb-banner-ib-title, {{WRAPPER}} .neb-banner-ib-content, {{WRAPPER}} .neb-banner-read-more' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'responsive_section',
			[
				'label' => __( 'Responsive', 'neve-pro-addon' ),
			]
		);

		$this->add_control(
			'responsive_switcher',
			[
				'label'       => __( 'Responsive Controls', 'neve-pro-addon' ),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __( 'If the description text is not suiting well on specific screen sizes, you may enable this option which will hide the description text.', 'neve-pro-addon' ),
			]
		);

		$this->add_control(
			'min_range',
			[
				'label'       => __( 'Minimum Size', 'neve-pro-addon' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Note: minimum size for extra small screens is 1px.', 'neve-pro-addon' ),
				'default'     => 1,
				'condition'   => [
					'responsive_switcher' => 'yes',
				],
			]
		);

		$this->add_control(
			'max_range',
			[
				'label'       => __( 'Maximum Size', 'neve-pro-addon' ),
				'type'        => Controls_Manager::NUMBER,
				'description' => __( 'Note: maximum size for extra small screens is 767px.', 'neve-pro-addon' ),
				'default'     => 767,
				'condition'   => [
					'responsive_switcher' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register styles related controls
	 */
	protected function register_style_controls() {

		$this->start_controls_section(
			'opacity_style',
			[
				'label' => __( 'Image', 'neve-pro-addon' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'image_bg_color',
			[
				'label'     => __( 'Background Color', 'neve-pro-addon' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .neb-banner-ib' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'image_opacity',
			[
				'label'     => __( 'Image Opacity', 'neve-pro-addon' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 1,
				],
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => .1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .neb-banner-ib .neb-banner-ib-img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_control(
			'image_hover_opacity',
			[
				'label'     => __( 'Hover Opacity', 'neve-pro-addon' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 1,
				],
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => .1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .neb-banner-ib:hover .neb-banner-ib-img' => 'opacity: {{SIZE}};',
					'{{WRAPPER}} .neb-banner-ib.active .neb-banner-ib-img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'css_filters',
				'selector' => '{{WRAPPER}} .neb-banner-ib .neb-banner-ib-img',
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name'     => 'hover_css_filters',
				'label'    => __( 'Hover CSS Filter', 'neve-pro-addon' ),
				'selector' => '{{WRAPPER}} .neb-banner-ib:hover .neb-banner-ib-img, {{WRAPPER}} .neb-banner-ib.active .neb-banner-ib-img',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'image_border',
				'selector' => '{{WRAPPER}} .neb-banner-ib',
			]
		);

		$this->add_responsive_control(
			'image_border_radius',
			[
				'label'      => __( 'Border Radius', 'neve-pro-addon' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .neb-banner-ib' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'blend_mode',
			[
				'label'     => __( 'Blend Mode', 'neve-pro-addon' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					''            => __( 'Normal', 'neve-pro-addon' ),
					'multiply'    => 'Multiply',
					'screen'      => 'Screen',
					'overlay'     => 'Overlay',
					'darken'      => 'Darken',
					'lighten'     => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation'  => 'Saturation',
					'color'       => 'Color',
					'luminosity'  => 'Luminosity',
				],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .neb-banner-ib' => 'mix-blend-mode: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'title_style',
			[
				'label' => __( 'Title', 'neve-pro-addon' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'color_of_title',
			[
				'label'     => __( 'Color', 'neve-pro-addon' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Color::get_type(),
					'value' => Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .neb-banner-ib-desc .neb-banner-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'style2_title_bg',
			[
				'label'       => __( 'Title Background', 'neve-pro-addon' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#f2f2f2',
				'description' => __( 'Choose a background color for the title', 'neve-pro-addon' ),
				'condition'   => [
					'image_animation' => 'animation5',
				],
				'selectors'   => [
					'{{WRAPPER}} .neb-banner-animation5 .neb-banner-ib-desc'    => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'style3_title_border',
			[
				'label'     => __( 'Title Border Color', 'neve-pro-addon' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'image_animation' => 'animation13',
				],
				'selectors' => [
					'{{WRAPPER}} .neb-banner-animation13 .neb-banner-ib-title::after'    => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .neb-banner-ib-desc .neb-banner-title',
				'scheme'   => Typography::TYPOGRAPHY_1,
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'label'    => __( 'Shadow', 'neve-pro-addon' ),
				'name'     => 'title_shadow',
				'selector' => '{{WRAPPER}} .neb-banner-ib-desc .neb-banner-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'styles_of_content',
			[
				'label' => __( 'Description', 'neve-pro-addon' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'color_of_content',
			[
				'label'     => __( 'Color', 'neve-pro-addon' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Color::get_type(),
					'value' => Color::COLOR_3,
				],
				'selectors' => [
					'{{WRAPPER}} .neb-banner .neb-banner-content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'scaled_border_color',
			[
				'label'     => __( 'Inner Border Color', 'neve-pro-addon' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'image_animation' => [ 'animation4', 'animation6' ],
				],
				'selectors' => [
					'{{WRAPPER}} .neb-banner-animation4 .neb-banner-ib-desc::after, {{WRAPPER}} .neb-banner-animation4 .neb-banner-ib-desc::before, {{WRAPPER}} .neb-banner-animation6 .neb-banner-ib-desc::before' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'content_typhography',
				'selector' => '{{WRAPPER}} .neb-banner .neb-banner-content',
				'scheme'   => Typography::TYPOGRAPHY_3,
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'label'    => __( 'Shadow', 'neve-pro-addon' ),
				'name'     => 'description_shadow',
				'selector' => '{{WRAPPER}} .neb-banner .neb-banner-content',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'styles_of_button',
			[
				'label'     => __( 'Button', 'neve-pro-addon' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'link_switcher'    => 'yes',
					'link_url_switch!' => 'yes',
				],
			]
		);

		$this->add_control(
			'color_of_button',
			[
				'label'     => __( 'Color', 'neve-pro-addon' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Color::get_type(),
					'value' => Color::COLOR_3,
				],
				'selectors' => [
					'{{WRAPPER}} .neb-banner .neb-banner-link' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_color_of_button',
			[
				'label'     => __( 'Hover Color', 'neve-pro-addon' ),
				'type'      => Controls_Manager::COLOR,
				'scheme'    => [
					'type'  => Color::get_type(),
					'value' => Color::COLOR_3,
				],
				'selectors' => [
					'{{WRAPPER}} .neb-banner .neb-banner-link:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typhography',
				'scheme'   => Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} .neb-banner .neb-banner-link',
			]
		);

		$this->add_control(
			'backcolor_of_button',
			[
				'label'     => __( 'Background Color', 'neve-pro-addon' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .neb-banner .neb-banner-link' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'hover_backcolor_of_button',
			[
				'label'     => __( 'Hover Background Color', 'neve-pro-addon' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .neb-banner .neb-banner-link:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'button_border',
				'selector' => '{{WRAPPER}} .neb-banner .neb-banner-link',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label'      => __( 'Border Radius', 'neve-pro-addon' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .neb-banner .neb-banner-link' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'label'    => __( 'Shadow', 'neve-pro-addon' ),
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} .neb-banner .neb-banner-link',
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => __( 'Padding', 'neve-pro-addon' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .neb-banner .neb-banner-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'container_style',
			[
				'label' => __( 'Container', 'neve-pro-addon' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'border',
				'selector' => '{{WRAPPER}} .neb-banner',
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label'      => __( 'Border Radius', 'neve-pro-addon' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors'  => [
					'{{WRAPPER}} .neb-banner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'shadow',
				'selector' => '{{WRAPPER}} .neb-banner',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget.
	 */
	protected function render() {

		$settings = $this->get_settings_for_display();

		$this->add_inline_editing_attributes( 'title' );
		$this->add_inline_editing_attributes( 'description', 'advanced' );

		$title_tag  = $settings['title_tag'];
		$title      = $settings['title'];
		$full_title = '<' . $title_tag . ' class="neb-banner-ib-title ult-responsive neb-banner-title"><div ' . $this->get_render_attribute_string( 'title' ) . '>' . $title . '</div></' . $title_tag . '>';

		$link = 'yes' === $settings['image_link_switcher'] ? $settings['image_custom_link']['url'] : get_permalink( $settings['image_existing_page_link'] );

		$link_title = $settings['link_url_switch'] === 'yes' ? $settings['link_title'] : '';

		$open_new_tab    = $settings['image_link_open_new_tab'] === 'yes' ? ' target="_blank"' : '';
		$nofollow_link   = $settings['image_link_add_nofollow'] === 'yes' ? ' rel="nofollow"' : '';
		$full_link       = '<a class="neb-banner-ib-link" href="' . $link . '" title="' . $link_title . '" ' . $open_new_tab . ' ' . $nofollow_link . '></a>';
		$animation_class = 'neb-banner-' . $settings['image_animation'];
		$hover_class     = ' ' . $settings['hover_effect'];
		$extra_class     = ! empty( $settings['extra_class'] ) ? ' ' . $settings['extra_class'] : '';
		$active          = $settings['active'] === 'yes' ? ' active' : '';
		$full_class      = $animation_class . $hover_class . $extra_class . $active;
		$min_size        = $settings['min_range'] . 'px';
		$max_size        = $settings['max_range'] . 'px';

		$banner_url = 'url' === $settings['link_selection'] ? $settings['link']['url'] : get_permalink( $settings['existing_link'] );

		$alt = esc_attr( Control_Media::get_image_alt( $settings['image'] ) );

		echo '<div class="neb-banner" id="neb-banner-' . esc_attr( $this->get_id() ) . '">';
		echo '<div class="neb-banner-ib ' . esc_attr( $full_class ) . ' neb-banner-min-height">';
		if ( ! empty( $settings['image']['url'] ) ) {
			if ( $settings['height'] === 'custom' ) {
				echo '<div class="neb-banner-img-wrap">';
			}
			echo '<img class="neb-banner-ib-img" alt="' . esc_attr( $alt ) . '" src="' . esc_url( $settings['image']['url'] ) . '">';
			if ( $settings['height'] === 'custom' ) {
				echo '</div>';
			}
		}

		echo '<div class="neb-banner-ib-desc">';
		echo ( $full_title ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( ! empty( $settings['description'] ) ) {
			echo '<div class="neb-banner-ib-content neb-banner-content">';
			echo '<div ' . $this->get_render_attribute_string( 'description' ) . '>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo wp_kses_post( $settings['description'] );
			echo '</div>';
			echo '</div>';
		}

		if ( 'yes' === $settings['link_switcher'] && ! empty( $settings['more_text'] ) ) {
			echo '<div class ="neb-banner-read-more">';
			echo '<a class = "neb-banner-link"';
			if ( ! empty( $banner_url ) ) {
				echo 'href="' . esc_url( $banner_url ) . '"';
			}
			if ( ! empty( $settings['link']['is_external'] ) ) {
				echo 'target="_blank"';
			}
			if ( ! empty( $settings['link']['nofollow'] ) ) {
				echo 'rel="nofollow"';
			}
			echo '>';
			echo esc_html( $settings['more_text'] );
			echo '</a>';
			echo '</div>';
		}
		echo '</div>';

		if ( $settings['link_url_switch'] === 'yes' && ( ! empty( $settings['image_custom_link']['url'] ) || ! empty( $settings['image_existing_page_link'] ) ) ) {
			echo ( $full_link );//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		echo '</div>';
		if ( $settings['responsive_switcher'] === 'yes' ) {
			echo '<style>';
			echo '@media( min-width: ' . esc_attr( $min_size ) . ' ) and (max-width: ' . esc_attr( $max_size ) . ') {';
			echo '#neb-banner-' . esc_attr( $this->get_id() ) . ' .neb-banner-ib-content {';
			echo 'display: none;';
			echo '}';
			echo '}';
			echo '</style>';
		}
		echo '</div>';
	}

	/**
	 * Get all posts.
	 *
	 * @return array
	 */
	private function get_all_post() {
		$options        = array();
		$posts_settings = apply_filters(
			'neb_banner_posts_options',
			array(
				'posts_per_page' => - 1,
				'post_type'      => array( 'page', 'post' ),
				'post_status'    => 'publish',
			)
		);
		$all_posts      = get_posts( $posts_settings );
		if ( ! empty( $all_posts ) ) {
			foreach ( $all_posts as $post ) {
				$post_title = __( 'Post', 'neve-pro-addon' ) . ' ' . $post->ID;
				if ( ! empty( $post->post_title ) ) {
					$post_title = ( mb_strlen( $post->post_title, 'UTF-8' ) > 20 ? mb_substr( $post->post_title, 0, 20, 'UTF-8' ) . '...' : $post->post_title );
				}
				$options[ $post->ID ] = $post_title;
			}
		}

		return $options;
	}
}
