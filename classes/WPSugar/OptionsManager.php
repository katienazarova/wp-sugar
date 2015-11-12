<?php

namespace WPSugar;

class OptionsManager {
    private $fields;

    function __construct($fields = array()) {
        $this->fields = $fields;
    }

    public function register() {
        add_action('admin_menu', array($this, 'registerOptions'));
    }
    
    public function registerOptions() {
        if (!isset($this->fields['name']) || !isset($this->fields['name']) || !isset($this->fields['name'])) {
            return;
        }

        add_settings_section(  
            $this->fields['name'],
            $this->fields['title'],
            '',
            'general'
        );

        if (is_array($this->fields['items'])) {
            foreach ($this->fields['items'] as $option) {
                $callback = 'render' . ucfirst($option['type']) . 'Option';
                try {
                    add_settings_field(
                        $option['name'],
                        $option['title'],
                        array($this, $callback),
                        'general',
                        $this->fields['name'],
                        array($option['name'])  
                    ); 
                    register_setting('general', $option['name'], 'esc_attr');
                } catch(Exception $e) {}
            }
        }
    }
    
    function renderTextOption($args) { 
        $option = get_option($args[0]);
        echo '<input type="text" id="' . $args[0] . '" name="'. $args[0] . '" value="' . $option . '" />';
    }
}