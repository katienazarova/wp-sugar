<?php

if (!function_exists('wpsugar_form_shortcode')) {
	function wpsugar_form_shortcode($atts = array()) {
        if (!isset($atts['form']) || !$atts['form']) {
            return '';
        }

        $atts = array_merge(array(
            'template'  => 'wpsugar-form'
        ), $atts);

        $config = WPSugar\Config::get();
        if (!$config || !isset($config['forms']) || !is_array($config['forms'])) {
            return;
        }

        foreach ($config['forms'] as $item) {
            if ($item['name'] === $atts['form']) {
                $form = $item;
                break;
            }
        }
        
        if (!isset($form) || !$form) {
            return;
        }
        
        return WPSugar\View::render($atts['template'], array('form' => $form), false);
	}
	add_shortcode('wpsugar-form', 'wpsugar_form_shortcode');
}