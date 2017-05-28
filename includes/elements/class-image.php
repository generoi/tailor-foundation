<?php

use Tailor_Element;

if (!defined('WPINC')) {
    die;
}

class Tailor_Foundation_Image_Element extends Tailor_Element {

    protected function register_controls() {
        $this->add_section('general', [
            'title'    => __('General', 'tailor-foundation'),
            'priority' => 10,
        ]);

        $this->add_section('attributes', [
            'title'    => __('Attributes', 'tailor-foundation'),
            'priority' => 30,
        ]);

        $priority = 0;
        $this->add_setting('type', [
            'sanitize_callback' => 'tailor_sanitize_text',
        ]);
        $this->add_control('type', [
            'label'   => __('Type', 'tailor-foundation'),
            'type'    => 'select',
            'choices' => [
                'library'  => __('Media Library', 'tailor-foundation'),
                'external' => __('External image', 'tailor-foundation'),
            ],
            'section'  => 'general',
            'priority' => $priority += 10,
        ]);

        $this->add_setting('image', [
            'sanitize_callback' => 'tailor_sanitize_number',
        ]);
        $this->add_control('image', [
            'label'        => __('Image', 'tailor-foundation'),
            'type'         => 'image',
            'section'      => 'general',
            'priority'     => $priority += 10,
            'dependencies' => [
                'type' => ['condition' => 'not', 'value' => 'external'],
            ],
        ]);

        $this->add_setting('src', [
            'sanitize_callback' =>  'tailor_sanitize_number',
        ]);
        $this->add_control('src', [
            'label'       => __('Image', 'tailor-foundation'),
            'type'        => 'url',
            'section'     => 'general',
            'priority'    => $priority += 10,
            'input_attrs' => [
                'placeholder' => 'http://',
            ],
            'dependencies' => [
                'type' => ['condition' => 'equals', 'value' => 'external'],
            ],
        ]);

        $general_control_types = array(
            'horizontal_alignment',
            'horizontal_alignment_tablet',
            'horizontal_alignment_mobile',
            'image_link',
            'image_size',
        );
        $general_control_arguments = [
            'horizontal_aligmment' => [
                'setting' => ['default' => 'center'],
            ],
            'image_size' => [
                'setting' => ['default' =>  'full'],
                'control' => [
                    'dependencies' => [
                        'type' => ['condition' => 'not', 'value' => 'external'],
                    ],
                ],
            ],
            'image_link' => [
                'control' => [
                    'dependencies' => [
                        'type' => ['condition' => 'not', 'value' =>  'external'],
                    ],
                ],
            ],
        ];
        tailor_control_presets($this, $general_control_types, $general_control_arguments, $priority);

        $priority = 0;
        $attribute_control_types = [
            'class',
        ];
        $attribute_control_arguments = [];
        tailor_control_presets($this, $attribute_control_types, $attribute_control_arguments, $priority);
    }
}
