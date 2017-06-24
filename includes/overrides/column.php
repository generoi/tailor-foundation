<?php

namespace TailorFoundation;

class Column extends Override {

    public function init() {
        add_action('tailor_element_css_rule_sets', [$this, 'remove_width_css'], 10, 3);
        add_action('tailor_element_register_controls', [$this, 'add_element_controls']);
        add_action('tailor_shortcode_html_attributes', [$this, 'add_foundation_classes'], 10, 3);
        add_action('tailor_canvas_enqueue_scripts', [$this, 'add_canvas_scripts']);
    }

    protected function get_column_count($columns) {
        if ($columns == 'auto') {
            return NULL;
        }
        return str_replace('cols_', '', $columns);
    }

    /**
     * For some reason we cant save these as numeric or Tailor wont save them
     * corretly when using a `select`. Internally store them as strings
     * prefixed by `cols_` which is the stripped anytime used.
     */
    protected function get_column_choices() {
        $column_choices['auto'] = __('Auto', 'tailor-foundation');
        foreach (range(1, 12) as $cols) {
            $column_choices["cols_$cols"] = sprintf(__('%s/12', 'tailor-foundation'), $cols);
        }
        return $column_choices;
    }

    protected function get_xy_column_choices() {
        $column_choices['full'] = __('Entire row', 'tailor-foundation');
        $column_choices['auto'] = __('Auto (whatever is left)', 'tailor-foundation');
        $column_choices['shrink'] = __("Shrink (only what's needed)", 'tailor-foundation');
        foreach (range(1, 12) as $cols) {
            $column_choices["cols_$cols"] = sprintf(__('%s/12', 'tailor-foundation'), $cols);
        }
        return $column_choices;
    }

    public function add_canvas_scripts() {
        if (apply_filters('tailor-foundation/grid', 'flex-grid') == 'flex-grid') {
            $js_url = plugins_url('column.canvas.js', __FILE__);
        } else {
            $js_url = plugins_url('cell.canvas.js', __FILE__);
        }
        wp_enqueue_script('tailor-foundation/canvas/column', $js_url, ['tailor-canvas'], $this->get_version(), true);
    }

    public function add_element_controls($element) {
        if ($element->tag != 'tailor_column') {
            return;
        }

        $setting = [
            'sanitize_callback' => 'tailor_sanitize_text',
        ];
        $setting_columns = [
            'sanitize_callback' => 'tailor_sanitize_text',
            'refresh' => ['method' => 'js'],
        ];

        $priority = 21;

        $column_align = [
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

        // Flex Grid
        $columns = [
            'type' => 'select',
            'label' => __('Column count', 'tailor-foundation'),
            'section' => 'general',
            'choices' => $this->get_column_choices(),
            'priority' => $priority++,
        ];
        // XY-Grid
        $columns_xy = [
            'type' => 'select',
            'label' => __('Column size', 'tailor-foundation'),
            'section' => 'general',
            'choices' => $this->get_xy_column_choices(),
            'priority' => $priority++,
        ];
        $column_offset = [
            'type' => 'select',
            'label' => __('Offset columns', 'tailor-foundation'),
            'section' => 'general',
            'choices' => ['', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
            'priority' => $priority++,
        ];
        $column_order = [
            'type' => 'select',
            'label' => __('Source order', 'tailor-foundation'),
            'description' => __('Rearrange columns on different screen sizes.', 'tailor-foundation'),
            'section' => 'general',
            'choices' => ['', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
            'priority' => $priority++,
        ];
        // Flex Grid
        $column_shrink = [
            'type' => 'switch',
            'label' => __('Shrink'),
            'description' => __('Only take up the horizontal space needed.', 'tailor-foundation'),
            'choices' => ['1' => __('Only take up the horizontal space needed.', 'tailor-foundation')],
            'section' => 'general',
            'priority' => $priority++,
        ];
        // Flex Grid
        $column_expand = [
            'type' => 'select-multi',
            'label' => __('Expand', 'tailor-foundation'),
            'description' => __('By setting small column count to 12, desktop to auto and this active, the column will stack on mobile and fit dynamically on desktop.', 'tailor-foundation'),
            'section' => 'general',
            'choices' => [
                'medium' => __('Tablet and up', 'tailor-foundation'),
                'large' => __('Desktop', 'tailor-foundation'),
            ],
            'priority' => $priority,
        ];

        if (apply_filters('tailor-foundation/grid', 'flex-grid') == 'flex-grid') {
            $element->add_setting('columns', $setting_columns + ['default' => 'auto']);
            $element->add_setting('columns_tablet', $setting_columns + ['default' => 'auto']);
            $element->add_setting('columns_mobile', $setting_columns);
            $element->add_setting('column_align', $setting);
            $element->add_setting('column_shrink', $setting);
            $element->add_setting('column_expand', $setting);
            $element->add_setting('column_offset', $setting);
            $element->add_setting('column_offset_tablet', $setting);
            $element->add_setting('column_offset_mobile', $setting);
            $element->add_setting('column_order', $setting);
            $element->add_setting('column_order_tablet', $setting);
            $element->add_setting('column_order_mobile', $setting);

            $element->add_control('column_align', $column_align);
            $element->add_control('columns', $columns);
            $element->add_control('column_offset', $column_offset);
            $element->add_control('column_order', $column_order);
            $element->add_control('column_shrink', $column_shrink);
            $element->add_control('column_expand', $column_expand);
        } else {
            $element->add_setting('columns', $setting_columns + ['default' => 'auto']);
            $element->add_setting('columns_tablet', $setting_columns + ['default' => 'full']);
            $element->add_setting('columns_mobile', $setting_columns + ['default' => 'full']);
            $element->add_setting('column_align', $setting);
            // $element->add_setting('column_shrink', $setting);
            // $element->add_setting('column_expand', $setting);
            $element->add_setting('column_offset', $setting);
            $element->add_setting('column_offset_tablet', $setting);
            $element->add_setting('column_offset_mobile', $setting);
            $element->add_setting('column_order', $setting);
            $element->add_setting('column_order_tablet', $setting);
            $element->add_setting('column_order_mobile', $setting);

            $element->add_control('column_align', $column_align);
            $element->add_control('columns', $columns_xy);
            $element->add_control('column_offset', $column_offset);
            $element->add_control('column_order', $column_order);
            // $element->add_control('column_shrink', $column_shrink);
            // $element->add_control('column_expand', $column_expand);
        }
    }

    public function add_foundation_classes($html_atts, $atts, $tag) {
        if ($tag == 'tailor_column') {

            if (apply_filters('tailor-foundation/grid', 'flex-grid') == 'flex-grid') {
                $html_atts['class'][] = 'column';

                $large_columns = !empty($atts['columns']) ? $this->get_column_count($atts['columns']) : false;
                $medium_columns = !empty($atts['columns_tablet']) ? $this->get_column_count($atts['columns_tablet']) : false;
                $small_columns = !empty($atts['columns_mobile']) ? $this->get_column_count($atts['columns_mobile']) : false;
                if ($large_columns) {
                    $html_atts['class'][] = 'large-' . $large_columns;
                }
                if ($medium_columns) {
                    $html_atts['class'][] = 'medium-' . $medium_columns;
                }
                if ($small_columns) {
                    $html_atts['class'][] = 'small-' . $small_columns;
                }
                if (!empty($atts['column_shrink'])) {
                    $html_atts['class'][] = 'shrink';
                }
                foreach ($this->multi_classes('column_expand', $atts) as $class) {
                    $html_atts['class'][] = "$class-expand";
                }
            } else {
                $html_atts['class'][] = 'cell';
                foreach ($this->responsive_classes('columns', $atts) as $breakpoint => $column) {
                    switch ($column) {
                        case 'full':
                            $html_atts['class'][] = "$breakpoint-12";
                            break;
                        case 'auto':
                        case 'shrink':
                            $html_atts['class'][] = ($breakpoint != 'small') ? "$breakpoint-$column" : $column;
                            break;
                        default:
                            $html_atts['class'][] = $breakpoint . '-' . str_replace('cols_', '', $column);
                            break;
                    }
                }
            }

            foreach($this->responsive_classes('column_offset', $atts) as $breakpoint => $offset) {
                $html_atts['class'][] = "$breakpoint-offset-$offset";
            }
            foreach($this->responsive_classes('column_order', $atts) as $breakpoint => $order) {
                $html_atts['class'][] = "$breakpoint-order-$order";
            }
            if (!empty($atts['column_align'])) {
                $html_atts['class'][] = 'align-self-' . $atts['column_align'];
            }
        }
        return $html_atts;
    }

    public function remove_width_css($css_rule_set, $atts, $element) {
        if ($element->tag == 'tailor_column') {
            $this->remove_css_rules($css_rule_set, [
                'width',
            ]);
        }
        return $css_rule_set;
    }
}

Column::get_instance()->init();
