<?php

namespace TailorFoundation;

class GridItem extends Override {

    public function init() {
        add_action('tailor_shortcode_html_attributes', [$this, 'add_foundation_classes'], 10, 3);
    }

    public function add_foundation_classes($html_atts, $atts, $tag) {
        if ($tag == 'tailor_grid_item') {
            $html_atts['class'][] = 'column';
        }
        return $html_atts;
    }
}

GridItem::get_instance()->init();
