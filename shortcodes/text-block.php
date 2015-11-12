<?php

if (!function_exists('wpsugar_text_block_shortcode')) {
	function wpsugar_text_block_shortcode($atts = array(), $content = null) {
        if (!$atts['id']) {
            return '';
        }

        $atts = array_merge(array(
            'template'  => 'wpsugar-text-block',
            'class'     => ''
        ), $atts);

        $post = get_post($atts['id']);
        if (!$post) {
            return '';
        }

        $item = array(
            'id'    => $post->ID,
            'title' => WPSugar\Localization::getTranslatedContent($post->post_title),
            'text'  => WPSugar\Localization::getTranslatedContent($post->post_content)
        );

        $meta = get_post_meta($post->ID);
        if ($meta && is_array($meta)) {
            foreach ($meta as $key => $value) {
                if (strstr($key, $post->post_type)) {
                    $item[str_replace($post->post_type . '_', '', $key)] = WPSugar\Localization::getTranslatedContent($value[0]);
                }
            }
        }

        $style = '';
        if (isset($atts['style']) && is_array($atts['style'])) {
            foreach ($atts['style'] as $name => $value) {
                $style .= "{$name}: {$value};";
            }
        }

        return WPSugar\View::render($atts['template'], array('result' => $item, 'style' => $style, 'class' => $atts['class']), false);
	}
	add_shortcode('wpsugar-text-block', 'wpsugar_text_block_shortcode');
}