<?php

namespace TailorFoundation;

class Styles extends Override {

    public function init() {
        add_action('tailor_element_register_controls', [$this, 'remove_element_controls']);
        add_action('tailor_element_register_controls', [$this, 'add_element_controls']);
        add_action('tailor_shortcode_html_attributes', [$this, 'add_foundation_classes'], 10, 3);
    }

    public function remove_element_controls($element) {
        $remove = [
            // General
            'max_width',
            'max_width_tablet',
            'max_width_mobile',
            'min_height',
            'min_height_tablet',
            'min_height_mobile',
            'vertical_alignment',
            'vertical_alignment_tablet',
            'vertical_alignment_mobile',

            // Colors
            'color',
            'link_color',
            'link_color_hover',
            'heading_color',
            'background_color',
            'border_color',

            // Attributes
            'padding',
            'padding_tablet',
            'padding_mobile',
            'margin',
            'margin_tablet',
            'margin_mobile',
            'border_style',
            'border_width',
            'border_width_tablet',
            'border_width_mobile',
            'border_radius',
            'shadow',
            'parallax',
            // 'background_image',
            'background_repeat',
            'background_position',
            // 'background_size',
            'background_attachment',
        ];

        $this->remove_controls($element, $remove);
        $this->remove_settings($element, $remove);
        // Always remove the colors section.
        $element->remove_section('colors');
    }

    public function add_element_controls($element) {
        $setting = [
            'sanitize_callback' => 'tailor_sanitize_text',
        ];
    }

    public function add_foundation_classes($html_atts, $atts, $tag) {
        return $html_atts;
    }
}

Styles::get_instance()->init();
