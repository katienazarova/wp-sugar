<?php

if (!function_exists('wpsugar_posts_list_shortcode')) {
	function wpsugar_posts_list_shortcode($atts) {
        if (!$atts['type']) {
            return '';
        }

        $atts = array_merge(array(
            'count'     => 10,
            'template'  => 'wpsugar-posts-list',
            'sort'      => 'sort',
            'order'     => 'ASC',
        ), $atts);

        $posts = get_posts(array(
            'post_type'         => $atts['type'],
            'posts_per_page'	=> $atts['count'],  
            'meta_key'          => $atts['type'] . '_' . $atts['sort'],
            'orderby'           => ($atts['sort'] == 'sort') ? 'meta_value_num' : 'meta_value',
            'order'             => $atts['order'],
        ));

        if (!$posts) {
            return '';
        }

        $items = array();
        foreach ($posts as $post) {
            $items[] = WPSugar\Utils::postToArray($post);
        }

        return WPSugar\View::render($atts['template'], array('items' => $items, 'type' => $atts['type']), false);
	}
	add_shortcode('wpsugar-posts-list', 'wpsugar_posts_list_shortcode');
}