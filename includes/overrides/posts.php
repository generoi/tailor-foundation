<?php

namespace TailorFoundation;

use WP_Query;

class Posts extends Override {

    public function init() {
        // row.php defines settings and controls.
        add_action('tailor_element_register_controls', [$this, 'add_element_controls'], 11);
        add_action('tailor_shortcode_html_attributes', [$this, 'remove_classes'], 10, 3);
        add_filter('tailor_shortcode_html', [$this, 'shortcode_html'], 10, 7);
    }

    public function remove_classes($html_atts, $atts, $tag) {
        $this->remove_html_classes($html_atts['class'], ['tailor-grid--*']);
        return $html_atts;
    }

    public function add_element_controls($element) {
        if ($element->tag != 'tailor_posts') {
            return;
        }
        $setting = [
            'sanitize_callback' => 'tailor_sanitize_text',
        ];

        $this->remove_controls($element, [
            'items_spacing',
            'style',
            'meta',
            'masonry',
        ]);
        $this->remove_settings($element, [
            'item_spacing',
        ]);

        $post_types = [];
        foreach (get_post_types(['public' => true], 'objects') as $post_type) {
            $post_types[$post_type->name] = $post_type->label;
        }
        $element->add_setting('post_type', $setting);
        $element->add_control('post_type', [
            'type' => 'select-multi',
            'label' => __('Post type', 'tailor-foundation'),
            'section' => 'query',
            'choices' => $post_types,
            'priority' => 25,
        ]);

        $element->add_setting('items_per_row_tablet', $setting + ['default' => '1']);
        $element->add_setting('items_per_row_mobile', $setting + ['default' => '1']);
    }

    public function shortcode_html($html, $outer_html, $inner_html, $html_atts, $atts, $content, $tag) {
        if ($tag != 'tailor_posts') {
            return $html;
        }
        $default_atts = apply_filters('tailor_shortcode_default_atts_' . $tag, []);
        $atts = shortcode_atts($default_atts, $atts, $tag);
        $items_per_row = (string) intval($atts['items_per_row']);
        $data = [
            'slides'            =>  $items_per_row,
            'autoplay'          =>  boolval($atts['autoplay']) ? 'true' : 'false',
            'autoplay-speed'    =>  intval($atts['autoplay_speed']) ? intval($atts['autoplay_speed']) : 3000,
            'arrows'            =>  boolval($atts['arrows']) ? 'true' : 'false',
            'dots'              =>  boolval($atts['dots']) ? 'true' : 'false',
            'fade'              =>  boolval($atts['fade'] && $items_per_row == '1') ? 'true' : 'false',
        ];

        $html_atts = [
            'id'            =>  empty($atts['id']) ? null : $atts['id'],
            'class'         =>  explode(' ', "tailor-element tailor-posts tailor-posts--{$atts['style']} tailor-{$atts['layout']} tailor-{$atts['layout']}--posts {$atts['class']}"),
            'data'          =>  array_filter($data),
        ];
        $html_atts = apply_filters('tailor_shortcode_html_attributes', $html_atts, $atts, $tag);
        $html_atts['class'] = implode(' ', (array) $html_atts['class']);
        $html_atts = tailor_get_attributes($html_atts);

        $offset = intval($atts['offset']);
        $paged = get_query_var('paged') ? absint(get_query_var('paged')) : absint(get_query_var('page'));
        $posts_per_page = intval($atts['posts_per_page']);
        if ($paged > 1) {
            $offset = (($paged - 1) * $posts_per_page) + $offset;
        }

        $query_args = [
            'post_type'             =>  strpos($atts['post_type'], ',') ? explode(',', $atts['post_type']) : $atts['post_type'], // custom
            'orderby'               =>  $atts['order_by'],
            'order'                 =>  $atts['order'],
            'posts_per_page'        =>  $posts_per_page,
            'cat'                   =>  $atts['categories'],
            'offset'                =>  $offset,
            'paged'                 =>  $paged,
        ];
        if (!empty($atts['tags'])) {
            $query_args['tag__and'] = strpos($atts['tags'], ',') ? explode(',', $atts['tags']) : (array) $atts['tags'];
        }

        $q = new WP_Query($query_args);
        ob_start();
        tailor_partial('loop', $atts['layout'], [
            'q'                     => $q,
            'layout_args'           => [
                'items_per_row'         => $atts['items_per_row'],
                'masonry'               => $atts['masonry'],
                'pagination'            => $atts['pagination'],
            ],
            'entry_args'            => [
                'meta'                  => explode(',', $atts['meta']),
                'image_link'            => $atts['image_link'],
                'image_size'            => $atts['image_size'],
                'aspect_ratio'          => $atts['aspect_ratio'],
                'stretch'               => $atts['stretch'],
            ],
            'atts' => $atts, // expose all attributes.
        ]);

        $outer_html = "<div {$html_atts}>%s</div>";
        $inner_html = '%s';
        $content = ob_get_clean();
        $html = sprintf($outer_html, sprintf($inner_html, $content));

        return $html;
    }
}

Posts::get_instance()->init();
