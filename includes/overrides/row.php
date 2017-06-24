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

        $priority = 21;

        $row_horizontal_align = [
            'type' => 'button-group',
            'label' => __('Horizontal alignment', 'tailor-foundation'),
            'section' => 'general',
            'choices' => [
                'left' => '<i class="tailor-icon tailor-align-left"></i>',
                'center' => '<i class="tailor-icon tailor-align-center"></i>',
                'right' => '<i class="tailor-icon tailor-align-right"></i>',
                'justify' => '<i class="tailor-icon tailor-align-justify"></i>',
            ],
            'priority' => $priority++,
        ];
        $row_vertical_align = [
            'type' => 'button-group',
            'label' => __('Vertical alignment', 'tailor-foundation'),
            'section' => 'general',
            'choices' => [
                'top' => '<i class="tailor-icon tailor-align-top"></i>',
                'middle' => '<i class="tailor-icon tailor-align-middle"></i>',
                'bottom' => '<i class="tailor-icon tailor-align-bottom"></i>',
            ],
            'priority' => $priority++,
        ];
        // XY-Grid
        $row_gutter = [
            'type' => 'select-multi',
            'label' => __('Gutter', 'tailor-foundation'),
            'description' => __('What kind of spacing should exist between the cells. Margins are applied only between cells, while paddings are applied on all sides.', 'tailor-foundation'),
            'section' => 'general',
            'choices' => [
                'grid-margin-x' => __('Horizontal margin', 'tailor-foundation'),
                'grid-margin-y' => __('Vertical margin', 'tailor-foundation'),
                'grid-padding-x' => __('Horizontal padding', 'tailor-foundation'),
                'grid-padding-y' => __('Vertical padding', 'tailor-foundation'),
            ],
            'priority' => $priority++,
        ];
        // Flex Grid
        $row_unstack = [
            'type' => 'select-multi',
            'label' => __('Unstack', 'tailor-foundation'),
            'description' => __('Stack all columns in the row by default, and then unstack them on a larger screen size, making each one equal-width.', 'tailor-foundation'),
            'section' => 'general',
            'choices' => [
                'medium' => __('Tablet', 'tailor-foundation'),
                'large' => __('Desktop', 'tailor-foundation'),
            ],
            'priority' => $priority++,
        ];
        // Flex Grid.
        $row_collapse = [
            'type' => 'select-multi',
            'label' => __('Collapse from', 'tailor-foundation'),
            'description' => __('Remove column gutters from this viewport up.', 'tailor-foundation'),
            'section' => 'general',
            'choices' => [
                'small' => __('Mobile', 'tailor-foundation'),
                'medium' => __('Tablet', 'tailor-foundation'),
                'large' => __('Desktop', 'tailor-foundation'),
            ],
            'priority' => $priority++,
        ];
        // XY-Grid
        $row_xy_collapse = [
            'type' => 'select-multi',
            'label' => __('Collapse', 'tailor-foundation'),
            'description' => __('Remove column gutters from this viewport up.', 'tailor-foundation'),
            'section' => 'general',
            'choices' => [
                'small-margin' => __('Mobile margin', 'tailor-foundation'),
                'small-padding' => __('Mobile padding', 'tailor-foundation'),
                'medium-margin' => __('Tablet margin', 'tailor-foundation'),
                'medium-padding' => __('Tablet padding', 'tailor-foundation'),
                'large-margin' => __('Desktop margin', 'tailor-foundation'),
                'large-padding' => __('Desktop padding', 'tailor-foundation'),
            ],
            'priority' => $priority++,
        ];
        // Flex Grid.
        $row_uncollapse = [
            'type' => 'select-multi',
            'label' => __('Uncollapse from', 'tailor-foundation'),
            'description' => __('Add back column gutters from this viewport up.', 'tailor-foundation'),
            'section' => 'general',
            'choices' => [
                'medium' => __('Tablet', 'tailor-foundation'),
                'large' => __('Desktop', 'tailor-foundation'),
            ],
            'priority' => $priority++,
        ];

        $row_grid = [
            'type' => 'select',
            'label' => __('Force grid', 'tailor-foundation'),
            'description' => __('Ignore any column sizes and force a grid with this many columns. Not that `auto` and `shrink` still apply.', 'tailor-foundation'),
            'section' => 'general',
            'choices' => ['', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
            'priority' => $priority++,
        ];

        if (apply_filters('tailor-foundation/grid', 'flex-grid') == 'flex-grid') {
            $element->add_setting('row_horizontal_align', $setting);
            $element->add_setting('row_vertical_align', $setting);
            $element->add_setting('row_unstack', $setting);
            $element->add_setting('row_collapse', $setting);
            $element->add_setting('row_uncollapse', $setting);
            // $element->add_setting('row_gutter', $setting);
            $element->add_setting('row_grid', $setting);
            $element->add_setting('row_grid_mobile', $setting);
            $element->add_setting('row_grid_tablet', $setting);

            $element->add_control('row_horizontal_align', $row_horizontal_align);
            $element->add_control('row_vertical_align', $row_vertical_align);
            $element->add_control('row_unstack', $row_unstack);
            $element->add_control('row_collapse', $row_collapse);
            $element->add_control('row_uncollapse', $row_uncollapse);
            $element->add_control('row_grid', $row_grid);
        } else {
            $element->add_setting('row_horizontal_align', $setting);
            $element->add_setting('row_vertical_align', $setting);
            // $element->add_setting('row_unstack', $setting);
            $element->add_setting('row_collapse', $setting);
            // $element->add_setting('row_uncollapse', $setting);
            $element->add_setting('row_gutter', $setting + ['default' => 'grid-margin-x,grid-margin-y']);
            $element->add_setting('row_grid', $setting);
            $element->add_setting('row_grid_mobile', $setting);
            $element->add_setting('row_grid_tablet', $setting);

            $element->add_control('row_horizontal_align', $row_horizontal_align);
            $element->add_control('row_vertical_align', $row_vertical_align);
            // $element->add_control('row_unstack', $row_unstack);
            $element->add_control('row_gutter', $row_gutter);
            $element->add_control('row_collapse', $row_xy_collapse);
            // $element->add_control('row_uncollapse', $row_uncollapse);
            $element->add_control('row_grid', $row_grid);
        }
    }

    public function add_foundation_classes($html_atts, $atts, $tag) {
        if ($tag == 'tailor_row' || $tag == 'tailor_grid') {

            if (apply_filters('tailor-foundation/grid', 'flex-grid') == 'flex-grid') {
                $html_atts['class'][] = 'row';
            } else {
                $html_atts['class'][] = 'grid-x';
            }

            if (!empty($atts['row_vertical_align'])) {
                $html_atts['class'][] = 'align-' . $atts['row_vertical_align'];
            }
            if (!empty($atts['row_horizontal_align'])) {
                $html_atts['class'][] = 'align-' . $atts['row_horizontal_align'];
            }

            foreach ($this->multi_classes('row_unstack', $atts) as $class) {
                $html_atts['class'][] = "$class-unstack";
            }
            foreach ($this->multi_classes('row_gutter', $atts) as $class) {
                $html_atts['class'][] = $class;
            }
            foreach ($this->multi_classes('row_collapse', $atts) as $class) {
                $html_atts['class'][] = "$class-collapse";
            }
            foreach ($this->multi_classes('row_uncollapse', $atts) as $class) {
                $html_atts['class'][] = "$class-uncollapse";
            }
            foreach($this->responsive_classes('row_grid', $atts) as $breakpoint => $grid) {
                $html_atts['class'][] = "$breakpoint-up-$grid";
            }
        }
        return $html_atts;
    }
}

Row::get_instance()->init();
