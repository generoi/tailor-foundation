<?php

namespace TailorFoundation;

class Hero extends Override {

    public function init() {
        add_action('tailor_element_register_controls', [$this, 'add_element_controls']);
        add_action('tailor_shortcode_html_attributes', [$this, 'add_foundation_classes'], 10, 3);
    }

    public function add_element_controls($element) {
        if ($element->tag != 'tailor_hero') {
            return;
        }
        $setting = [
            'sanitize_callback' => 'tailor_sanitize_text',
        ];
        $element->add_setting('size', $setting + ['default' => 'medium']);
        $element->add_control('size', [
            'type' => 'select',
            'label' => __('Size', 'tailor-foundation'),
            'section' => 'general',
            'choices' => [
                'small'  => __('Small', 'tailor-foundation'),
                'medium' => __('Medium', 'tailor-foundation'),
                'large'  => __('Large', 'tailor-foundation'),
            ],
            'priority' => 21,
        ]);
    }

    public function add_foundation_classes($html_atts, $atts, $tag) {
        if ($tag == 'tailor_hero') {
            $html_atts['class'][] = 'callout';
            if (!empty($atts['style']) && $atts['style'] != 'default') {
                $html_atts['class'][] = $atts['style'];
            }
            if (!empty($atts['size']) && $atts['size'] != 'medium') {
                $html_atts['class'][] = $atts['size'];
            }
        }
        return $html_atts;
    }
}

Hero::get_instance()->init();
