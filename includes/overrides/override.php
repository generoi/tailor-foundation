<?php

namespace TailorFoundation;

class Override {

    private static $instances = [];

    public static function get_instance() {
        $cls = get_called_class();
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static;
        }
        return self::$instances[$cls];
    }

    public function remove_controls($element, $controls) {
        foreach ($controls as $control) {
            $element->remove_control($control);
        }
    }

    public function remove_settings($element, $settings) {
        foreach ($settings as $setting) {
            $element->remove_setting($setting);
            $element->remove_setting($setting . '_mobile');
            $element->remove_setting($setting . '_tablet');
        }
    }

    public function remove_html_classes(&$classes, $remove_classes) {
        foreach ($classes as $idx => $class) {
            foreach ($remove_classes as $remove) {
                // Wildcard suffix
                if (substr($remove, -1) == '*') {
                    if (strpos($class, substr($remove, 0, -1)) === 0) {
                        unset($classes[$idx]);
                    }
                }
                // Wildcard prefix
                if (substr($remove, 0, 1) == '*') {
                    if (strpos($class, substr($remove, 1)) !== FALSE) {
                        unset($classes[$idx]);
                    }
                }
            }
        }
    }

    public function remove_css_rules(&$css_rule_set, $settings) {
        foreach ($settings as $setting) {
            $settings[] = $setting . '_mobile';
            $settings[] = $setting . '_tablet';
        }
        foreach ($css_rule_set as $idx => $rule) {
            if (empty($rule['setting'])) {
                continue;
            }
            if (in_array($rule['setting'], $settings)) {
                unset($css_rule_set[$idx]);
            }
        }
    }

    public function multi_classes($property, $atts) {
        $classes = [];
        if (!empty($atts[$property])) {
            if (!is_array($atts[$property])) {
                $atts[$property] = explode(',', $atts[$property]);
            }
            foreach ($atts[$property] as $attribute) {
                $classes[] = trim($attribute);
            }
        }
        return $classes;
    }

    public function responsive_classes($property, $atts) {
        $breakpoints = [];
        if (!empty($atts[$property])) {
            $breakpoints['large'] = $atts[$property];
        }
        if (!empty($atts[$property . '_tablet'])) {
            $breakpoints['medium'] = $atts[$property . '_tablet'];
        }
        if (!empty($atts[$property . 'mobile'])) {
            $breakpoints['small'] = $atts[$property . '_mobile'];
        }
        return $breakpoints;
    }

    public function get_version() {
        return \TailorFoundation::VERSION;
    }
}
