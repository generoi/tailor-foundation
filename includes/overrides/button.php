<?php

namespace TailorFoundation;

class Button extends Override {

    public function init() {
        add_action('tailor_element_css_rule_sets', [$this, 'remove_css'], 10, 3);
        add_action('tailor_element_register_controls', [$this, 'add_element_controls']);
        add_action('tailor_shortcode_html_attributes', [$this, 'add_foundation_classes'], 10, 3);
        add_filter('tailor_shortcode_html', [$this, 'shortcode_html'], 10, 7);
    }

    public function add_element_controls($element) {
        if ($element->tag != 'tailor_button') {
            return;
        }
        $this->remove_settings($element, [
            'horizontal_alignment',
            'size',
        ]);
        $setting = [
            'sanitize_callback' => 'tailor_sanitize_text',
        ];
        $element->add_setting('horizontal_alignment', $setting);
        $element->add_setting('size', $setting);

        $size_control = $element->get_control('size');
        $size_setting = $element->get_setting('size');
        $size_setting->default = 'default';
        $size_control->choices = [
            'default' => __('Default', 'tailor-foundation'),
            'tiny'    => __('Tiny', 'tailor-foundation'),
            'small'   => __('Small', 'tailor-foundation'),
            'medium'  => __('Medium', 'tailor-foundation'),
            'large'   => __('Large', 'tailor-foundation'),
        ];
    }

    public function add_foundation_classes($html_atts, $atts, $tag) {
        if ($tag == 'tailor_button') {
            $html_atts['class'][] = 'button';
            if (!empty($atts['style']) && $atts['style'] != 'default') {
                $html_atts['class'][] = $atts['style'];
            }
            if (!empty($atts['size']) && $atts['size'] != 'default') {
                $html_atts['class'][] = $atts['size'];
            }
            if (!empty($atts['horizontal_alignment']) && $atts['horizontal_alignment'] == 'justify') {
                $html_atts['class'][] = 'expanded';
            }
            $this->remove_html_classes($html_atts['class'], ['tailor-button--*', '*-text-*']);
        }
        return $html_atts;
    }

    public function shortcode_html($html, $outer_html, $inner_html, $html_atts, $atts, $content, $tag) {
        if ($tag == 'tailor_button') {
            $href = '';
            if (!empty($atts['href'])) {
                $href = 'href="' . esc_url($atts['href']) . '"';
                $href .= !empty($atts['target']) ? ' target="_blank"' : '';
            }

            $html = "<a {$html_atts} {$href}>%s</a>";
            $html = sprintf($html, $content);
            if (!empty($atts['horizontal_alignment']) && $atts['horizontal_alignment'] != 'justify') {
                $html = '<div class="text-' . $atts['horizontal_alignment'] . '">' . $html . '</div>';
            }
        }
        return $html;
    }

    public function remove_css($css_rule_set, $atts, $element) {
        if ($element->tag == 'tailor_button') {
            $this->remove_css_rules($css_rule_set, [
                'border_color_hover',
                'background_color_hover',
                'color_hover',
            ]);
        }
        return $css_rule_set;
    }
}

Button::get_instance()->init();
