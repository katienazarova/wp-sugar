<?php

namespace WPSugar;

class CustomTypesManager {
    private $fields;

    function __construct($fields = array()) {
        $this->fields = $fields;
        $this->fields['params'] = array_merge(array(
            "public"        => true,
            "menu_position" => 5,
            "supports"      => array("title", "editor", "thumbnail"),
            "query_var"     => true,
            "has_archive"   => false,
        ), $this->fields['params']);
    }

    public function register() {
        add_action('init', array($this, 'registerCustomType'));
    }
    
    public function registerCustomType() {
        if (isset($this->fields['name']) && isset($this->fields['params'])) {
            register_post_type($this->fields['name'], $this->fields['params']);
        }
        if (isset($this->fields['custom_fields'])) {
            foreach ($this->fields['custom_fields'] as &$field) {
                $field['id'] = $this->fields['name'] . '_' . $field['id'];
                if (isset($field['std']) && $field['std'] === '#CURRENT_DATE#') {
                    $field['std'] = date('Y-m-d', time());
                }
            }

            add_action('add_meta_boxes_' . $this->fields['name'], array($this, 'addMetaBox'));
            add_action('save_post', array($this, 'saveCustomFields'));
        }
        flush_rewrite_rules();
        add_theme_support('post-thumbnails');
    }
    
    public function addMetaBox($post) {
        add_meta_box(
            'meta-box-' . $post->post_type,
            'Дополнительные свойства',
            array($this, 'showMetaBox'),
            $post->post_type,
            'normal',
            'core'
        );
    }

    public function showMetaBox() {
        global $post;

        View::render('metabox', array(
            'post' => $post,
            'fields' => $this->fields['custom_fields'],
            'nonce' => wp_create_nonce(basename(__FILE__))
        ), true);
    }
    
    public function saveCustomFields($post_id) {
        global $activities_custom_fields;

        if (!isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], basename(__FILE__))) {
            return $post_id;
        }
    
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
    
        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        } elseif (!current_user_can('edit_post', $post_id)) {
            return $post_id;
        }

        foreach ($this->fields['custom_fields'] as $field) {
            $old = get_post_meta($post_id, $field['id'], true);
            $new = $_POST[$field['id']];
    
            if ($new && $new != $old) {
                update_post_meta($post_id, $field['id'], stripslashes(htmlspecialchars($new)));
            } elseif ('' == $new && $old) {
                delete_post_meta($post_id, $field['id'], $old);
            }
        }
    }
}