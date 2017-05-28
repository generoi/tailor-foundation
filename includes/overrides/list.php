<?php

namespace TailorFoundation;

class TailorList extends Override {

    public function init() {
        add_action('tailor_element_register_controls', [$this, 'add_element_controls']);
        add_action('tailor_shortcode_html_attributes', [$this, 'add_foundation_classes'], 10, 3);
        add_filter('tailor_shortcode_html', [$this, 'shortcode_html'], 10, 7);
    }

    public function add_element_controls($element) {
        $setting = [
            'sanitize_callback' => 'tailor_sanitize_text',
        ];
        if ($element->tag == 'tailor_list') {
            $element->add_section('general', [
                'title' => __('General', 'tailor-foundation'),
                'priority' => 10,
            ]);
            $element->add_setting('no_bullet', $setting + ['default' => '1']);

            $element->add_control('no_bullet', [
                'type' => 'switch',
                'label' => __('Remove bullets'),
                'choices' => ['1' => __('Remove bullets', 'tailor-foundation')],
                'section' => 'general',
                'priority' => 24,
            ]);
        }
        if ($element->tag == 'tailor_list_item') {
            $graphic_control = $element->get_control('graphic_type');
            unset($graphic_control->choices['number']);
            $element->add_setting('icon_vertical_align', $setting);
            $element->add_control('icon_vertical_align', [
                'type' => 'button-group',
                'label' => __('Graphic alignment', 'tailor-foundation'),
                'section' => 'general',
                'choices' => [
                    'top' => '<i class="tailor-icon tailor-align-top"></i>',
                    'middle' => '<i class="tailor-icon tailor-align-middle"></i>',
                    'bottom' => '<i class="tailor-icon tailor-align-bottom"></i>',
                ],
                'priority' => 29,
            ]);
        }
    }

    public function add_foundation_classes($html_atts, $atts, $tag) {
        if ($tag == 'tailor_list') {
            if (!empty($atts['no_bullet'])) {
                $html_atts['class'][] = 'no-bullet';
            }
        }
        return $html_atts;
    }

    public function shortcode_html($html, $outer_html, $inner_html, $html_atts, $atts, $content, $tag) {
        if ($tag == 'tailor_list') {
            $outer_html = "<ul {$html_atts}>%s</div>";
            $html = sprintf($outer_html, sprintf($inner_html, $content));
        }
        if ($tag == 'tailor_list_item') {
            $title = !empty($atts['title']) ? '<h3 class="tailor-list__title">' . esc_html((string) $atts['title']) . '</h3>' : '';
            $graphic_type = empty($atts['graphic_type']) ? 'icon' : $atts['graphic_type'];
            $graphic = '';

            if ('image' == $graphic_type) {
                if (is_numeric($atts['image'])) {
                    $background_image_info = wp_get_attachment_image_src($atts['image'], 'full');
                    $background_image_src = $background_image_info[0];
                    $graphic = '<img src="' . $background_image_src . '">';
                }
            }
            else if ('icon' == $graphic_type && ! empty($atts['icon' ])) {
                $graphic = sprintf('<span class="' . esc_attr($atts['icon']) . '"></span>');
            }
            else if ('number' == $graphic_type) {
                $graphic = sprintf('<span></span>');
            }

            if (!empty($graphic)) {
                $alignment = !empty($atts['icon_vertical_align']) ? $atts['icon_vertical_align'] : '';
                $graphic = '<div class="media-object-section tailor-list__graphic ' . $alignment . '">' . $graphic . '</div>';
            }

            $outer_html = "<li {$html_atts}><div class=\"media-object\">%s</div></li>";
            $inner_html = $graphic .
                '<div class="media-object-section main-section tailor-list__body">' .
                    $title .
                    '<div class="tailor-list__content">%s</div>' .
                '</div>';
            $html = sprintf($outer_html, sprintf($inner_html, $content));
        }
        return $html;
    }
}

TailorList::get_instance()->init();
