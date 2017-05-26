<?php
/*
Plugin Name:        Tailor Foundation
Plugin URI:         http://genero.fi
Description:        Foundation elements for Tailor
Version:            0.0.1
Author:             Genero
Author URI:         http://genero.fi/
License:            MIT License
License URI:        http://opensource.org/licenses/MIT
*/

if (!defined('ABSPATH')) {
    exit;
}

class TailorFoundation {
    const VERSION = '0.0.1';

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        add_action('tailor_load_elements', [$this, 'load_elements'], 20);
        add_action('tailor_register_elements', [$this, 'register_elements'], 20);
        add_action('tailor_enqueue_sidebar_scripts', [$this, 'add_sidebar_scripts']);
        // add_filter('tailor_enable_canvas_styles', '__return_false');
        remove_action('wp_enqueue_scripts', [tailor(), 'enqueue_frontend_styles']);

        $this->load_overrides();
    }

    public function add_sidebar_scripts($handle) {
        $js_url = plugins_url('js/tailor-foundation.sidebar.js', __FILE__);
        wp_enqueue_script('tailor-foundation/sidebar/main', $js_url, [$handle], self::VERSION, true);
    }

    public function load_overrides() {
        require_once __DIR__ . '/includes/overrides/override.php';
        require_once __DIR__ . '/includes/overrides/column.php';
        require_once __DIR__ . '/includes/overrides/row.php';
        require_once __DIR__ . '/includes/overrides/grid.php';
        require_once __DIR__ . '/includes/overrides/grid-item.php';
        require_once __DIR__ . '/includes/overrides/button.php';
        require_once __DIR__ . '/includes/overrides/hero.php';
        require_once __DIR__ . '/includes/overrides/helpers.php';
        require_once __DIR__ . '/includes/overrides/styles.php';
    }

    public function load_elements() {
        // require_once __DIR__ . 'elements/class-row';
        // require_once __DIR__ . 'elements/class-column';
    }

    public function register_elements($element_manager) {
        // $element_manager->add_element('tailor_foundation_card', [
        //     'label'       => __('Column', 'tailor-foundation'),
        //     'description' => __('', 'tailor-foundation'),
        // ]);
    }
}

add_action('plugins_loaded', [TailorFoundation::get_instance(), 'init'], 11);
