<?php

function tailor_foundation_shortcode_image_element($atts, $content = null, $tag) {
    $default_atts = apply_filters('tailor_shortcode_default_atts_' . $tag, []);
    $atts = shortcode_atts($default_atts, $atts, $tag);
    $html_atts = [
        'id'    => empty($atts['id']) ? null : $atts['id'],
        'class' => explode(' ', "tailor-element tailor-image {$atts['class']}"),
        'data'  => [],
    ];

    // External images
    if ($atts['type'] == 'external' && !empty($atts['src'])) {
        $content = '<img src="' . esc_url($atts['src']) . '"/>';
    }

    // Media Library images
    else if ($atts['type'] != 'external' && is_numeric($atts['image'])) {
        $attachment_id = $atts['image'];
        $content = wp_get_attachment_image($attachment_id, $atts['image_size']);
        if ($atts['image_link'] != 'none') {
            if (in_array($atts['image_link'], ['file', 'lightbox'])) {
                $href = wp_get_attachment_url($attachment_id);
            }
            else {
                $href = get_attachment_link($attachment_id);
            }

            if ($atts['image_link'] == 'lightbox') {
                $content = '<a class="tailor-image__link is-lightbox-image" href="' . esc_url($href) . '">' . $content . '</a>';
            } else {
                $content = '<a href="' . esc_url($href) . '">' . $content . '</a>';
            }
        }
    }
    else {
        $content = sprintf(
            '<p class="tailor-notification tailor-notification--warning">%s</p>',
            __( 'Please select an image to display', 'tailor-foundation' )
        );
    }

    $html_atts = apply_filters('tailor_shortcode_html_attributes', $html_atts, $atts, $tag);
    $html_atts['class'] = implode(' ', (array) $html_atts['class']);
    $html_atts = tailor_get_attributes($html_atts);

    $outer_html = "<div {$html_atts}>%s</div>";
    $inner_html = '%s';
    $content = do_shortcode($content);
    $html = sprintf($outer_html, sprintf($inner_html, $content));

    $html = apply_filters('tailor_shortcode_html', $html, $outer_html, $inner_html, $html_atts, $atts, $content, $tag);
    return $html;
}

add_shortcode('tailor_foundation_image', 'tailor_foundation_shortcode_image_element');
