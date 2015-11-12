<?php
namespace WPSugar;

class Utils {
	public static function formatDate($date) {
		$monthes = array(
            "01" => 'Января', "02" => 'Февраля', "03" => 'Марта', "04" => 'Апреля',
            "05" => 'Мая', "06" => 'Июня', "07" => 'Июля', "08" => 'Августа',
            "09" => 'Сентября', "10" => 'Октября', "11" => 'Ноября', "12" => 'Декабря'
        );
        $date_parts = explode('-', $date);

        return $date_parts[2] . ' ' . $monthes[$date_parts[1]]; // . ' ' . $date_parts[0];
	}
    
    public static function postToArray($post) {
        $image = wp_get_attachment_url(get_post_thumbnail_id($post->ID), 'full');
        $item = array(
            'id'    => $post->ID,
            'url'   => get_permalink($post->ID),
            'code'  => $post->post_name,
            'title' => $post->post_title,
            'image' => $image,
            'text'  => $post->post_content
        );

        $meta = get_post_meta($post->ID);
        if ($meta && is_array($meta)) {
            foreach ($meta as $key => $value) {
                if (strstr($key, $post->post_type)) {
                    $item[str_replace($post->post_type . '_', '', $key)] = $value[0];
                }
            }
        }

        return $item;
    }
}