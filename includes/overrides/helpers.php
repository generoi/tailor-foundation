<?php

namespace TailorFoundation;

class Helpers extends Override {

    public function get_breakpoints() {
        return [
            '' => '',
            'small' => __('Mobile', 'tailor-foundation'),
            'medium' => __('Tablet', 'tailor-foundation'),
            'large' => __('Desktop', 'tailor-foundation'),
        ];
    }

    public function init() {
        add_action('tailor_element_register_controls', [$this, 'add_element_controls']);
        add_action('tailor_shortcode_html_attributes', [$this, 'add_foundation_classes'], 11, 3);
        add_action('tailor_canvas_enqueue_scripts', [$this, 'add_canvas_scripts']);
    }

    public function add_canvas_scripts() {
        $js_url = plugins_url('helpers.canvas.js', __FILE__);
        wp_enqueue_script('tailor-foundation/canvas/helpers', $js_url, ['tailor-canvas'], $this->get_version(), true);
    }

    public function add_element_controls($element) {
        if ($hidden = $element->get_control('hidden')) {
            $this->add_visibility_controls($element);
            $element->remove_control('hidden');
            $element->remove_setting('hidden');
        }
    }

    public function add_visibility_controls($element) {
        $hidden = $element->get_control('hidden');
        $setting_basic = [
            'sanitize_callback' => 'tailor_sanitize_text',
        ];

        $element->add_section('visibility', [
            'title' => __('Visibility', 'tailor-foundation'),
            'priority' => 40,
        ]);

        $element->add_setting('show_for', $setting_basic);
        $element->add_setting('show_for_only', $setting_basic);
        $element->add_setting('hide_for', $setting_basic);
        $element->add_setting('hide_for_only', $setting_basic);

        $element->add_control('show_for', [
            'type' => 'select',
            'label' => __('Show on viewports larger than', 'tailor-foundation'),
            'section' => 'visibility',
            'choices' => $this->get_breakpoints(),
            'priority' => $hidden->priority,
        ]);
        $element->add_control('show_for_only', [
            'label' => __('Show only on specified viewport'),
            'type' => 'switch',
            'choices' => ['1' => __('Show only on specified viewport')],
            'section' => 'visibility',
            'priority' => $hidden->priority + 1,
        ]);

        $element->add_control('hide_for', [
            'type' => 'select',
            'label' => __('Hide on viewports larger than', 'tailor-foundation'),
            'section' => 'visibility',
            'choices' => $this->get_breakpoints(),
            'priority' => $hidden->priority + 2,
        ]);
        $element->add_control('hide_for_only', [
            'label' => __('Hide only on specified viewport'),
            'type' => 'switch',
            'choices' => ['1' => __('Show only on specified viewport')],
            'section' => 'visibility',
            'priority' => $hidden->priority + 3,
        ]);
    }

    public function add_foundation_classes($html_atts, $atts, $tag) {
        // Visibility classes.
        foreach (['show', 'hide'] as $type) {
            if (!empty($atts["${type}_for"])) {
                $prefix = $atts["${type}_for"];
                $suffix = !empty($atts["${type}_for_only"]) ? '-only' : '';
                if ($prefix == 'small' && empty($suffix)) {
                    if ($type == 'hide') {
                        $html_atts['class'][] = 'hide';
                    }
                }
                else {
                    $html_atts['class'][] = "${type}-for-${prefix}${suffix}";
                }
            }
        }

        $screen_sizes = [
            '' => 'large',
            'mobile' => 'small',
            'tablet' => 'medium',
        ];
        foreach ($screen_sizes as $screen_size => $css_class) {
            $setting_postfix = empty($screen_size) ? '' : "_{$screen_size}";
            $prefix = ($screen_size == 'mobile') ? '' : "{$css_class}-";
            if (!empty($atts["horizontal_alignment{$setting_postfix}"])) {
                $alignment = $atts["horizontal_alignment{$setting_postfix}"];
                $html_atts['class'][] = "${prefix}text-{$alignment}";
            }

            if (!empty($atts["vertical_alignment{$setting_postfix}"])) {
                $alignment = $atts["vertical_alignment{$setting_postfix}" ];
                // $html_atts['class'][] = "${prefix}text-{$alignment}";
                // $html_atts['class'][] = "${prefix}align-{$alignment}{$class_postfix}";
            }
        }

        // Strip away core classes.
        $this->remove_html_classes($html_atts['class'], ['u-text-*', 'u-align-*']);

        return $html_atts;
    }
}

Helpers::get_instance()->init();
