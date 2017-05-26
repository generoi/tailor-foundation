<?php

namespace TailorFoundation;

class Row extends Override {

    public function init() {
        add_action('tailor_element_register_controls', [$this, 'add_element_controls']);
        add_action('tailor_shortcode_html_attributes', [$this, 'add_foundation_classes'], 10, 3);
    }

    public function add_element_controls($element) {
        if ($element->tag != 'tailor_row' && $element->tag != 'tailor_grid') {
            return;
        }

        $this->remove_controls($element, [
            'horizontal_alignment',
            'vertical_alignment',
            'collapse',
            'min_column_height',
            'column_spacing',
        ]);
        $this->remove_settings($element, [
            'horizontal_alignment',
            'vertical_alignment',
            // 'collapse', shortcode-row.php depends on it, the control is hidden though.
            'min_column_height',
            'column_spacing',
        ]);

        $setting = [
            'sanitize_callback' => 'tailor_sanitize_text',
        ];

        $element->add_setting('row_horizontal_align', $setting);
        $element->add_setting('row_vertical_align', $setting);
        $element->add_setting('row_unstack', $setting);
        $element->add_setting('row_collapse', $setting);
        $element->add_setting('row_uncollapse', $setting);
        $element->add_setting('row_grid', $setting);
        $element->add_setting('row_grid_mobile', $setting);
        $element->add_setting('row_grid_tablet', $setting);

        $element->add_control('row_horizontal_align', [
            'type' => 'button-group',
            'label' => __('Horizontal alignment', 'tailor-foundation'),
            'section' => 'general',
            'choices' => [
                'left' => '<i class="tailor-icon tailor-align-left"></i>',
                'center' => '<i class="tailor-icon tailor-align-center"></i>',
                'right' => '<i class="tailor-icon tailor-align-right"></i>',
                'justify' => '<i class="tailor-icon tailor-align-justify"></i>',
            ],
            'priority' => 21,
        ]);
        $element->add_control('row_vertical_align', [
            'type' => 'button-group',
            'label' => __('Vertical alignment', 'tailor-foundation'),
            'section' => 'general',
            'choices' => [
                'top' => '<i class="tailor-icon tailor-align-top"></i>',
                'middle' => '<i class="tailor-icon tailor-align-middle"></i>',
                'bottom' => '<i class="tailor-icon tailor-align-bottom"></i>',
            ],
            'priority' => 22,
        ]);
        $element->add_control('row_unstack', [
            'type' => 'select-multi',
            'label' => __('Unstack', 'tailor-foundation'),
            'description' => __('Stack all columns in the row by default, and then unstack them on a larger screen size, making each one equal-width.', 'tailor-foundation'),
            'section' => 'general',
            'choices' => [
                'medium' => __('Tablet', 'tailor-foundation'),
                'large' => __('Desktop', 'tailor-foundation'),
            ],
            'priority' => 23,
        ]);
        $element->add_control('row_collapse', [
            'type' => 'select-multi',
            'label' => __('Collapse from', 'tailor-foundation'),
            'description' => __('Remove column gutters from this viewport up.', 'tailor-foundation'),
            'section' => 'general',
            'choices' => [
                'small' => __('Mobile', 'tailor-foundation'),
                'medium' => __('Tablet', 'tailor-foundation'),
                'large' => __('Desktop', 'tailor-foundation'),
            ],
            'priority' => 23,
        ]);
        $element->add_control('row_uncollapse', [
            'type' => 'select-multi',
            'label' => __('Uncollapse from', 'tailor-foundation'),
            'description' => __('Add back column gutters from this viewport up.', 'tailor-foundation'),
            'section' => 'general',
            'choices' => [
                'medium' => __('Tablet', 'tailor-foundation'),
                'large' => __('Desktop', 'tailor-foundation'),
            ],
            'priority' => 23,
        ]);
        $element->add_control('row_grid', [
            'type' => 'select',
            'label' => __('Force grid', 'tailor-foundation'),
            'description' => __('Ignore any column sizes and force a grid', 'tailor-foundation'),
            'section' => 'general',
            'choices' => ['', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
            'priority' => 24,
        ]);
    }

    public function add_foundation_classes($html_atts, $atts, $tag) {
        if ($tag == 'tailor_row' || $tag == 'tailor_grid') {
            $html_atts['class'][] = 'row';
            if (!empty($atts['row_vertical_align'])) {
                $html_atts['class'][] = 'align-' . $atts['row_vertical_align'];
            }
            if (!empty($atts['row_horizontal_align'])) {
                $html_atts['class'][] = 'align-' . $atts['row_horizontal_align'];
            }
            if (!empty($atts['row_collapse'])) {
                $html_atts['class'][] = $atts['row_collapse'] . '-collapse';
            }
            if (!empty($atts['row_uncollapse'])) {
                $html_atts['class'][] = $atts['row_uncollapse'] . '-uncollapse';
            }
            if (!empty($atts['row_grid'])) {
                $html_atts['class'][] = 'large-up-' . $atts['row_grid'];
            }
            if (!empty($atts['row_grid_tablet'])) {
                $html_atts['class'][] = 'medium-up-' . $atts['row_grid_tablet'];
            }
            if (!empty($atts['row_grid_mobile'])) {
                $html_atts['class'][] = 'small-up-' . $atts['row_grid_mobile'];
            }
        }
        return $html_atts;
    }
}

Row::get_instance()->init();
