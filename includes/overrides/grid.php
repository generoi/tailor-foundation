<?php

namespace TailorFoundation;

class Grid extends Override {

    public function init() {
        // row.php defines settings and controls.
        add_action('tailor_element_register_controls', [$this, 'add_element_controls'], 11);
        add_action('tailor_shortcode_html_attributes', [$this, 'remove_classes'], 10, 3);
    }

    public function remove_classes($html_atts, $atts, $tag) {
        $this->remove_html_classes($html_atts['class'], ['tailor-grid--*']);
        return $html_atts;
    }

    public function add_element_controls($element) {
        if ($element->tag != 'tailor_grid') {
            return;
        }

        $this->remove_controls($element, [
            'items_per_row',
            'min_item_height',
            'hidden',
            'collapse',
        ]);
        $this->remove_settings($element, [
            // 'items_per_row',
            'min_item_height',
            'hidden',
        ]);

        // Override what row.php has already defined.
        $grid_control = $element->get_control('row_grid');
        $grid_control->label = __('Items per row', 'tailor-foundation');
        $grid_control->description = '';
        $grid_control->priority = 20;
    }
}

Grid::get_instance()->init();
